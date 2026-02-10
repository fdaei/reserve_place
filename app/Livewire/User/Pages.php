<?php

namespace App\Livewire\User;

use Livewire\Component;

class Pages extends Component
{
    public $urlTitle;
    public function render(){
        $page=\App\Models\Page::where("url_text",$this->urlTitle)->first();
        if (!$page){
            abort(404);
        }
        $page->visit_count++;
        $page->update();
        view()->share('title', $page->title);
        return view('livewire.user.pages',[
            "page"=>$page
        ])
            ->extends("app")
            ->section("content");
    }
}
