<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SyncHostRoles extends Command
{
    protected $signature = 'admin:sync-host-roles {--dry-run : فقط گزارش کاربران میزبان بدون تغییر دیتابیس}';

    protected $description = 'Attach the configured host role to legacy users who already own host content.';

    public function handle(): int
    {
        $hostRoleSlug = config('access-control.host_role');
        $query = $this->missingHostRoleQuery($hostRoleSlug);
        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info("Legacy hosts missing role [{$hostRoleSlug}]: {$count}");
            $sample = (clone $query)
                ->select(['id', 'name', 'family', 'phone'])
                ->latest('id')
                ->limit(20)
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'phone' => $user->phone,
                ]);

            if ($sample->isNotEmpty()) {
                $this->table(['ID', 'Name', 'Phone'], $sample->all());
            }

            return self::SUCCESS;
        }

        $role = Role::firstOrCreate([
            'slug' => $hostRoleSlug,
        ], [
            'name' => 'میزبان',
        ]);

        $attached = 0;
        (clone $query)
            ->select('users.*')
            ->chunkById(100, function ($users) use ($role, &$attached) {
                foreach ($users as $user) {
                    $user->roles()->syncWithoutDetaching([$role->id]);
                    $attached++;
                }
            });

        $this->info("Attached role [{$hostRoleSlug}] to {$attached} host user(s).");

        return self::SUCCESS;
    }

    protected function missingHostRoleQuery(string $hostRoleSlug): Builder
    {
        return User::query()
            ->hasHostedContent()
            ->whereDoesntHave('roles', fn (Builder $role) => $role->where('slug', $hostRoleSlug));
    }
}
