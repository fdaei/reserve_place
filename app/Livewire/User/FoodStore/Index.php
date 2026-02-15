<?php

namespace App\Livewire\User\FoodStore;

use App\Models\City;
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
    public $a = 0;//city id

    #[Url(except: '')]
    public $searchText="";


    public function render()
    {

        $this->provinces=Province::where("is_use",true)->where("country_id",1)->get()->keyBy("id");
        $this->cities=City::where("is_use",true)->get()->keyBy("id");
        $query = \App\Models\FoodStore::query();
        $sortType = (int)$this->a;
        if ($sortType === 1) {
            $query->orderBy("id", "DESC");
        } elseif ($sortType === 2) {
            $query->orderBy("id", "ASC");
        } elseif ($sortType === 3) {
            $query->orderBy("point", "DESC")->orderBy("id", "DESC");
        } else {
            $query->orderBy("id", "DESC");
        }
        if ($this->searchText != "") {
            $query->search($this->searchText);
        }
        if ($this->p != 0) {
            $query->where("province_id", $this->p);
            if ($this->c != 0) {
                $query->where("city_id", $this->c);
            }
        }

        $stores = $query->paginate(getConfigs("paginationItemCount",12));

        return view('livewire.user.food-store.index',["list"=>$stores])
            ->extends("app")
            ->section("content");
    }
    public function mount(){
        view()->share('title', "رستوران ها");
    }


    public function search(){
        $this->resetPage();
    }

    public function filter(){
        $this->resetPage();
    }

    public function updatedC(){
        $this->resetPage();
    }
    public function updatedP(){
        $this->c = 0;
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
