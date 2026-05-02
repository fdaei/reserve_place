<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();

        if ($user && Schema::hasColumn('users', 'last_seen_at')) {
            $lastTrackedAt = $request->session()->get('admin_last_seen_tracked_at');

            if (! $lastTrackedAt || now()->diffInSeconds($lastTrackedAt) >= 60) {
                $user->forceFill(['last_seen_at' => now()])->saveQuietly();
                $request->session()->put('admin_last_seen_tracked_at', now());
            }
        }

        return $response;
    }
}
