<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteUser;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Services\ChatAccessService;
use App\Services\ChatNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NoteController extends Controller
{


public function __construct(
    private ChatAccessService $chatAccess,
    private ChatNotificationService $chatNotifications
) {}

    public function index(){

            $notes = Note::all();

        return view('dashboard.note.index', compact('notes'));
    }

    // public function create(){

    //     if (auth()->user()->hasRole('super_admin')){
    //         $offices = Office::all();
    //     }elseif(auth()->user()->hasRole('owner')){
    //         $officeNames = Office::where('owner_id', auth()->user()->id)->pluck('name');
    //         //            $roles = Role::where(function ($query) use ($officeNames) {
    //         //                foreach ($officeNames as $officeName) {
    //         //                    $query->orWhere('name', 'like', $officeName . '%');
    //         //                }
    //         //            })->get();
    //         $offices = auth()->user()->offices;
    //     }else{
    //         //            $roles = Role::where('name', 'like', auth()->user()->office->name.'%')->get();
    //         $offices = Office::find(auth()->user()->office_id);
    //     }


    //         //        $employees = HomeController::employeeList();
    //     return view('dashboard.note.create', compact( 'offices'));
    // }


    public function create()
{
    $user = auth()->user();

    if ($user->hasRole('super_admin')) {

        // Super Admin: All offices
        $offices = Office::query()
            ->orderBy('name')
            ->get();

    } elseif ($user->hasRole('owner')) {

        // Owner: Only owned offices
        $offices = Office::query()
            ->where('owner_id', $user->id)
            ->orderBy('name')
            ->get();

    } else {

        // Admin / Team Leader / Employee:
        // Only assigned office
        $offices = Office::query()
            ->where('id', $user->office_id)
            ->get();
    }

    return view('dashboard.note.create', compact('offices'));
}

    // public function store(Request $request){
    //     $request->validate([
    //         'title' => 'required',
    //         'description' => 'required',
    //         'office_id' => 'required',
    //         'role' => 'required_without:employees',
    //         'employees' => 'required_without:role',
    //     ]);

    //     $note = Note::create([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'office_id' => $request->office_id,
    //     ]);

    //     if($request->role){
    //         $users = User::role($request->role)->where('office_id', $request->office_id)->get();
    //         foreach ($users as $user){
    //             NoteUser::create(['note_id' => $note->id, 'user_id' => $user->id]);
    //         }
    //     }
    //     if($request->employees){
    //         if ($request->employees[0] == 'all'){
    //             $office = Office::find($request->office_id);
    //             foreach ($office->users as $user){
    //                 NoteUser::create(['note_id' => $note->id, 'user_id' => $user->id]);
    //             }
    //         }else{
    //             foreach ($request->employees as $key=>$value){
    //                 NoteUser::create(['note_id' => $note->id, 'user_id' => $value]);
    //             }
    //         }
    //     }
    //     return redirect('note')->with('success', 'Note created successfully');
    // }


    
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'office_id' => 'required|integer|exists:offices,id',
            'role' => 'nullable|required_without:employees|string',
            'employees' => 'nullable|required_without:role|array',
            'employees.*' => 'required|string',
        ]);

        $sender = auth()->user();
        $officeId = (int) $data['office_id'];

        /*
        |--------------------------------------------------------------------------
        | 1. Office permission
        |--------------------------------------------------------------------------
        */

        if ($sender->hasRole('super_admin')) {
            $allowed = true;
        } elseif ($sender->hasRole('owner')) {
            $allowed = $sender->offices()
                ->whereKey($officeId)
                ->exists();
        } else {
            $allowed = (int) $sender->office_id === $officeId;
        }

        abort_unless($allowed, 403, 'Office access denied.');

        /*
        |--------------------------------------------------------------------------
        | 2. Collect recipients
        |--------------------------------------------------------------------------
        */

        $recipientIds = collect();

        if (!empty($data['role'])) {
            $role = Role::query()
                ->where('name', $data['role'])
                ->first();

            if (!$role) {
                throw ValidationException::withMessages([
                    'role' => 'Selected role does not exist.',
                ]);
            }

            $roleIds = User::role($role->name)
                ->where('office_id', $officeId)
                ->pluck('id');

            $recipientIds = $recipientIds->merge($roleIds);
        }

        $employees = $data['employees'] ?? [];

        if (in_array('all', $employees, true)) {

            $allIds = User::where('office_id', $officeId)
                ->pluck('id');

            $recipientIds = $recipientIds->merge($allIds);

        } elseif (!empty($employees)) {

            foreach ($employees as $value) {
                if (!ctype_digit((string) $value) || (int) $value < 1) {
                    throw ValidationException::withMessages([
                        'employees' => 'Invalid employee selected.',
                    ]);
                }
            }

            $selectedIds = collect($employees)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $validIds = User::where('office_id', $officeId)
                ->whereIn('id', $selectedIds)
                ->pluck('id');

            if ($validIds->count() !== $selectedIds->count()) {
                throw ValidationException::withMessages([
                    'employees' => 'Some employees do not belong to this office.',
                ]);
            }

            $recipientIds = $recipientIds->merge($validIds);
        }

        $recipientIds = $recipientIds
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values();

        if ($recipientIds->isEmpty()) {
            throw ValidationException::withMessages([
                'employees' => 'No employees found for this note.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Create note and assign employees
        |--------------------------------------------------------------------------
        */

        $note = DB::transaction(function () use (
            $data,
            $officeId,
            $recipientIds
        ) {
            $note = Note::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'office_id' => $officeId,
            ]);

            foreach ($recipientIds as $userId) {
                NoteUser::create([
                    'note_id' => $note->id,
                    'user_id' => $userId,
                ]);
            }

            return $note;
        }, 3);

        /*
        |--------------------------------------------------------------------------
        | 4. Prepare chat message
        |--------------------------------------------------------------------------
        */

        $title = trim($data['title']);

        $description = trim(strip_tags(
            $data['description']
        ));

        $messageText = "📢 NEW NOTICE\n\n"
            . "📌 Title: {$title}\n\n"
            . "📝 Description:\n{$description}\n\n"
            . "Please check your Notes section.";

        // Chat API allows max 5000 characters.
        $messageText = Str::limit($messageText, 5000, '');

        /*
        |--------------------------------------------------------------------------
        | 5. Send messages and notifications
        |--------------------------------------------------------------------------
        */

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipientIds as $userId) {

            // A note can be assigned to its creator,
            // but a private chat to yourself is skipped.
            if ($userId === (int) $sender->id) {
                continue;
            }

            try {
                DB::transaction(function () use (
                    $sender,
                    $userId,
                    $messageText
                ) {
                    $receiver = User::findOrFail($userId);

                    if (!$this->chatAccess->canChat(
                        $sender,
                        $receiver
                    )) {
                        throw new \RuntimeException(
                            'Chat access denied for user ' . $userId
                        );
                    }

                    $conversation = $this->findOrCreateNoteChat(
                        $sender,
                        $receiver
                    );

                    $message = ChatMessage::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => $sender->id,
                        'message' => $messageText,
                    ]);

                    $conversation->touch();

                    $this->chatNotifications->recordNoteMessage(
                        $message,
                        $receiver
                    );
                }, 3);

                $sentCount++;

            } catch (\Throwable $e) {
                $failedCount++;

                Log::error('Note chat delivery failed', [
                    'note_id' => $note->id,
                    'receiver_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Redirect
        |--------------------------------------------------------------------------
        */

        $message = "Note created successfully. "
            . "Messages saved: {$sentCount}.";

        if ($failedCount > 0) {
            return redirect('note')->with(
                'warning',
                $message . " Failed: {$failedCount}. Check logs."
            );
        }

        return redirect('note')->with(
            'success',
            $message . ' Push notifications queued.'
        );
    }


    
    private function findOrCreateNoteChat(
        User $sender,
        User $receiver
    ): ChatConversation {

        $ids = [
            (int) $sender->id,
            (int) $receiver->id,
        ];

        sort($ids);

        // Called inside DB::transaction().
        foreach ($ids as $id) {
            User::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();
        }

        // Use existing private conversation if available.
        $conversation = ChatConversation::query()
            ->where('type', 'private')
            ->whereHas('participants', function ($q) use ($sender) {
                $q->where('user_id', $sender->id);
            })
            ->whereHas('participants', function ($q) use ($receiver) {
                $q->where('user_id', $receiver->id);
            })
            ->has('participants', '=', 2)
            ->orderBy('id')
            ->first();

        if ($conversation) {
            return $conversation;
        }

        // Otherwise create a new conversation.
        $conversation = ChatConversation::create([
            'type' => 'private',
            'name' => null,
            'created_by' => $sender->id,
        ]);

        foreach ($ids as $id) {
            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $id,
                'last_read_at' => null,
                'last_read_message_id' => 0,
            ]);
        }

        return $conversation;
    }


    // OfficeController.php
    public function getOfficeRoleAndEmployee($id)
    {
        $office = Office::find($id);
        $employees = User::where('office_id', $office->id)->get();
        $roles = Role::where('name', 'like', $office->name.'%')->orwhereIn('name', ['admin', 'team_leader', 'employee'])->get();

//        dd($employees, $roles);

        return response()->json([
            'employees' => $employees,
            'roles' => $roles,
        ]);

    }

    public function status(Note $note){
        if ($note->status == '1'){
            $note->update(['status' => '0']);
        }else{
            $note->update(['status' => '1']);
        }
        return back()->with('success', 'status changed successfully');
    }

    public function delete(Note $note){
        $note->delete();
        return back()->with('success', 'Note deleted successfully');
    }



    
public function recordNoteMessage(
    ChatMessage $message,
    User $receiver
): void {
    $conversation = ChatConversation::findOrFail(
        $message->conversation_id
    );

    if ((int) $receiver->id === (int) $message->sender_id) {
        return;
    }

    if (!$this->access->canView($receiver, $conversation)) {
        throw new \RuntimeException(
            'Note receiver cannot view this conversation.'
        );
    }

    $notice = ChatNotification::firstOrCreate(
        [
            'user_id' => $receiver->id,
            'message_id' => $message->id,
        ],
        [
            'sender_id' => $message->sender_id,
            'conversation_id' => $conversation->id,
            'expires_at' => now()->addMinutes(10),
        ]
    );

    if ($notice->wasRecentlyCreated) {
        $noticeId = (int) $notice->id;

        DB::afterCommit(function () use ($noticeId) {
            try {
                SendChatPush::dispatch($noticeId);
            } catch (\Throwable $e) {
                Log::warning(
                    'Note push dispatch failed',
                    [
                        'notification_id' => $noticeId,
                        'exception' => get_class($e),
                    ]
                );
            }
        });
    }
}

}
