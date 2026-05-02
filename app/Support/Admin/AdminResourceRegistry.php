<?php

namespace App\Support\Admin;

use App\Models\ActivityLog;
use App\Models\AdminNotification;
use App\Models\Banner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\City;
use App\Models\Comment;
use App\Models\Commission;
use App\Models\Country;
use App\Models\DiscountCode;
use App\Models\FoodStore;
use App\Models\FooterLink;
use App\Models\Friend;
use App\Models\HostWalletTransaction;
use App\Models\OptionCategory;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Permission;
use App\Models\PopularCity;
use App\Models\Province;
use App\Models\Residence;
use App\Models\Role;
use App\Models\SecurityEvent;
use App\Models\Settlement;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\SupportAreaTickets;
use App\Models\Tour;
use App\Models\User;
use App\Models\WithdrawRequest;
use App\Rules\NationalCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Morilog\Jalali\Jalalian;

class AdminResourceRegistry
{
    public static function all(): array
    {
        return [
            'users' => [
                'title' => 'کاربران عادی',
                'singular' => 'کاربر',
                'icon' => 'fa-users',
                'model' => User::class,
                'permission' => 'users-manage',
                'with' => ['roles'],
                'scope' => fn (Builder $query) => $query->regularCustomers(),
                'search' => ['name', 'family', 'phone', 'national_code', 'id'],
                'columns' => self::userColumns(),
                'fields' => self::userFields(),
                'rules' => self::userRules(),
            ],
            'hosts' => [
                'title' => 'میزبان‌ها',
                'singular' => 'میزبان',
                'icon' => 'fa-star',
                'model' => User::class,
                'permission' => 'users-manage',
                'role_slug' => config('access-control.host_role'),
                'with' => ['roles'],
                'scope' => fn (Builder $query) => $query->hosts(),
                'search' => ['name', 'family', 'phone', 'national_code', 'id'],
                'columns' => self::userColumns(),
                'fields' => self::userFields(),
                'rules' => self::userRules(),
            ],
            'employees' => [
                'title' => 'کارمندان',
                'singular' => 'کارمند',
                'icon' => 'fa-user',
                'model' => User::class,
                'permission' => 'users-manage',
                'role_slug' => config('access-control.employee_role'),
                'with' => ['roles'],
                'scope' => fn (Builder $query) => $query->whereHas('roles', fn (Builder $role) => $role->where('slug', config('access-control.employee_role'))),
                'search' => ['name', 'family', 'phone', 'national_code', 'id'],
                'columns' => self::userColumns(),
                'fields' => self::userFields(),
                'rules' => self::userRules(),
            ],
            'roles' => [
                'title' => 'نقش‌ها',
                'singular' => 'نقش',
                'icon' => 'fa-user',
                'model' => Role::class,
                'permission' => 'roles-manage',
                'with' => ['permissions', 'users'],
                'search' => ['name', 'slug'],
                'columns' => [
                    ['key' => 'name', 'label' => 'عنوان'],
                    ['key' => 'slug', 'label' => 'کلید'],
                    ['key' => 'users_count', 'label' => 'کاربران', 'type' => 'number', 'count' => 'users'],
                    ['key' => 'permissions_count', 'label' => 'دسترسی‌ها', 'type' => 'number', 'count' => 'permissions'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'عنوان نقش', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'کلید نقش', 'type' => 'text', 'required' => true, 'help' => 'مانند admin یا host'],
                    [
                        'name' => 'permission_ids',
                        'label' => 'مجوزها',
                        'type' => 'checkbox-group',
                        'relation' => 'permissions',
                        'options' => fn () => self::modelOptions(Permission::class, 'name'),
                        'value' => fn (?Role $role) => $role?->permissions()->pluck('permissions.id')->all() ?? [],
                        'help' => 'مجوزهای قابل استفاده برای این نقش را انتخاب کنید.',
                    ],
                ],
                'rules' => [
                    'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
                    'slug' => ['required', 'string', 'max:255', Rule::unique('roles', 'slug')],
                    'permission_ids' => ['nullable', 'array'],
                    'permission_ids.*' => ['exists:permissions,id'],
                ],
            ],
            'permissions' => [
                'title' => 'مجوزها',
                'singular' => 'مجوز',
                'icon' => 'fa-key',
                'model' => Permission::class,
                'permission' => 'permissions-manage',
                'with' => ['roles'],
                'search' => ['name', 'slug'],
                'columns' => [
                    ['key' => 'name', 'label' => 'عنوان'],
                    ['key' => 'slug', 'label' => 'کلید'],
                    ['key' => 'roles_count', 'label' => 'نقش‌ها', 'type' => 'number', 'count' => 'roles'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'عنوان مجوز', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'کلید مجوز', 'type' => 'text', 'required' => true, 'help' => 'مانند bookings.view'],
                ],
                'rules' => [
                    'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')],
                    'slug' => ['required', 'string', 'max:255', Rule::unique('permissions', 'slug')],
                ],
            ],
            'residences' => self::listingConfig('residences', 'اقامتگاه‌ها', 'اقامتگاه', Residence::class, 'fa-building'),
            'pending-properties' => self::listingConfig('pending-properties', 'در انتظار تایید اقامتگاه‌ها', 'اقامتگاه', Residence::class, 'fa-clock-o', 0),
            'tours' => self::listingConfig('tours', 'تورها', 'تور', Tour::class, 'fa-bus'),
            'pending-tours' => self::listingConfig('pending-tours', 'در انتظار تایید تورها', 'تور', Tour::class, 'fa-clock-o', 0),
            'restaurants' => self::listingConfig('restaurants', 'کافه و رستوران', 'رستوران', FoodStore::class, 'fa-cutlery'),
            'pending-restaurants' => self::listingConfig('pending-restaurants', 'در انتظار تایید رستوران‌ها', 'رستوران', FoodStore::class, 'fa-clock-o', 0),
            'travel-partners' => self::listingConfig('travel-partners', 'همسفر', 'همسفر', Friend::class, 'fa-users'),
            'pending-partners' => self::listingConfig('pending-partners', 'در انتظار تایید همسفر', 'همسفر', Friend::class, 'fa-clock-o', 0),
            'blog' => [
                'title' => 'مدیریت وبلاگ',
                'singular' => 'پست وبلاگ',
                'icon' => 'fa-pencil',
                'model' => BlogPost::class,
                'permission' => config('access-control.content_manage_permission'),
                'with' => ['author', 'blogCategory'],
                'search' => ['title', 'slug', 'category'],
                'status_column' => 'status',
                'columns' => [
                    ['key' => 'title', 'label' => 'عنوان'],
                    ['key' => 'author.full_name', 'label' => 'نویسنده'],
                    ['key' => 'blogCategory.name', 'label' => 'دسته‌بندی', 'display' => fn (BlogPost $post) => data_get($post, 'blogCategory.name') ?: $post->category],
                    ['key' => 'published_at', 'label' => 'تاریخ انتشار', 'type' => 'datetime'],
                    ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'اسلاگ', 'type' => 'text', 'help' => 'در صورت خالی بودن از عنوان ساخته می‌شود.'],
                    ['name' => 'user_id', 'label' => 'نویسنده', 'type' => 'select', 'options' => fn () => self::userOptions()],
                    ['name' => 'blog_category_id', 'label' => 'دسته‌بندی', 'type' => 'select', 'options' => fn () => self::modelOptions(BlogCategory::class, 'name'), 'required' => true],
                    ['name' => 'featured_image', 'label' => 'تصویر شاخص', 'type' => 'file', 'directory' => 'blog', 'accept' => 'image/*'],
                    ['name' => 'excerpt', 'label' => 'خلاصه', 'type' => 'textarea', 'span' => 2],
                    ['name' => 'body', 'label' => 'متن', 'type' => 'textarea', 'required' => true, 'span' => 2],
                    ['name' => 'published_at', 'label' => 'تاریخ انتشار', 'type' => 'datetime-local'],
                    ['name' => 'status', 'label' => 'منتشر شود', 'type' => 'checkbox'],
                    ['name' => 'meta_title', 'label' => 'عنوان سئو', 'type' => 'text'],
                    ['name' => 'meta_description', 'label' => 'توضیح سئو', 'type' => 'textarea'],
                ],
                'rules' => [
                    'title' => ['required', 'string', 'max:255'],
                    'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')],
                    'user_id' => ['nullable', 'exists:users,id'],
                    'blog_category_id' => ['required', 'exists:blog_categories,id'],
                    'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                    'excerpt' => ['nullable', 'string'],
                    'body' => ['required', 'string'],
                    'published_at' => ['nullable', 'date'],
                    'status' => ['nullable', 'boolean'],
                    'meta_title' => ['nullable', 'string', 'max:255'],
                    'meta_description' => ['nullable', 'string'],
                ],
            ],
            'blog-categories' => [
                'title' => 'دسته‌بندی وبلاگ',
                'singular' => 'دسته وبلاگ',
                'icon' => 'fa-folder-open-o',
                'model' => BlogCategory::class,
                'permission' => config('access-control.content_manage_permission'),
                'with' => ['posts'],
                'search' => ['name', 'slug'],
                'status_column' => 'status',
                'columns' => [
                    ['key' => 'name', 'label' => 'عنوان'],
                    ['key' => 'slug', 'label' => 'اسلاگ'],
                    ['key' => 'posts_count', 'label' => 'پست‌ها', 'type' => 'number', 'count' => 'posts'],
                    ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'اسلاگ', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'توضیحات', 'type' => 'textarea', 'span' => 2],
                    ['name' => 'sort_order', 'label' => 'ترتیب', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
                ],
                'rules' => [
                    'name' => ['required', 'string', 'max:255', Rule::unique('blog_categories', 'name')],
                    'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_categories', 'slug')],
                    'description' => ['nullable', 'string'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'status' => ['nullable', 'boolean'],
                ],
            ],
            'pages' => [
                'title' => 'مدیریت صفحات',
                'singular' => 'صفحه',
                'icon' => 'fa-file-text-o',
                'model' => Page::class,
                'permission' => 'pages-manage',
                'with' => ['category'],
                'search' => ['title', 'url_text'],
                'status_column' => 'status',
                'columns' => [
                    ['key' => 'title', 'label' => 'عنوان'],
                    ['key' => 'category.name', 'label' => 'دسته‌بندی'],
                    ['key' => 'url_text', 'label' => 'آدرس'],
                    ['key' => 'visit_count', 'label' => 'بازدید', 'type' => 'number'],
                    ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                    ['name' => 'category_id', 'label' => 'دسته‌بندی', 'type' => 'select', 'options' => fn () => self::pageCategoryOptions()],
                    ['name' => 'url_text', 'label' => 'آدرس صفحه', 'type' => 'text', 'required' => true],
                    ['name' => 'text', 'label' => 'متن صفحه', 'type' => 'textarea', 'required' => true, 'span' => 2],
                    ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
                ],
                'rules' => [
                    'title' => ['required', 'string', 'max:255'],
                    'category_id' => ['nullable', 'exists:page_categories,id'],
                    'url_text' => ['required', 'string', 'max:255'],
                    'text' => ['required', 'string'],
                    'status' => ['nullable', 'boolean'],
                ],
            ],
            'banners' => [
                'title' => 'بنرها',
                'singular' => 'بنر',
                'icon' => 'fa-picture-o',
                'model' => Banner::class,
                'permission' => config('access-control.content_manage_permission'),
                'with' => ['creator'],
                'search' => ['title', 'position'],
                'status_column' => 'status',
                'columns' => [
                    ['key' => 'title', 'label' => 'عنوان'],
                    ['key' => 'position', 'label' => 'جایگاه'],
                    ['key' => 'sort_order', 'label' => 'ترتیب', 'type' => 'number'],
                    ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                    ['name' => 'subtitle', 'label' => 'زیرعنوان', 'type' => 'text'],
                    ['name' => 'image_path', 'label' => 'تصویر', 'type' => 'file', 'directory' => 'banners', 'accept' => 'image/*', 'required' => true],
                    ['name' => 'link_url', 'label' => 'لینک', 'type' => 'url'],
                    ['name' => 'position', 'label' => 'جایگاه', 'type' => 'select', 'options' => config('entity-types.banner_positions', [])],
                    ['name' => 'sort_order', 'label' => 'ترتیب', 'type' => 'number'],
                    ['name' => 'starts_at', 'label' => 'شروع نمایش', 'type' => 'datetime-local'],
                    ['name' => 'ends_at', 'label' => 'پایان نمایش', 'type' => 'datetime-local'],
                    ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
                ],
                'rules' => [
                    'title' => ['required', 'string', 'max:255'],
                    'subtitle' => ['nullable', 'string', 'max:255'],
                    'image_path' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                    'link_url' => ['nullable', 'url:http,https'],
                    'position' => ['required', 'string', 'max:80'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'starts_at' => ['nullable', 'date'],
                    'ends_at' => ['nullable', 'date', 'after:starts_at'],
                    'status' => ['nullable', 'boolean'],
                ],
            ],
            'countries' => self::locationConfig('countries', 'کشورها', 'کشور', Country::class, 'fa-globe'),
            'provinces' => self::locationConfig('provinces', 'استان‌ها', 'استان', Province::class, 'fa-map-marker'),
            'cities' => self::locationConfig('cities', 'شهرها', 'شهر', City::class, 'fa-map-pin'),
            'popular-cities' => self::popularCityConfig(),
            'tools' => self::optionCategoryConfig('tools', 'امکانات اقامتگاه', 'residence'),
            'tools-foodstore' => self::optionCategoryConfig('tools-foodstore', 'امکانات رستوران', 'foodstore'),
            'tools-friends' => self::optionCategoryConfig('tools-friends', 'آپشن همسفر', 'friend'),
            'comments' => [
                'title' => 'نظرات',
                'singular' => 'نظر',
                'icon' => 'fa-star',
                'model' => Comment::class,
                'permission' => config('access-control.content_manage_permission'),
                'with' => ['user', 'residence', 'store'],
                'search' => ['id', 'body'],
                'status_column' => 'status',
                'columns' => [
                    ['key' => 'user.full_name', 'label' => 'کاربر'],
                    ['key' => 'residence.title', 'label' => 'اقامتگاه'],
                    ['key' => 'store.title', 'label' => 'رستوران'],
                    ['key' => 'body', 'label' => 'متن نظر', 'type' => 'truncate'],
                    ['key' => 'point', 'label' => 'امتیاز', 'type' => 'number'],
                    ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                    ['key' => 'created_at', 'label' => 'تاریخ', 'type' => 'date'],
                ],
                'fields' => [
                    ['name' => 'user_id', 'label' => 'کاربر', 'type' => 'select', 'options' => fn () => self::userOptions(), 'required' => true],
                    ['name' => 'residence_id', 'label' => 'اقامتگاه', 'type' => 'select', 'options' => fn () => self::modelOptions(Residence::class)],
                    ['name' => 'store_id', 'label' => 'رستوران', 'type' => 'select', 'options' => fn () => self::modelOptions(FoodStore::class)],
                    ['name' => 'point', 'label' => 'امتیاز', 'type' => 'number', 'required' => true],
                    ['name' => 'body', 'label' => 'متن نظر', 'type' => 'textarea', 'span' => 2, 'required' => true],
                    ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['approved' => 'تایید شده', 'pending' => 'در انتظار', 'rejected' => 'رد شده']],
                ],
                'rules' => [
                    'user_id' => ['required', 'exists:users,id'],
                    'residence_id' => ['nullable', 'exists:residences,id'],
                    'store_id' => ['nullable', 'exists:food_stores,id'],
                    'point' => ['required', 'integer', 'between:1,5'],
                    'body' => ['required', 'string', 'max:5000'],
                    'status' => ['required', 'string', Rule::in(['approved', 'pending', 'rejected'])],
                ],
            ],
            'supportAreas' => [
                'title' => 'دسته‌بندی پیام‌ها',
                'singular' => 'دسته‌بندی پیام',
                'icon' => 'fa-list-alt',
                'model' => SupportAreaTickets::class,
                'permission' => 'tickets-manage',
                'search' => ['title'],
                'status_column' => 'status',
                'columns' => [
                    ['key' => 'title', 'label' => 'عنوان'],
                    ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                    ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
                ],
                'rules' => [
                    'title' => ['required', 'string', 'max:255'],
                    'status' => ['nullable', 'boolean'],
                ],
            ],
            'booking-requests' => self::bookingRequestConfig(),
            'bookings' => self::bookingConfig(),
            'host-wallet' => self::walletConfig(),
            'wallet-transactions' => self::walletConfig('همه تراکنش‌ها'),
            'withdraw-requests' => self::withdrawConfig(),
            'settlements' => self::settlementConfig(),
            'commissions' => self::commissionConfig(),
            'discounts' => self::discountConfig(),
            'footer-links' => self::footerLinkConfig(),
            'notifications' => self::notificationConfig(),
            'sms-templates' => self::smsTemplateConfig(),
            'sms-logs' => self::smsLogConfig(),
            'activity-logs' => self::activityLogConfig(),
            'security-events' => self::securityEventConfig(),
        ];
    }

    public static function get(string $key): array
    {
        abort_unless(static::exists($key), 404);

        return static::all()[$key];
    }

    public static function exists(string $key): bool
    {
        return array_key_exists($key, static::all());
    }

    public static function allows(string $key, string $ability): bool
    {
        $config = static::get($key);

        return match ($ability) {
            'create' => $config['create'] ?? true,
            'edit' => $config['edit'] ?? true,
            'delete' => $config['delete'] ?? true,
            'show' => $config['show'] ?? true,
            default => true,
        };
    }

    public static function keys(): array
    {
        return array_keys(static::all());
    }

    public static function fields(string $key): array
    {
        $rules = static::get($key)['rules'] ?? [];

        return collect(static::get($key)['fields'] ?? [])
            ->map(function (array $field) {
                if (isset($field['options']) && is_callable($field['options'])) {
                    $field['options'] = $field['options']();
                }

                return $field;
            })
            ->map(function (array $field) use ($rules) {
                $fieldRules = $rules[$field['name']] ?? [];
                $field['required'] = $field['required'] ?? collect($fieldRules)->contains(fn ($rule) => $rule === 'required');

                return $field;
            })
            ->all();
    }

    public static function rules(string $key, ?Model $model = null): array
    {
        $rules = static::get($key)['rules'] ?? [];

        if ($model) {
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = collect($fieldRules)
                    ->map(function ($rule) use ($model) {
                        return $rule instanceof Unique
                            ? $rule->ignore($model->getKey())
                            : $rule;
                    })
                    ->all();
            }
        }

        return $rules;
    }

    public static function attributes(string $key): array
    {
        return collect(static::fields($key))
            ->mapWithKeys(fn (array $field) => [$field['name'] => $field['label'] ?? $field['name']])
            ->all();
    }

    public static function fileFields(string $key): array
    {
        return collect(static::fields($key))
            ->filter(fn (array $field) => ($field['type'] ?? null) === 'file')
            ->values()
            ->all();
    }

    public static function syncFields(string $key): array
    {
        return collect(static::fields($key))
            ->filter(fn (array $field) => ($field['type'] ?? null) === 'checkbox-group')
            ->values()
            ->all();
    }

    public static function prepareForValidation(string $key, array $payload): array
    {
        foreach (static::fields($key) as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'checkbox' && ! array_key_exists($name, $payload)) {
                $payload[$name] = 0;
            }

            if (in_array($type, ['money', 'number'], true) && (array_key_exists($name, $payload) || array_key_exists($name.'_display', $payload))) {
                $rawValue = $payload[$name] ?? $payload[$name.'_display'] ?? null;
                $normalized = convertPersianToEnglishNumbers((string) $rawValue);
                $payload[$name] = preg_replace('/[^\d-]+/', '', (string) $normalized);

                if ($payload[$name] === '' && array_key_exists('default', $field)) {
                    $payload[$name] = (string) $field['default'];
                }
            }

            if (in_array($type, ['date', 'datetime-local'], true)) {
                $displayValue = $payload[$name.'_display'] ?? null;
                $rawValue = (string) ($payload[$name] ?? $displayValue ?? '');
                $parsedValue = PersianDate::parse($rawValue, $type);
                $payload[$name] = filled($rawValue) && $parsedValue === null ? $rawValue : $parsedValue;
            }

            if ($name === 'phone' || str_ends_with($name, '_phone')) {
                $payload[$name] = array_key_exists($name, $payload)
                    ? preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $payload[$name]))
                    : ($payload[$name] ?? null);
            }

            if ($name === 'national_code' && array_key_exists($name, $payload)) {
                $payload[$name] = preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $payload[$name]));
            }

            if ($type === 'url' && array_key_exists($name, $payload)) {
                $payload[$name] = trim((string) $payload[$name]);
            }
        }

        if ($key === 'sms-logs') {
            if (blank($payload['phone'] ?? null) && filled($payload['user_id'] ?? null)) {
                $payload['phone'] = User::query()->whereKey($payload['user_id'])->value('phone');
            }

            if (blank($payload['message'] ?? null) && filled($payload['template_id'] ?? null)) {
                $payload['message'] = SmsTemplate::query()->whereKey($payload['template_id'])->value('body');
            }
        }

        return $payload;
    }

    public static function applySearch(Builder $query, array $config, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        $model = new $config['model'];

        if (method_exists($model, 'scopeSearch')) {
            return $query->search($search);
        }

        $fields = $config['search'] ?? ['id'];

        return $query->where(function (Builder $builder) use ($fields, $search) {
            foreach ($fields as $field) {
                if (str_contains($field, '.')) {
                    continue;
                }

                $builder->orWhere($field, 'like', '%'.$search.'%');
            }
        });
    }

    public static function preparePayload(string $key, array $payload, ?Model $model = null): array
    {
        foreach (static::fields($key) as $field) {
            if (($field['type'] ?? 'text') === 'checkbox') {
                $payload[$field['name']] = (bool) ($payload[$field['name']] ?? false);
            }

            if (isset($field['default']) && ! array_key_exists($field['name'], $payload)) {
                $payload[$field['name']] = $field['default'];
            }

            if (($field['persist'] ?? true) === false || ($field['type'] ?? null) === 'checkbox-group') {
                unset($payload[$field['name']]);
            }
        }

        if (array_key_exists('slug', $payload) && blank($payload['slug'] ?? null) && filled($payload['title'] ?? null)) {
            $payload['slug'] = static::makeSlug($payload['title']);
        }

        if ($key === 'blog' && blank($payload['published_at'] ?? null) && ! empty($payload['status'])) {
            $payload['published_at'] = now();
        }

        if ($key === 'bookings' && blank($payload['booking_number'] ?? null)) {
            $payload['booking_number'] = 'INJ-'.now()->format('ymd').'-'.random_int(1000, 9999);
        }

        if ($key === 'booking-requests' && blank($payload['request_number'] ?? null)) {
            $payload['request_number'] = 'REQ-'.now()->format('ymd').'-'.random_int(1000, 9999);
        }

        if ($key === 'booking-requests' && isset($payload['total_amount'])) {
            $rate = (float) getConfigs('payment_commission_percent', 10);
            $commission = (int) round(((int) $payload['total_amount']) * ($rate / 100));
            $payload['commission_amount'] = $payload['commission_amount'] ?? $commission;
            $payload['host_share_amount'] = $payload['host_share_amount'] ?? max(0, (int) $payload['total_amount'] - (int) $payload['commission_amount']);
        }

        if ($key === 'discounts' && isset($payload['code'])) {
            $payload['code'] = Str::upper(trim($payload['code']));
            $payload['min_order_amount'] = (int) ($payload['min_order_amount'] ?? 0);
            $payload['used_count'] = (int) ($payload['used_count'] ?? 0);
        }

        if ($key === 'blog' && filled($payload['blog_category_id'] ?? null)) {
            $payload['category'] = BlogCategory::query()->whereKey($payload['blog_category_id'])->value('name');
        }

        if ($key === 'sms-logs' && blank($payload['phone'] ?? null) && filled($payload['user_id'] ?? null)) {
            $payload['phone'] = User::query()->whereKey($payload['user_id'])->value('phone');
        }

        if ($key === 'sms-logs' && blank($payload['message'] ?? null) && filled($payload['template_id'] ?? null)) {
            $payload['message'] = SmsTemplate::query()->whereKey($payload['template_id'])->value('body');
        }

        foreach ($payload as $field => $value) {
            if ($value === '') {
                $payload[$field] = null;
            }
        }

        return $payload;
    }

    public static function displayValue(Model $model, array $column): string
    {
        if (isset($column['display']) && is_callable($column['display'])) {
            return (string) (($column['display'])($model) ?: '-');
        }

        if (isset($column['count'])) {
            $relation = $column['count'];

            return (string) ($model->{$relation.'_count'} ?? $model->{$relation}()->count());
        }

        $value = data_get($model, $column['key']);

        if (($column['key'] ?? '') === 'bookable') {
            $value = data_get($model, 'bookable.title') ?: data_get($model, 'bookable.name');
        }

        return match ($column['type'] ?? 'text') {
            'date' => static::formatDate($value),
            'datetime' => static::formatDate($value, true),
            'money' => filled($value) ? number_format((int) $value).' تومان' : '-',
            'number' => filled($value) ? number_format((int) $value) : '0',
            'boolean' => $value ? 'بله' : 'خیر',
            'image' => filled($value) ? (string) $value : '',
            'status' => static::statusLabel($value),
            'truncate' => filled($value) ? Str::limit(strip_tags((string) $value), 70) : '-',
            default => filled($value) ? (string) $value : '-',
        };
    }

    public static function statusLabel($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value) || $value === 1 || $value === 0 || $value === '1' || $value === '0') {
            return (bool) $value ? 'فعال' : 'در انتظار';
        }

        return [
            '2' => 'رد شده',
            'pending' => 'در انتظار بررسی',
            'pending_host' => 'در انتظار تأیید میزبان',
            'awaiting_payment' => 'تأیید میزبان - منتظر پرداخت',
            'assigned' => 'اختصاص داده شده',
            'called' => 'تماس گرفته شد',
            'converted' => 'تبدیل شده',
            'confirmed' => 'تایید شده',
            'completed' => 'تکمیل شده',
            'staying' => 'در حال اقامت',
            'ended' => 'پایان اقامت',
            'releasable' => 'آماده تسویه',
            'cancelled' => 'لغو شده',
            'paid' => 'پرداخت شده',
            'unpaid' => 'پرداخت نشده',
            'failed' => 'ناموفق',
            'refunded' => 'مسترد شده',
            'blocked' => 'مسدود شده',
            'available' => 'قابل برداشت',
            'manual_approved' => 'تأیید دستی ادمین',
            'not_started' => 'منتظر شروع اقامت',
            'draft' => 'پیش‌نویس',
            'sent' => 'ارسال شده',
            'failed' => 'ناموفق',
            'posted' => 'ثبت شده',
            'approved' => 'تایید شده',
            'accepted' => 'پذیرفته شده',
            'rejected' => 'رد شده',
            'settled' => 'تسویه شده',
            'credit' => 'بستانکار',
            'debit' => 'بدهکار',
        ][$value] ?? (string) $value;
    }

    public static function statusClass($value): string
    {
        if (is_bool($value) || $value === 1 || $value === '1') {
            return 'active';
        }

        if ($value === 0 || $value === '0') {
            return 'pending';
        }

        return match ($value) {
            'confirmed', 'completed', 'paid', 'posted', 'available', 'approved', 'accepted', 'settled', 'converted', 'sent', 'manual_approved' => 'active',
            2, '2', 'cancelled', 'rejected', 'failed', 'refunded' => 'inactive',
            'assigned', 'called', 'staying', 'not_started' => 'info',
            'blocked', 'unpaid', 'pending_host', 'awaiting_payment', 'releasable', 'ended' => 'pending',
            default => 'pending',
        };
    }

    public static function userOptions(?string $roleSlug = null): array
    {
        return User::query()
            ->when($roleSlug, function (Builder $query) use ($roleSlug) {
                if ($roleSlug === config('access-control.host_role')) {
                    $query->hosts();

                    return;
                }

                $query->whereHas('roles', fn (Builder $role) => $role->where('slug', $roleSlug));
            })
            ->orderBy('name')
            ->orderBy('family')
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => $user->full_name.' - '.$user->phone])
            ->prepend('انتخاب کنید', '')
            ->all();
    }

    public static function modelOptions(string $modelClass, string $titleField = 'title'): array
    {
        return $modelClass::query()
            ->latest('id')
            ->limit(250)
            ->get()
            ->mapWithKeys(fn ($model) => [$model->id => ($model->{$titleField} ?? $model->full_name ?? $model->name ?? ('#'.$model->id))])
            ->prepend('انتخاب کنید', '')
            ->all();
    }

    protected static function userColumns(): array
    {
        return [
            ['key' => 'full_name', 'label' => 'نام و نام خانوادگی'],
            ['key' => 'phone', 'label' => 'موبایل'],
            ['key' => 'national_code', 'label' => 'کد ملی'],
            ['key' => 'birth_day', 'label' => 'تاریخ تولد', 'type' => 'date'],
            ['key' => 'last_seen_at', 'label' => 'آخرین فعالیت', 'type' => 'datetime'],
            ['key' => 'created_at', 'label' => 'عضویت', 'type' => 'date'],
        ];
    }

    protected static function userFields(): array
    {
        return [
            ['name' => 'name', 'label' => 'نام', 'type' => 'text', 'required' => true],
            ['name' => 'family', 'label' => 'نام خانوادگی', 'type' => 'text', 'required' => true],
            ['name' => 'phone', 'label' => 'شماره موبایل', 'type' => 'text', 'required' => true],
            ['name' => 'national_code', 'label' => 'کد ملی', 'type' => 'text', 'inputmode' => 'numeric', 'maxlength' => 10],
            ['name' => 'birth_day', 'label' => 'تاریخ تولد', 'type' => 'date'],
            ['name' => 'profile_image', 'label' => 'تصویر پروفایل', 'type' => 'file', 'directory' => 'user', 'accept' => 'image/*'],
        ];
    }

    protected static function userRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'family' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'digits_between:10,11'],
            'national_code' => ['nullable', 'digits:10', new NationalCode()],
            'birth_day' => ['nullable', 'date'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    protected static function listingConfig(string $key, string $title, string $singular, string $modelClass, string $icon, ?int $forcedStatus = null): array
    {
        $fields = [
            ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
            ['name' => 'user_id', 'label' => 'مالک/میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role')), 'required' => true],
            ['name' => 'province_id', 'label' => 'استان', 'type' => 'select', 'options' => fn () => self::modelOptions(Province::class, 'name'), 'required' => true],
            ['name' => 'city_id', 'label' => 'شهر', 'type' => 'select', 'options' => fn () => self::modelOptions(City::class, 'name')],
            ['name' => 'address', 'label' => 'آدرس', 'type' => 'textarea', 'span' => 2],
            ['name' => 'amount', 'label' => 'قیمت', 'type' => 'money'],
            ['name' => 'image', 'label' => 'تصویر', 'type' => 'file', 'directory' => self::listingImageDirectory($modelClass), 'accept' => 'image/*'],
            ['name' => 'lat', 'label' => 'عرض جغرافیایی', 'type' => 'text'],
            ['name' => 'lng', 'label' => 'طول جغرافیایی', 'type' => 'text'],
            ['name' => 'vip', 'label' => 'ویژه', 'type' => 'checkbox'],
            ['name' => 'status', 'label' => 'فعال/تایید شده', 'type' => 'checkbox'],
        ];

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['required', 'string'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'lat' => ['nullable', 'string', 'max:80'],
            'lng' => ['nullable', 'string', 'max:80'],
            'vip' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ];

        if ($modelClass === Residence::class) {
            array_splice($fields, 4, 0, [
                ['name' => 'residence_type', 'label' => 'نوع اقامتگاه', 'type' => 'select', 'options' => Residence::getResidenceType()],
                ['name' => 'area_type', 'label' => 'نوع منطقه', 'type' => 'select', 'options' => Residence::getAreaType()],
                ['name' => 'room_number', 'label' => 'اتاق', 'type' => 'number'],
                ['name' => 'area', 'label' => 'متراژ', 'type' => 'number'],
                ['name' => 'people_number', 'label' => 'ظرفیت', 'type' => 'number'],
                ['name' => 'last_week_amount', 'label' => 'قیمت هفته قبل', 'type' => 'money'],
            ]);
            $rules += [
                'residence_type' => ['nullable', 'integer'],
                'area_type' => ['nullable', 'integer'],
                'room_number' => ['nullable', 'integer'],
                'area' => ['nullable', 'integer'],
                'people_number' => ['nullable', 'integer'],
                'last_week_amount' => ['nullable', 'integer'],
            ];
        }

        if ($modelClass === Tour::class) {
            array_splice($fields, 4, 0, [
                ['name' => 'description', 'label' => 'توضیحات', 'type' => 'textarea', 'span' => 2],
                ['name' => 'tour_type', 'label' => 'نوع تور', 'type' => 'select', 'options' => Tour::getTourType()],
                ['name' => 'residence_type', 'label' => 'نوع اقامت', 'type' => 'select', 'options' => Tour::getResidenceType()],
                ['name' => 'tour_duration', 'label' => 'مدت تور', 'type' => 'number'],
                ['name' => 'min_people', 'label' => 'حداقل نفرات', 'type' => 'number'],
                ['name' => 'max_people', 'label' => 'حداکثر نفرات', 'type' => 'number'],
                ['name' => 'tour_time_frame', 'label' => 'بازه زمانی', 'type' => 'text'],
                ['name' => 'open_tour_time', 'label' => 'زمان شروع', 'type' => 'datetime-local'],
                ['name' => 'expire_date', 'label' => 'تاریخ انقضا', 'type' => 'date'],
            ]);
            $rules += [
                'description' => ['nullable', 'string'],
                'tour_type' => ['nullable', 'integer'],
                'residence_type' => ['nullable', 'integer'],
                'tour_duration' => ['nullable', 'integer'],
                'min_people' => ['nullable', 'integer'],
                'max_people' => ['nullable', 'integer'],
                'tour_time_frame' => ['nullable', 'string', 'max:255'],
                'open_tour_time' => ['nullable', 'string', 'max:255'],
                'expire_date' => ['nullable', 'date'],
            ];
        }

        if ($modelClass === FoodStore::class) {
            array_splice($fields, 4, 0, [
                ['name' => 'store_type', 'label' => 'نوع مجموعه', 'type' => 'select', 'options' => FoodStore::getStoreType()],
                ['name' => 'food_type', 'label' => 'نوع غذا', 'type' => 'select', 'options' => FoodStore::getFoodType()],
                ['name' => 'open_time', 'label' => 'ساعت باز شدن', 'type' => 'time'],
                ['name' => 'close_time', 'label' => 'ساعت بسته شدن', 'type' => 'time'],
            ]);
            $rules += [
                'store_type' => ['nullable', 'integer'],
                'food_type' => ['nullable', 'integer'],
                'open_time' => ['nullable'],
                'close_time' => ['nullable'],
            ];
        }

        if ($modelClass === Friend::class) {
            $fields = [
                ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                ['name' => 'user_id', 'label' => 'کاربر', 'type' => 'select', 'options' => fn () => self::userOptions(), 'required' => true],
                ['name' => 'country_id', 'label' => 'کشور', 'type' => 'select', 'options' => fn () => self::modelOptions(Country::class, 'name'), 'required' => true],
                ['name' => 'province_id', 'label' => 'استان', 'type' => 'select', 'options' => fn () => self::modelOptions(Province::class, 'name'), 'required' => true],
                ['name' => 'travel_type', 'label' => 'نوع سفر', 'type' => 'select', 'options' => Friend::getTravelType()],
                ['name' => 'travel_duration', 'label' => 'مدت سفر', 'type' => 'text'],
                ['name' => 'my_gender', 'label' => 'جنسیت من', 'type' => 'select', 'options' => Friend::getGrnders()],
                ['name' => 'my_age', 'label' => 'سن من', 'type' => 'text'],
                ['name' => 'friend_gender', 'label' => 'جنسیت همسفر', 'type' => 'select', 'options' => Friend::getGrnders()],
                ['name' => 'machine_type', 'label' => 'نوع مسیر', 'type' => 'select', 'options' => Friend::getMachineType()],
                ['name' => 'start_date', 'label' => 'تاریخ شروع', 'type' => 'date'],
                ['name' => 'travel_version', 'label' => 'نسخه سفر', 'type' => 'number'],
                ['name' => 'image', 'label' => 'تصویر', 'type' => 'file', 'directory' => 'friends', 'accept' => 'image/*'],
                ['name' => 'vip', 'label' => 'ویژه', 'type' => 'checkbox'],
                ['name' => 'status', 'label' => 'فعال/تایید شده', 'type' => 'checkbox'],
            ];
            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'user_id' => ['required', 'exists:users,id'],
                'country_id' => ['required', 'exists:countries,id'],
                'province_id' => ['required', 'exists:provinces,id'],
                'travel_type' => ['nullable', 'integer'],
                'travel_duration' => ['nullable', 'string', 'max:255'],
                'my_gender' => ['nullable', 'integer'],
                'my_age' => ['nullable', 'string', 'max:80'],
                'friend_gender' => ['nullable', 'integer'],
                'machine_type' => ['nullable', 'integer'],
                'start_date' => ['nullable', 'date'],
                'travel_version' => ['nullable', 'integer'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'vip' => ['nullable', 'boolean'],
                'status' => ['nullable', 'boolean'],
            ];
        }

        return [
            'title' => $title,
            'singular' => $singular,
            'icon' => $icon,
            'model' => $modelClass,
            'permission' => $modelClass === Friend::class ? 'travel-partners-manage' : match ($modelClass) {
                Residence::class => 'residences-manage',
                Tour::class => 'tours-manage',
                FoodStore::class => 'restaurants-manage',
                default => config('access-control.content_manage_permission'),
            },
            'with' => $modelClass === Friend::class ? ['owner', 'country', 'province'] : ['owner', 'province', 'city'],
            'scope' => $forcedStatus === null ? null : fn (Builder $query) => $query->where('status', $forcedStatus),
            'search' => ['title', 'address', 'id'],
            'status_column' => 'status',
            'filters' => $modelClass === Residence::class ? [
                ['name' => 'province_id', 'label' => 'استان', 'type' => 'select', 'options' => fn () => self::modelOptions(Province::class, 'name')],
                ['name' => 'city_id', 'label' => 'شهر', 'type' => 'select', 'options' => fn () => self::modelOptions(City::class, 'name')],
                ['name' => 'residence_type', 'label' => 'نوع اقامتگاه', 'type' => 'select', 'options' => ['' => 'همه نوع‌ها'] + Residence::getResidenceType()],
            ] : [],
            'quick_actions' => $forcedStatus === 0 ? [
                ['field' => 'status', 'status' => 1, 'label' => 'تأیید', 'class' => 'success', 'confirm' => 'این مورد تأیید و فعال شود؟'],
                ['field' => 'status', 'status' => 2, 'label' => 'رد', 'class' => 'danger', 'confirm' => 'این مورد رد شود؟'],
            ] : [],
            'columns' => [
                ['key' => 'image', 'label' => 'تصویر', 'type' => 'image', 'directory' => self::listingImageDirectory($modelClass)],
                ['key' => 'title', 'label' => 'عنوان'],
                ['key' => 'owner.full_name', 'label' => $modelClass === Residence::class ? 'میزبان' : 'مالک', 'display' => fn ($item) => data_get($item, 'owner.full_name') ?: '-'],
                ['key' => $modelClass === Friend::class ? 'province.name' : 'city.name', 'label' => $modelClass === Friend::class ? 'استان' : 'شهر'],
                ['key' => 'province.name', 'label' => 'استان'],
                ['key' => 'amount', 'label' => $modelClass === Residence::class ? 'قیمت هر شب' : 'قیمت', 'type' => $modelClass === Friend::class ? 'number' : 'money'],
                ['key' => 'residence_type', 'label' => 'نوع اقامتگاه', 'display' => fn ($item) => $modelClass === Residence::class ? (Residence::getResidenceType($item->residence_type) ?? '-') : ($item->vip ? 'ویژه' : 'عادی')],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => $fields,
            'rules' => $rules,
        ];
    }

    protected static function locationConfig(string $key, string $title, string $singular, string $modelClass, string $icon): array
    {
        $fields = [
            ['name' => 'name', 'label' => 'نام', 'type' => 'text', 'required' => true],
        ];

        $rules = ['name' => ['required', 'string', 'max:255']];
        $columns = [['key' => 'name', 'label' => 'نام']];
        $with = [];

        if ($modelClass === Province::class) {
            $fields[] = ['name' => 'country_id', 'label' => 'کشور', 'type' => 'select', 'options' => fn () => self::modelOptions(Country::class, 'name'), 'required' => true];
            $fields[] = ['name' => 'banner_image', 'label' => 'بنر استان', 'type' => 'file', 'directory' => 'locations', 'accept' => 'image/*'];
            $fields[] = ['name' => 'is_use', 'label' => 'فعال', 'type' => 'checkbox'];
            $fields[] = ['name' => 'sort_order', 'label' => 'ترتیب نمایش', 'type' => 'number'];
            $rules += ['country_id' => ['required', 'exists:countries,id'], 'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 'is_use' => ['nullable', 'boolean'], 'sort_order' => ['nullable', 'integer', 'min:0']];
            $columns[] = ['key' => 'country.name', 'label' => 'کشور'];
            $columns[] = ['key' => 'cities_count', 'label' => 'تعداد شهرها', 'type' => 'number', 'count' => 'cities'];
            $columns[] = ['key' => 'residences_count', 'label' => 'تعداد اقامتگاه‌ها', 'type' => 'number', 'count' => 'residences'];
            $columns[] = ['key' => 'banner_image', 'label' => 'بنر استان', 'type' => 'image'];
            $columns[] = ['key' => 'is_use', 'label' => 'وضعیت', 'type' => 'status'];
            $with = ['country'];
        }

        if ($modelClass === City::class) {
            $fields[] = ['name' => 'province_id', 'label' => 'استان', 'type' => 'select', 'options' => fn () => self::modelOptions(Province::class, 'name'), 'required' => true];
            $fields[] = ['name' => 'banner_image', 'label' => 'بنر شهر', 'type' => 'file', 'directory' => 'locations', 'accept' => 'image/*'];
            $fields[] = ['name' => 'is_use', 'label' => 'فعال', 'type' => 'checkbox'];
            $fields[] = ['name' => 'sort_order', 'label' => 'ترتیب نمایش', 'type' => 'number'];
            $rules += ['province_id' => ['required', 'exists:provinces,id'], 'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 'is_use' => ['nullable', 'boolean'], 'sort_order' => ['nullable', 'integer', 'min:0']];
            $columns[] = ['key' => 'province.name', 'label' => 'استان'];
            $columns[] = ['key' => 'residences_count', 'label' => 'تعداد اقامتگاه‌ها', 'type' => 'number', 'count' => 'residences'];
            $columns[] = ['key' => 'banner_image', 'label' => 'بنر شهر', 'type' => 'image'];
            $columns[] = ['key' => 'is_use', 'label' => 'وضعیت', 'type' => 'status'];
            $with = ['province'];
        }

        return compact('title', 'singular', 'icon', 'modelClass') + [
            'model' => $modelClass,
            'permission' => config('access-control.content_manage_permission'),
            'with' => $with,
            'search' => ['name'],
            'status_column' => $modelClass === Country::class ? null : 'is_use',
            'columns' => $columns,
            'fields' => $fields,
            'rules' => $rules,
        ];
    }

    protected static function optionCategoryConfig(string $key, string $title, string $type): array
    {
        return [
            'title' => $title,
            'singular' => 'دسته امکانات',
            'icon' => $type === 'foodstore' ? 'fa-cutlery' : ($type === 'friend' ? 'fa-users' : 'fa-tags'),
            'model' => OptionCategory::class,
            'permission' => config('access-control.content_manage_permission'),
            'scope' => fn (Builder $query) => $query->where('type', $type),
            'search' => ['title'],
            'columns' => [
                ['key' => 'title', 'label' => 'عنوان'],
                ['key' => 'type', 'label' => 'نوع'],
                ['key' => 'options_count', 'label' => 'گزینه‌ها', 'type' => 'number', 'count' => 'options'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                ['name' => 'type', 'label' => 'نوع', 'type' => 'select', 'options' => config('entity-types.option_types', []), 'default' => $type, 'required' => true],
            ],
            'rules' => [
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:80'],
            ],
        ];
    }

    protected static function popularCityConfig(): array
    {
        return [
            'title' => 'شهرهای محبوب',
            'singular' => 'شهر محبوب',
            'icon' => 'fa-star',
            'model' => PopularCity::class,
            'permission' => config('access-control.content_manage_permission'),
            'with' => ['city.province'],
            'search' => ['id'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'city.name', 'label' => 'شهر'],
                ['key' => 'city.province.name', 'label' => 'استان'],
                ['key' => 'image_path', 'label' => 'تصویر', 'type' => 'image'],
                ['key' => 'sort_order', 'label' => 'ترتیب نمایش', 'type' => 'number'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'city_id', 'label' => 'انتخاب شهر', 'type' => 'select', 'options' => fn () => self::modelOptions(City::class, 'name'), 'required' => true],
                ['name' => 'image_path', 'label' => 'تصویر اختصاصی', 'type' => 'file', 'directory' => 'locations', 'accept' => 'image/*'],
                ['name' => 'sort_order', 'label' => 'ترتیب نمایش', 'type' => 'number'],
                ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
            ],
            'rules' => [
                'city_id' => ['required', 'exists:cities,id'],
                'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'status' => ['nullable', 'boolean'],
            ],
        ];
    }

    protected static function footerLinkConfig(): array
    {
        return [
            'title' => 'لینک‌های فوتر',
            'singular' => 'لینک فوتر',
            'icon' => 'fa-link',
            'model' => FooterLink::class,
            'permission' => 'settings-manage',
            'search' => ['title', 'url'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'title', 'label' => 'عنوان لینک'],
                ['key' => 'url', 'label' => 'آدرس / URL'],
                ['key' => 'sort_order', 'label' => 'ترتیب نمایش', 'type' => 'number'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'عنوان لینک', 'type' => 'text', 'required' => true],
                ['name' => 'url', 'label' => 'آدرس / Slug / URL', 'type' => 'text', 'required' => true],
                ['name' => 'sort_order', 'label' => 'ترتیب نمایش', 'type' => 'number'],
                ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
            ],
            'rules' => [
                'title' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:500'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'status' => ['nullable', 'boolean'],
            ],
        ];
    }

    protected static function bookingRequestConfig(): array
    {
        return [
            'title' => 'درخواست‌های رزرو',
            'singular' => 'درخواست رزرو',
            'icon' => 'fa-phone',
            'model' => BookingRequest::class,
            'permission' => 'bookings-manage',
            'with' => ['customer', 'host', 'assignee', 'bookable'],
            'search' => ['guest_name', 'guest_phone', 'id'],
            'status_column' => 'status',
            'quick_actions' => [
                ['field' => 'status', 'status' => 'approved', 'label' => 'ثبت تأیید میزبان', 'class' => 'success', 'confirm' => 'درخواست توسط میزبان تأیید شود و رزرو منتظر پرداخت ساخته شود؟'],
                ['field' => 'status', 'status' => 'rejected', 'label' => 'رد درخواست', 'class' => 'danger', 'confirm' => 'این درخواست رد شود؟'],
                ['field' => 'status', 'status' => 'cancelled', 'label' => 'لغو درخواست', 'class' => 'warning', 'confirm' => 'این درخواست لغو شود؟'],
                ['field' => 'payment_status', 'status' => 'paid', 'label' => 'شبیه‌سازی پرداخت', 'class' => 'info', 'confirm' => 'پرداخت این درخواست به صورت دستی پرداخت‌شده ثبت شود؟'],
                ['field' => 'settlement_status', 'status' => 'releasable', 'label' => 'آزادسازی مبلغ', 'class' => 'success', 'confirm' => 'مبلغ رزرو برای میزبان قابل برداشت شود؟'],
                ['field' => 'settlement_status', 'status' => 'settled', 'label' => 'تسویه با میزبان', 'class' => 'dark', 'confirm' => 'این رزرو با میزبان تسویه شده ثبت شود؟'],
            ],
            'columns' => [
                ['key' => 'request_number', 'label' => 'کد رزرو / شماره درخواست'],
                ['key' => 'guest_name', 'label' => 'مهمان'],
                ['key' => 'guest_phone', 'label' => 'موبایل'],
                ['key' => 'bookable', 'label' => 'اقامتگاه'],
                ['key' => 'host.full_name', 'label' => 'میزبان'],
                ['key' => 'starts_at', 'label' => 'تاریخ ورود', 'type' => 'date'],
                ['key' => 'ends_at', 'label' => 'تاریخ خروج', 'type' => 'date'],
                ['key' => 'nights_count', 'label' => 'تعداد شب', 'display' => fn (BookingRequest $request) => $request->starts_at && $request->ends_at ? max(1, $request->starts_at->diffInDays($request->ends_at)).' شب' : '-'],
                ['key' => 'total_amount', 'label' => 'مبلغ کل', 'type' => 'money'],
                ['key' => 'commission_amount', 'label' => 'مبلغ کمیسیون', 'type' => 'money'],
                ['key' => 'host_share_amount', 'label' => 'سهم میزبان', 'type' => 'money'],
                ['key' => 'host_approval_status', 'label' => 'وضعیت تأیید میزبان', 'type' => 'status'],
                ['key' => 'payment_status', 'label' => 'وضعیت پرداخت', 'type' => 'status'],
                ['key' => 'stay_status', 'label' => 'وضعیت اقامت', 'type' => 'status'],
                ['key' => 'settlement_status', 'label' => 'وضعیت تسویه', 'type' => 'status'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'request_number', 'label' => 'کد رزرو / شماره درخواست', 'type' => 'text'],
                ['name' => 'customer_id', 'label' => 'مشتری', 'type' => 'select', 'options' => fn () => self::userOptions()],
                ['name' => 'host_id', 'label' => 'میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role'))],
                ['name' => 'assigned_to', 'label' => 'کارمند مسئول', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.employee_role'))],
                ['name' => 'bookable_type', 'label' => 'نوع سرویس', 'type' => 'select', 'options' => config('entity-types.bookable_types', [])],
                ['name' => 'bookable_id', 'label' => 'شناسه سرویس', 'type' => 'number'],
                ['name' => 'guest_name', 'label' => 'نام مهمان', 'type' => 'text', 'required' => true],
                ['name' => 'guest_phone', 'label' => 'موبایل مهمان', 'type' => 'text', 'required' => true],
                ['name' => 'starts_at', 'label' => 'شروع', 'type' => 'datetime-local'],
                ['name' => 'ends_at', 'label' => 'پایان', 'type' => 'datetime-local', 'min_source' => 'starts_at'],
                ['name' => 'guests_count', 'label' => 'تعداد نفرات', 'type' => 'number'],
                ['name' => 'total_amount', 'label' => 'مبلغ', 'type' => 'money'],
                ['name' => 'commission_amount', 'label' => 'مبلغ کمیسیون', 'type' => 'money'],
                ['name' => 'host_share_amount', 'label' => 'سهم میزبان', 'type' => 'money'],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => BookingRequest::statuses()],
                ['name' => 'host_approval_status', 'label' => 'وضعیت تأیید میزبان', 'type' => 'select', 'options' => BookingRequest::hostApprovalStatuses()],
                ['name' => 'payment_status', 'label' => 'وضعیت پرداخت', 'type' => 'select', 'options' => BookingRequest::paymentStatuses()],
                ['name' => 'stay_status', 'label' => 'وضعیت اقامت', 'type' => 'select', 'options' => BookingRequest::stayStatuses()],
                ['name' => 'settlement_status', 'label' => 'وضعیت تسویه', 'type' => 'select', 'options' => BookingRequest::settlementStatuses()],
                ['name' => 'notes', 'label' => 'یادداشت', 'type' => 'textarea', 'span' => 2],
                ['name' => 'rejected_reason', 'label' => 'دلیل رد/لغو', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => self::bookingRequestRules() + [
                'request_number' => ['nullable', 'string', 'max:80', Rule::unique('booking_requests', 'request_number')],
                'commission_amount' => ['nullable', 'integer', 'min:0'],
                'host_share_amount' => ['nullable', 'integer', 'min:0'],
                'status' => ['required', 'string', 'max:80'],
                'host_approval_status' => ['nullable', 'string', 'max:80'],
                'payment_status' => ['nullable', 'string', 'max:80'],
                'stay_status' => ['nullable', 'string', 'max:80'],
                'settlement_status' => ['nullable', 'string', 'max:80'],
                'notes' => ['nullable', 'string'],
                'rejected_reason' => ['nullable', 'string'],
            ],
        ];
    }

    protected static function bookingConfig(): array
    {
        return [
            'title' => 'رزروها',
            'singular' => 'رزرو',
            'icon' => 'fa-calendar-check-o',
            'model' => Booking::class,
            'permission' => 'bookings-manage',
            'with' => ['customer', 'host', 'bookable'],
            'search' => ['booking_number', 'id'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'booking_number', 'label' => 'شماره رزرو'],
                ['key' => 'customer.full_name', 'label' => 'کاربر / مهمان'],
                ['key' => 'bookable', 'label' => 'اقامتگاه'],
                ['key' => 'host.full_name', 'label' => 'میزبان'],
                ['key' => 'starts_at', 'label' => 'تاریخ ورود', 'type' => 'date'],
                ['key' => 'ends_at', 'label' => 'تاریخ خروج', 'type' => 'date'],
                ['key' => 'total_amount', 'label' => 'مبلغ کل', 'type' => 'money'],
                ['key' => 'commission_amount', 'label' => 'کمیسیون', 'type' => 'money'],
                ['key' => 'host_share_amount', 'label' => 'سهم میزبان', 'type' => 'money'],
                ['key' => 'payment_status', 'label' => 'پرداخت', 'type' => 'status'],
                ['key' => 'settlement_status', 'label' => 'تسویه', 'type' => 'status'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'booking_request_id', 'label' => 'درخواست رزرو', 'type' => 'select', 'options' => fn () => self::modelOptions(BookingRequest::class, 'guest_name')],
                ['name' => 'customer_id', 'label' => 'مشتری', 'type' => 'select', 'options' => fn () => self::userOptions()],
                ['name' => 'host_id', 'label' => 'میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role'))],
                ['name' => 'bookable_type', 'label' => 'نوع سرویس', 'type' => 'select', 'options' => config('entity-types.bookable_types', [])],
                ['name' => 'bookable_id', 'label' => 'شناسه سرویس', 'type' => 'number'],
                ['name' => 'booking_number', 'label' => 'شماره رزرو', 'type' => 'text'],
                ['name' => 'starts_at', 'label' => 'شروع', 'type' => 'datetime-local'],
                ['name' => 'ends_at', 'label' => 'پایان', 'type' => 'datetime-local', 'min_source' => 'starts_at'],
                ['name' => 'guests_count', 'label' => 'تعداد نفرات', 'type' => 'number'],
                ['name' => 'subtotal', 'label' => 'جمع جزء', 'type' => 'money'],
                ['name' => 'discount_amount', 'label' => 'تخفیف', 'type' => 'money'],
                ['name' => 'commission_amount', 'label' => 'کمیسیون', 'type' => 'money'],
                ['name' => 'host_share_amount', 'label' => 'سهم میزبان', 'type' => 'money'],
                ['name' => 'total_amount', 'label' => 'مبلغ نهایی', 'type' => 'money'],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => Booking::statuses()],
                ['name' => 'payment_status', 'label' => 'وضعیت پرداخت', 'type' => 'select', 'options' => ['unpaid' => 'پرداخت نشده', 'paid' => 'پرداخت شده']],
                ['name' => 'settlement_status', 'label' => 'وضعیت تسویه', 'type' => 'select', 'options' => BookingRequest::settlementStatuses()],
                ['name' => 'paid_at', 'label' => 'زمان پرداخت', 'type' => 'datetime-local'],
                ['name' => 'released_at', 'label' => 'زمان آزادسازی', 'type' => 'datetime-local'],
                ['name' => 'settled_at', 'label' => 'زمان تسویه', 'type' => 'datetime-local'],
                ['name' => 'notes', 'label' => 'یادداشت', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'booking_request_id' => ['nullable', 'exists:booking_requests,id'],
                'customer_id' => ['nullable', 'exists:users,id'],
                'host_id' => ['nullable', 'exists:users,id'],
                'bookable_type' => ['nullable', 'string', 'max:255'],
                'bookable_id' => ['nullable', 'integer'],
                'booking_number' => ['nullable', 'string', 'max:80', Rule::unique('bookings', 'booking_number')],
                'starts_at' => ['nullable', 'date'],
                'ends_at' => ['nullable', 'date', 'after:starts_at'],
                'guests_count' => ['nullable', 'integer', 'min:1'],
                'subtotal' => ['nullable', 'integer', 'min:0'],
                'discount_amount' => ['nullable', 'integer', 'min:0'],
                'commission_amount' => ['nullable', 'integer', 'min:0'],
                'host_share_amount' => ['nullable', 'integer', 'min:0'],
                'total_amount' => ['nullable', 'integer', 'min:0'],
                'status' => ['required', 'string', 'max:80'],
                'payment_status' => ['required', 'string', 'max:80'],
                'settlement_status' => ['nullable', 'string', 'max:80'],
                'paid_at' => ['nullable', 'date'],
                'released_at' => ['nullable', 'date'],
                'settled_at' => ['nullable', 'date'],
                'notes' => ['nullable', 'string'],
            ],
        ];
    }

    protected static function walletConfig(string $title = 'کیف پول میزبان‌ها'): array
    {
        return [
            'title' => $title,
            'singular' => 'تراکنش کیف پول',
            'icon' => 'fa-money',
            'model' => HostWalletTransaction::class,
            'permission' => 'finance-manage',
            'with' => ['host', 'booking'],
            'search' => ['reference_number', 'description'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'host.full_name', 'label' => 'میزبان'],
                ['key' => 'type', 'label' => 'نوع', 'type' => 'status'],
                ['key' => 'amount', 'label' => 'مبلغ', 'type' => 'money'],
                ['key' => 'balance_after', 'label' => 'مانده', 'type' => 'money'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ['key' => 'available_at', 'label' => 'زمان آزادسازی', 'type' => 'datetime'],
            ],
            'fields' => [
                ['name' => 'host_id', 'label' => 'میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role')), 'required' => true],
                ['name' => 'booking_id', 'label' => 'رزرو', 'type' => 'select', 'options' => fn () => self::modelOptions(Booking::class, 'booking_number')],
                ['name' => 'type', 'label' => 'نوع', 'type' => 'select', 'options' => ['credit' => 'بستانکار', 'debit' => 'بدهکار']],
                ['name' => 'amount', 'label' => 'مبلغ', 'type' => 'money', 'required' => true],
                ['name' => 'balance_after', 'label' => 'مانده بعد از تراکنش', 'type' => 'money'],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['blocked' => 'مسدود شده', 'pending' => 'در انتظار', 'posted' => 'قابل برداشت', 'settled' => 'تسویه شده', 'cancelled' => 'لغو شده']],
                ['name' => 'reference_number', 'label' => 'شماره پیگیری', 'type' => 'text'],
                ['name' => 'available_at', 'label' => 'زمان آزادسازی', 'type' => 'datetime-local'],
                ['name' => 'description', 'label' => 'شرح', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'host_id' => ['required', 'exists:users,id'],
                'booking_id' => ['nullable', 'exists:bookings,id'],
                'type' => ['required', 'string', 'max:80'],
                'amount' => ['required', 'integer'],
                'balance_after' => ['nullable', 'integer'],
                'status' => ['required', 'string', 'max:80'],
                'reference_number' => ['nullable', 'string', 'max:255'],
                'available_at' => ['nullable', 'date'],
                'description' => ['nullable', 'string'],
            ],
        ];
    }

    protected static function withdrawConfig(): array
    {
        return [
            'title' => 'درخواست‌های برداشت',
            'singular' => 'درخواست برداشت',
            'icon' => 'fa-money',
            'model' => WithdrawRequest::class,
            'permission' => 'finance-manage',
            'with' => ['host', 'reviewer'],
            'search' => ['iban', 'card_number'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'host.full_name', 'label' => 'میزبان'],
                ['key' => 'amount', 'label' => 'مبلغ درخواست‌شده', 'type' => 'money'],
                ['key' => 'available_balance_snapshot', 'label' => 'موجودی قابل برداشت', 'type' => 'money'],
                ['key' => 'card_number', 'label' => 'شماره کارت'],
                ['key' => 'iban', 'label' => 'شبا'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ['key' => 'created_at', 'label' => 'تاریخ', 'type' => 'date'],
            ],
            'fields' => [
                ['name' => 'host_id', 'label' => 'میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role')), 'required' => true],
                ['name' => 'reviewed_by', 'label' => 'بررسی‌کننده', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.employee_role'))],
                ['name' => 'amount', 'label' => 'مبلغ', 'type' => 'money', 'required' => true],
                ['name' => 'available_balance_snapshot', 'label' => 'موجودی قابل برداشت میزبان', 'type' => 'money'],
                ['name' => 'iban', 'label' => 'شبا', 'type' => 'text'],
                ['name' => 'card_number', 'label' => 'شماره کارت', 'type' => 'text'],
                ['name' => 'account_owner', 'label' => 'نام صاحب حساب', 'type' => 'text'],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['pending' => 'در انتظار بررسی', 'approved' => 'تایید شده', 'rejected' => 'رد شده', 'paid' => 'واریز شده']],
                ['name' => 'reviewed_at', 'label' => 'زمان بررسی', 'type' => 'datetime-local'],
                ['name' => 'paid_at', 'label' => 'تاریخ واریز', 'type' => 'datetime-local'],
                ['name' => 'receipt_path', 'label' => 'رسید واریز', 'type' => 'file', 'directory' => 'settlements', 'accept' => 'image/*,application/pdf'],
                ['name' => 'notes', 'label' => 'یادداشت', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'host_id' => ['required', 'exists:users,id'],
                'reviewed_by' => ['nullable', 'exists:users,id'],
                'amount' => ['required', 'integer', 'min:1'],
                'available_balance_snapshot' => ['nullable', 'integer', 'min:0'],
                'iban' => ['nullable', 'string', 'max:80'],
                'card_number' => ['nullable', 'string', 'max:80'],
                'account_owner' => ['nullable', 'string', 'max:255'],
                'status' => ['required', 'string', 'max:80'],
                'reviewed_at' => ['nullable', 'date'],
                'paid_at' => ['nullable', 'date'],
                'receipt_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
                'notes' => ['nullable', 'string'],
            ],
        ];
    }

    protected static function settlementConfig(): array
    {
        return [
            'title' => 'تسویه حساب‌ها',
            'singular' => 'تسویه حساب',
            'icon' => 'fa-check-square-o',
            'model' => Settlement::class,
            'permission' => 'finance-manage',
            'with' => ['host', 'withdrawRequest'],
            'search' => ['iban', 'card_number', 'account_owner'],
            'status_column' => 'status',
            'filters' => [
                ['name' => 'host_id', 'label' => 'انتخاب میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role'))],
                ['name' => 'from_date', 'label' => 'از تاریخ', 'type' => 'date', 'field' => 'requested_at', 'operator' => '>='],
                ['name' => 'to_date', 'label' => 'تا تاریخ', 'type' => 'date', 'field' => 'requested_at', 'operator' => '<='],
            ],
            'quick_actions' => [
                ['field' => 'status', 'status' => 'paid', 'label' => 'تأیید واریز', 'class' => 'success', 'confirm' => 'واریز این تسویه تأیید شود؟'],
                ['field' => 'status', 'status' => 'rejected', 'label' => 'رد', 'class' => 'danger', 'confirm' => 'این تسویه رد شود؟'],
            ],
            'columns' => [
                ['key' => 'host.full_name', 'label' => 'میزبان'],
                ['key' => 'amount', 'label' => 'مبلغ تسویه', 'type' => 'money'],
                ['key' => 'card_number', 'label' => 'شماره کارت'],
                ['key' => 'iban', 'label' => 'شماره شبا'],
                ['key' => 'requested_at', 'label' => 'تاریخ درخواست', 'type' => 'datetime'],
                ['key' => 'paid_at', 'label' => 'تاریخ واریز', 'type' => 'datetime'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ['key' => 'receipt_path', 'label' => 'رسید', 'type' => 'image'],
            ],
            'fields' => [
                ['name' => 'host_id', 'label' => 'میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role')), 'required' => true],
                ['name' => 'withdraw_request_id', 'label' => 'درخواست برداشت', 'type' => 'select', 'options' => fn () => self::modelOptions(WithdrawRequest::class, 'id')],
                ['name' => 'amount', 'label' => 'مبلغ تسویه', 'type' => 'money', 'required' => true],
                ['name' => 'card_number', 'label' => 'شماره کارت', 'type' => 'text'],
                ['name' => 'iban', 'label' => 'شماره شبا', 'type' => 'text'],
                ['name' => 'account_owner', 'label' => 'نام صاحب حساب', 'type' => 'text'],
                ['name' => 'requested_at', 'label' => 'تاریخ درخواست', 'type' => 'datetime-local'],
                ['name' => 'paid_at', 'label' => 'تاریخ واریز', 'type' => 'datetime-local'],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['pending' => 'در انتظار بررسی', 'approved' => 'تأیید شده', 'paid' => 'واریز شده', 'rejected' => 'رد شده']],
                ['name' => 'receipt_path', 'label' => 'رسید واریز', 'type' => 'file', 'directory' => 'settlements', 'accept' => 'image/*,application/pdf'],
                ['name' => 'admin_notes', 'label' => 'توضیح ادمین', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'host_id' => ['required', 'exists:users,id'],
                'withdraw_request_id' => ['nullable', 'exists:withdraw_requests,id'],
                'amount' => ['required', 'integer', 'min:1'],
                'card_number' => ['nullable', 'string', 'max:80'],
                'iban' => ['nullable', 'string', 'max:80'],
                'account_owner' => ['nullable', 'string', 'max:255'],
                'requested_at' => ['nullable', 'date'],
                'paid_at' => ['nullable', 'date'],
                'status' => ['required', 'string', 'max:80'],
                'receipt_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
                'admin_notes' => ['nullable', 'string'],
            ],
        ];
    }

    protected static function commissionConfig(): array
    {
        return [
            'title' => 'کمیسیون‌ها',
            'singular' => 'کمیسیون',
            'icon' => 'fa-percent',
            'model' => Commission::class,
            'permission' => 'finance-manage',
            'with' => ['host', 'booking'],
            'search' => ['id'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'booking.booking_number', 'label' => 'شماره رزرو'],
                ['key' => 'booking.bookable', 'label' => 'اقامتگاه', 'display' => fn (Commission $commission) => data_get($commission, 'booking.bookable.title') ?: data_get($commission, 'booking.bookable.name') ?: '-'],
                ['key' => 'host.full_name', 'label' => 'میزبان'],
                ['key' => 'booking.total_amount', 'label' => 'مبلغ کل رزرو', 'type' => 'money'],
                ['key' => 'rate', 'label' => 'درصد کمیسیون'],
                ['key' => 'amount', 'label' => 'مبلغ کمیسیون', 'type' => 'money'],
                ['key' => 'host_share_amount', 'label' => 'سهم میزبان', 'type' => 'money'],
                ['key' => 'created_at', 'label' => 'تاریخ', 'type' => 'date'],
                ['key' => 'booking.payment_status', 'label' => 'وضعیت پرداخت', 'type' => 'status'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'booking_id', 'label' => 'رزرو', 'type' => 'select', 'options' => fn () => self::modelOptions(Booking::class, 'booking_number')],
                ['name' => 'host_id', 'label' => 'میزبان', 'type' => 'select', 'options' => fn () => self::userOptions(config('access-control.host_role'))],
                ['name' => 'rate', 'label' => 'درصد کمیسیون', 'type' => 'number'],
                ['name' => 'amount', 'label' => 'مبلغ', 'type' => 'money'],
                ['name' => 'host_share_amount', 'label' => 'سهم میزبان', 'type' => 'money'],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['pending' => 'در انتظار', 'settled' => 'تسویه شده', 'cancelled' => 'لغو شده']],
                ['name' => 'settled_at', 'label' => 'زمان تسویه', 'type' => 'datetime-local'],
                ['name' => 'notes', 'label' => 'یادداشت', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'booking_id' => ['nullable', 'exists:bookings,id'],
                'host_id' => ['nullable', 'exists:users,id'],
                'rate' => ['nullable', 'numeric', 'min:0'],
                'amount' => ['nullable', 'integer', 'min:0'],
                'host_share_amount' => ['nullable', 'integer', 'min:0'],
                'status' => ['required', 'string', 'max:80'],
                'settled_at' => ['nullable', 'date'],
                'notes' => ['nullable', 'string'],
            ],
        ];
    }

    protected static function discountConfig(): array
    {
        return [
            'title' => 'کد تخفیف',
            'singular' => 'کد تخفیف',
            'icon' => 'fa-ticket',
            'model' => DiscountCode::class,
            'permission' => 'finance-manage',
            'with' => ['creator'],
            'search' => ['code', 'title'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'code', 'label' => 'کد'],
                ['key' => 'title', 'label' => 'عنوان'],
                ['key' => 'type', 'label' => 'نوع'],
                ['key' => 'value', 'label' => 'مقدار', 'type' => 'number'],
                ['key' => 'used_count', 'label' => 'استفاده', 'type' => 'number'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'کد', 'type' => 'text', 'required' => true],
                ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                ['name' => 'type', 'label' => 'نوع', 'type' => 'select', 'options' => ['percent' => 'درصدی', 'fixed' => 'مبلغ ثابت']],
                ['name' => 'value', 'label' => 'مقدار', 'type' => 'money', 'required' => true],
                ['name' => 'max_amount', 'label' => 'سقف تخفیف', 'type' => 'money'],
                ['name' => 'min_order_amount', 'label' => 'حداقل سفارش', 'type' => 'money', 'default' => 0],
                ['name' => 'usage_limit', 'label' => 'سقف استفاده', 'type' => 'number'],
                ['name' => 'used_count', 'label' => 'تعداد استفاده شده', 'type' => 'number', 'default' => 0],
                ['name' => 'starts_at', 'label' => 'شروع', 'type' => 'datetime-local'],
                ['name' => 'expires_at', 'label' => 'انقضا', 'type' => 'datetime-local'],
                ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
            ],
            'rules' => [
                'code' => ['required', 'string', 'max:80', Rule::unique('discount_codes', 'code')],
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', Rule::in(['percent', 'fixed'])],
                'value' => ['required', 'integer', 'min:1'],
                'max_amount' => ['nullable', 'integer', 'min:0'],
                'min_order_amount' => ['nullable', 'integer', 'min:0'],
                'usage_limit' => ['nullable', 'integer', 'min:0'],
                'used_count' => ['nullable', 'integer', 'min:0'],
                'starts_at' => ['nullable', 'date'],
                'expires_at' => ['nullable', 'date', 'after:starts_at'],
                'status' => ['nullable', 'boolean'],
            ],
        ];
    }

    protected static function bookingRequestRules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:users,id'],
            'host_id' => ['nullable', 'exists:users,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'bookable_type' => ['nullable', 'string', 'max:255'],
            'bookable_id' => ['nullable', 'integer'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['required', 'digits_between:10,11'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'guests_count' => ['nullable', 'integer', 'min:1'],
            'total_amount' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected static function notificationConfig(): array
    {
        return [
            'title' => 'اعلان‌ها',
            'singular' => 'اعلان',
            'icon' => 'fa-bell',
            'model' => AdminNotification::class,
            'permission' => 'notifications-manage',
            'with' => ['creator', 'user'],
            'search' => ['title', 'message'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'title', 'label' => 'عنوان'],
                ['key' => 'type', 'label' => 'نوع'],
                ['key' => 'audience', 'label' => 'مخاطب'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ['key' => 'created_at', 'label' => 'تاریخ', 'type' => 'datetime'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                ['name' => 'message', 'label' => 'متن پیام', 'type' => 'textarea', 'span' => 2, 'required' => true],
                ['name' => 'type', 'label' => 'نوع', 'type' => 'select', 'options' => ['info' => 'اطلاع‌رسانی', 'success' => 'موفق', 'warning' => 'هشدار', 'danger' => 'خطا']],
                ['name' => 'audience', 'label' => 'مخاطب', 'type' => 'select', 'options' => ['all' => 'همه کاربران', 'employees' => 'کارمندان', 'hosts' => 'میزبان‌ها', 'specific_user' => 'کاربر مشخص']],
                ['name' => 'user_id', 'label' => 'کاربر', 'type' => 'select', 'options' => fn () => self::userOptions()],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['draft' => 'پیش‌نویس', 'sent' => 'ارسال شده', 'failed' => 'ناموفق']],
                ['name' => 'send_now', 'label' => 'پس از ذخیره ارسال شود', 'type' => 'checkbox', 'persist' => false],
            ],
            'rules' => [
                'title' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string', 'max:5000'],
                'type' => ['required', 'string', Rule::in(['info', 'success', 'warning', 'danger'])],
                'audience' => ['required', 'string', Rule::in(['all', 'employees', 'hosts', 'specific_user'])],
                'user_id' => ['nullable', 'exists:users,id', 'required_if:audience,specific_user'],
                'status' => ['required', 'string', Rule::in(['draft', 'sent', 'failed'])],
                'send_now' => ['nullable', 'boolean'],
            ],
        ];
    }

    protected static function smsTemplateConfig(): array
    {
        return [
            'title' => 'قالب‌های پیامک',
            'singular' => 'قالب پیامک',
            'icon' => 'fa-commenting',
            'model' => SmsTemplate::class,
            'permission' => 'sms-manage',
            'with' => ['creator'],
            'search' => ['title', 'body'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'title', 'label' => 'عنوان'],
                ['key' => 'type', 'label' => 'نوع'],
                ['key' => 'body', 'label' => 'متن', 'type' => 'truncate'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'عنوان', 'type' => 'text', 'required' => true],
                ['name' => 'type', 'label' => 'نوع', 'type' => 'text', 'required' => true],
                ['name' => 'body', 'label' => 'متن قالب', 'type' => 'textarea', 'span' => 2, 'required' => true],
                ['name' => 'status', 'label' => 'فعال', 'type' => 'checkbox'],
            ],
            'rules' => [
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:80'],
                'body' => ['required', 'string', 'max:1000'],
                'status' => ['nullable', 'boolean'],
            ],
        ];
    }

    protected static function smsLogConfig(): array
    {
        return [
            'title' => 'ارسال و لاگ پیامک',
            'singular' => 'پیامک',
            'icon' => 'fa-commenting-o',
            'model' => SmsLog::class,
            'permission' => 'sms-manage',
            'with' => ['creator', 'template', 'user'],
            'search' => ['phone', 'message'],
            'status_column' => 'status',
            'columns' => [
                ['key' => 'phone', 'label' => 'شماره'],
                ['key' => 'template.title', 'label' => 'قالب'],
                ['key' => 'message', 'label' => 'متن', 'type' => 'truncate'],
                ['key' => 'provider', 'label' => 'ارائه‌دهنده'],
                ['key' => 'status', 'label' => 'وضعیت', 'type' => 'status'],
                ['key' => 'sent_at', 'label' => 'ارسال', 'type' => 'datetime'],
            ],
            'fields' => [
                ['name' => 'template_id', 'label' => 'قالب', 'type' => 'select', 'options' => fn () => self::modelOptions(SmsTemplate::class, 'title')],
                ['name' => 'user_id', 'label' => 'کاربر', 'type' => 'select', 'options' => fn () => self::userOptions()],
                ['name' => 'phone', 'label' => 'شماره موبایل', 'type' => 'text', 'required' => true],
                ['name' => 'message', 'label' => 'متن پیامک', 'type' => 'textarea', 'span' => 2, 'required' => true],
                ['name' => 'status', 'label' => 'وضعیت', 'type' => 'select', 'options' => ['draft' => 'پیش‌نویس', 'sent' => 'ارسال شده', 'failed' => 'ناموفق']],
                ['name' => 'send_now', 'label' => 'ارسال شود', 'type' => 'checkbox', 'persist' => false],
            ],
            'rules' => [
                'template_id' => ['nullable', 'exists:sms_templates,id'],
                'user_id' => ['nullable', 'exists:users,id'],
                'phone' => ['required', 'digits_between:10,11'],
                'message' => ['required', 'string', 'max:1000'],
                'status' => ['required', 'string', Rule::in(['draft', 'sent', 'failed'])],
                'send_now' => ['nullable', 'boolean'],
            ],
        ];
    }

    protected static function activityLogConfig(): array
    {
        return [
            'title' => 'لاگ فعالیت‌ها',
            'singular' => 'لاگ فعالیت',
            'icon' => 'fa-history',
            'model' => ActivityLog::class,
            'permission' => 'activity-logs-view',
            'with' => ['user'],
            'search' => ['action', 'model_type'],
            'columns' => [
                ['key' => 'user.full_name', 'label' => 'کاربر'],
                ['key' => 'action', 'label' => 'عملیات'],
                ['key' => 'model_type', 'label' => 'مدل'],
                ['key' => 'model_id', 'label' => 'شناسه', 'type' => 'number'],
                ['key' => 'created_at', 'label' => 'تاریخ', 'type' => 'datetime'],
            ],
            'fields' => [
                ['name' => 'action', 'label' => 'عملیات', 'type' => 'text'],
                ['name' => 'description', 'label' => 'توضیحات', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'action' => ['sometimes', 'string'],
                'description' => ['sometimes', 'nullable', 'string'],
            ],
            'create' => false,
            'edit' => false,
            'delete' => false,
        ];
    }

    protected static function securityEventConfig(): array
    {
        return [
            'title' => 'رخدادهای امنیتی',
            'singular' => 'رخداد امنیتی',
            'icon' => 'fa-shield',
            'model' => SecurityEvent::class,
            'permission' => 'security-view',
            'with' => ['user'],
            'search' => ['event', 'level'],
            'columns' => [
                ['key' => 'user.full_name', 'label' => 'کاربر'],
                ['key' => 'event', 'label' => 'رخداد'],
                ['key' => 'level', 'label' => 'سطح'],
                ['key' => 'ip_address', 'label' => 'IP'],
                ['key' => 'created_at', 'label' => 'تاریخ', 'type' => 'datetime'],
            ],
            'fields' => [
                ['name' => 'event', 'label' => 'رخداد', 'type' => 'text'],
                ['name' => 'details', 'label' => 'جزئیات', 'type' => 'textarea', 'span' => 2],
            ],
            'rules' => [
                'event' => ['sometimes', 'string'],
                'details' => ['sometimes', 'nullable', 'string'],
            ],
            'create' => false,
            'edit' => false,
            'delete' => false,
        ];
    }

    protected static function pageCategoryOptions(): array
    {
        if (! Schema::hasTable('page_categories')) {
            return ['' => 'بدون دسته‌بندی'];
        }

        return PageCategory::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('بدون دسته‌بندی', '')
            ->all();
    }

    protected static function listingImageDirectory(string $modelClass): string
    {
        return match ($modelClass) {
            Residence::class => 'residences',
            Tour::class => 'tours',
            FoodStore::class => 'food_store',
            Friend::class => 'friends',
            default => 'uploads',
        };
    }

    protected static function makeSlug(string $title): string
    {
        $slug = Str::slug($title);

        if ($slug !== '') {
            return $slug;
        }

        return trim(preg_replace('/\s+/u', '-', $title), '-');
    }

    protected static function formatDate($value, bool $withTime = false): string
    {
        if (! $value) {
            return '-';
        }

        try {
            $format = $withTime ? '%Y/%m/%d H:i' : '%Y/%m/%d';

            return Jalalian::fromDateTime($value)->format($format);
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
