<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Add extends Component{

    public $id;
    public $title;
    public $urlTitle;
    public $text;
    public $status=1;
    function mount(){
        if ($this->id!=0){
            $model=Page::find($this->id);
            $this->title=$model->title;
            $this->urlTitle=$model->url_text;
            $this->text=$model->text;
            $this->status=$model->status;
        }
    }
    public function render()
    {
        return view('livewire.admin.pages.add')
            ->extends("app")
            ->section("content");
    }
    public function update(){
        if(!auth()->check()){
            return;
        }
        $this->validate([
            'title' => 'min:3|required|string',
            'urlTitle' => 'min:3|required|string',
            'text' => 'min:3|required|string',
        ], [], [
            'title' => 'عنوان',
            'urlTitle' => 'عنوان',
            'text' => 'محتوا',
        ]);
        if ($this->id!=0){
            Page::find($this->id)->update([
                "url_text"=>$this->urlTitle,
                "title"=>$this->title,
                "text"=>$this->text,
                "status"=>$this->status,
            ]);
        }else{
            Page::create([
                "url_text"=>$this->urlTitle,
                "title"=>$this->title,
                "text"=>$this->text,
                "status"=>$this->status,
            ]);

        }
        return Redirect::to("admin/pages");
    }

}
