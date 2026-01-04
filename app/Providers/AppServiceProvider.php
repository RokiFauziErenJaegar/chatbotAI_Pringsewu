<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('chat', function (Request $request) {
            $ip = $request->ip();
            $convId = (string) $request->session()->get('conv_id', 'no-session');

            // kombinasi ip + session: lebih susah dibypass
            $key = 'chat:' . sha1($ip . '|' . $convId);

            // aturan: max 20 request per menit
            return Limit::perMinute(20)->by($key)->response(function () {
                return response()->json([
                    'ok' => false,
                    'reply' => 'Terlalu banyak permintaan. Silakan tunggu sebentar (rate limit aktif).'
                ], 429);
            });
        });
    }
}
