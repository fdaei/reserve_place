<?php

namespace App\Livewire\User\Residence;

use App\Models\CallResidences;
use App\Models\Comment;
use App\Models\Residence;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Detail extends Component
{
    public $id;

    public $residence;

    public $point;

    public $commentBody = '';

    private function findVisibleResidence()
    {
        if (auth()->check() && auth()->user()->canManageContent()) {
            return Residence::find($this->id);
        }

        return Residence::query()
            ->where('id', $this->id)
            ->where(function ($query) {
                $query->where('status', true);

                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->first();
    }

    public function mount(){
        $this->residence = $this->findVisibleResidence();
        if (!$this->residence){
            abort(404);
        }
        $this->residence->view++;
        $this->residence->update();
        view()->share('title', $this->residence->title);
    }
    public function render(){
        $this->residence = $this->findVisibleResidence();
        if (!$this->residence){
            abort(404);
        }
        return view('livewire.user.residence.detail')
            ->extends("app")
            ->section("content");
    }

    public function submitPoint(){
        $this->validate([
            'point' => ['required', 'integer', 'between:1,5'],
            'commentBody' => ['required', 'string', 'max:1000'],
        ], [], [
            'point' => 'امتیاز',
            'commentBody' => 'متن نظر',
        ]);

        $totalSum=$this->point;
        foreach ($this->residence->comments as $comment){
            $totalSum=$totalSum+$comment->point;
        }
        $average=$this->point;
        if (sizeof($this->residence->comments)>0){
            $average=$totalSum/(sizeof($this->residence->comments)+1);
        }
        $this->residence->point=$average;
        $this->residence->save();
        Comment::create([
            "user_id"=>auth()->user()->id,
            "residence_id"=>$this->residence->id,
            "point"=>$this->point,
            "body"=>$this->commentBody,
            "status"=>"approved",
        ]);
        session()->put('comment', "امتیاز شما با موفقیت ثبت شد.");
    }
    function login(){
        session()->put('detail-id', $this->residence->id);
        Redirect::to("login");
    }

    public function callToPhone(){
        $this->residence->calls++;
        $this->residence->update();
        CallResidences::create([
            "user_id"=>8,
            "model_id"=>$this->id
        ]);
    }

}
