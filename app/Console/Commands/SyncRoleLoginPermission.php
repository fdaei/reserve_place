<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SyncRoleLoginPermission extends Command
{
    protected $signature = 'admin:sync-role-login-permission {--dry-run : فقط گزارش نقش‌های ناسازگار بدون تغییر دیتابیس}';

    protected $description = 'Attach admin-panel-access to roles that already have admin permissions.';

    public function handle(): int
    {
        $loginPermission = Permission::query()
            ->where('slug', config('access-control.admin_login_permission'))
            ->first();

        if (! $loginPermission) {
            $this->error('Admin login permission was not found.');

            return self::FAILURE;
        }

        $query = Role::query()
            ->whereHas('permissions')
            ->whereDoesntHave('permissions', fn (Builder $permission) => $permission->whereKey($loginPermission->id));

        $roles = (clone $query)
            ->with('permissions:id,slug')
            ->orderBy('id')
            ->get();

        if ($this->option('dry-run')) {
            $this->info("Roles with admin permissions but without [{$loginPermission->slug}]: {$roles->count()}");

            if ($roles->isNotEmpty()) {
                $this->table(
                    ['ID', 'Name', 'Slug', 'Permissions'],
                    $roles->map(fn (Role $role) => [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'permissions' => $role->permissions->pluck('slug')->implode(', '),
                    ])->all()
                );
            }

            return self::SUCCESS;
        }

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching([$loginPermission->id]);
        }

        $this->info("Attached [{$loginPermission->slug}] to {$roles->count()} role(s).");

        return self::SUCCESS;
    }
}
