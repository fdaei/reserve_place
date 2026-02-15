<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Option;
use App\Models\OptionCategory;
use App\Models\Province;
use App\Models\Residence;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Redirect;

class Tools extends Component
{
    use WithPagination,WithFileUploads;
    public $search="";
    public $cId;

    public function render()
    {

        $query = \App\Models\Option::query();
        $query->where("type",$this->type);
        $query->where("option_category_id",$this->cId);
        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);

        return view('livewire.admin.tools', [
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
    public $id,$title,$icon,$showFilter=0;

    public function remove($id){
        $user=Option::find($id);
        $user->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $model=Option::find($id);
            $this->form="edit";
            $this->id=$id;
            $this->title=$model->title;
            $this->showFilter=$model->show_filter;
            $this->icon=null;
        }elseif ($form=="empty"){
            $this->id=null;
            $this->title=null;
            $this->icon=null;
            $this->showFilter=0;
        }
    }
    public function add(){
        $this->validate([
            'icon' => ['required','file', 'mimes:jpg,jpeg,png,webp,gif','max:5048'],
            'title' => 'string|min:3|max:32|required',
        ]);
        $this->form="empty";
        $extenstion = $this->icon->getClientOriginalExtension();
        $filename = time() . "." . $extenstion;
        $this->icon->storeAs("options", $filename, 'public');
        Option::create([
            "title"=>$this->title,
            "icon"=>$filename,
            "type"=>$this->type,
            "show_filter"=>$this->showFilter,
            "option_category_id"=>$this->cId
        ]);
        $this->setForm("empty");
        $this->dispatch("create");
    }
    public function edit(){
        $this->validate([
            'icon' => ['nullable','file', 'mimes:jpg,jpeg,png,webp,gif','max:5048'],
            'title' => 'string|min:3|max:32|required',
        ]);
        if ($this->icon){
            $extenstion = $this->icon->getClientOriginalExtension();
            $filename = time() . "." . $extenstion;
            $this->icon->storeAs("options", $filename, 'public');
            $this->icon=null;
        }else
            $filename=Option::find($this->id)->icon;
        Option::find($this->id)->update([
            "icon"=>$filename,
            "title"=>$this->title,
            "show_filter"=>$this->showFilter,
            "option_category_id"=>$this->cId
        ]);
        $this->setForm("empty");
        $this->dispatch("edited");
    }
    public $type="residence";
    public function mount($type="residence")
    {
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
        $this->type=$type;
    }
}
