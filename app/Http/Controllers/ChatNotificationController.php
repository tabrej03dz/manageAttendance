<?php
namespace App\Http\Controllers;

use App\Models\ChatDevice;
use App\Models\ChatNotification;
use App\Services\ChatAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatNotificationController extends Controller
{
    public function feed(Request $request, ChatAccessService $access)
    {
        $user = $request->user();
        $data = $request->validate(['after_id' => ['nullable', 'integer', 'min:0']]);
        $counts = $access->unreadCounts($user);
        if (!$request->has('after_id')) {
            return response()->json(array_merge(['success' => true, 'notifications' => [],
                'cursor' => (int) ChatNotification::query()->where('user_id', $user->id)->max('id')], $counts))
                ->header('Cache-Control', 'private, no-store');
        }
        // Scan all own notices so read/revoked rows also advance the cursor.
        $rows = ChatNotification::query()->where('user_id', $user->id)
            ->where('id', '>', $data['after_id'] ?? 0)->with(['conversation', 'sender:id,name'])
            ->orderBy('id')->limit(50)->get();
        $notices = $rows->filter(fn ($row) => !$row->read_at && $row->conversation
            && $access->canView($user, $row->conversation)
            && $row->conversation->participants()->where('user_id', $user->id)->exists())
            ->map(fn ($row) => [
                'id' => $row->id, 'conversation_id' => $row->conversation_id,
                'message_id' => $row->message_id,
                'title' => ($row->sender?->name ?? 'User') . ' sent a message',
                'body' => 'Open chat to read the new message.',
                'url' => route('chat.show', $row->conversation_id),
            ])->values();
        return response()->json(array_merge(['success' => true, 'notifications' => $notices,
            'cursor' => (int) ($rows->last()?->id ?? ($data['after_id'] ?? 0))], $counts))
            ->header('Cache-Control', 'private, no-store');
    }

    public function registerDevice(Request $request)
    {
        $data = $request->validate([
            'device_id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{12,128}$/'],
            'platform' => ['required', 'in:android,ios'],
            'fcm_token' => ['required', 'string', 'min:20', 'max:4096'],
        ]);
        $userId = $request->user()->id;
        $hash = hash('sha256', $data['fcm_token']);
        DB::transaction(function () use ($data, $hash, $userId) {
            // This installation/token belongs to the currently authenticated account.
            ChatDevice::query()->where('token_hash', $hash)->where(function ($q) use ($userId, $data) {
                $q->where('user_id', '!=', $userId)->orWhere('device_id', '!=', $data['device_id']);
            })->delete();
            ChatDevice::updateOrCreate(['user_id' => $userId, 'device_id' => $data['device_id']], [
                'platform' => $data['platform'], 'fcm_token' => $data['fcm_token'], 'token_hash' => $hash,
            ]);
        }, 3);
        return response()->json(['success' => true]);
    }

    public function unregisterDevice(Request $request, string $deviceId)
    {
        ChatDevice::query()->where('user_id', $request->user()->id)->where('device_id', $deviceId)->delete();
        return response()->json(['success' => true]);
    }

    public function markRead(Request $request, ChatNotification $notification, ChatAccessService $access)
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 404);
        $conversation = $notification->conversation;
        abort_unless($conversation && $access->canView($request->user(), $conversation), 403);
        $notification->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
