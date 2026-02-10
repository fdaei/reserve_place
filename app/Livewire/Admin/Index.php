<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

class Index extends Component
{
    public function mount()
    {
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
    }
    public function render()
    {
        return view('livewire.admin.index')
            ->extends("app")
            ->section("content");
    }
}
