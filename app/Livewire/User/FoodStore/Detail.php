<?php

namespace App\Livewire\User\FoodStore;

use App\Models\CallResidences;
use App\Models\Comment;
use App\Models\FoodStore;
use App\Models\Residence;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Detail extends Component
{
    public $id;

    public $model;

    public $point;

    public function mount(){
        $this->model=FoodStore::find($this->id);
        if (!$this->model){
            abort(404);
        }
        $this->model->view++;
        $this->model->update();
        view()->share('title', $this->model->title);
    }
    public function render(){
        $this->model=FoodStore::find($this->id);
        return view('livewire.user.food-store.detail')
            ->extends("app")
            ->section("content");
    }

    public function submitPoint(){
        $totalSum=$this->point;
        foreach ($this->model->comments as $comment){
            $totalSum=$totalSum+$comment->point;
        }
        $average=$this->point;
        if (sizeof($this->model->comments)>0){
            $average=$totalSum/(sizeof($this->model->comments)+1);
        }
        $this->model->point=$average;
        $this->model->save();
        Comment::create([
            "user_id"=>auth()->user()->id,
            "store_id"=>$this->model->id,
            "point"=>$this->point,
        ]);
        session()->put('comment', "امتیاز شما با موفقیت ثبت شد.");
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
            "type"=>"store"
        ]);
    }
}
