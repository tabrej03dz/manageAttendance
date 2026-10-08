<?php
namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessageRead;
use App\Models\ChatNotification;
use App\Models\ChatParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatReadService
{
    public function __construct(private ChatAccessService $access) {}

    // Existing API: all messages up to this ID were displayed.
    public function mark(
        User $user,
        ChatConversation $conversation,
        int $messageId
    ): ?int {
        abort_unless($this->access->canView($user, $conversation), 403);

        if ($messageId <= 0) {
            return null;
        }

        $conversation->messages()->whereKey($messageId)->firstOrFail();

        return $this->record($user, $conversation, null, $messageId);
    }

    // Exact messages actually displayed to the viewer.
    public function markSeen(
        User $user,
        ChatConversation $conversation,
        array $messageIds
    ): ?int {
        abort_unless($this->access->canView($user, $conversation), 403);

        $ids = array_values(array_unique(array_map('intval', $messageIds)));

        abort_if(
            count($ids) !== $conversation->messages()
                ->whereIn('id', $ids)->count(),
            422,
            'Message IDs must belong to this conversation.'
        );

        return $this->record($user, $conversation, $ids, null);
    }

    private function record(
        User $user,
        ChatConversation $conversation,
        ?array $ids,
        ?int $through
    ): ?int {
        return DB::transaction(function () use (
            $user, $conversation, $ids, $through
        ) {
            ChatConversation::query()
                ->whereKey($conversation->id)
                ->lockForUpdate()->firstOrFail();

            $query = $conversation->messages()
                ->where('sender_id', '!=', $user->id);

            if ($ids !== null) {
                $query->whereIn('id', $ids);
            } else {
                $query->where('id', '<=', $through);
            }

            $now = now();

            $query->select('id')->chunkById(200, function ($messages) use (
                $user, $now
            ) {
                $rows = $messages->map(fn ($message) => [
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                ])->all();

                ChatMessageRead::query()->insertOrIgnore($rows);

                ChatMessageRead::query()
                    ->where('user_id', $user->id)
                    ->whereIn('message_id', $messages->pluck('id'))
                    ->update(['last_seen_at' => $now]);
            });

            $notices = ChatNotification::query()
                ->where('user_id', $user->id)
                ->where('conversation_id', $conversation->id)
                ->whereNull('read_at');

            if ($ids !== null) {
                $notices->whereIn('message_id', $ids);
            } else {
                $notices->where('message_id', '<=', $through);
            }

            $notices->update(['read_at' => $now]);

            $participant = ChatParticipant::query()
                ->where('conversation_id', $conversation->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()->first();

            // Monitoring users get seen records without becoming participants.
            if (!$participant) {
                return null;
            }

            $firstUnread = $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('id', '>', (int) $participant->last_read_message_id)
                ->whereDoesntHave(
                    'reads',
                    fn ($q) => $q->where('user_id', $user->id)
                )->min('id');

            $prefix = $conversation->messages();

            if ($firstUnread !== null) {
                $prefix->where('id', '<', $firstUnread);
            }

            $lastId = max(
                (int) $participant->last_read_message_id,
                (int) $prefix->max('id')
            );

            if ($lastId > (int) $participant->last_read_message_id) {
                $participant->update([
                    'last_read_message_id' => $lastId,
                    'last_read_at' => $now,
                ]);
            }

            return $lastId;
        }, 3);
    }
}