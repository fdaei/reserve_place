<?php

namespace App\Livewire\User\Friend;

use App\Models\City;
use App\Models\Friend;
use App\Models\Images;
use App\Models\OptionValue;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Add extends Component
{



    use WithFileUploads;

    public function render()
    {
        if (!auth()->check()) Redirect::to("login");

        return view('livewire.user.friend.add')
            ->extends("app")
            ->section("content");
    }

    public $country = 1;
    public function updatedCountry(){
        $p=Province::where("country_id",$this->country)->first();
        if ($p)
            $this->province=$p->id;
    }
    public $province = 27;
    public $travelType = 1;
    public $travelDuration = 3;
    public $myGender = 1;
    public $myAge = "25-35";
    public $friendGender = 1;
    public $machineType = 1;
    public $startDate = "";
    public $startDateValidation ;
    public $travelVersion = 1;

    public $page = 1;


    public $options = [];
    public $gallery = [
    ];
    public $mainImage = "";

    #[Validate('image|max:5120|mimes:jpg,jpeg')]
    public $image;

    function continue()
    {
        if ($this->startDate) {
            try {
                $this->startDateValidation = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat(
                    'Y/m/d',
                    $this->startDate
                );
            }catch (\Exception $e){
            }
        }
        $city = Province::find($this->province);
        $this->validate([
            'country' => 'min:0|required|exists:countries,id',
            'province' => 'min:0|required|integer|exists:provinces,id',
            'travelType' => 'min:1||max:'.sizeof(Friend::getTravelType()).'|required|integer',
            'travelDuration' => 'min:1|max:30|required|integer',
            'myGender' => 'min:1|max:4|required|integer',
            'myAge' => 'min:1|required|string',
            'friendGender' => 'min:1|required|integer',
            'machineType' => 'min:1|max:3|required|integer',
            'startDateValidation' => 'required|date|after:today',
            'travelVersion' => 'min:1|max:3|required|integer',
        ], [
            'startDateValidation.after' => 'تاریخ برگزاری تور باید تاریخی بعد از امروز باشد.',
        ], [
            'country' => 'کشور',
            'province' => 'استان',
            'travelType' => 'نوع سفر',
            'travelDuration' => 'بازه زمانی سفر',
            'myGender' => 'وضعیت من',
            'myAge' => 'سن من',
            'friendGender' => 'وضعیت همسفر',
            'machineType' => 'نوع مسیر سفر',
            'startDateValidation' => 'تاریخ سفر',
            'travelVersion' => 'سبک سفر',
        ]);


        if ($city && $city->country_id != $this->country) {
            $this->addError('city', 'استان انتخاب‌شده با کشور هماهنگ نیست.');
            return;
        }
        if (sizeof($this->gallery)==0){
            $this->addError('image', 'حداقل باید یک تصویر را ارسال کنید.');
            return;
        }
        $this->page = 2;

    }

    public function updatedImage()
    {
        if (sizeof($this->gallery)>1){
            $this->addError('image', 'تعداد تصاویر به حداکثر تعداد مجاز رسیده است.');
            return;
        }
        $size=getimagesize($this->image->getRealPath());
        if ($size[0]<500 or $size[1]<500){
            $this->addError('image', 'حداقل عرض و ارتفاع تصویر باید 500پیکسل باشد.');
            return;
        }
        $imageName = "injaa_" . time() . "." . $this->image->extension();
        $url = $this->image->storeAs('public/friends', $imageName);
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
        if (Storage::disk('local')->exists('public/friends/' . $image)) {
            Storage::disk('local')->delete('public/friends/' . $image);
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
        if ($this->startDate) {
            try {
                $this->startDateValidation = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat(
                    'Y/m/d',
                    $this->startDate
                );
            }catch (\Exception $e){
            }
        }
        $city = Province::find($this->province);
        $this->validate([
            'country' => 'min:0|required|exists:countries,id',
            'province' => 'min:0|required|integer|exists:provinces,id',
            'travelType' => 'min:1||max:'.sizeof(Friend::getTravelType()).'|required|integer',
            'travelDuration' => 'min:1|max:30|required|integer',
            'myGender' => 'min:1|max:4|required|integer',
            'myAge' => 'min:1|required|string',
            'friendGender' => 'min:1|required|integer',
            'machineType' => 'min:1|max:3|required|integer',
            'startDateValidation' => 'required|date|after:today',
            'travelVersion' => 'min:1|max:3|required|integer',
        ], [
            'startDateValidation.after' => 'تاریخ برگزاری تور باید تاریخی بعد از امروز باشد.',
        ], [
            'country' => 'کشور',
            'province' => 'استان',
            'travelType' => 'نوع سفر',
            'travelDuration' => 'بازه زمانی سفر',
            'myGender' => 'وضعیت من',
            'myAge' => 'سن من',
            'friendGender' => 'وضعیت همسفر',
            'machineType' => 'نوع مسیر سفر',
            'startDateValidation' => 'تاریخ سفر',
            'travelVersion' => 'سبک سفر',
        ]);



        if ($city && $city->country_id != $this->country) {
            $this->addError('city', 'استان انتخاب‌شده با کشور هماهنگ نیست.');
            return;
        }
        if (sizeof($this->gallery)==0){
            $this->addError('image', 'حداقل باید یک تصویر را ارسال کنید.');
            return;
        }
        $city->is_use = true;
        $city->save();
        Province::where("id", $this->province)->update([
            "is_use" => true
        ]);
        $data=[
            "title" => "دنبال همسفر " . ($this->friendGender!=4?Friend::getGrnders($this->friendGender):"") . " برای " . $city->name . " ، "  .$this->travelDuration ." روزه ".Friend::getMachineType($this->machineType),
            "country_id" => $this->country,
            "province_id" => $this->province,
            "user_id" => auth()->user()->id,
            "travel_type" => $this->travelType,
            "travel_duration" => $this->travelDuration,
            "my_gender" => $this->myGender,
            "my_age" => $this->myAge,
            "friend_gender" => $this->friendGender,
            "machine_type" => $this->machineType,
            "start_date" => $this->startDateValidation,
            "travel_version"=>$this->travelVersion,
            "image"=>$this->mainImage,
            "status" => true,
        ];
        $model=null;
        if ($this->id==null){
            $model = Friend::create($data);
            session()->put('message', "درخواست همسفر با موفقیت ثبت شد");
        }else{
            $model=Friend::find($this->id);
            $model->update($data);
            Images::where("friend_id", $this->id)->delete();
            OptionValue::where("friend_id",$this->id)->delete();
            session()->put('message', "رخواست همسفر با موفقیت ویرایش شد");
        }
        foreach ($this->gallery as $item) {
            Images::create([
                "url" => $item,
                "friend_id" => $model->id
            ]);
        }
        foreach ($this->options as $option) {
            OptionValue::create([
                "option_id" => $option,
                "friend_id" => $model->id,
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
        view()->share('title', "افزودن اقامتگاه");
        if ($this->id != null) {
            $model=Friend::find($this->id);
            $this->title=$model->title;
            $this->country=$model->country_id;
            $this->province=$model->province_id;
            $this->travelType=$model->travel_type;
            $this->travelDuration=$model->travel_duration;
            $this->myGender=$model->my_gender;
            $this->myAge=$model->my_age;
            $this->friendGender=$model->friend_gender;
            $this->machineType=$model->machine_type;
            $this->startDateValidatio=$model->start_date;
            $gregorianDate = new \DateTime($model->start_date);
            $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
            $this->startDate=$jalaliDate->format('%Y/%m/%d');
            $this->travelVersion=$model->travel_version;
            $this->mainImage=$model->image;
            view()->share('title', $this->title);

            foreach ($model->images as $image){
                $this->gallery[$image->url]=$image->url;
            }
            foreach ($model->optionValues as $value){
                $this->options[]=$value->option_id;
            }
        }
    }

}
