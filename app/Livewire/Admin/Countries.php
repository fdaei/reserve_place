<?php

namespace App\Livewire\Admin;

use App\Models\Country;
use App\Models\Province;
use App\Models\Residence;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class Countries extends Component
{

    use WithPagination;
    public $search="";
    public $cId;

    public function render()
    {

        $query = \App\Models\Country::query();

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

        return view('livewire.admin.countries',[
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
        $user=Country::find($id);
        $user->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $model=Country::find($id);
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
        Country::create([
            "name"=>$this->name,
            "is_use"=>false
        ]);
        $this->setForm("empty");
        $this->form="empty";
        $this->dispatch("create");
    }
    public function edit(){
        Country::find($this->id)->update([
            "name"=>$this->name,
        ]);
        $this->setForm("empty");
        $this->form="empty";
        $this->dispatch("edited");
    }
    public function mount()
    {
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
    }
}



