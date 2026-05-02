<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

class Index extends Component
{

    public function render()
    {
        return view('livewire.admin.index')
            ->extends("app")
            ->section("content");
    }
}
