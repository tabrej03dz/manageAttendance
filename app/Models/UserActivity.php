<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserActivity extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'ended_at' => 'datetime',
        'active_seconds' => 'integer',
        'page_views' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(UserActivityPage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Online status
    |--------------------------------------------------------------------------
    */

    public function getLiveStatusAttribute(): string
    {
        if (!$this->last_seen_at) {
            return 'offline';
        }

        $seconds = $this->last_seen_at->diffInSeconds(now());

        if ($seconds <= 45 && $this->status === 'active') {
            return 'online';
        }

        if ($seconds <= 180) {
            return 'idle';
        }

        return 'offline';
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