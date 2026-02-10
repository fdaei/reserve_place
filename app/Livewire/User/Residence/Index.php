<?php

namespace App\Livewire\User\Residence;

use App\Models\City;
use App\Models\OptionValue;
use App\Models\Province;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $provinces=[];
    public $cities=[];

    public $p = 0;//province id
    public $c = 0;//city id
    public $n = 0;//people number
    public $r = 0;//room number
    public $a = 0;//amount order
    public $area = 0;//area type
    public $residenceType = [];//residence type
    public $options = [];//residence type
    public $optionModel = [];//residence type
    public $page=1;

    #[Url(except: '')]
    public $searchText="";


    public function render()
    {

        $this->provinces=Province::where("is_use",true)->where("country_id",1)->get()->keyBy("id");
        $this->cities=City::where("is_use",true)->get()->keyBy("id");
        $query = \App\Models\Residence::query();
        if ($this->a != 0) {
            $query->orderBy("amount", $this->a == 1 ? "ASC" : "DESC");
        } else {
            $query->orderBy("id", "DESC");
        }
        if ($this->searchText != "") {
            $query->search($this->searchText);
        }
        if ($this->p != 0) {
            $query->where("province_id", $this->p);
        }
        if ($this->c != 0) {
            $query->where("city_id", $this->c);
        }
        if ($this->area != 0) {
            $query->where("area_type", $this->area);
        }
        if ($this->n != 0) {
            $query->where("people_number", ">=", $this->n);
        }
        if ($this->r != 0) {
            $query->where("room_number", ">=", $this->r);
        }
        if (sizeof($this->residenceType) != 0) {
            $this->page=1;
            foreach ($this->residenceType as $key=>$item) {
                if ($key==0){
                    $query->where("residence_type", $item);
                }else{
                    $query->orWhere("residence_type", $item);
                }
            }
        }
        if (sizeof($this->options) != 0) {
            $ids = OptionValue::whereIn('option_id', $this->options)
                ->distinct()
                ->pluck('residence_id')
                ->filter()
                ->toArray();
            $query->whereIn('id', $ids);
        }

        $residences = $query->orderBy("id", "DESC")->paginate(getConfigs("paginationItemCount",12));

        return view('livewire.user.residence.index',["residences"=>$residences])
            ->extends("app")
            ->section("content");
    }
    public function addPage(){
        $this->page++;
    }
    public function mount(){
        view()->share('title', getConfigs("website-main-title"));
    }


    public function search(){
        $this->resetPage();
    }
    public function updatedC(){
        $this->resetPage();
    }
    public function updatedP(){
        $this->resetPage();
    }
    public function updatedN(){
        $this->resetPage();
    }
    public function updatedR(){
        $this->resetPage();
    }
    public function updatedA(){
        $this->resetPage();
    }
    public function updatedArea(){
        $this->resetPage();
    }
    public function updatedResidenceType(){
        $this->resetPage();
    }
    public function updatedOptions(){
        $this->resetPage();
    }

}
