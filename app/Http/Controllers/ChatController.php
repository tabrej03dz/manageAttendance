<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Services\ChatAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    public function __construct(private ChatAccessService $access) {}

    private function pageData(User $user): array
    {
        $conversations = $this->access->visibleQuery($user)
            ->with(['users:id,name', 'latestMessage.sender:id,name',
                'participants' => fn ($q) => $q->where('user_id', $user->id)])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->where('sender_id', '!=', $user->id)
                    ->whereRaw('EXISTS (SELECT 1 FROM chat_participants p
                        WHERE p.conversation_id = chat_messages.conversation_id
                        AND p.user_id = ? AND (p.last_read_at IS NULL
                        OR chat_messages.created_at > p.last_read_at))', [$user->id]);
            }])
            ->orderByDesc(ChatMessage::query()->select('created_at')
                ->whereColumn('chat_messages.conversation_id', 'chat_conversations.id')
                ->orderByDesc('id')->limit(1))
            ->orderByDesc('chat_conversations.id')->get();

        foreach ($conversations as $item) {
            $item->setAttribute('is_monitoring', $item->participants->isEmpty());
        }

        return [
            'conversations' => $conversations,
            'allowedUsers' => $this->access->allowedUsers($user),
            'canCreateTeamChat' => $this->access->role($user) === 'team_leader',
            'canSend' => false,
        ];
    }

    public function index()
    {
        return view('chat.index', $this->pageData(Auth::user()));
    }

    public function startPrivateChat(User $user)
    {
        $sender = Auth::user();
        abort_unless($this->access->canChat($sender, $user), 403, 'You cannot chat with this user.');
        $conversation = $this->privateConversation($sender, $user);

        return redirect()->route('chat.show', $conversation->id);
    }

    private function privateConversation(User $a, User $b): ChatConversation
    {
        return DB::transaction(function () use ($a, $b) {
            // Lock in a fixed order BEFORE checking existence to avoid duplicate chats.
            $ids = [(int) $a->id, (int) $b->id];
            sort($ids);
            foreach ($ids as $id) {
                User::query()->whereKey($id)->lockForUpdate()->firstOrFail();
            }

            $conversation = ChatConversation::query()->where('type', 'private')
                ->whereHas('participants', fn ($q) => $q->where('user_id', $a->id))
                ->whereHas('participants', fn ($q) => $q->where('user_id', $b->id))
                ->has('participants', '=', 2)->orderBy('id')->first();

            if (!$conversation) {
                $conversation = ChatConversation::create([
                    'type' => 'private', 'name' => null, 'created_by' => $a->id,
                ]);
                foreach ($ids as $id) {
                    ChatParticipant::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $id, 'last_read_at' => null,
                    ]);
                }
            }

            return $conversation;
        }, 3);
    }

    private function authorizeView(ChatConversation $conversation): User
    {
        $user = Auth::user();
        abort_unless($this->access->canView($user, $conversation), 403, 'You cannot view this conversation.');

        return $user;
    }

    private function updateRead(ChatConversation $conversation, User $user, $readAt): void
    {
        // Monitors never become participants and never mark someone else's chat read.
        ChatParticipant::query()->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)->update(['last_read_at' => $readAt]);
    }

    public function show(ChatConversation $conversation)
    {
        $user = $this->authorizeView($conversation);
        $readAt = now();
        $conversation->load('users:id,name');
        $messages = $conversation->messages()->with(['sender:id,name', 'replyTo.sender:id,name'])
            ->orderBy('id')->get();
        $this->updateRead($conversation, $user, $readAt);

        return view('chat.index', array_merge($this->pageData($user), [
            'conversation' => $conversation, 'messages' => $messages,
            'canSend' => $this->access->canSend($user, $conversation),
            'isMonitoring' => !$conversation->participants()->where('user_id', $user->id)->exists(),
        ]));
    }

    // A team broadcast sends separate private messages. Employees cannot group-chat.
    public function createTeamChat(Request $request)
    {
        $user = Auth::user();
        abort_unless($this->access->role($user) === 'team_leader', 403);
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $text = trim($data['message']);
        if ($text === '') {
            throw ValidationException::withMessages(['message' => 'Enter a team message.']);
        }
        $members = $this->access->users()->filter(fn (User $target) =>
            $this->access->canMonitorUser($user, $target) && $this->access->canChat($user, $target)
        )->sortBy('id');
        if ($members->isEmpty()) {
            return back()->withErrors(['team' => 'No members found in your reporting team.']);
        }

        // Each delivery commits independently; no nested locks across the whole team.
        foreach ($members as $member) {
            $conversation = $this->privateConversation($user, $member);
            DB::transaction(function () use ($conversation, $user, $text) {
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id, 'message' => $text,
                ]);
                $conversation->touch();
            });
        }

        return redirect()->route('chat.index')->with('success', "Message sent privately to {$members->count()} team members.");
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        $user = $this->authorizeView($conversation);
        // Checks persisted conversation pair again, including old employee-employee chats.
        abort_unless($this->access->canSend($user, $conversation), 403, 'This conversation is read-only for you.');
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx'],
            'reply_to_id' => ['nullable', 'integer'],
        ]);
        $text = trim($data['message'] ?? '');
        if ($text === '' && !$request->hasFile('attachment')) {
            throw ValidationException::withMessages(['message' => 'Enter a message or select a file.']);
        }
        $reply = !empty($data['reply_to_id'])
            ? $conversation->messages()->whereKey($data['reply_to_id'])->firstOrFail()
            : null;

        $path = null;
        $file = $request->file('attachment');
        if ($file) {
            $path = $file->store('chat-attachments', 'local');
            abort_unless($path, 500, 'Unable to save attachment.');
        }
        try {
            DB::transaction(function () use ($conversation, $user, $text, $reply, $path, $file) {
                ChatMessage::create([
                    'conversation_id' => $conversation->id, 'sender_id' => $user->id,
                    'message' => $text !== '' ? $text : null,
                    'attachment' => $path,
                    'attachment_name' => $file ? basename(str_replace('\\', '/', $file->getClientOriginalName())) : null,
                    'attachment_type' => $file?->getMimeType(),
                    'reply_to_id' => $reply?->id,
                ]);
                $conversation->touch();
            });
        } catch (\Throwable $error) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $error;
        }

        return redirect()->route('chat.show', $conversation->id);
    }

    public function messages(Request $request, ChatConversation $conversation)
    {
        $user = $this->authorizeView($conversation);
        $data = $request->validate(['last_message_id' => ['nullable', 'integer', 'min:0']]);
        $readAt = now();
        $messages = $conversation->messages()->with(['sender:id,name', 'replyTo.sender:id,name'])
            ->where('id', '>', $data['last_message_id'] ?? 0)->orderBy('id')->get();
        $this->updateRead($conversation, $user, $readAt);
        $canSend = $this->access->canSend($user, $conversation);

        return response()->json([
            'success' => true, 'can_send' => $canSend,
            'messages' => $messages->map(fn (ChatMessage $message) => [
                'id' => $message->id, 'sender_id' => $message->sender_id,
                'sender_name' => $message->sender?->name ?? 'Deleted User',
                'message' => $message->message,
                'is_mine' => (int) $message->sender_id === (int) $user->id,
                'attachment' => $message->attachment ? route('chat.attachment', $message->id) : null,
                'attachment_name' => $message->attachment_name,
                'attachment_type' => $message->attachment_type,
                'reply_to' => $message->replyTo && (int) $message->replyTo->conversation_id === (int) $conversation->id ? [
                    'id' => $message->replyTo->id, 'message' => $message->replyTo->message,
                    'sender_name' => $message->replyTo->sender?->name ?? 'Deleted User',
                ] : null,
                'created_at' => $message->created_at->format('h:i A'),
            ])->values(),
        ]);
    }

    public function markRead(ChatConversation $conversation)
    {
        $user = $this->authorizeView($conversation);
        $this->updateRead($conversation, $user, now());

        return response()->json(['success' => true]);
    }

    public function attachment(ChatMessage $message)
    {
        $conversation = ChatConversation::query()->findOrFail($message->conversation_id);
        $this->authorizeView($conversation);
        $path = $message->attachment;
        abort_unless($path && str_starts_with($path, 'chat-attachments/') && !str_contains($path, '..'), 404);
        // Legacy files work until chat:secure-attachments relocates them.
        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        $name = basename(str_replace('\\', '/', $message->attachment_name ?: 'attachment'));
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store'];
        $mime = Storage::disk($disk)->mimeType($path);
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return Storage::disk($disk)->response($path, $name, $headers);
        }

        return Storage::disk($disk)->download($path, $name, $headers);
    }
}
