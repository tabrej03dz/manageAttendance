<?php

namespace App\Services;

use App\Jobs\SendChatPush;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatNotificationService
{
    public function __construct(private ChatAccessService $access) {}

    // Call INSIDE the transaction that creates the message.
    public function record(ChatMessage $message): void
    {
        $conversation = ChatConversation::query()->findOrFail($message->conversation_id);
        foreach ($this->recipientIds($message) as $id) {
            if ((int) $id === (int) $message->sender_id) {
                continue;
            }
            $receiver = $this->access->users()->get((int) $id);
            if (!$receiver || !$this->access->canView($receiver, $conversation)) {
                continue;
            }
            $notice = ChatNotification::firstOrCreate([
                'user_id' => $receiver->id,
                'message_id' => $message->id,
            ], [
                'sender_id' => $message->sender_id,
                'conversation_id' => $message->conversation_id,
                'expires_at' => now()->addMinutes(10),
            ]);
            if ($notice->wasRecentlyCreated) {
                $noticeId = (int) $notice->id;
                DB::afterCommit(function () use ($noticeId) {
                    try {
                        SendChatPush::dispatch($noticeId);
                    } catch (\Throwable $e) {
                        // Durable notification row remains; the recovery command can queue it again.
                        Log::warning('Chat push queue dispatch failed', ['notification_id' => $noticeId, 'exception' => get_class($e)]);
                    }
                });
            }
        }
    }

    public function recipientIds(ChatMessage $message): \Illuminate\Support\Collection
    {
        $conversation = ChatConversation::query()->findOrFail($message->conversation_id);
        $ids = $conversation->participants()->pluck('user_id')->map(fn($id) => (int) $id);
        $sender = $this->access->users()->get((int) $message->sender_id);
        if ($sender && in_array($this->access->role($sender), ['employee', 'team_leader'], true)) {
            // Add only scoped seniors who can already view this conversation.
            $seniorIds = $this->access->users()->filter(
                fn(User $senior) =>
                $this->access->canMonitorUser($senior, $sender)
                    && $this->access->canView($senior, $conversation)
            )->keys()->map(fn($id) => (int) $id);
            $ids = $ids->merge($seniorIds);
        }
        return $ids->unique()->filter(function ($id) use ($message, $conversation) {
            if ((int) $id === (int) $message->sender_id) { return false; }
            $receiver = $this->access->users()->get((int) $id);
            return $receiver && $this->access->canView($receiver, $conversation);
        })->values();
    }

    public function markConversationRead(User $user, ChatConversation $conversation, $readAt): void
    {
        ChatNotification::query()->where('user_id', $user->id)
            ->where('conversation_id', $conversation->id)->whereNull('read_at')
            ->where('created_at', '<=', $readAt)->update(['read_at' => $readAt]);
    }
}
