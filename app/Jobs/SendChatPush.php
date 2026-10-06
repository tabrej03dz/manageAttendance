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
use Illuminate\Support\Facades\Cache;

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
        // Share the same cache store between requests/workers for these guards.
        $lock = Cache::lock('chat-push-delivery-' . $this->notificationId, 600);
        if (!$lock->get()) {
            if ($this->job && $this->job->getConnectionName() !== 'sync') {
                $this->release(10);
            }
            return;
        }
        try {
            $this->deliver($access, $fcm);
        } finally {
            $lock->release();
        }
    }

    private function deliver(ChatAccessService $access, ChatFcmService $fcm): void
    {
        $notice = ChatNotification::query()->with(['conversation', 'message.sender'])->find($this->notificationId);
        if (!$notice || $notice->push_sent_at || $notice->read_at || ($notice->expires_at && $notice->expires_at->isPast())) {
            return;
        }
        $user = User::query()->find($notice->user_id);
        $conversation = $notice->conversation;
        $message = $notice->message;
        if (!$message) {
            return;
        }
        $sender = $access->users()->get((int) $message->sender_id);
        $isParticipant = $user && $conversation
            && $conversation->participants()->where('user_id', $user->id)->exists();
        $isScopedSenior = $user && $sender
            && in_array($access->role($sender), ['employee', 'team_leader'], true)
            && $access->canMonitorUser($user, $sender);
        // Recheck access at send time; seniors do not become chat participants.
        if (
            !$user || !$conversation || !$access->canView($user, $conversation)
            || (!$isParticipant && !$isScopedSenior)
        ) {
            return;
        }
        $notification = $fcm->notificationFor($message);
        $devices = ChatDevice::query()->where('user_id', $user->id)->get();
        if ($devices->isEmpty()) {
            Log::warning('Chat push skipped: recipient has no registered device', [
                'notification_id' => $notice->id,
                'user_id' => $user->id,
            ]);
            return;
        }
        $accepted = 0;
        $failures = 0;
        foreach ($devices as $device) {
            // A device might have changed account since it was loaded.
            if (!ChatDevice::query()->whereKey($device->id)->where('user_id', $user->id)->where('token_hash', $device->token_hash)->exists()) {
                continue;
            }
            // Record each successful token so a later device failure does not resend to it.
            $receiptKey = 'chat-push-sent-' . $notice->id . '-' . $device->token_hash;
            if (Cache::has($receiptKey)) {
                $accepted++;
                continue;
            }
            try {
                $sent = $fcm->send($device, [
                    'type' => 'chat_message',
                    'notification_id' => $notice->id,
                    'conversation_id' => $notice->conversation_id,
                    'message_id' => $notice->message_id,
                    'title' => $notification['title'],
                    'body' => $notification['body'],
                ], $notification);
                if ($sent) {
                    $accepted++;
                    Cache::put($receiptKey, true, now()->addDay());
                }
            } catch (\Throwable $e) {
                $failures++;
                Log::warning('Chat push device delivery failed', [
                    'notification_id' => $notice->id,
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'exception' => get_class($e),
                ]);
            }
        }
        if ($failures > 0) {
            throw new \RuntimeException('Chat push failed for ' . $failures . ' device(s); successful devices will not be resent while receipts are retained.');
        }
        if ($accepted > 0) {
            $notice->update(['push_sent_at' => now()]);
        }
    }
}
