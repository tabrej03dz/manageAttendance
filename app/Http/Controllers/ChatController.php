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


     * ROLE HIERARCHY


     * ============================================================


     *


     * Higher -> Lower:


     * super_admin -> owner -> admin -> team_leader -> employee


     *


     * Rules:


     * - Higher role ko apne se sabhi lower-role users dikhte hain.


     * - Lower role ko apne se sabhi higher-role users dikhte hain.


     * - Same role wale users aapas me nahi dikhte.


     * - Self chat allowed nahi hai.


     */


    private function roleLevel(User $user): ?int


    {


        if ($user->hasRole('super_admin')) {


            return 5;


        }


        if ($user->hasRole('owner')) {


            return 4;


        }


        if ($user->hasRole('admin')) {


            return 3;


        }


        if ($user->hasRole('team_leader')) {


            return 2;


        }


        if ($user->hasRole('employee')) {


            return 1;


        }


        return null;


    }

/**
     * ============================================================
     * PRIVATE CHAT PERMISSION
     * ============================================================
     *
     * Rules:
     * - Same office required.
     * - Same department required.
     * - Same role blocked.
     * - Higher <-> Lower roles allowed.
     *
     * IMPORTANT:
     * activeOfficeId() use kiya gaya hai because app me office switching
     * session('active_office_id') se hoti hai.
     */
    private function canStartPrivateChat(
        User $authUser,
        User $targetUser
    ): bool {
        if ((int) $authUser->id === (int) $targetUser->id) {
            return false;
        }

        $authLevel = $this->roleLevel($authUser);
        $targetLevel = $this->roleLevel($targetUser);

        if ($authLevel === null || $targetLevel === null) {
            return false;
        }

        /*
         * Same role wale aapas me nahi dikhenge.
         */
        if ($authLevel === $targetLevel) {
            return false;
        }

        /*
         * Current/active office compare karo.
         *
         * Logged-in user ke liye activeOfficeId() session switched office
         * ko respect karta hai.
         */
        $authOfficeId = $authUser->activeOfficeId();

        if (!$authOfficeId) {
            return false;
        }

        /*
         * Target exactly current office ka hona chahiye.
         */
        if (
            empty($targetUser->office_id) ||
            (int) $targetUser->office_id !== (int) $authOfficeId
        ) {
            return false;
        }

        /*
         * Same department compulsory.
         *
         * department_id NULL hone par list intentionally empty rahegi,
         * taaki accidental cross-department visibility na ho.
         */
        if (
            empty($authUser->department_id) ||
            empty($targetUser->department_id) ||
            (int) $authUser->department_id !== (int) $targetUser->department_id
        ) {
            return false;
        }

        return true;
    }

    /**
     * ============================================================
     * ALLOWED USERS FOR "+ START NEW CHAT"
     * ============================================================
     *
     * Sirf:
     * - current active office
     * - same department
     * - different hierarchy role
     */
    private function getAllowedUsers(User $user)
    {
        if ($this->roleLevel($user) === null) {
            return collect();
        }

        $activeOfficeId = $user->activeOfficeId();

        if (!$activeOfficeId || empty($user->department_id)) {
            return collect();
        }

        $users = User::query()
            ->with('roles')
            ->where('id', '!=', $user->id)
            ->where('office_id', $activeOfficeId)
            ->where('department_id', $user->department_id)
            ->get()
            ->filter(function (User $targetUser) use ($user) {
                return $this->canStartPrivateChat(
                    $user,
                    $targetUser
                );
            })
            ->unique('id')
            ->values();

        return $this->sortUsersByRoleHierarchy($users);
    }

    /**
     * ============================================================
     * SORT USERS BY ROLE HIERARCHY
     * ============================================================
     *
     * Order:
     * super_admin -> owner -> admin -> team_leader -> employee
     */
    private function sortUsersByRoleHierarchy(
        \Illuminate\Support\Collection $users
    ): \Illuminate\Support\Collection {
        return $users
            ->sort(function (User $first, User $second) {
                $firstLevel = $this->roleLevel($first) ?? 0;
                $secondLevel = $this->roleLevel($second) ?? 0;

                if ($firstLevel !== $secondLevel) {
                    return $secondLevel <=> $firstLevel;
                }

                return strcasecmp(
                    (string) $first->name,
                    (string) $second->name
                );
            })
            ->values();
    }
}
