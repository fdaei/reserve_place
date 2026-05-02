<?php

namespace App\Http\Middleware;

use App\Services\Admin\SecurityEventService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if ($permissions === []) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermissionBySlug($permission)) {
                return $next($request);
            }
        }

        app(SecurityEventService::class)->log('permission_denied', $request, [
            'permissions' => $permissions,
            'route' => $request->path(),
        ], $user, 'warning');

        abort(403);
    }
}
