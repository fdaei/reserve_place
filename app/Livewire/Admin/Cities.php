<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use App\Models\Residence;
use Livewire\Component;
use Livewire\WithPagination;

class Cities extends Component
{
    use WithPagination;
    public $search="";
    public $pId;
    public $cId;

    public function render()
    {

        $query = \App\Models\City::query();
        $query->where("province_id",$this->pId);
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $collection = $list->getCollection()->keyBy('id');
        $villas = Residence::where("province_id", $this->pId)->get();

        foreach ($villas as $villa) {
            if (isset($collection[$villa->city_id])) {
                $collection[$villa->city_id]['residence_count'] = ($collection[$villa->city_id]['residence_count'] ?? 0) + 1;
            }
        }

        $list->setCollection($collection);

        return view('livewire.admin.cities', [
            'list' => $list
        ])
            ->extends('app')
            ->section('content');
    }
    public function updated($propertyName)
    {
        $this->resetPage();
    }


    protected $listeners = ["remove"];
    public $form="empty";
    public $id,$name;

    public function remove($id){
        $user=City::find($id);
        $user->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $model=City::find($id);
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
        City::create([
            "name"=>$this->name,
            "province_id"=>$this->pId,
        ]);
        $this->setForm("empty");
        $this->dispatch("create");
    }
    public function edit(){
        City::find($this->id)->update([
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
