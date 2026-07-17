<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityPage extends Model
{
    protected $fillable = [
        'user_activity_id',
        'user_id',
        'office_id',
        'route_name',
        'page_title',
        'page_url',
        'visit_count',
        'active_seconds',
        'first_visited_at',
        'last_visited_at',
    ];

    protected $casts = [
        'visit_count' => 'integer',
        'active_seconds' => 'integer',
        'first_visited_at' => 'datetime',
        'last_visited_at' => 'datetime',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(
            UserActivity::class,
            'user_activity_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function getFormattedDurationAttribute(): string
    {
        $seconds = (int) $this->active_seconds;

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf(
                '%02d:%02d:%02d',
                $hours,
                $minutes,
                $remainingSeconds
            );
        }

        return sprintf(
            '%02d:%02d',
            $minutes,
            $remainingSeconds
        );
    }
}