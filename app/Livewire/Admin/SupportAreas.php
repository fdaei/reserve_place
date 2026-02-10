<?php

namespace App\Livewire\Admin;

use App\Models\Country;
use App\Models\Residence;
use App\Models\SupportAreaTickets;
use Livewire\Component;
use Livewire\WithPagination;

class SupportAreas extends Component
{
    use WithPagination;
    public $search="";
    public $cId;

    public function render()
    {

        $query = \App\Models\SupportAreaTickets::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);



        return view('livewire.admin.support-areas',[
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
    public $id,$name,$status=1;


    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $model=SupportAreaTickets::find($id);
            $this->form="edit";
            $this->id=$id;
            $this->name=$model->title;
            $this->status=$model->status;
        }elseif ($form=="empty"){
            $this->id=null;
            $this->name=null;
        }
    }
    public function add(){
        $this->form="empty";
        SupportAreaTickets::create([
            "title"=>$this->name,
            "status"=>$this->status
        ]);
        $this->setForm("empty");
        $this->form="empty";
        $this->dispatch("create");
    }
    public function edit(){
        SupportAreaTickets::find($this->id)->update([
            "title"=>$this->name,
            "status"=>$this->status
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
