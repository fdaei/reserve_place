<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Redirect;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    public $user;
    public function render(){

        return view('livewire.user.profile')
            ->extends("app")
            ->section("content");
    }

    public function mount(){
        if (!auth()->check()) {
            Redirect::to("login");
            return;
        }
        $this->user=auth()->user();
        view()->share('title', "ویرایش پروفایل");
        $this->name=$this->user->name;
        $this->family=$this->user->family;
        $this->nationalCode=$this->user->national_code;
        $this->birthDay=$this->user->birth_day;
    }

    #[Validate('image|max:5120|mimes:jpg,jpeg')]
    public $image;
    public $name;
    public $family;
    public $nationalCode;
    public $birthDay;

    function save(){
        $validationArray=[
            'name' => 'min:0|max:190|required|String',
            'family' => 'min:0|max:190|required|String',
            'nationalCode' => 'min:10|max:10|required|String',
            'birthDay' => 'min:7|max:11|required|String',
        ];
        if ($this->image){
            $validationArray["image"]='image|max:5120|mimes:jpg,jpeg|required';
        }
        $this->validate($validationArray, [], [
            'image' => 'تصویر',
            'name' => 'نام',
            'family' => 'نام خانوادگی ',
            'nationalCode' => 'کدملی',
            'birthDay' => 'تاریخ تولد',
        ]);
        $imageName=auth()->user()->profile_image;
        if ($this->image){
            $imageName = "injaa_" . time() . "." . $this->image->extension();
            $url = $this->image->storeAs('/user', $imageName);
        }
        auth()->user()->update([
            "name"=>$this->name,
            "family"=>$this->family,
            "national_code"=>$this->nationalCode,
            "birth_day"=>$this->birthDay,
            "profile_image"=>$imageName,
        ]);
        session()->put('message', "پروفایل با موفقیت ویرایش شد");
        Redirect::to("/dashboard");
    }
}
