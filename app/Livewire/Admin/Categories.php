<?php

namespace App\Livewire\Admin;

use App\Models\Option;
use App\Models\OptionCategory;
use Livewire\Component;

class Categories extends Component
{
    public $search = '';

    public $type = 'residence';

    public $form = 'empty';

    public $id;

    public $title;

    public function render()
    {
        $query = OptionCategory::query()->where('type', $this->type);
        if (! empty($this->search)) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        $counts = Option::query()
            ->where('type', $this->type)
            ->selectRaw('option_category_id, COUNT(*) as aggregate')
            ->groupBy('option_category_id')
            ->pluck('aggregate', 'option_category_id');

        $list = $query->orderBy('title')->get()->map(function (OptionCategory $category) use ($counts) {
            $category->option_count = $counts->get($category->id, 0);

            return $category;
        });

        return view('livewire.admin.categories', [
            'list' => $list,
            'typeLabel' => $this->getTypeLabel(),
        ])
            ->extends('app')
            ->section('content');
    }

    protected $listeners = ['remove'];

    public function remove($id)
    {
        OptionCategory::findOrFail($id)->delete();
        $this->dispatch('removed');
    }

    public function setForm($form, $id = null)
    {
        $this->form = $form;

        if ($form === 'edit') {
            $model = OptionCategory::findOrFail($id);
            $this->id = $id;
            $this->title = $model->title;

            return;
        }

        $this->id = null;
        $this->title = '';
    }

    public function add()
    {
        $this->validate([
            'title' => 'required|string|min:2|max:80',
        ]);

        OptionCategory::create([
            'title' => trim($this->title),
            'type' => $this->type,
        ]);

        $this->setForm('empty');
        $this->dispatch('create');
    }

    public function edit()
    {
        $this->validate([
            'title' => 'required|string|min:2|max:80',
        ]);

        OptionCategory::findOrFail($this->id)->update([
            'title' => trim($this->title),
            'type' => $this->type,
        ]);

        $this->setForm('empty');
        $this->dispatch('edited');
    }

    public function mount($type = 'residence')
    {
        $this->type = $type;
    }

    protected function getTypeLabel(): string
    {
        return config('entity-types.option_types.'.$this->type, config('entity-types.option_types.residence'));
    }
}
