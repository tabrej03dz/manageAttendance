<?php

namespace App\Services;

use App\Models\ChatDevice;
use App\Models\ChatMessage;
use Illuminate\Support\Str;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChatFcmService
{
    private function credentials(): array
    {
        $path = config('chat_notifications.credentials');
        if (!$path || !is_readable($path)) {
            throw new \RuntimeException('Chat Firebase credentials are missing.');
        }
        $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (($json['type'] ?? '') !== 'service_account') {
            throw new \RuntimeException('Chat Firebase service-account credentials are required.');
        }
        return $json;
    }

    public function notificationFor(ChatMessage $message): array
    {
        $senderName = (string) ($message->sender?->name ?? '');
        $text = (string) ($message->message ?? '');
        if (trim($text) !== '') {
            $body = Str::limit($text, 120);
        } else {
            $mime = strtolower(trim((string) $message->attachment_type));
            $extension = strtolower(pathinfo((string) ($message->attachment_name ?: $message->attachment), PATHINFO_EXTENSION));
            if (str_starts_with($mime, 'image/') || $mime === 'image') {
                $body = '📷 Sent a photo';
            } elseif (str_starts_with($mime, 'audio/') || in_array($mime, ['audio', 'voice'], true)) {
                $body = '🎤 Sent a voice message';
            } elseif (str_starts_with($mime, 'video/') || $mime === 'video') {
                $body = '🎥 Sent a video';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif', 'avif'], true)) {
                $body = '📷 Sent a photo';
            } elseif (in_array($extension, ['aac', 'wav', 'opus', 'ogg', 'caf', 'm4a', 'mp3', 'amr', 'flac'], true)) {
                $body = '🎤 Sent a voice message';
            } elseif (in_array($extension, ['mp4', 'webm', 'mov', 'mkv', 'avi', '3gp'], true)) {
                $body = '🎥 Sent a video';
            } else {
                $body = '📎 Sent a file';
            }
        }
        return ['title' => trim($senderName) !== '' ? $senderName : 'New chat message', 'body' => $body];
    }

    public function send(ChatDevice $device, array $data, array $notification): bool
    {
        if (!config('chat_notifications.fcm_enabled')) {
            throw new \RuntimeException('Chat FCM is disabled.');
        }
        $credentials = $this->credentials();
        $project = config('chat_notifications.project_id') ?: ($credentials['project_id'] ?? null);
        if (!$project || !preg_match('/^[a-z][a-z0-9-]{4,62}$/', $project)) {
            throw new \RuntimeException('Invalid Chat Firebase project ID.');
        }
        $key = 'chat-fcm-oauth-' . hash('sha256', ($credentials['client_email'] ?? '') . ($credentials['private_key_id'] ?? ''));
        $accessToken = Cache::get($key);
        if (!$accessToken) {
            $auth = new ServiceAccountCredentials('https://www.googleapis.com/auth/firebase.messaging', $credentials);
            $token = $auth->fetchAuthToken(HttpHandlerFactory::build(new Client(['timeout' => 10, 'connect_timeout' => 5])));
            $accessToken = $token['access_token'] ?? null;
            if (!$accessToken) {
                throw new \RuntimeException('Firebase OAuth token could not be obtained.');
            }
            Cache::put($key, $accessToken, max(1, (int) ($token['expires_in'] ?? 3600) - 60));
        }
        $response = Http::withToken($accessToken)->acceptJson()->connectTimeout(5)->timeout(15)
            ->post("https://fcm.googleapis.com/v1/projects/{$project}/messages:send", [
                'message' => [
                    'token' => $device->fcm_token,
                    'notification' => $notification,
                    'data' => array_map(fn($value) => (string) $value, $data),
                    'android' => [
                        'priority' => 'high',
                        'ttl' => '300s',
                        'notification' => ['sound' => 'default', 'tag' => 'chat-message-' . $data['message_id']],
                    ],
                    'apns' => [
                        'headers' => ['apns-priority' => '10', 'apns-collapse-id' => 'chat-message-' . $data['message_id'], 'apns-push-type' => 'alert', 'apns-expiration' => (string) (time() + 300)],
                        'payload' => ['aps' => ['sound' => 'default']],
                    ],
                ],
            ]);
        if ($response->successful()) {
            return true;
        }
        foreach ($response->json('error.details', []) as $detail) {
            if (($detail['@type'] ?? '') === 'type.googleapis.com/google.firebase.fcm.v1.FcmError'
                && ($detail['errorCode'] ?? '') === 'UNREGISTERED'
            ) {
                ChatDevice::query()->whereKey($device->id)->where('token_hash', $device->token_hash)->delete();
                return false;
            }
        }
        if ($response->status() === 401) {
            Cache::forget($key);
        }
        throw new \RuntimeException('FCM delivery failed (HTTP ' . $response->status() . ').');
    }
}
