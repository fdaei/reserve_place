<?php

namespace App\Livewire\User\Friend;

use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component{
    use WithPagination;
    public $countries=[];
    public $provinces=[];

    public $p = 0;//province id
    public $c = 0;//country id

    #[Url(except: '')]
    public $searchText="";


    public function render()
    {

        $this->countries=Country::all()->keyBy("id");
        $this->provinces=Province::all()->keyBy("id");
        $query = \App\Models\Friend::query();
        if ($this->searchText != "") {
            $query->search($this->searchText);
        }
        if ($this->c != 0) {
            $query->where("country_id", $this->c);
            if ($this->p != 0) {
                $query->where("province_id", $this->p);
            }
        }

        $stores = $query->orderBy("id", "DESC")->paginate(getConfigs("paginationItemCount",12));

        return view('livewire.user.friend.index',["list"=>$stores])
            ->extends("app")
            ->section("content");
    }
    public function mount(){
        view()->share('title', "همسفران");
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
