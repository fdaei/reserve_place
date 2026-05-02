<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;

    public $search = '';

    public $category = 'all';

    public $status = 'all';

    public function render()
    {
        $query = Page::query()
            ->with('category')
            ->when($this->search !== '', function ($builder) {
                $search = trim($this->search);

                $builder->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%'.$search.'%')
                        ->orWhere('url_text', 'like', '%'.$search.'%');
                });
            })
            ->when($this->status === 'published', fn ($builder) => $builder->where('status', 1))
            ->when($this->status === 'draft', fn ($builder) => $builder->where('status', 0))
            ->when($this->category !== 'all' && Schema::hasColumn('pages', 'category_id'), function ($builder) {
                $builder->where('category_id', $this->category);
            })
            ->latest('id');

        return view('livewire.admin.blog', [
            'list' => $query->paginate(10),
            'categories' => $this->pageCategories(),
            'authorName' => $this->resolveAuthorName(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName = null)
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'status']);
        $this->resetPage();
    }

    public function remove($id)
    {
        $page = Page::find($id);

        if (! $page) {
            return;
        }

        $page->delete();
        session()->flash('admin_success', 'پست با موفقیت حذف شد.');
        $this->resetPage();
    }

    protected function pageCategories(): array
    {
        if (! Schema::hasTable('page_categories')) {
            return ['all' => 'همه دسته‌بندی‌ها'];
        }

        return PageCategory::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('همه دسته‌بندی‌ها', 'all')
            ->all();
    }

    protected function resolveAuthorName(): string
    {
        $name = trim((auth()->user()->name ?? '').' '.(auth()->user()->family ?? ''));

        return $name !== '' ? $name : 'مدیر محتوا';
    }
}
