<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminResourceRequest;
use App\Services\Admin\ActivityLogService;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\ReservationRequestService;
use App\Services\Sms\SmsService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\Admin\AdminResourceRegistry;
use App\Support\Admin\AdminFileManager;
use App\Support\Admin\PersianDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminResourceController extends Controller
{
    public function index(Request $request, string $resource)
    {
        $config = AdminResourceRegistry::get($resource);
        $this->ensureResourcePermission($config);
        $query = $this->baseQuery($config);

        AdminResourceRegistry::applySearch($query, $config, $request->query('search'));

        $statusColumn = $config['status_column'] ?? null;
        if ($statusColumn && $request->query('status') !== null && $request->query('status') !== '') {
            $query->where($statusColumn, $request->query('status'));
        }

        $filters = $this->configuredFilters($config);
        $this->applyConfiguredFilters($query, $filters, $request);

        match ($request->query('sort')) {
            'oldest' => $query->oldest('id'),
            'amount_desc' => $this->safeOrderBy($query, 'amount', 'desc'),
            'amount_asc' => $this->safeOrderBy($query, 'amount', 'asc'),
            default => $query->latest('id'),
        };

        return view('admin.resources.index', [
            'resource' => $resource,
            'config' => $config,
            'items' => $query->paginate(12)->withQueryString(),
            'statusOptions' => $this->statusOptions($config),
            'filters' => $filters,
        ]);
    }

    public function create(string $resource)
    {
        $config = AdminResourceRegistry::get($resource);
        abort_unless(AdminResourceRegistry::allows($resource, 'create'), 404);
        $this->ensureResourcePermission($config);

        return view('admin.resources.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => null,
            'fields' => AdminResourceRegistry::fields($resource),
        ]);
    }

    public function store(AdminResourceRequest $request, string $resource)
    {
        $config = AdminResourceRegistry::get($resource);
        $payload = AdminResourceRegistry::preparePayload($resource, $request->validated());
        $this->ensureResourcePermission($config);

        $this->applyCurrentUserDefaults($resource, $payload);
        $this->handleUploads($resource, $request, $payload);

        $item = DB::transaction(function () use ($config, $payload, $resource, $request) {
            /** @var Model $item */
            $item = $config['model']::create($payload);
            $this->syncConfiguredRole($config, $item);
            $this->syncConfiguredRelations($resource, $item, $request);
            $this->runPostSaveActions($resource, $item, $request);

            return $item;
        });

        app(ActivityLogService::class)->log('create', $item, $request, description: 'ایجاد '.$config['singular']);

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('admin_success', $config['singular'].' با موفقیت ایجاد شد.');
    }

    public function show(string $resource, int $id)
    {
        $config = AdminResourceRegistry::get($resource);
        abort_unless(AdminResourceRegistry::allows($resource, 'show'), 404);
        $this->ensureResourcePermission($config);
        $item = $this->baseQuery($config)->findOrFail($id);

        return view('admin.resources.show', [
            'resource' => $resource,
            'config' => $config,
            'item' => $item,
            'fields' => AdminResourceRegistry::fields($resource),
        ]);
    }

    public function edit(string $resource, int $id)
    {
        $config = AdminResourceRegistry::get($resource);
        abort_unless(AdminResourceRegistry::allows($resource, 'edit'), 404);
        $this->ensureResourcePermission($config);
        $item = $this->baseQuery($config)->findOrFail($id);

        return view('admin.resources.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => $item,
            'fields' => AdminResourceRegistry::fields($resource),
        ]);
    }

    public function update(AdminResourceRequest $request, string $resource, int $id)
    {
        $config = AdminResourceRegistry::get($resource);
        abort_unless(AdminResourceRegistry::allows($resource, 'edit'), 404);
        $this->ensureResourcePermission($config);
        /** @var Model $item */
        $item = $config['model']::findOrFail($id);
        $payload = AdminResourceRegistry::preparePayload($resource, $request->validated(), $item);

        $this->applyCurrentUserDefaults($resource, $payload);
        $this->handleUploads($resource, $request, $payload, $item);

        DB::transaction(function () use ($item, $payload, $config, $resource, $request) {
            $item->update($payload);
            $this->syncConfiguredRole($config, $item);
            $this->syncConfiguredRelations($resource, $item, $request);
            $this->runPostSaveActions($resource, $item, $request);
        });

        app(ActivityLogService::class)->log('update', $item, $request, description: 'بروزرسانی '.$config['singular']);

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('admin_success', $config['singular'].' با موفقیت بروزرسانی شد.');
    }

    public function destroy(string $resource, int $id)
    {
        $config = AdminResourceRegistry::get($resource);
        abort_unless(AdminResourceRegistry::allows($resource, 'delete'), 404);
        $this->ensureResourcePermission($config);
        /** @var Model $item */
        $item = $config['model']::findOrFail($id);

        try {
            $item->delete();
            $this->deleteManagedFiles($resource, $item);
        } catch (\Throwable $exception) {
            return back()->with('admin_warning', 'حذف این رکورد به دلیل وابستگی اطلاعاتی ممکن نیست.');
        }

        app(ActivityLogService::class)->log('delete', $item, request(), description: 'حذف '.$config['singular']);

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('admin_success', $config['singular'].' حذف شد.');
    }

    public function updateStatus(Request $request, string $resource, int $id)
    {
        $config = AdminResourceRegistry::get($resource);
        $statusColumn = $config['status_column'] ?? 'status';
        $this->ensureResourcePermission($config);
        /** @var Model $item */
        $item = $config['model']::findOrFail($id);

        $allowedColumns = collect($config['quick_actions'] ?? [])
            ->pluck('field')
            ->push($statusColumn)
            ->filter()
            ->unique()
            ->all();
        $statusColumn = $request->input('field', $statusColumn);
        abort_unless(in_array($statusColumn, $allowedColumns, true), 403);

        $nextStatus = $request->input('status', $this->nextStatus($item->{$statusColumn}, $resource));

        $item->update([
            $statusColumn => $nextStatus,
        ]);

        $this->runPostSaveActions($resource, $item, $request);
        app(ActivityLogService::class)->log('status_update', $item, $request, [
            'status_column' => $statusColumn,
            'status' => $nextStatus,
        ], 'تغییر وضعیت '.$config['singular']);

        return back()->with('admin_success', 'وضعیت با موفقیت بروزرسانی شد.');
    }

    protected function baseQuery(array $config): Builder
    {
        $query = $config['model']::query();

        if (! empty($config['with'])) {
            $query->with($config['with']);
        }

        $counts = collect($config['columns'] ?? [])
            ->pluck('count')
            ->filter()
            ->all();

        if ($counts) {
            $query->withCount($counts);
        }

        if (($config['scope'] ?? null) && is_callable($config['scope'])) {
            ($config['scope'])($query);
        }

        return $query;
    }

    protected function statusOptions(array $config): array
    {
        $statusColumn = $config['status_column'] ?? null;

        if (! $statusColumn) {
            return [];
        }

        $statusField = collect($config['fields'] ?? [])->firstWhere('name', $statusColumn);
        if (! empty($statusField['options']) && is_array($statusField['options'])) {
            return $statusField['options'];
        }

        if (($config['model'] ?? null) === \App\Models\BookingRequest::class) {
            return \App\Models\BookingRequest::statuses();
        }

        if (($config['model'] ?? null) === \App\Models\Booking::class) {
            return \App\Models\Booking::statuses();
        }

        return [
            '1' => 'فعال / تایید شده',
            '0' => 'غیرفعال / در انتظار',
        ];
    }

    protected function configuredFilters(array $config): array
    {
        return collect($config['filters'] ?? [])
            ->map(function (array $filter) {
                if (isset($filter['options']) && is_callable($filter['options'])) {
                    $filter['options'] = $filter['options']();
                }

                return $filter;
            })
            ->all();
    }

    protected function applyConfiguredFilters(Builder $query, array $filters, Request $request): void
    {
        foreach ($filters as $filter) {
            $field = $filter['name'] ?? null;
            $value = $field ? $request->query($field) : null;
            $type = $filter['type'] ?? 'select';

            if ($type === 'date' && blank($value)) {
                $value = $request->query($field.'_display');
            }

            if (! $field || $value === null || $value === '') {
                continue;
            }

            if ($type === 'date') {
                $parsed = PersianDate::parse((string) $value);
                if (! $parsed) {
                    continue;
                }

                $column = $filter['field'] ?? $field;
                $operator = $filter['operator'] ?? '=';
                $date = Carbon::parse($parsed);

                $query->where(
                    $column,
                    $operator,
                    $operator === '<=' ? $date->endOfDay() : $date->startOfDay()
                );

                continue;
            }

            $query->where($filter['field'] ?? $field, $value);
        }
    }

    protected function safeOrderBy(Builder $query, string $column, string $direction): void
    {
        $table = $query->getModel()->getTable();

        if (Schema::hasColumn($table, $column)) {
            $query->orderBy($column, $direction);
        } else {
            $query->latest('id');
        }
    }

    protected function nextStatus($current, string $resource)
    {
        if ($resource === 'booking-requests') {
            return match ($current) {
                'pending' => 'approved',
                'approved', 'accepted', 'assigned', 'called' => 'converted',
                default => 'pending',
            };
        }

        if (is_bool($current) || $current === 1 || $current === 0 || $current === '1' || $current === '0') {
            return ! (bool) $current;
        }

        return match ($current) {
            'pending' => 'confirmed',
            'confirmed', 'assigned' => 'completed',
            'approved' => 'paid',
            default => 'pending',
        };
    }

    protected function applyCurrentUserDefaults(string $resource, array &$payload): void
    {
        if ($resource === 'blog' && empty($payload['user_id'])) {
            $payload['user_id'] = auth()->id();
        }

        if ($resource === 'banners' && empty($payload['user_id'])) {
            $payload['user_id'] = auth()->id();
        }

        if ($resource === 'discounts' && empty($payload['created_by'])) {
            $payload['created_by'] = auth()->id();
        }

        if (in_array($resource, ['notifications', 'sms-templates', 'sms-logs'], true) && empty($payload['created_by'])) {
            $payload['created_by'] = auth()->id();
        }
    }

    protected function syncConfiguredRole(array $config, Model $item): void
    {
        if (empty($config['role_slug']) || ! method_exists($item, 'roles')) {
            return;
        }

        $role = Role::firstOrCreate([
            'slug' => $config['role_slug'],
        ], [
            'name' => $config['singular'] ?? $config['role_slug'],
        ]);

        $item->roles()->syncWithoutDetaching([$role->id]);
    }

    protected function syncConfiguredRelations(string $resource, Model $item, Request $request): void
    {
        foreach (AdminResourceRegistry::syncFields($resource) as $field) {
            $relation = $field['relation'] ?? null;

            if (! $relation || ! method_exists($item, $relation)) {
                continue;
            }

            $ids = $request->input($field['name'], []);
            if ($resource === 'roles' && $relation === 'permissions') {
                $ids = $this->withAdminLoginPermission($ids);
            }

            $item->{$relation}()->sync($ids);
        }
    }

    protected function withAdminLoginPermission(array $permissionIds): array
    {
        $permissionIds = collect($permissionIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($permissionIds->isEmpty()) {
            return [];
        }

        $loginPermissionId = Permission::query()
            ->where('slug', config('access-control.admin_login_permission'))
            ->value('id');

        return $loginPermissionId
            ? $permissionIds->push((int) $loginPermissionId)->unique()->values()->all()
            : $permissionIds->all();
    }

    protected function handleUploads(string $resource, Request $request, array &$payload, ?Model $item = null): void
    {
        foreach (AdminResourceRegistry::fileFields($resource) as $field) {
            $name = $field['name'];

            if (! $request->hasFile($name)) {
                unset($payload[$name]);

                continue;
            }

            $path = AdminFileManager::store(
                $request->file($name),
                $field['directory'] ?? 'uploads',
                $field['disk'] ?? 'public',
            );

            if ($item && filled($item->{$name} ?? null)) {
                AdminFileManager::delete($item->{$name}, $field['disk'] ?? 'public');
            }

            $payload[$name] = $path;
        }
    }

    protected function deleteManagedFiles(string $resource, Model $item): void
    {
        foreach (AdminResourceRegistry::fileFields($resource) as $field) {
            AdminFileManager::delete($item->{$field['name']} ?? null, $field['disk'] ?? 'public');
        }
    }

    protected function runPostSaveActions(string $resource, Model $item, Request $request): void
    {
        if (in_array($resource, ['residences', 'pending-properties', 'tours', 'pending-tours', 'restaurants', 'pending-restaurants'], true)
            && filled($item->user_id ?? null)) {
            User::query()->whereKey($item->user_id)->first()?->assignHostRole();
        }

        if ($resource === 'booking-requests' && (in_array($item->status, ['approved'], true) || in_array($item->host_approval_status ?? null, ['approved', 'manual_approved'], true))) {
            app(ReservationRequestService::class)->approve($item);
            $item->refresh();
        }

        if ($resource === 'booking-requests' && ($item->payment_status ?? null) === 'paid' && $request->input('field') !== 'settlement_status') {
            app(ReservationRequestService::class)->recordPayment($item);
            $item->refresh();
        }

        if ($resource === 'booking-requests' && ($item->settlement_status ?? null) === 'releasable') {
            app(ReservationRequestService::class)->releaseAmount($item);
            $item->refresh();
        }

        if ($resource === 'booking-requests' && ($item->settlement_status ?? null) === 'settled') {
            app(ReservationRequestService::class)->settleWithHost($item);
            $item->refresh();
        }

        if ($resource === 'notifications' && ($request->boolean('send_now') || $item->status === 'sent') && blank($item->sent_at)) {
            app(AdminNotificationService::class)->send($item);
            $item->refresh();
        }

        if ($resource === 'sms-logs' && ($request->boolean('send_now') || $item->status === 'sent') && blank($item->sent_at)) {
            app(SmsService::class)->send($item);
            $item->refresh();
        }
    }

    protected function ensureResourcePermission(array $config): void
    {
        $permission = $config['permission'] ?? null;

        if ($permission && ! auth()->user()?->hasPermissionBySlug($permission)) {
            abort(403);
        }
    }
}
