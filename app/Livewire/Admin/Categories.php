<?php

namespace App\Livewire\Admin;

use App\Models\Option;
use App\Models\OptionCategory;
use App\Models\Province;
use App\Models\Residence;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{

    use WithPagination;
    public $search="";
    public $type="residence";


    public function render()
    {

        $query = \App\Models\OptionCategory::query();
        $query->where("type",$this->type);

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $collection = $list->getCollection()->keyBy('id');
        $options = Option::where("type",$this->type)->get();

        foreach ($options as $option) {
            if (isset($collection[$option->option_category_id])) {
                $collection[$option->option_category_id]['option_count'] = ($collection[$option->option_category_id]['option_count'] ?? 0) + 1;
            }
        }

        $list->setCollection($collection);

        return view('livewire.admin.categories',[
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
    public $id,$title;

    public function remove($id){
        $user=OptionCategory::find($id);
        $user->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $model=OptionCategory::find($id);
            $this->form="edit";
            $this->id=$id;
            $this->title=$model->title;
        }elseif ($form=="empty"){
            $this->id=null;
            $this->title=null;
        }
    }
    public function add(){
        $this->form="empty";
        OptionCategory::create([
            "title"=>$this->title,
            "type"=>$this->type,
        ]);
        $this->setForm("empty");
        $this->dispatch("create");
    }
    public function edit(){
        OptionCategory::find($this->id)->update([
            "title"=>$this->title,
            "type"=>$this->type,
        ]);
        $this->setForm("empty");
        $this->dispatch("edited");
    }
    public function mount($type="residence")
    {
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
        $this->type=$type;
    }

}
