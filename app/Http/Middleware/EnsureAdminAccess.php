<?php

namespace App\Http\Middleware;

use App\Services\Admin\SecurityEventService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if (! $user->isSuperAdmin() && ! $user->canAccessAdminPanel()) {
            app(SecurityEventService::class)->log('admin_access_denied', $request, [], $user, 'warning');
            abort(403);
        }

        return $next($request);
    }
}
