<?php

namespace App\Jobs;

use App\Models\ChatDevice;
use App\Models\ChatNotification;
use App\Models\User;
use App\Services\ChatAccessService;
use App\Services\ChatFcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendChatPush implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 4;
    public int $timeout = 75;
    public int $uniqueFor = 300;
    public function __construct(public int $notificationId)
    {
        $this->onConnection('chat_push')->onQueue('chat-push')->afterCommit();
    }
    public function uniqueId(): string
    {
        return 'chat-notification-' . $this->notificationId;
    }
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(ChatAccessService $access, ChatFcmService $fcm): void
    {
        $notice = ChatNotification::query()->with('conversation')->find($this->notificationId);
        if (!$notice || $notice->push_sent_at || $notice->read_at || ($notice->expires_at && $notice->expires_at->isPast())) {
            return;
        }
        $user = User::query()->find($notice->user_id);
        $conversation = $notice->conversation;
        if (
            !$user || !$conversation || !$access->canView($user, $conversation)
            || !$conversation->participants()->where('user_id', $user->id)->exists()
        ) {
            $notice->update(['push_sent_at' => now()]);
            return;
        }
        $devices = ChatDevice::query()->where('user_id', $user->id)->get();
        if ($devices->isEmpty()) {
            Log::warning('Chat push skipped: recipient has no registered device', [
                'notification_id' => $notice->id,
                'user_id' => $user->id,
            ]);
            return;
        }
        $attempted = false;
        foreach ($devices as $device) {
            // A device might have changed account since it was loaded.
            if (!ChatDevice::query()->whereKey($device->id)->where('user_id', $user->id)->where('token_hash', $device->token_hash)->exists()) {
                continue;
            }
            $attempted = true;
            $fcm->send($device, [
                'type' => 'chat_message',
                'notification_id' => $notice->id,
                'conversation_id' => $notice->conversation_id,
                'message_id' => $notice->message_id,
            ]);
        }
        if ($attempted) {
            $notice->update(['push_sent_at' => now()]);
        }
    }
}
