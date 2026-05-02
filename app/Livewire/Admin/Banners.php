<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithFileUploads;

class Banners extends Component
{
    use WithFileUploads;

    public $mainBannerImageTemp;
    public $discountBannerImageTemp;

    public $mainBannerImage = '';
    public $discountBannerImage = '';
    public $bannerPosition = 'home';
    public $bannerLink = '';
    public $bannerStatus = '1';

    public function mount()
    {
        $this->mainBannerImage = $this->getConfigValue('mainBannerImage');
        $this->discountBannerImage = $this->getConfigValue('discountBannerImage');
        $this->bannerPosition = $this->getConfigValue('bannerPosition', 'home');
        $this->bannerLink = $this->getConfigValue('bannerLink');
        $this->bannerStatus = $this->getConfigValue('bannerStatus', '1');
    }

    protected function rules()
    {
        return [
            'mainBannerImageTemp' => 'nullable|image|max:4096',
            'discountBannerImageTemp' => 'nullable|image|max:4096',
            'bannerPosition' => 'required|string|max:50',
            'bannerLink' => 'nullable|string|max:1000',
            'bannerStatus' => 'required|in:0,1',
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->mainBannerImageTemp) {
            $this->mainBannerImage = $this->mainBannerImageTemp->store('banners', 'public');
            $this->mainBannerImageTemp = null;
        }

        if ($this->discountBannerImageTemp) {
            $this->discountBannerImage = $this->discountBannerImageTemp->store('banners', 'public');
            $this->discountBannerImageTemp = null;
        }

        $this->putConfigValue('mainBannerImage', $this->mainBannerImage);
        $this->putConfigValue('discountBannerImage', $this->discountBannerImage);
        $this->putConfigValue('bannerPosition', $this->bannerPosition);
        $this->putConfigValue('bannerLink', $this->bannerLink);
        $this->putConfigValue('bannerStatus', $this->bannerStatus);

        $this->dispatch('edited');
    }

    public function render()
    {
        return view('livewire.admin.banners')
            ->extends('app')
            ->section('content');
    }

    protected function getConfigValue(string $title, string $default = ''): string
    {
        return (string) Config::firstOrCreate(
            ['title' => $title],
            ['value' => $default],
        )->value;
    }

    protected function putConfigValue(string $title, string $value): void
    {
        Config::updateOrCreate(
            ['title' => $title],
            ['value' => $value],
        );
    }
}
