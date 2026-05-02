<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Add extends Component
{

    public $id = 0;
    public $title = '';
    public $urlTitle = '';
    public $text = '';
    public $status = 1;

    public function mount()
    {
        if ((int) $this->id !== 0) {
            $this->loadPage((int) $this->id);
        }
    }

    public function render()
    {
        return view('livewire.admin.pages.add', [
            'pages' => Page::query()->orderBy('id', 'ASC')->get(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function selectPage(int $pageId): void
    {
        session()->forget('page_saved');

        if ($pageId === 0) {
            $this->startCreating();

            return;
        }

        $this->loadPage($pageId);
    }

    public function startCreating(): void
    {
        session()->forget('page_saved');
        $this->resetValidation();
        $this->id = 0;
        $this->title = '';
        $this->urlTitle = '';
        $this->text = '';
        $this->status = 1;
    }

    public function update()
    {
        if (!auth()->check()) {
            return;
        }

        if ((int) $this->id === 0 && blank($this->urlTitle)) {
            $this->urlTitle = $this->buildUniqueUrlTitle();
        }

        $validated = $this->validate([
            'title' => 'min:3|required|string|max:255',
            'urlTitle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'url_text')->ignore((int) $this->id ?: null),
            ],
            'text' => 'min:3|required|string',
            'status' => 'required|integer|in:0,1',
        ], [], [
            'title' => 'عنوان صفحه',
            'urlTitle' => 'آدرس صفحه',
            'text' => 'محتوا',
            'status' => 'وضعیت',
        ]);

        $attributes = [
            'url_text' => trim($validated['urlTitle']),
            'title' => trim($validated['title']),
            'text' => trim($validated['text']),
            'status' => (int) $validated['status'],
        ];

        if ((int) $this->id !== 0) {
            $page = Page::findOrFail((int) $this->id);
            $page->update($attributes);
            session()->flash('page_saved', 'تغییرات صفحه با موفقیت ذخیره شد.');
        } else {
            $page = Page::create($attributes);
            session()->flash('page_saved', 'صفحه جدید با موفقیت ایجاد شد.');
        }

        $this->loadPage((int) $page->id);
    }

    protected function loadPage(int $pageId): void
    {
        $model = Page::findOrFail($pageId);

        $this->id = (int) $model->id;
        $this->title = $model->title;
        $this->urlTitle = $model->url_text;
        $this->text = $model->text;
        $this->status = $model->status;
        $this->resetValidation();
    }

    protected function buildUniqueUrlTitle(): string
    {
        $baseTitle = trim((string) preg_replace('/\s+/u', '-', $this->title));
        $baseTitle = trim(str_replace(['/', '\\'], '-', $baseTitle), '-');
        $baseTitle = $baseTitle !== '' ? $baseTitle : 'page';

        $urlTitle = $baseTitle;
        $counter = 2;

        while (Page::query()->where('url_text', $urlTitle)->exists()) {
            $urlTitle = $baseTitle . '-' . $counter;
            $counter++;
        }

        return $urlTitle;
    }
}
