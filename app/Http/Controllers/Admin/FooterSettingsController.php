<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\FooterLink;
use App\Services\Admin\ActivityLogService;
use Illuminate\Http\Request;

class FooterSettingsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        $this->ensureDefaultLinks();

        return view('admin.footer-links.index', [
            'values' => Config::pluck('value', 'title')->all(),
            'links' => FooterLink::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function updateTexts(Request $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        $validated = $request->validate([
            'settings.footer_about_text' => ['nullable', 'string', 'max:3000'],
            'settings.footer_contact_text' => ['nullable', 'string', 'max:3000'],
            'settings.footer_copyright_text' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated['settings'] ?? [] as $key => $value) {
            Config::updateOrCreate(['title' => $key], ['value' => $value]);
        }

        app(ActivityLogService::class)->log('footer_settings_update', FooterLink::class, $request, description: 'بروزرسانی متن‌های فوتر');

        return back()->with('admin_success', 'متن‌های فوتر ذخیره شد.');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        FooterLink::query()->create($this->validatedLink($request));

        return back()->with('admin_success', 'لینک فوتر افزوده شد.');
    }

    public function update(Request $request, FooterLink $footerLink)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        $footerLink->update($this->validatedLink($request));

        return back()->with('admin_success', 'لینک فوتر بروزرسانی شد.');
    }

    public function destroy(FooterLink $footerLink)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        $footerLink->delete();

        return back()->with('admin_success', 'لینک فوتر حذف شد.');
    }

    private function validatedLink(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]) + ['status' => false];
    }

    private function ensureDefaultLinks(): void
    {
        foreach ([
            ['title' => 'شرایط و قوانین', 'url' => 'terms', 'sort_order' => 10],
            ['title' => 'حریم خصوصی', 'url' => 'privacy', 'sort_order' => 20],
            ['title' => 'سوالات متداول', 'url' => 'faq', 'sort_order' => 30],
            ['title' => 'تماس با ما', 'url' => 'contact', 'sort_order' => 40],
        ] as $link) {
            FooterLink::query()->firstOrCreate(['url' => $link['url']], $link + ['status' => true]);
        }
    }
}
