<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * ============================================================
     * MAIN CHAT PAGE
     * ============================================================
     */
    public function index()
    {
        $user = Auth::user();

        $conversations = $this->conversationList($user);
        $allowedUsers = $this->getAllowedUsers($user);

        return view('chat.index', compact(
            'conversations',
            'allowedUsers'
        ));
    }


    /**
     * ============================================================
     * START / OPEN PRIVATE CHAT
     * ============================================================
     */
    public function startPrivateChat(User $user)
    {
        $authUser = Auth::user();

        /*
         * Khud se chat nahi.
         */
        abort_if(
            (int) $authUser->id === (int) $user->id,
            403,
            'You cannot chat with yourself.'
        );

        /*
         * Role permission check.
         */
        if (!$this->canStartPrivateChat($authUser, $user)) {
            abort(
                403,
                'You are not allowed to chat with this user.'
            );
        }

        /*
         * Check karein ki in dono users ki private
         * conversation already hai ya nahi.
         */
        $conversation = ChatConversation::query()

            ->where('type', 'private')

            ->whereHas(
                'participants',
                function ($query) use ($authUser) {
                    $query->where(
                        'user_id',
                        $authUser->id
                    );
                }
            )

            ->whereHas(
                'participants',
                function ($query) use ($user) {
                    $query->where(
                        'user_id',
                        $user->id
                    );
                }
            )

            ->withCount('participants')

            ->having(
                'participants_count',
                '=',
                2
            )

            ->first();


        /*
         * Existing nahi hai to new create.
         */
        if (!$conversation) {

            $conversation = DB::transaction(
                function () use ($authUser, $user) {

                    $conversation =
                        ChatConversation::create([
                            'type' => 'private',
                            'name' => null,
                            'created_by' => $authUser->id,
                        ]);


                    /*
                     * Sender participant.
                     */
                    ChatParticipant::create([
                        'conversation_id' =>
                            $conversation->id,

                        'user_id' =>
                            $authUser->id,

                        'last_read_at' =>
                            now(),
                    ]);


                    /*
                     * Receiver participant.
                     */
                    ChatParticipant::create([
                        'conversation_id' =>
                            $conversation->id,

                        'user_id' =>
                            $user->id,

                        'last_read_at' =>
                            null,
                    ]);


                    return $conversation;
                }
            );
        }


        return redirect()->route(
            'chat.show',
            $conversation->id
        );
    }


    /**
     * ============================================================
     * OPEN CONVERSATION
     * ============================================================
     */
    public function show(
        ChatConversation $conversation
    ) {
        $user = Auth::user();


        /*
         * User ko conversation dekhne ka access hai?
         */
        $this->authorizeConversation(
            $conversation,
            $user
        );


        /*
         * Conversation users.
         */
        $conversation->load([
            'users:id,name',
        ]);


        /*
         * Messages.
         */
        $messages = $conversation
            ->messages()

            ->with([
                'sender:id,name',
                'replyTo.sender:id,name',
            ])

            ->orderBy('id')

            ->get();


        /*
         * Agar logged-in user actual participant hai
         * to conversation read mark karein.
         *
         * Super Admin/Admin monitoring ke case me
         * participant na ho to kuch create nahi hoga.
         */
        ChatParticipant::query()

            ->where(
                'conversation_id',
                $conversation->id
            )

            ->where(
                'user_id',
                $user->id
            )

            ->update([
                'last_read_at' => now(),
            ]);


        /*
         * Sidebar data.
         */
        $conversations =
            $this->conversationList($user);


        /*
         * New chat dropdown.
         */
        $allowedUsers =
            $this->getAllowedUsers($user);


        return view(
            'chat.index',
            compact(
                'conversation',
                'messages',
                'conversations',
                'allowedUsers'
            )
        );
    }


    /**
     * ============================================================
     * CREATE / OPEN TEAM CHAT
     *
     * Sirf Team Leader.
     * ============================================================
     */
    public function createTeamChat()
    {
        $user = Auth::user();


        /*
         * Sirf Team Leader team chat bana sakta hai.
         */
        if (!$user->hasRole('team_leader')) {

            abort(
                403,
                'Only Team Leader can create team chat.'
            );
        }


        /*
         * TL ke employees.
         */
        $employees = User::query()

            ->where(
                'team_leader_id',
                $user->id
            )

            ->get();


        if ($employees->isEmpty()) {

            return back()->withErrors([
                'team' =>
                    'No employees found in your team.',
            ]);
        }


        /*
         * Har TL ka ek team chat.
         */
        $conversation =
            ChatConversation::firstOrCreate(

                [
                    'type' => 'team',
                    'created_by' => $user->id,
                ],

                [
                    'name' =>
                        $user->name . ' Team',
                ]

            );


        /*
         * TL participant.
         */
        ChatParticipant::firstOrCreate(

            [
                'conversation_id' =>
                    $conversation->id,

                'user_id' =>
                    $user->id,
            ],

            [
                'last_read_at' =>
                    now(),
            ]

        );


        /*
         * Saare current employees add.
         */
        foreach ($employees as $employee) {

            ChatParticipant::firstOrCreate([
                'conversation_id' =>
                    $conversation->id,

                'user_id' =>
                    $employee->id,
            ]);
        }


        /*
         * Agar employee dusre TL me transfer ho gaya,
         * purane team chat se remove.
         */
        $currentUserIds =
            $employees
                ->pluck('id')
                ->push($user->id)
                ->unique()
                ->values();


        ChatParticipant::query()

            ->where(
                'conversation_id',
                $conversation->id
            )

            ->whereNotIn(
                'user_id',
                $currentUserIds
            )

            ->delete();


        return redirect()->route(
            'chat.show',
            $conversation->id
        );
    }


    /**
     * ============================================================
     * SEND MESSAGE
     * ============================================================
     */
    public function send(
        Request $request,
        ChatConversation $conversation
    ) {
        $user = Auth::user();


        /*
         * Conversation access check.
         */
        $this->authorizeConversation(
            $conversation,
            $user
        );


        /*
         * IMPORTANT:
         *
         * Super Admin/Admin kisi aur ki conversation
         * monitor kar sakta hai.
         *
         * Lekin us conversation me participant nahi hai
         * to usme message inject nahi karega.
         */
        $isParticipant =
            ChatParticipant::query()

                ->where(
                    'conversation_id',
                    $conversation->id
                )

                ->where(
                    'user_id',
                    $user->id
                )

                ->exists();


        if (!$isParticipant) {

            abort(
                403,
                'You can view this conversation but cannot send a message in it.'
            );
        }


        /*
         * Validation.
         */
        $request->validate([

            'message' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:10240',

                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx',
            ],

            'reply_to_id' => [
                'nullable',
                'integer',
            ],

        ]);


        /*
         * Message ya attachment me se
         * kam se kam ek required.
         */
        if (
            !$request->filled('message') &&
            !$request->hasFile('attachment')
        ) {

            return back()

                ->withErrors([
                    'message' =>
                        'Please enter a message or select a file.',
                ])

                ->withInput();
        }


        /*
         * Reply validation.
         */
        $replyTo = null;


        if ($request->filled('reply_to_id')) {

            $replyTo =
                ChatMessage::query()

                    ->where(
                        'conversation_id',
                        $conversation->id
                    )

                    ->where(
                        'id',
                        $request->reply_to_id
                    )

                    ->firstOrFail();
        }


        /*
         * Attachment.
         */
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;


        if ($request->hasFile('attachment')) {

            $file =
                $request->file('attachment');


            $attachmentPath =
                $file->store(
                    'chat-attachments',
                    'public'
                );


            $attachmentName =
                $file->getClientOriginalName();


            $attachmentType =
                $file->getClientMimeType();
        }


        /*
         * Message create.
         */
        ChatMessage::create([

            'conversation_id' =>
                $conversation->id,

            'sender_id' =>
                $user->id,

            'message' =>
                $request->filled('message')
                    ? trim($request->message)
                    : null,

            'attachment' =>
                $attachmentPath,

            'attachment_name' =>
                $attachmentName,

            'attachment_type' =>
                $attachmentType,

            'reply_to_id' =>
                $replyTo?->id,

        ]);


        /*
         * Sender ka last read update.
         */
        ChatParticipant::query()

            ->where(
                'conversation_id',
                $conversation->id
            )

            ->where(
                'user_id',
                $user->id
            )

            ->update([
                'last_read_at' => now(),
            ]);


        return redirect()->route(
            'chat.show',
            $conversation->id
        );
    }


    /**
     * ============================================================
     * AJAX - GET NEW MESSAGES
     * ============================================================
     */
    public function messages(
        Request $request,
        ChatConversation $conversation
    ) {
        $user = Auth::user();


        /*
         * Access check.
         */
        $this->authorizeConversation(
            $conversation,
            $user
        );


        $lastMessageId =
            (int) $request->get(
                'last_message_id',
                0
            );


        /*
         * Sirf new messages.
         */
        $messages =
            $conversation
                ->messages()

                ->with([
                    'sender:id,name',
                    'replyTo.sender:id,name',
                ])

                ->when(
                    $lastMessageId > 0,

                    function ($query) use (
                        $lastMessageId
                    ) {

                        $query->where(
                            'id',
                            '>',
                            $lastMessageId
                        );
                    }
                )

                ->orderBy('id')

                ->get();


        /*
         * Participant ka read time update.
         *
         * Super Admin/Admin monitoring kare aur
         * participant na ho to update nahi hoga.
         */
        ChatParticipant::query()

            ->where(
                'conversation_id',
                $conversation->id
            )

            ->where(
                'user_id',
                $user->id
            )

            ->update([
                'last_read_at' => now(),
            ]);


        /*
         * JSON.
         */
        return response()->json([

            'success' => true,

            'messages' =>
                $messages
                    ->map(
                        function ($message) use ($user) {

                            return [

                                'id' =>
                                    $message->id,

                                'sender_id' =>
                                    $message->sender_id,

                                'sender_name' =>
                                    $message
                                        ->sender?->name
                                        ?? 'User',

                                'message' =>
                                    $message->message,

                                'is_mine' =>
                                    (int) $message->sender_id
                                    ===
                                    (int) $user->id,

                                'attachment' =>
                                    $message->attachment
                                        ? Storage::disk(
                                            'public'
                                        )->url(
                                            $message->attachment
                                        )
                                        : null,

                                'attachment_name' =>
                                    $message
                                        ->attachment_name,

                                'attachment_type' =>
                                    $message
                                        ->attachment_type,

                                'reply_to' =>
                                    $message->replyTo
                                        ? [

                                            'id' =>
                                                $message
                                                    ->replyTo
                                                    ->id,

                                            'message' =>
                                                $message
                                                    ->replyTo
                                                    ->message,

                                            'sender_name' =>
                                                $message
                                                    ->replyTo
                                                    ->sender?->name
                                                ?? 'User',

                                        ]
                                        : null,

                                'created_at' =>
                                    $message
                                        ->created_at
                                        ->format(
                                            'h:i A'
                                        ),

                            ];
                        }
                    )
                    ->values(),

        ]);
    }


    /**
     * ============================================================
     * MARK READ
     * ============================================================
     */
    public function markRead(
        ChatConversation $conversation
    ) {
        $user = Auth::user();


        $this->authorizeConversation(
            $conversation,
            $user
        );


        ChatParticipant::query()

            ->where(
                'conversation_id',
                $conversation->id
            )

            ->where(
                'user_id',
                $user->id
            )

            ->update([
                'last_read_at' => now(),
            ]);


        return response()->json([
            'success' => true,
        ]);
    }


    /**
     * ============================================================
     * CONVERSATION LIST
     * ============================================================
     */
    private function conversationList(
        User $user
    ) {
        $isAdmin =
            $this->isAdminUser($user);


        $query =
            ChatConversation::query()

                ->with([
                    'users:id,name',
                    'latestMessage.sender:id,name',
                ]);


        /*
         * Super Admin/Admin
         * => ALL chats.
         *
         * Team Leader/Employee
         * => sirf participant chats.
         */
        if (!$isAdmin) {

            $query->whereHas(
                'participants',

                function ($participantQuery) use ($user) {

                    $participantQuery->where(
                        'user_id',
                        $user->id
                    );
                }
            );
        }


        /*
         * Unread count.
         */
        if (!$isAdmin) {

            $query->withCount([

                'messages as unread_count' =>
                    function ($messageQuery) use ($user) {

                        $messageQuery

                            ->where(
                                'sender_id',
                                '!=',
                                $user->id
                            )

                            ->whereRaw(
                                "
                                chat_messages.created_at >
                                COALESCE(
                                    (
                                        SELECT last_read_at
                                        FROM chat_participants
                                        WHERE
                                            chat_participants.conversation_id =
                                            chat_messages.conversation_id
                                        AND
                                            chat_participants.user_id = ?
                                        LIMIT 1
                                    ),
                                    '1970-01-01 00:00:00'
                                )
                                ",
                                [
                                    $user->id
                                ]
                            );
                    }

            ]);

        } else {

            /*
             * Admin monitoring list me
             * unread badge zero.
             */
            $query->withCount([

                'messages as unread_count' =>
                    function ($messageQuery) {

                        $messageQuery
                            ->whereRaw(
                                '1 = 0'
                            );
                    }

            ]);
        }


        /*
         * Latest conversation first.
         */
        return $query

            ->orderByDesc(

                ChatMessage::select(
                    'created_at'
                )

                    ->whereColumn(
                        'chat_messages.conversation_id',
                        'chat_conversations.id'
                    )

                    ->latest()

                    ->limit(1)

            )

            ->orderByDesc(
                'chat_conversations.id'
            )

            ->get();
    }


    /**
     * ============================================================
     * AUTHORIZE CONVERSATION
     * ============================================================
     */
    private function authorizeConversation(
        ChatConversation $conversation,
        User $user
    ): void {

        /*
         * Super Admin/Admin sab dekh sakte hain.
         */
        if ($this->isAdminUser($user)) {
            return;
        }


        /*
         * TL/Employee participant hona chahiye.
         */
        $exists =
            ChatParticipant::query()

                ->where(
                    'conversation_id',
                    $conversation->id
                )

                ->where(
                    'user_id',
                    $user->id
                )

                ->exists();


        abort_unless(
            $exists,
            403,
            'You do not have access to this chat.'
        );
    }


    /**
     * ============================================================
     * PRIVATE CHAT PERMISSION
     * ============================================================
     */
    // private function canStartPrivateChat(
    //     User $authUser,
    //     User $targetUser
    // ): bool {

    //     /*
    //      * Super Admin / Admin
    //      * => kisi bhi user ko message.
    //      */
    //     if ($this->isAdminUser($authUser)) {
    //         return true;
    //     }


    //     /*
    //      * Team Leader
    //      * => sirf apne employees.
    //      */
    //     if (
    //         $authUser->hasRole(
    //             'team_leader'
    //         )
    //     ) {

    //         return
    //             (int) $targetUser->team_leader_id
    //             ===
    //             (int) $authUser->id;
    //     }


    //     /*
    //      * Employee
    //      * => sirf apne Team Leader.
    //      */
    //     if (
    //         $authUser->hasRole(
    //             'employee'
    //         )
    //     ) {

    //         if (!$authUser->team_leader_id) {
    //             return false;
    //         }


    //         return
    //             (int) $authUser->team_leader_id
    //             ===
    //             (int) $targetUser->id;
    //     }


    //     return false;
    // }

    /**
     * ============================================================
     * PRIVATE CHAT PERMISSION
     * ============================================================
     */
    // private function canStartPrivateChat(
    //     User $authUser,
    //     User $targetUser
    // ): bool {

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Self chat not allowed
    //     |--------------------------------------------------------------------------
    //     */
    //     if ((int) $authUser->id === (int) $targetUser->id) {
    //         return false;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | SUPER ADMIN
    //     |--------------------------------------------------------------------------
    //     | Super Admin sabko message kar sakta hai.
    //     */
    //     if ($authUser->hasRole('super_admin')) {
    //         return true;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | OWNER
    //     |--------------------------------------------------------------------------
    //     | Owner sirf apne office ke:
    //     | - Admin
    //     | - Team Leader
    //     | - Employee
    //     */
    //     if ($authUser->hasRole('owner')) {

    //         return
    //             !empty($authUser->office_id) &&
    //             !empty($targetUser->office_id) &&
    //             (int) $authUser->office_id ===
    //             (int) $targetUser->office_id;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | ADMIN
    //     |--------------------------------------------------------------------------
    //     | Admin:
    //     | - apne office ke Team Leader
    //     | - apne office ke Employee
    //     | - apne office ke Owner
    //     */
    //     if ($authUser->hasRole('admin')) {

    //         if (
    //             empty($authUser->office_id) ||
    //             empty($targetUser->office_id)
    //         ) {
    //             return false;
    //         }

    //         if (
    //             (int) $authUser->office_id !==
    //             (int) $targetUser->office_id
    //         ) {
    //             return false;
    //         }

    //         return $targetUser->hasAnyRole([
    //             'owner',
    //             'team_leader',
    //             'employee',
    //         ]);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | TEAM LEADER / REPORTING MANAGER
    //     |--------------------------------------------------------------------------
    //     | TL:
    //     | - apne assigned employees
    //     | - same office Admin
    //     | - same office Owner
    //     */
    //     if ($authUser->hasRole('team_leader')) {

    //         /*
    //         * Apna direct employee
    //         */
    //         if (
    //             (int) $targetUser->team_leader_id ===
    //             (int) $authUser->id
    //         ) {
    //             return true;
    //         }

    //         /*
    //         * Same office Admin / Owner
    //         */
    //         if (
    //             !empty($authUser->office_id) &&
    //             !empty($targetUser->office_id) &&
    //             (int) $authUser->office_id ===
    //             (int) $targetUser->office_id &&
    //             $targetUser->hasAnyRole([
    //                 'admin',
    //                 'owner',
    //             ])
    //         ) {
    //             return true;
    //         }

    //         return false;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | NORMAL EMPLOYEE
    //     |--------------------------------------------------------------------------
    //     | Employee:
    //     | - apna assigned Reporting Manager / Team Leader
    //     | - same office Admin
    //     | - same office Owner
    //     |
    //     | Kisi dusre TL ko nahi.
    //     | Kisi dusre office ke kisi user ko nahi.
    //     | Super Admin ko list me nahi.
    //     */
    //     if ($authUser->hasRole('employee')) {

    //         /*
    //         * Assigned Reporting Manager
    //         */
    //         if (
    //             !empty($authUser->team_leader_id) &&
    //             (int) $authUser->team_leader_id ===
    //             (int) $targetUser->id
    //         ) {
    //             return true;
    //         }

    //         /*
    //         * Same office Admin / Owner
    //         */
    //         if (
    //             !empty($authUser->office_id) &&
    //             !empty($targetUser->office_id) &&
    //             (int) $authUser->office_id ===
    //             (int) $targetUser->office_id &&
    //             $targetUser->hasAnyRole([
    //                 'admin',
    //                 'owner',
    //             ])
    //         ) {
    //             return true;
    //         }

    //         return false;
    //     }

    //     return false;
    // }



    /**
     * ============================================================
     * ALLOWED USERS FOR "+ START NEW CHAT"
     * ============================================================
     */
    // private function getAllowedUsers(
    //     User $user
    // ) {
    //     /*
    //      * Super Admin / Admin
    //      * => sab users.
    //      */
    //     if ($this->isAdminUser($user)) {

    //         return User::query()

    //             ->where(
    //                 'id',
    //                 '!=',
    //                 $user->id
    //             )

    //             ->orderBy('name')

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }


    //     /*
    //      * Team Leader
    //      * => sirf apne employees.
    //      */
    //     if (
    //         $user->hasRole(
    //             'team_leader'
    //         )
    //     ) {

    //         return User::query()

    //             ->where(
    //                 'team_leader_id',
    //                 $user->id
    //             )

    //             ->where(
    //                 'id',
    //                 '!=',
    //                 $user->id
    //             )

    //             ->orderBy('name')

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }


    //     /*
    //      * Employee
    //      * => sirf apna Team Leader.
    //      */
    //     if (
    //         $user->hasRole(
    //             'employee'
    //         )
    //     ) {

    //         if (!$user->team_leader_id) {
    //             return collect();
    //         }


    //         return User::query()

    //             ->where(
    //                 'id',
    //                 $user->team_leader_id
    //             )

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }


    //     return collect();
    // }

    /**
     * ============================================================
     * ALLOWED USERS FOR "+ START NEW CHAT"
     * ============================================================
     */
    // private function getAllowedUsers(User $user)
    // {
    //     /*
    //     |--------------------------------------------------------------------------
    //     | SUPER ADMIN
    //     |--------------------------------------------------------------------------
    //     | Sab users dikhenge.
    //     */
    //     if ($user->hasRole('super_admin')) {

    //         return User::query()
    //             ->where('id', '!=', $user->id)
    //             ->orderBy('name')
    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | OWNER
    //     |--------------------------------------------------------------------------
    //     | Sirf apne office ke niche ke log:
    //     | - Admin
    //     | - Team Leader
    //     | - Employee
    //     */
    //     if ($user->hasRole('owner')) {

    //         if (empty($user->office_id)) {
    //             return collect();
    //         }

    //         return User::query()

    //             ->where('id', '!=', $user->id)

    //             ->where(
    //                 'office_id',
    //                 $user->office_id
    //             )

    //             ->whereHas('roles', function ($query) {

    //                 $query->whereIn(
    //                     'roles.name',
    //                     [
    //                         'admin',
    //                         'team_leader',
    //                         'employee',
    //                     ]
    //                 );
    //             })

    //             ->orderBy('name')

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | ADMIN
    //     |--------------------------------------------------------------------------
    //     | Apne office me:
    //     | - Owner
    //     | - Team Leader
    //     | - Employee
    //     */
    //     if ($user->hasRole('admin')) {

    //         if (empty($user->office_id)) {
    //             return collect();
    //         }

    //         return User::query()

    //             ->where('id', '!=', $user->id)

    //             ->where(
    //                 'office_id',
    //                 $user->office_id
    //             )

    //             ->whereHas('roles', function ($query) {

    //                 $query->whereIn(
    //                     'roles.name',
    //                     [
    //                         'owner',
    //                         'team_leader',
    //                         'employee',
    //                     ]
    //                 );
    //             })

    //             ->orderBy('name')

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | TEAM LEADER / REPORTING MANAGER
    //     |--------------------------------------------------------------------------
    //     | Show:
    //     | - apne assigned employees
    //     | - same office Admin
    //     | - same office Owner
    //     */
    //     if ($user->hasRole('team_leader')) {

    //         return User::query()

    //             ->where(
    //                 'id',
    //                 '!=',
    //                 $user->id
    //             )

    //             ->where(function ($query) use ($user) {

    //                 /*
    //                 * Apne direct employees
    //                 */
    //                 $query->where(function ($employeeQuery) use ($user) {

    //                     $employeeQuery
    //                         ->where(
    //                             'team_leader_id',
    //                             $user->id
    //                         )
    //                         ->whereHas(
    //                             'roles',
    //                             function ($roleQuery) {

    //                                 $roleQuery->where(
    //                                     'roles.name',
    //                                     'employee'
    //                                 );
    //                             }
    //                         );
    //                 });

    //                 /*
    //                 * Same office Admin / Owner
    //                 */
    //                 if (!empty($user->office_id)) {

    //                     $query->orWhere(function ($higherQuery) use ($user) {

    //                         $higherQuery
    //                             ->where(
    //                                 'office_id',
    //                                 $user->office_id
    //                             )
    //                             ->whereHas(
    //                                 'roles',
    //                                 function ($roleQuery) {

    //                                     $roleQuery->whereIn(
    //                                         'roles.name',
    //                                         [
    //                                             'admin',
    //                                             'owner',
    //                                         ]
    //                                     );
    //                                 }
    //                             );
    //                     });
    //                 }
    //             })

    //             ->orderBy('name')

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | NORMAL EMPLOYEE
    //     |--------------------------------------------------------------------------
    //     | Show ONLY:
    //     |
    //     | - assigned Team Leader / Reporting Manager
    //     | - same office Admin
    //     | - same office Owner
    //     |
    //     | Dusre Team Leaders nahi.
    //     | Dusre office ke users nahi.
    //     | Super Admin nahi.
    //     */
    //     if ($user->hasRole('employee')) {

    //         return User::query()

    //             ->where(
    //                 'id',
    //                 '!=',
    //                 $user->id
    //             )

    //             ->where(function ($query) use ($user) {

    //                 /*
    //                 * Assigned Reporting Manager
    //                 */
    //                 if (!empty($user->team_leader_id)) {

    //                     $query->where(
    //                         'id',
    //                         $user->team_leader_id
    //                     );

    //                 } else {

    //                     $query->whereRaw('1 = 0');
    //                 }

    //                 /*
    //                 * Same office Admin / Owner
    //                 */
    //                 if (!empty($user->office_id)) {

    //                     $query->orWhere(function ($higherQuery) use ($user) {

    //                         $higherQuery
    //                             ->where(
    //                                 'office_id',
    //                                 $user->office_id
    //                             )

    //                             ->whereHas(
    //                                 'roles',
    //                                 function ($roleQuery) {

    //                                     $roleQuery->whereIn(
    //                                         'roles.name',
    //                                         [
    //                                             'admin',
    //                                             'owner',
    //                                         ]
    //                                     );
    //                                 }
    //                             );
    //                     });
    //                 }
    //             })

    //             ->orderBy('name')

    //             ->get([
    //                 'id',
    //                 'name',
    //             ]);
    //     }

    //     return collect();
    // }


    /**
     * ============================================================
     * ADMIN CHECK
     *
     * Dono roles ko ek jagah handle kiya hai.
     * ============================================================
     */
    private function isAdminUser(
        User $user
    ): bool {

        return $user->hasAnyRole([
            'super_admin',
            'admin',
        ]);
    }


    /**
 * ============================================================
 * PRIVATE CHAT PERMISSION
 * ============================================================
 */
private function canStartPrivateChat(
    User $authUser,
    User $targetUser
): bool {

    // Khud ko message nahi
    if ((int) $authUser->id === (int) $targetUser->id) {
        return false;
    }

    /*
     * Same role wale aapas me chat list me nahi aayenge.
     */
    if ($this->usersHaveSameRole($authUser, $targetUser)) {
        return false;
    }

    /*
     * Super Admin sab different-role users ko access kar sakta hai.
     */
    if ($authUser->hasRole('super_admin')) {
        return true;
    }

    /*
     * Kisi user ko Super Admin higher role ke roop me
     * dikhaya ja sakta hai.
     */
    if ($targetUser->hasRole('super_admin')) {
        return true;
    }

    /*
     * Baaki hierarchy same office ke andar chalegi.
     */
    if (
        empty($authUser->office_id) ||
        empty($targetUser->office_id) ||
        (int) $authUser->office_id !==
        (int) $targetUser->office_id
    ) {
        return false;
    }

    /*
     * Target auth user ka koi ancestor/higher manager hai?
     */
    if (
        $this->isHierarchyAncestor(
            $targetUser,
            $authUser
        )
    ) {
        return true;
    }

    /*
     * Target auth user ke niche/descendant me hai?
     */
    if (
        $this->isHierarchyAncestor(
            $authUser,
            $targetUser
        )
    ) {
        return true;
    }

    return false;
}


/**
 * ============================================================
 * ALLOWED USERS FOR "+ START NEW CHAT"
 * ============================================================
 */
private function getAllowedUsers(User $user)
{
    /*
     * Candidate users.
     *
     * Super Admin ke liye sab.
     * Baaki ke liye:
     * - same office
     * - plus Super Admin
     */
    if ($user->hasRole('super_admin')) {

        $candidates = User::query()
            ->with('roles')
            ->where('id', '!=', $user->id)
            ->get();

    } else {

        $candidates = User::query()
            ->with('roles')

            ->where(
                'id',
                '!=',
                $user->id
            )

            ->where(function ($query) use ($user) {

                /*
                 * Same office users
                 */
                if (!empty($user->office_id)) {

                    $query->where(
                        'office_id',
                        $user->office_id
                    );

                } else {

                    $query->whereRaw('1 = 0');
                }

                /*
                 * Super Admin higher role
                 */
                $query->orWhereHas(
                    'roles',
                    function ($roleQuery) {

                        $roleQuery->where(
                            'roles.name',
                            'super_admin'
                        );
                    }
                );
            })

            ->get();
    }

    /*
     * Ab actual hierarchy permission lagao.
     */
    $allowedUsers = $candidates
        ->filter(function ($targetUser) use ($user) {

            return $this->canStartPrivateChat(
                $user,
                $targetUser
            );
        })
        ->unique('id')
        ->values();

    /*
     * Hierarchical sorting.
     */
    return $this->sortChatUsersHierarchically(
        $allowedUsers
    );
}


/**
 * ============================================================
 * CHECK SAME ROLE
 * ============================================================
 */
private function usersHaveSameRole(
    User $firstUser,
    User $secondUser
): bool {

    $firstRoles = $firstUser
        ->roles
        ->pluck('name');

    $secondRoles = $secondUser
        ->roles
        ->pluck('name');

    return $firstRoles
        ->intersect($secondRoles)
        ->isNotEmpty();
}


/**
 * ============================================================
 * CHECK IF USER IS ANCESTOR / HIGHER MANAGER
 * ============================================================
 *
 * Example:
 *
 * Owner
 *   |
 * Admin
 *   |
 * Team Leader
 *   |
 * Employee
 *
 * isHierarchyAncestor(Owner, Employee) => true
 * isHierarchyAncestor(Admin, Employee) => true
 * isHierarchyAncestor(Employee, Owner) => false
 */
private function isHierarchyAncestor(
    User $possibleAncestor,
    User $user
): bool {

    /*
     * Safety against circular hierarchy.
     */
    $visitedIds = [];

    $currentUser = $user;

    while (!empty($currentUser->team_leader_id)) {

        $leaderId = (int) $currentUser->team_leader_id;

        /*
         * Circular reference protection.
         */
        if (isset($visitedIds[$leaderId])) {
            break;
        }

        $visitedIds[$leaderId] = true;

        /*
         * Ancestor mil gaya.
         */
        if (
            $leaderId ===
            (int) $possibleAncestor->id
        ) {
            return true;
        }

        /*
         * Next parent/reporting manager.
         */
        $currentUser = User::query()
            ->select([
                'id',
                'team_leader_id',
                'office_id',
            ])
            ->find($leaderId);

        if (!$currentUser) {
            break;
        }
    }

    return false;
}


/**
 * ============================================================
 * SORT CHAT USERS HIERARCHICALLY
 * ============================================================
 */
private function sortChatUsersHierarchically(
    \Illuminate\Support\Collection $users
): \Illuminate\Support\Collection {

    if ($users->isEmpty()) {
        return collect();
    }

    /*
     * Duplicate IDs remove.
     */
    $users = $users
        ->filter(function ($user) {
            return !empty($user->id);
        })
        ->unique('id')
        ->values();

    $userIds = $users
        ->pluck('id')
        ->map(function ($id) {
            return (int) $id;
        })
        ->flip();

    /*
     * Parent/reporting manager ke according group.
     */
    $childrenByLeader = $users
        ->groupBy(function ($user) use ($userIds) {

            $leaderId =
                (int) $user->team_leader_id;

            /*
             * Parent current collection me nahi hai
             * to root treat karo.
             */
            if (
                $leaderId <= 0 ||
                $leaderId === (int) $user->id ||
                !$userIds->has($leaderId)
            ) {
                return 0;
            }

            return $leaderId;
        });

    $sorted = collect();

    $addedIds = [];

    $queue = [];

    /*
     * Root users.
     */
    foreach (
        $childrenByLeader
            ->get(0, collect())
            ->sortBy('name')
        as $root
    ) {
        $queue[] = $root;
    }

    /*
     * Parent -> children traversal.
     */
    while (!empty($queue)) {

        $currentUser =
            array_shift($queue);

        $currentUserId =
            (int) $currentUser->id;

        if (
            isset(
                $addedIds[$currentUserId]
            )
        ) {
            continue;
        }

        $sorted->push(
            $currentUser
        );

        $addedIds[$currentUserId] =
            true;

        $children =
            $childrenByLeader
                ->get(
                    $currentUserId,
                    collect()
                )
                ->sortBy('name');

        foreach ($children as $child) {

            $childId =
                (int) $child->id;

            if (
                !isset(
                    $addedIds[$childId]
                )
            ) {
                $queue[] = $child;
            }
        }
    }

    /*
     * Circular / malformed hierarchy wale users.
     */
    foreach (
        $users->sortBy('name')
        as $user
    ) {

        $userId =
            (int) $user->id;

        if (
            !isset(
                $addedIds[$userId]
            )
        ) {

            $sorted->push(
                $user
            );

            $addedIds[$userId] =
                true;
        }
    }

    return $sorted->values();
}
}
