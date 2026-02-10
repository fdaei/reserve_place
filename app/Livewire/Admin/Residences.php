<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Province;
use App\Models\Residence;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Residences extends Component
{
    use WithPagination;
    public $search="";

    public $provinces,$cities;
    public function mount(){
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
        $this->provinces=Province::where("country_id",1)->get()->keyBy("id");
        $this->cities=City::all()->keyBy("id");
    }

    public function render()
    {

        $query = \App\Models\Residence::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        return view('livewire.admin.residences',[
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
    public $id,$name;

    public function remove($id){
        $user=Province::find($id);
        $user->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            Auth::logout();
            $user = \App\Models\User::find(Residence::find($id)->user_id);
            Auth::login($user);
            return redirect('/edit-residence/'.$id);
        }elseif ($form=="empty"){
            $this->id=null;
            $this->name=null;
        }
    }
    public function add(){
        $this->form="empty";
        Province::create([
            "name"=>$this->name,
            "is_use"=>false
        ]);
        $this->setForm("empty");
        $this->dispatch("create");
    }
    public function edit(){
        Auth::logout();
        $user = \App\Models\User::find(Residence::find($this->id)->user_id);
        Auth::login($user);
        return redirect('/edit-residence/'.$this->id);
    }
}
