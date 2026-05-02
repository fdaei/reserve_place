<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleAssignmentRequest;
use App\Services\Admin\ActivityLogService;
use App\Models\Role;
use App\Models\User;

class RoleAssignmentController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('roles-manage'), 403);

        return view('admin.role-assign.index', [
            'users' => User::query()->assignableAdminUsers()->with('roles')->latest('id')->paginate(12),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(RoleAssignmentRequest $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('roles-manage'), 403);

        $user = User::query()->findOrFail($request->validated('user_id'));
        $user->roles()->sync($request->validated('roles', []));

        app(ActivityLogService::class)->log('role_assignment_update', $user, $request, [
            'role_ids' => $request->validated('roles', []),
        ], 'بروزرسانی نقش‌های کارمند');

        return back()->with('admin_success', 'نقش‌های کاربر بروزرسانی شد.');
    }
}
