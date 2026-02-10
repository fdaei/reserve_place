<?php

namespace App\Livewire\Admin;

use App\Models\Province;
use App\Models\Residence;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;

class Provinces extends Component
{
    use WithPagination;
    public $search="";
    public $cId;

    public function render()
    {

        $query = \App\Models\Province::query();

        $query->where('country_id',$this->cId);
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $collection = $list->getCollection()->keyBy('id');
        $villas = Residence::all();

        foreach ($villas as $villa) {
            if (isset($collection[$villa->city_id])) {
                $collection[$villa->city_id]['residence_count'] = ($collection[$villa->city_id]['residence_count'] ?? 0) + 1;
            }
        }

        $list->setCollection($collection);

        return view('livewire.admin.provinces',[
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
            $model=Province::find($id);
            $this->form="edit";
            $this->id=$id;
            $this->name=$model->name;
        }elseif ($form=="empty"){
            $this->id=null;
            $this->name=null;
        }
    }
    public function add(){
        $this->form="empty";
        Province::create([
            "name"=>$this->name,
            "country_id"=>$this->cId,
            "is_use"=>false
        ]);
        $this->setForm("empty");
        $this->dispatch("create");
    }
    public function edit(){
        Province::find($this->id)->update([
            "name"=>$this->name,
        ]);
        $this->setForm("empty");
        $this->dispatch("edited");
    }
    public function mount()
    {
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
    }
}
