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

    private function findVisibleFriend()
    {
        if (auth()->check() && auth()->user()->canManageContent()) {
            return Friend::find($this->id);
        }

        return Friend::query()
            ->where('id', $this->id)
            ->where(function ($query) {
                $query->where('status', 1);

                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->first();
    }

    public function mount(){
        $this->model = $this->findVisibleFriend();
        if (!$this->model){
            abort(404);
        }
        $this->model->view++;
        $this->model->update();
        view()->share('title', $this->model->title);
    }
    public function render(){
        $this->model = $this->findVisibleFriend();
        if (!$this->model){
            abort(404);
        }
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
