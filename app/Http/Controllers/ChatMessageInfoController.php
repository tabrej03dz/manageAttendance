<?php
namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\ChatAccessService;
use App\Services\ChatReadService;
use Illuminate\Http\Request;

class ChatMessageInfoController extends Controller
{
    public function seen(
        Request $request,
        ChatConversation $conversation,
        ChatReadService $reads,
        ChatAccessService $access
    ) {
        $user = $request->user();

        abort_unless($access->canView($user, $conversation), 403);

        $data = $request->validate([
            'message_ids' => ['required', 'array', 'min:1', 'max:200'],
            'message_ids.*' => [
                'required', 'integer', 'min:1', 'distinct',
            ],
        ]);

        $lastId = $reads->markSeen(
            $user,
            $conversation,
            $data['message_ids']
        );

        return response()->json([
            'success' => true,
            'data' => [
                'recorded' => true,
                'last_read_message_id' => $lastId,
            ],
        ]);
    }

    public function info(
        Request $request,
        ChatMessage $message,
        ChatAccessService $access
    ) {
        $user = $request->user();

        abort_unless(
            (int) $message->sender_id === (int) $user->id,
            403,
            'Only the sender can view message info.'
        );

        abort_unless(
            $message->conversation
                && $access->canView($user, $message->conversation),
            403
        );

        $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $rows = $message->reads()
            ->with('user:id,name')
            ->orderBy('first_seen_at')
            ->orderBy('id')
            ->paginate(100);

        return response()->json([
            'success' => true,
            'data' => [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sent_at' => $message->created_at?->toIso8601String(),
                'seen_count' => $rows->total(),
                'seen_by' => $rows->getCollection()->map(fn ($read) => [
                    'user_id' => $read->user_id,
                    'name' => $read->user?->name ?? 'Deleted user',
                    'first_seen_at' => $read->first_seen_at
                        ->toIso8601String(),
                    'last_seen_at' => $read->last_seen_at
                        ->toIso8601String(),
                ])->values(),
                'pagination' => [
                    'page' => $rows->currentPage(),
                    'last_page' => $rows->lastPage(),
                    'per_page' => 100,
                ],
            ],
        ]);
    }
}