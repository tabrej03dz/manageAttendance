<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Models\UserActivityPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserActivityController extends Controller
{
    /**
     * Update current user activity every few seconds.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
            ],
            'route_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'page_url' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'page_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'active_seconds' => [
                'nullable',
                'integer',
                'min:1',
                'max:60',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $activity = UserActivity::query()
            ->whereKey($validated['activity_id'])
            ->where('user_id', $request->user()->id)
            ->whereNull('ended_at')
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity session not found.',
            ], 404);
        }

        $addedSeconds = (int) (
            $validated['active_seconds'] ?? 0
        );

        $isActive = filter_var(
            $validated['is_active'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        try {
            DB::transaction(function () use (
                $activity,
                $validated,
                $addedSeconds,
                $isActive
            ) {
                /*
                |--------------------------------------------------------------------------
                | Activity basic details update
                |--------------------------------------------------------------------------
                */

                UserActivity::query()
                    ->whereKey($activity->id)
                    ->update([
                        'last_seen_at' => now(),

                        'current_route' =>
                            $validated['route_name']
                            ?? $activity->current_route,

                        'current_url' =>
                            $validated['page_url']
                            ?? $activity->current_url,

                        'current_page_title' =>
                            $validated['page_title']
                            ?? $activity->current_page_title,

                        'status' =>
                            $isActive ? 'active' : 'idle',

                        'updated_at' => now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Active seconds increment
                |--------------------------------------------------------------------------
                |
                | Model instance par increment use nahi karna.
                | Query builder use karna hai.
                |
                */

                if ($isActive && $addedSeconds > 0) {
                    DB::table('user_activities')
                        ->where('id', $activity->id)
                        ->increment(
                            'active_seconds',
                            $addedSeconds
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Current page find
                |--------------------------------------------------------------------------
                */

                $pageQuery = UserActivityPage::query()
                    ->where(
                        'user_activity_id',
                        $activity->id
                    );

                if (!empty($validated['route_name'])) {
                    $pageQuery->where(
                        'route_name',
                        $validated['route_name']
                    );
                } else {
                    $pageQuery->where(
                        'page_url',
                        $validated['page_url'] ?? ''
                    );
                }

                $page = $pageQuery->first();

                /*
                |--------------------------------------------------------------------------
                | Page activity update
                |--------------------------------------------------------------------------
                */

                if ($page) {
                    UserActivityPage::query()
                        ->whereKey($page->id)
                        ->update([
                            'page_title' =>
                                $validated['page_title']
                                ?? $page->page_title,

                            'page_url' =>
                                $validated['page_url']
                                ?? $page->page_url,

                            'last_visited_at' => now(),
                            'updated_at' => now(),
                        ]);

                    if ($isActive && $addedSeconds > 0) {
                        DB::table('user_activity_pages')
                            ->where('id', $page->id)
                            ->increment(
                                'active_seconds',
                                $addedSeconds
                            );
                    }
                }
            });

            return response()->json([
                'success' => true,
                'activity_id' => $activity->id,
                'added_seconds' =>
                    $isActive ? $addedSeconds : 0,
                'status' =>
                    $isActive ? 'active' : 'idle',
            ]);
        } catch (\Throwable $exception) {
            Log::error('User activity heartbeat failed', [
                'user_id' => $request->user()?->id,
                'activity_id' => $validated['activity_id'],
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            report($exception);

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to update user activity.',
            ], 500);
        }
    }

    /**
     * End current user activity session.
     */
    public function end(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
            ],
        ]);

        $activity = UserActivity::query()
            ->whereKey($validated['activity_id'])
            ->where('user_id', $request->user()->id)
            ->whereNull('ended_at')
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => true,
                'message' =>
                    'Activity session already ended or not found.',
            ]);
        }

        try {
            UserActivity::query()
                ->whereKey($activity->id)
                ->update([
                    'last_seen_at' => now(),
                    'ended_at' => now(),
                    'status' => 'ended',
                    'updated_at' => now(),
                ]);

            if (
                (int) session(
                    'current_user_activity_id'
                ) ===
                (int) $activity->id
            ) {
                session()->forget(
                    'current_user_activity_id'
                );
            }

            return response()->json([
                'success' => true,
                'message' =>
                    'Activity session ended successfully.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('User activity end failed', [
                'user_id' => $request->user()?->id,
                'activity_id' => $validated['activity_id'],
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            report($exception);

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to end activity session.',
            ], 500);
        }
    }
}