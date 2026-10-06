<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatNotification;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Services\ChatAccessService;
use App\Services\ChatNotificationService;
use App\Services\ChatReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    public function __construct(
        private ChatAccessService $access,
        private ChatNotificationService $notifications,
        private ChatReadService $reads
    ) {}

    private function ok($data, int $status = 200, array $extra = [])
    {
        return response()->json(array_merge(['success' => true, 'data' => $data], $extra), $status)
            ->header('Cache-Control', 'private, no-store');
    }

    private function authorizeView(Request $request, ChatConversation $conversation): User
    {
        $user = $request->user();
        abort_unless($this->access->canView($user, $conversation), 403, 'You cannot view this conversation.');
        return $user;
    }

    private function userData(?User $user, ?int $fallbackId = null): array
    {
        return [
            'id' => $user ? (int) $user->id : $fallbackId,
            'name' => $user?->name ?? 'Deleted User',
            'role' => $user ? $this->access->role($user) : null,
            'office_id' => $user?->office_id,
            'team_leader_id' => $user?->team_leader_id,
        ];
    }

    private function timestamp($value): ?string
    {
        return $value ? $value->copy()->utc()->toISOString() : null;
    }

    private function messageData(ChatMessage $message, User $viewer): array
    {
        $message->loadMissing('replyTo');
        $reply = $message->replyTo;
        $reply = $reply && (int) $reply->conversation_id === (int) $message->conversation_id ? $reply : null;
        return [
            'id' => (int) $message->id,
            'conversation_id' => (int) $message->conversation_id,
            'sender' => $this->userData($this->access->users()->get((int) $message->sender_id), (int) $message->sender_id),
            'is_mine' => (int) $message->sender_id === (int) $viewer->id,
            'message' => $message->message,
            'attachment' => $message->attachment ? [
                'url' => route('api.chat.attachments.show', $message->id),
                'name' => $message->attachment_name,
                'mime_type' => $message->attachment_type,
            ] : null,
            'reply_to' => $reply ? [
                'id' => (int) $reply->id,
                'message' => $reply->message,
                'sender' => $this->userData($this->access->users()->get((int) $reply->sender_id), (int) $reply->sender_id),
                'has_attachment' => !empty($reply->attachment),
            ] : null,
            'created_at' => $this->timestamp($message->created_at),
        ];
    }

    private function conversationData(ChatConversation $conversation, User $viewer): array
    {
        $conversation->loadMissing(['participants', 'latestMessage.replyTo']);
        $own = $conversation->participants->firstWhere('user_id', $viewer->id);
        $unread = $conversation->getAttribute('unread_count');
        if ($unread === null) {
            $unread = $own ? $conversation->messages()->where('sender_id', '!=', $viewer->id)
                ->where('id', '>', (int) $own->last_read_message_id)->count() : 0;
        }
        return [
            'id' => (int) $conversation->id,
            'type' => $conversation->type,
            'name' => $conversation->name,
            'participants' => $conversation->participants->map(fn ($p) => array_merge(
                $this->userData($this->access->users()->get((int) $p->user_id), (int) $p->user_id),
                ['last_read_message_id' => (int) $p->last_read_message_id]
            ))->values()->all(),
            'is_monitoring' => $own === null,
            'can_send' => $this->access->canSend($viewer, $conversation),
            'unread_count' => (int) $unread,
            'last_read_message_id' => $own ? (int) $own->last_read_message_id : null,
            'latest_message' => $conversation->latestMessage
                ? $this->messageData($conversation->latestMessage, $viewer) : null,
            'updated_at' => $this->timestamp($conversation->updated_at),
        ];
    }

    public function users(Request $request)
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $users = $this->access->allowedUsers($request->user());
        $search = mb_strtolower(trim($data['search'] ?? ''));
        if ($search !== '') {
            $users = $users->filter(fn ($user) => str_contains(mb_strtolower($user->name), $search))->values();
        }
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 30);
        return $this->ok($users->forPage($page, $perPage)->map(fn ($u) => $this->userData($u))->values()->all(), 200, [
            'pagination' => ['page' => $page, 'per_page' => $perPage, 'total' => $users->count(),
                'has_more' => $page * $perPage < $users->count()],
        ]);
    }

    public function conversations(Request $request)
    {
        $data = $request->validate([
            'scope' => ['nullable', 'in:mine,monitoring,all'],
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $user = $request->user();
        $query = $this->access->visibleQuery($user);
        $scope = $data['scope'] ?? 'mine';
        if ($scope === 'mine') {
            $query->whereHas('participants', fn ($q) => $q->where('user_id', $user->id));
        } elseif ($scope === 'monitoring') {
            $query->whereDoesntHave('participants', fn ($q) => $q->where('user_id', $user->id));
        }
        $search = trim($data['search'] ?? '');
        if ($search !== '') {
            $ids = $this->access->users()->filter(fn ($u) => str_contains(mb_strtolower($u->name), mb_strtolower($search)))->keys();
            $query->where(function ($q) use ($ids, $search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('participants', fn ($p) => $p->whereIn('user_id', $ids));
            });
        }
        $result = $query->with(['participants', 'latestMessage.replyTo'])
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('sender_id', '!=', $user->id)
                ->whereRaw('EXISTS (SELECT 1 FROM chat_participants p WHERE p.conversation_id = chat_messages.conversation_id
                    AND p.user_id = ? AND chat_messages.id > p.last_read_message_id)', [$user->id])])
            ->orderByDesc('updated_at')->orderByDesc('id')
            ->paginate((int) ($data['per_page'] ?? 30), ['*'], 'page', (int) ($data['page'] ?? 1));
        return $this->ok($result->getCollection()->map(fn ($c) => $this->conversationData($c, $user))->all(), 200, [
            'pagination' => ['page' => $result->currentPage(), 'per_page' => $result->perPage(),
                'total' => $result->total(), 'has_more' => $result->hasMorePages()],
        ]);
    }

    private function privateConversation(User $a, User $b): ChatConversation
    {
        return DB::transaction(function () use ($a, $b) {
            $ids = [(int) $a->id, (int) $b->id];
            sort($ids);
            foreach ($ids as $id) { User::query()->whereKey($id)->lockForUpdate()->firstOrFail(); }
            $conversation = ChatConversation::query()->where('type', 'private')
                ->whereHas('participants', fn ($q) => $q->where('user_id', $a->id))
                ->whereHas('participants', fn ($q) => $q->where('user_id', $b->id))
                ->has('participants', '=', 2)->orderBy('id')->first();
            if (!$conversation) {
                $conversation = ChatConversation::create(['type' => 'private', 'name' => null, 'created_by' => $a->id]);
                foreach ($ids as $id) {
                    ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $id,
                        'last_read_at' => null, 'last_read_message_id' => 0]);
                }
            }
            return $conversation;
        }, 3);
    }

    public function start(Request $request)
    {
        $data = $request->validate(['user_id' => ['required', 'integer', 'min:1']]);
        $user = $request->user();
        $target = $this->access->users()->get((int) $data['user_id']);
        abort_unless($target, 404, 'User not found.');
        abort_unless($this->access->canChat($user, $target), 403, 'You cannot chat with this user.');
        $conversation = $this->privateConversation($user, $target);
        return $this->ok($this->conversationData($conversation, $user));
    }

    public function show(Request $request, ChatConversation $conversation)
    {
        return $this->ok($this->conversationData($conversation, $this->authorizeView($request, $conversation)));
    }

    public function messages(Request $request, ChatConversation $conversation)
    {
        $user = $this->authorizeView($request, $conversation);
        $data = $request->validate([
            'before_id' => ['nullable', 'integer', 'min:1', 'prohibits:after_id'],
            'after_id' => ['nullable', 'integer', 'min:0', 'prohibits:before_id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $limit = (int) ($data['limit'] ?? 30);
        $query = $conversation->messages()->with('replyTo');
        $forward = isset($data['after_id']);
        if ($forward) { $query->where('id', '>', $data['after_id']); }
        if (isset($data['before_id'])) { $query->where('id', '<', $data['before_id']); }
        $rows = $query->orderBy('id', $forward ? 'asc' : 'desc')->limit($limit + 1)->get();
        $hasMore = $rows->count() > $limit;
        $rows = $rows->take($limit);
        if (!$forward) { $rows = $rows->reverse(); }
        return $this->ok($rows->map(fn ($m) => $this->messageData($m, $user))->values()->all(), 200, [
            'pagination' => ['direction' => $forward ? 'newer' : 'older', 'limit' => $limit,
                'has_more' => $hasMore, 'oldest_id' => $rows->first()?->id, 'newest_id' => $rows->last()?->id],
            'can_send' => $this->access->canSend($user, $conversation),
        ]);
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        $user = $this->authorizeView($request, $conversation);
        abort_unless($this->access->canSend($user, $conversation), 403, 'This conversation is read-only for you.');
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,webm,aac,wav,mp4,x-m4a,opus,ogg,caf,m4a,pdf,doc,docx,xls,xlsx'],
            'reply_to_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $text = trim($data['message'] ?? '');
        $file = $request->file('attachment');
        if ($text === '' && !$file) {
            throw ValidationException::withMessages(['message' => 'Enter a message or select a file.']);
        }
        $reply = isset($data['reply_to_id'])
            ? $conversation->messages()->whereKey($data['reply_to_id'])->firstOrFail() : null;
        $path = $file ? $file->store('chat-attachments', 'local') : null;
        abort_if($file && !$path, 500, 'Unable to save attachment.');
        try {
            $message = DB::transaction(function () use ($conversation, $user, $text, $reply, $file, $path) {
                ChatConversation::query()->whereKey($conversation->id)->lockForUpdate()->firstOrFail();
                $message = ChatMessage::create([
                    'conversation_id' => $conversation->id, 'sender_id' => $user->id,
                    'message' => $text !== '' ? $text : null, 'attachment' => $path,
                    'attachment_name' => $file ? basename(str_replace('\\', '/', $file->getClientOriginalName())) : null,
                    'attachment_type' => $file?->getMimeType(), 'reply_to_id' => $reply?->id,
                ]);
                $conversation->touch();
                $this->notifications->record($message);
                return $message;
            }, 3);
        } catch (\Throwable $error) {
            if ($path) { Storage::disk('local')->delete($path); }
            throw $error;
        }
        return $this->ok($this->messageData($message, $user), 201);
    }

    public function read(Request $request, ChatConversation $conversation)
    {
        $user = $this->authorizeView($request, $conversation);
        $data = $request->validate(['last_message_id' => ['required', 'integer', 'min:0']]);
        $id = $this->reads->mark($user, $conversation, (int) $data['last_message_id']);
        return $this->ok(['marked' => $id !== null, 'last_read_message_id' => $id,
            'unread' => $this->access->unreadCounts($user)]);
    }

    public function unread(Request $request)
    {
        return $this->ok($this->access->unreadCounts($request->user()));
    }

    public function attachment(Request $request, ChatMessage $message)
    {
        $conversation = ChatConversation::query()->findOrFail($message->conversation_id);
        $this->authorizeView($request, $conversation);
        $path = $message->attachment;
        abort_unless($path && str_starts_with($path, 'chat-attachments/') && !str_contains($path, '..'), 404);
        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        $name = basename(str_replace('\\', '/', $message->attachment_name ?: 'attachment'));
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store'];
        $mime = Storage::disk($disk)->mimeType($path);
        if (!$request->boolean('download') && in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return Storage::disk($disk)->response($path, $name, $headers);
        }
        return Storage::disk($disk)->download($path, $name, $headers);
    }

    private function teamMembers(User $user)
    {
        abort_unless($this->access->role($user) === 'team_leader', 403, 'Only team leaders can broadcast to their team.');
        return $this->access->users()->filter(fn (User $target) =>
            $this->access->canMonitorUser($user, $target) && $this->access->canChat($user, $target)
        )->sortBy('id')->values();
    }

    public function team(Request $request)
    {
        return $this->ok($this->teamMembers($request->user())->map(fn ($u) => $this->userData($u))->all());
    }

    public function broadcast(Request $request)
    {
        $user = $request->user();
        $members = $this->teamMembers($user);
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $text = trim($data['message']);
        if ($text === '' || $members->isEmpty()) {
            throw ValidationException::withMessages(['message' => $text === '' ? 'Enter a team message.' : 'No reporting team members found.']);
        }
        $deliveries = DB::transaction(function () use ($user, $members, $text) {
            // All user locks have one global order; one transaction makes the broadcast atomic.
            $ids = $members->pluck('id')->push($user->id)->unique()->sort()->values();
            foreach ($ids as $id) { User::query()->whereKey($id)->lockForUpdate()->firstOrFail(); }
            $conversations = $members->map(fn ($member) => $this->privateConversation($user, $member))->sortBy('id');
            $result = [];
            foreach ($conversations as $conversation) {
                ChatConversation::query()->whereKey($conversation->id)->lockForUpdate()->firstOrFail();
                $message = ChatMessage::create(['conversation_id' => $conversation->id, 'sender_id' => $user->id, 'message' => $text]);
                $conversation->touch();
                $this->notifications->record($message);
                $result[] = ['conversation_id' => (int) $conversation->id, 'message_id' => (int) $message->id];
            }
            return $result;
        }, 3);
        return $this->ok(['sent_count' => count($deliveries), 'deliveries' => $deliveries], 201);
    }

    public function notifications(Request $request, \App\Services\ChatFcmService $fcm)
    {
        $data = $request->validate([
            'before_id' => ['nullable', 'integer', 'min:1', 'prohibits:after_id'],
            'after_id' => ['nullable', 'integer', 'min:0', 'prohibits:before_id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'unread_only' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        $visible = $this->access->visibleQuery($user);
        $query = ChatNotification::query()->where('user_id', $user->id)
            ->whereHas('message')->with('message.sender')
            ->whereIn('conversation_id', $visible->select('chat_conversations.id'));
        if ($request->boolean('unread_only')) { $query->whereNull('read_at'); }
        $forward = isset($data['after_id']);
        if ($forward) { $query->where('id', '>', $data['after_id']); }
        if (isset($data['before_id'])) { $query->where('id', '<', $data['before_id']); }
        $limit = (int) ($data['limit'] ?? 30);
        $rows = $query->orderBy('id', $forward ? 'asc' : 'desc')->limit($limit + 1)->get();
        $hasMore = $rows->count() > $limit;
        $rows = $rows->take($limit);
        if (!$forward) { $rows = $rows->reverse(); }
        return $this->ok($rows->map(fn ($n) => [
            'id' => (int) $n->id, 'conversation_id' => (int) $n->conversation_id,
            'message_id' => (int) $n->message_id,
            'sender' => $this->userData($this->access->users()->get((int) $n->sender_id), (int) $n->sender_id),
            'title' => $fcm->notificationFor($n->message)['title'],
            'body' => $fcm->notificationFor($n->message)['body'], 'read_at' => $this->timestamp($n->read_at),
            'created_at' => $this->timestamp($n->created_at),
        ])->values()->all(), 200, [
            'pagination' => ['has_more' => $hasMore, 'oldest_id' => $rows->first()?->id, 'newest_id' => $rows->last()?->id,
                'direction' => $forward ? 'newer' : 'older', 'limit' => $limit],
            'unread' => $this->access->unreadCounts($user),
        ]);
    }
}
