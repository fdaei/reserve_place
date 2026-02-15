<?php

namespace App\Livewire\User;

use App\Models\FoodStore;
use App\Models\Friend;
use App\Models\OptionValue;
use App\Models\Residence;
use App\Models\Images;
use App\Models\Tour;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Dashboard extends Component{
    protected $listeners = ["removeResidence","removeTour", "removeFriend", "removeFoodstore"];
    public function render(){
        return view('livewire.user.dashboard')
            ->extends("app")
            ->section("content");
    }

    function mount(){
        if (!auth()->check()){
            Redirect::to("login");
        }
        view()->share('title', "داشبورد");

    }
    function logout(){
        auth()->logout();
        Redirect::to("login");
    }
    function removeResidence($id){
        $residence=Residence::find($id);
        foreach ($residence->images as $item) {
            if (Storage::disk('public')->exists('residences/' . $item->url)) {
                Storage::disk('public')->delete('residences/' . $item->url);
            }
        }
        Images::where("residence_id", $id)->delete();
        OptionValue::where("residence_id",$id)->delete();
        $residence->delete();
        session()->put('message', "اقامتگاه با موفقیت حذف شد");
    }
    function removeFriend($id){
        $model=Friend::find($id);
        foreach ($model->images as $item) {
            if (Storage::disk('public')->exists('friends/' . $item->url)) {
                Storage::disk('public')->delete('friends/' . $item->url);
            }
        }
        Images::where("friend_id", $id)->delete();
        OptionValue::where("friend_id",$id)->delete();
        $model->delete();
        session()->put('message', "درخواست همسفر با موفقیت حذف شد");
    }
    function removeFoodstore($id){
        $model=FoodStore::find($id);
        foreach ($model->images as $item) {
            if (Storage::disk('public')->exists('food_store/' . $item->url)) {
                Storage::disk('public')->delete('food_store/' . $item->url);
            }
        }
        Images::where("store_id", $id)->delete();
        OptionValue::where("foodstore_id",$id)->delete();
        $model->delete();
        session()->put('message', "رستوران با موفقیت حذف شد");
    }
    function removeTour($id){
        $model=Tour::find($id);
        foreach ($model->images as $item) {
            if (Storage::disk('public')->exists('tours/' . $item->url)) {
                Storage::disk('public')->delete('tours/' . $item->url);
            }
        }
        Images::where("tour_id", $id)->delete();
        $model->delete();
        session()->put('message', "تور با موفقیت حذف شد");
    }

}
