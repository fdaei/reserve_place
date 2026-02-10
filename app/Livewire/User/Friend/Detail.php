<?php

namespace App\Livewire\User\Friend;

use App\Models\CallResidences;
use App\Models\Comment;
use App\Models\FoodStore;
use App\Models\Friend;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Detail extends Component
{
    public $id;

    public $model;

    public $point;

    public function mount(){
        $this->model=Friend::find($this->id);
        if (!$this->model){
            abort(404);
        }
        $this->model->view++;
        $this->model->update();
        view()->share('title', $this->model->title);
    }
    public function render(){
        $this->model=Friend::find($this->id);
        return view('livewire.user.friend.detail')
            ->extends("app")
            ->section("content");
    }

    function login(){
        session()->put('detail-id', $this->model->id);
        Redirect::to("login");
    }

    public function callToPhone(){
        $this->model->calls++;
        $this->model->update();
        CallResidences::create([
            "user_id"=>8,
            "model_id"=>$this->id,
            "type"=>"friend"
        ]);
    }
}
