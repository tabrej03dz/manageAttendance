<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatNotification;
use App\Models\ChatParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatReadService
{
    // Call only after authorization. Never advances another participant's receipt.
    public function mark(User $user, ChatConversation $conversation, int $messageId): ?int
    {
        return DB::transaction(function () use ($user, $conversation, $messageId) {
            ChatConversation::query()->whereKey($conversation->id)->lockForUpdate()->firstOrFail();
            $message = $messageId > 0
                ? $conversation->messages()->whereKey($messageId)->firstOrFail() : null;
            $participant = ChatParticipant::query()->where('conversation_id', $conversation->id)
                ->where('user_id', $user->id)->lockForUpdate()->first();
            if (!$participant) {
                ChatNotification::query()->where('user_id', $user->id)
                    ->where('conversation_id', $conversation->id)->whereNull('read_at')
                    ->where('message_id', '<=', $messageId)->update(['read_at' => now()]);
                return null;
            }
            $lastId = max((int) $participant->last_read_message_id, $messageId);
            if ($lastId > (int) $participant->last_read_message_id) {
                $participant->update([
                    'last_read_message_id' => $lastId,
                    'last_read_at' => $message->created_at,
                ]);
            }
            ChatNotification::query()->where('user_id', $user->id)
                ->where('conversation_id', $conversation->id)->whereNull('read_at')
                ->where('message_id', '<=', $lastId)->update(['read_at' => now()]);
            return $lastId;
        });
    }
}
