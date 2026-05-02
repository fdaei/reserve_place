<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Config;
use App\Services\Admin\ActivityLogService;
use App\Support\Admin\AdminFileManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class SeasonalBanners extends Component
{
    use WithFileUploads;

    public array $titles = [];

    public array $descriptions = [];

    public array $images = [];

    public array $storedImages = [];

    protected array $seasons = [
        'spring' => 'بهار',
        'summer' => 'تابستان',
        'autumn' => 'پاییز',
        'winter' => 'زمستان',
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('settings-manage'), 403);

        foreach ($this->seasons as $key => $label) {
            $this->titles[$key] = (string) Config::query()->where('title', "season_{$key}_title")->value('value');
            $this->descriptions[$key] = (string) Config::query()->where('title', "season_{$key}_description")->value('value');
            $this->storedImages[$key] = (string) Config::query()->where('title', "season_{$key}_image")->value('value');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.seasonal-banners', [
            'seasons' => $this->seasons,
        ])
            ->extends('app')
            ->section('content');
    }

    public function saveSeason(string $season): void
    {
        if (! array_key_exists($season, $this->seasons)) {
            return;
        }

        $this->validate([
            "titles.{$season}" => ['nullable', 'string', 'max:255'],
            "descriptions.{$season}" => ['nullable', 'string', 'max:1000'],
            "images.{$season}" => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'string' => ':attribute باید متن باشد.',
            'max' => ':attribute بیش از حد مجاز است.',
            'image' => ':attribute باید تصویر باشد.',
            'mimes' => ':attribute باید تصویر jpg، png یا webp باشد.',
        ], [
            "titles.{$season}" => 'عنوان '.$this->seasons[$season],
            "descriptions.{$season}" => 'توضیحات '.$this->seasons[$season],
            "images.{$season}" => 'تصویر '.$this->seasons[$season],
        ]);

        Config::query()->updateOrCreate(
            ['title' => "season_{$season}_title"],
            ['value' => $this->titles[$season] ?? '']
        );
        Config::query()->updateOrCreate(
            ['title' => "season_{$season}_description"],
            ['value' => $this->descriptions[$season] ?? '']
        );

        if (! empty($this->images[$season])) {
            $previousImage = $this->storedImages[$season] ?? null;
            $path = AdminFileManager::store($this->images[$season], 'settings');
            AdminFileManager::delete($previousImage);

            Config::query()->updateOrCreate(
                ['title' => "season_{$season}_image"],
                ['value' => $path]
            );

            $this->storedImages[$season] = $path;
            unset($this->images[$season]);
        }

        app(ActivityLogService::class)->log('settings_update', self::class, request(), description: 'بروزرسانی بنر فصلی '.$this->seasons[$season]);
        $this->dispatch('season-saved');
    }
}
