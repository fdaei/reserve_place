<?php

namespace App\Livewire\User\FoodStore;

use App\Models\City;
use App\Models\FoodStore;
use App\Models\Images;
use App\Models\OptionValue;
use App\Models\Province;
use App\Models\Residence;
use App\Models\Tour;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Add extends Component
{
    use WithFileUploads;

    public function render()
    {
        if (!auth()->check()) Redirect::to("login");
        return view('livewire.user.food-store.add')
            ->extends("app")
            ->section("content");
    }





    public $storeType=1;
    public $foodType=1;
    public $openTime="08:00";
    public $closeTime="23:00";
    public $latLen;
    public $province = 27;
    public $city = 384;
    public $address;
    public $page = 1;


    public $gallery = [
    ];
    public $options = [];
    public $mainImage = "";

    #[Validate('image|max:5120|mimes:jpg,jpeg')]
    public $image;

    function continue()
    {
        $city = City::find($this->city);
        $this->validate([
            'title' => 'min:10|required|string',
            'province' => 'min:0|required|exists:provinces,id',
            'city' => 'min:0|required|integer|exists:cities,id',
            'address' => 'required|string',
            'storeType'     => 'required|integer|min:0|max:10',
            'foodType'      => 'required|integer|min:0',
            'openTime'      => 'required|date_format:H:i',
            'closeTime'     => 'required|date_format:H:i|after:openTime', // بعد از openTime باشد
        ], [], [
            'title' => 'عنوان',
            'province' => 'استان',
            'city' => 'شهر',
            'address'       => 'آدرس',
            'storeType'      => 'مدل رستوران',
            'foodType'      => 'نوع غذا',
            'openTime'      => 'ساعت باز کردن',
            'closeTime'     => 'ساعت تعطیلی',
        ]);
        if ($city && $city->province_id != $this->province) {
            $this->addError('city', 'شهر انتخاب‌شده با استان هماهنگ نیست.');
            return;
        }
        if (sizeof($this->gallery)==0){
            $this->addError('image', 'حداقل باید یک تصویر را ارسال کنید.');
            return;
        }
        if ($this->latLen == "") {
            $this->addError('latLen', 'موقعیت جغرافیایی اقامتگاه را وارد کنید.');
            return;
        }
        $this->page = 2;

    }


    public function updatedImage()
    {
        if (sizeof($this->gallery)>3){
            $this->addError('image', 'تعداد تصاویر به حداکثر تعداد مجاز رسیده است.');
            return;
        }
        $size=getimagesize($this->image->getRealPath());
        if ($size[0]<500 or $size[1]<500){
            $this->addError('image', 'حداقل عرض و ارتفاع تصویر باید 500پیکسل باشد.');
            return;
        }
        $imageName = "injaa_" . time() . "." . $this->image->extension();
        $url = $this->image->storeAs('/public/food_store', $imageName);
        if (sizeof($this->gallery) == 0) {
            $this->mainImage = $imageName;
        }
        $this->gallery[$imageName] = $imageName;
    }

    public function changeMainImage($image)
    {
        $this->mainImage = $image;
    }


    public function delete($image)
    {
        if (Storage::disk('local')->exists('/public/food_store/' . $image)) {
            Storage::disk('local')->delete('/public/food_store/' . $image);
        }
        unset($this->gallery[$image]);
        $this->gallery=array_values($this->gallery);
        if ($image == $this->mainImage) {
            if (sizeof($this->gallery) != 0) {
                $this->mainImage = $this->gallery[0];
            }
        }
    }

    public $title;
    public $id;

    function save()
    {
        $city = City::find($this->city);
        $this->validate([
            'title' => 'min:10|required|string',
            'province' => 'min:0|required|exists:provinces,id',
            'city' => 'min:0|required|integer|exists:cities,id',
            'address' => 'required|string',
            'storeType'     => 'required|integer|min:0|max:10',
            'foodType'      => 'required|integer|min:0',
            'openTime'      => 'required|date_format:H:i',
            'closeTime'     => 'required|date_format:H:i|after:openTime', // بعد از openTime باشد
        ], [], [
            'title' => 'عنوان',
            'province' => 'استان',
            'city' => 'شهر',
            'address'       => 'آدرس',
            'storeType'      => 'مدل رستوران',
            'foodType'      => 'نوع غذا',
            'openTime'      => 'ساعت باز کردن',
            'closeTime'     => 'ساعت تعطیلی',
        ]);
        if ($city && $city->province_id != $this->province) {
            $this->addError('city', 'شهر انتخاب‌شده با استان هماهنگ نیست.');
            return;
        }
        if (sizeof($this->gallery)==0){
            $this->addError('image', 'حداقل باید یک تصویر را ارسال کنید.');
            return;
        }
        if ($this->latLen == "") {
            $this->addError('latLen', 'موقعیت جغرافیایی اقامتگاه را وارد کنید.');
            return;
        }
        $hasPool = "";
        foreach ($this->options as $option) {
            if ($option == 1) {
                $hasPool = "استخردار";
            }
        }


        $this->latLen = explode(":", $this->latLen);
        $city = City::find($this->city);
        $city->is_use = true;
        $city->save();
        Province::where("id", $this->province)->update([
            "is_use" => true
        ]);
        $data=[
            "title" => $this->title,
            "province_id" => $this->province,
            "city_id" => $this->city,
            "user_id" => auth()->user()->id,
            "address"=>$this->address,
            "store_type"=>$this->storeType,
            "food_type"=>$this->foodType,
            "open_time"=>$this->openTime,
            "close_time"=>$this->closeTime,
            "lat" => $this->latLen[0],
            "lng" => $this->latLen[1],
            "image" => $this->mainImage,
            "status" => true,
        ];
        $residence=null;
        if ($this->id==null){
            $residence = FoodStore::create($data);
            session()->put('message', "اقامتگاه با موفقیت ثبت شد");
        }else{
            $residence=FoodStore::find($this->id);
            $residence->update($data);
            Images::where("store_id", $this->id)->delete();
            OptionValue::where("foodstore_id",$this->id)->delete();
            session()->put('message', "اقامتگاه با موفقیت ویرایش شد");
        }
        foreach ($this->gallery as $item) {
            Images::create([
                "url" => $item,
                "store_id" => $residence->id
            ]);
        }
        foreach ($this->options as $option) {
            OptionValue::create([
                "option_id" => $option,
                "foodstore_id" => $residence->id,
                "value" => true,
            ]);
        }

        Redirect::to("/dashboard");
    }
    function mount($id=null)
    {
        if (!auth()->check()){
            Redirect::to("login");
            return ;
        }
        $this->id=$id;
        $this->latLen="36.907681:50.675039";
        view()->share('title', "افزودن اقامتگاه");
        if ($this->id != null) {
            $store=FoodStore::find($this->id);



            $this->title=$store->title;
            $this->province=$store->province_id;
            $this->city=$store->city_id;
            $this->address=$store->address;
            $this->storeType=$store->store_type;
            $this->foodType=$store->food_type;
            $this->openTime=explode(":",$store->open_time)[0].":".explode(":",$store->open_time)[1];
            $this->closeTime=explode(":",$store->close_time)[0].":".explode(":",$store->close_time)[1];
            $this->latLen=$store->lat.":".$store->lng;
            $this->mainImage=$store->image;

            view()->share('title', $this->title);

            $this->page=1;
            foreach ($store->images as $image){
                $this->gallery[$image->url]=$image->url;
            }
            foreach ($store->optionValues as $value){
                $this->options[]=$value->option_id;
            }
        }
    }

}
