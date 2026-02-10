<?php

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Models\FoodStore;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use App\Models\Residence;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use WithPagination;
    public $search="";
    public $villas;
    public $stores;
    public $users;
    public function mount(){
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
        $this->villas=Residence::all()->keyBy("id");
        $this->stores=FoodStore::all()->keyBy("id");
        $this->users=User::all()->keyBy("id");
    }
    public function render(){

        $query = \App\Models\Comment::query();

        if (!empty($this->search)) {
            $query->where('point', $this->search );
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);

        return view('livewire.admin.comments',[
            "list" => $list
        ])
            ->extends("app")
            ->section("content");
    }
    public function updated($propertyName)
    {
        $this->resetPage();
    }


    protected $listeners = ["remove"];
    public $form="empty";
    public $id,$point;

    public function remove($id){
        $user=Comment::find($id);
        $user->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $model=Comment::find($id);
            $this->form="edit";
            $this->id=$id;
            $this->point=$model->point;
        }elseif ($form=="empty"){
            $this->id=null;
            $this->point=null;
        }
    }
    public function edit(){
        Comment::find($this->id)->update([
            "point"=>$this->point,
        ]);
        $this->setForm("empty");
        $this->dispatch("edited");
    }
}
