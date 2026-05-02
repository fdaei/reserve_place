<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Livewire\WithPagination;

class AllPages extends Component
{
    use WithPagination;
    public $search = '';
    protected $listeners = ['remove'];



    public function render()
    {
        $query = Page::query();

        if (!empty($this->search)) {
            $query->where(function ($builder) {
                $builder->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('url_text', 'like', '%' . $this->search . '%');
            });
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        return view('livewire.admin.pages.all-pages', [
            'list' => $list,
        ])
            ->extends('app')
            ->section('content');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function remove($id)
    {
        $page = Page::find($id);
        if (!$page) {
            return;
        }

        $page->delete();
        $this->dispatch('removed');
    }
}
