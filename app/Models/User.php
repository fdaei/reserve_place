<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'family',
        'national_code',
        'birth_day',
        'phone',
        'profile_image',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
    ];

    public function residences()
    {
        return $this->hasMany(Residence::class, 'user_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id', 'id');
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'user_id', 'id');
    }

    public function foodstores()
    {
        return $this->hasMany(FoodStore::class, 'user_id', 'id');
    }

    public function friends()
    {
        return $this->hasMany(Friend::class, 'user_id', 'id');
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    public function bookingRequestsAsCustomer(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'customer_id');
    }

    public function bookingRequestsAsHost(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'host_id');
    }

    public function bookingsAsCustomer(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function bookingsAsHost(): HasMany
    {
        return $this->hasMany(Booking::class, 'host_id');
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(HostWalletTransaction::class, 'host_id');
    }

    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(WithdrawRequest::class, 'host_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'host_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'host_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions()
    {
        return Permission::query()
            ->whereHas('roles.users', fn ($query) => $query->where('users.id', $this->id));
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $fullName = trim(implode(' ', array_filter([$this->name, $this->family])));

            return $fullName !== '' ? $fullName : 'کاربر #'.$this->id;
        });
    }

    protected function nationalCode(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $value)) ?: null,
            set: fn ($value) => [
                'national_code' => preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $value)) ?: null,
            ],
        );
    }

    protected function profileImageUrl(): Attribute
    {
        return Attribute::get(function () {
            return \App\Support\Admin\AdminFileManager::url($this->profile_image)
                ?? asset('storage/static/profile-example.png');
        });
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->where(fn (Builder $query) => $query->where('name', $roleName)->orWhere('slug', $roleName))
            ->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()
            ->where(fn (Builder $query) => $query
                ->where('slug', config('access-control.super_admin_role'))
                ->orWhere('name', 'مدیر کل')
                ->orWhere('slug', 'super-admin'))
            ->exists();
    }

    public function hasPermissionBySlug(string $permissionSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    public function hasAnyPermission(array $permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions()->whereIn('slug', $permissionSlugs)->exists();
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->hasPermissionBySlug(config('access-control.admin_login_permission'));
    }

    public function canManageContent(): bool
    {
        return $this->hasPermissionBySlug(config('access-control.content_manage_permission'));
    }

    public function assignRoleBySlug(string $roleSlug, ?string $roleName = null): void
    {
        if (blank($roleSlug)) {
            return;
        }

        $role = Role::firstOrCreate([
            'slug' => $roleSlug,
        ], [
            'name' => $roleName ?: $roleSlug,
        ]);

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function assignHostRole(): void
    {
        $this->assignRoleBySlug(config('access-control.host_role'), 'میزبان');
    }

    public function scopeHasHostedContent(Builder $query): Builder
    {
        return $query->where(function (Builder $host) {
            $host->has('residences')
                ->orHas('tours')
                ->orHas('foodstores');
        });
    }

    public function scopeRegularCustomers(Builder $query): Builder
    {
        return $query->whereDoesntHave('roles')
            ->whereDoesntHave('residences')
            ->whereDoesntHave('tours')
            ->whereDoesntHave('foodstores');
    }

    public function scopeAssignableAdminUsers(Builder $query): Builder
    {
        return $query->where(function (Builder $user) {
            $user->whereHas('roles')
                ->orWhere(fn (Builder $host) => $host->hasHostedContent());
        });
    }

    public function scopeEmployees(Builder $query): Builder
    {
        return $query->whereHas('roles', fn (Builder $role) => $role->where('slug', config('access-control.employee_role')));
    }

    public function scopeHosts(Builder $query): Builder
    {
        return $query->where(function (Builder $host) {
            $host->whereHas('roles', fn (Builder $role) => $role->where('slug', config('access-control.host_role')))
                ->orWhere(fn (Builder $legacyHost) => $legacyHost->hasHostedContent());
        });
    }

    public function scopeOnline(Builder $query, int $minutes = 5): Builder
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeSearch(Builder $query, $searchText)
    {
        return $query->where(function (Builder $builder) use ($searchText) {
            $builder->where('name', 'like', '%'.$searchText.'%')
                ->orWhere('family', 'like', '%'.$searchText.'%')
                ->orWhere('phone', 'like', '%'.$searchText.'%')
                ->orWhere('id', 'like', '%'.$searchText.'%');

            if (Schema::hasColumn('users', 'email')) {
                $builder->orWhere('email', 'like', '%'.$searchText.'%');
            }
        });
    }
}
