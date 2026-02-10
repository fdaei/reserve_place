<?php

namespace App\Livewire\Admin;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Redirect;
use Morilog\Jalali\Jalalian;

class Users extends Component
{
    use WithPagination;
    public $name="";
    public $family="";
    public $nationalCode="";
    public $phone="";
    public $createdAt="";

    public function render()
    {

        $query = \App\Models\User::query();

        if (!empty($this->name)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->name . '%')
                    ->orWhere('name', $this->name);
            });
        }

        if (!empty($this->family)) {
            $query->where('family',  $this->family);
        }

        if (!empty($this->nationalCode)) {
            $query->where('national_code', $this->nationalCode);
        }

        if (!empty($this->phone)) {
            $query->where('phone', $this->phone);
        }

        if (!empty($this->createdAt)) {
            $fixedDate = str_replace('/', '-', trim($this->createdAt));
            $jalaliDate = Jalalian::fromFormat('Y-m-d', $fixedDate);
            $gregorianDate = $jalaliDate->toCarbon()->toDateString();
            $query->whereDate('created_at', $gregorianDate);
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);

        return view('livewire.admin.users',[
            "list" => $list
        ])
            ->extends("app")
            ->section("content");
    }
    public function updated($propertyName)
    {
        $this->resetPage();
    }


    function login($id){
        Auth::logout();
        $user = \App\Models\User::find($id);
        Auth::login($user);
        return redirect('profile');
    }
    protected $listeners = ["remove"];

    public function remove($id){
        $user=User::find($id);
        $user->delete();
        $this->dispatch("removed");
    }


    public function mount()
    {
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
    }
}
