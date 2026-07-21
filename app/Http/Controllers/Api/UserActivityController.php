<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function heartbeat(
        Request $request
    ): JsonResponse {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
            ],
            'screen_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'active_seconds' => [
                'required',
                'integer',
                'min:1',
                'max:60',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $activity = UserActivity::query()
            ->whereKey($validated['activity_id'])
            ->where(
                'user_id',
                $request->user()->id
            )
            ->where(
                'source',
                'mobile_app'
            )
            ->whereNull('ended_at')
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' =>
                    'App activity session not found.',
            ], 404);
        }

        DB::transaction(function () use (
            $activity,
            $validated
        ) {
            DB::table('user_activities')
                ->where('id', $activity->id)
                ->update([
                    'last_seen_at' => now(),

                    'current_route' =>
                        $validated['screen_name']
                        ?? $activity->current_route,

                    'current_page_title' =>
                        $validated['screen_name']
                        ?? $activity->current_page_title,

                    'status' =>
                        $validated['is_active']
                            ? 'active'
                            : 'idle',

                    'updated_at' => now(),
                ]);

            if ($validated['is_active']) {
                DB::table('user_activities')
                    ->where('id', $activity->id)
                    ->increment(
                        'active_seconds',
                        $validated['active_seconds']
                    );
            }
        });

        return response()->json([
            'success' => true,
        ]);
    }

    public function end(
        Request $request
    ): JsonResponse {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
            ],
        ]);

        UserActivity::query()
            ->whereKey($validated['activity_id'])
            ->where(
                'user_id',
                $request->user()->id
            )
            ->whereNull('ended_at')
            ->update([
                'last_seen_at' => now(),
                'ended_at' => now(),
                'status' => 'ended',
            ]);

        return response()->json([
            'success' => true,
        ]);
    }
}