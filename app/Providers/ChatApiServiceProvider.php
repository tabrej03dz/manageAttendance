<?php

namespace App\Providers;

use App\Services\ChatAccessService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class ChatApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The directory cache must expire each request, including with Octane.
        $this->app->scoped(ChatAccessService::class, fn () => new ChatAccessService());
    }

    public function boot(): void
    {
        foreach (['chat-api' => 120, 'chat-send' => 30, 'chat-broadcast' => 5, 'chat-devices' => 30] as $name => $count) {
            RateLimiter::for($name, fn (Request $request) => Limit::perMinute($count)
                ->by($name.':'.($request->user()?->id ?? $request->ip())));
        }
    }
}
