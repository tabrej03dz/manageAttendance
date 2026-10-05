<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

   

  public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(600)->by(
            $request->bearerToken()
                ? sha1($request->bearerToken())
                : $request->ip()
        );
    });

    // Chat limiters
    $chatKey = fn (Request $request) => $request->user()?->id
        ? 'user:' . $request->user()->id
        : ($request->bearerToken() ? sha1($request->bearerToken()) : $request->ip());

    RateLimiter::for('chat-api', function (Request $request) use ($chatKey) {
        return Limit::perMinute(300)->by($chatKey($request));
    });

    RateLimiter::for('chat-send', function (Request $request) use ($chatKey) {
        return Limit::perMinute(60)->by($chatKey($request));
    });

    RateLimiter::for('chat-broadcast', function (Request $request) use ($chatKey) {
        return Limit::perMinute(10)->by($chatKey($request));
    });

    RateLimiter::for('chat-devices', function (Request $request) use ($chatKey) {
        return Limit::perMinute(30)->by($chatKey($request));
    });

    $this->routes(function () {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    });
}
}
