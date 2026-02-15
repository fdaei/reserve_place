<?php

namespace App\Livewire\User\Residence;

use App\Models\City;
use App\Models\OptionValue;
use App\Models\Province;
use App\Models\Residence;
use App\Models\Images;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddResidence extends Component
{
    use WithFileUploads;

    public function render()
    {
        if (!auth()->check()) Redirect::to("login");

        return view('livewire.user.residence.add-residence1')
            ->extends("app")
            ->section("content");
    }


    public $roomNumber;
    public $area;
    public $peopleNumber;
    public $amountValidate;
    public $amount;
    public $lastWeekAmountValidate = 0;
    public $lastWeekAmount ;
    public $address;
    public $latLen;
    public $province = 27;
    public $city = 384;
    public $residenceType = 1;
    public $areaType = 1;
    public $page = 1;


    public $gallery = [
    ];
    public $options = [];
    public $mainImage = "";

    #[Validate('image|max:5120|mimes:jpg,jpeg,png,webp,gif')]
    public $image;

    function continue()
    {
        $city = City::find($this->city);
        $this->amountValidate=str_replace(",","",$this->amount);
        $this->lastWeekAmountValidate=str_replace(",","",$this->lastWeekAmount);
        $this->validate([
            'province' => 'min:0|required|exists:provinces,id',
            'city' => 'min:0|required|integer|exists:cities,id',
            'roomNumber' => 'min:0|max:10|required|integer',
            'area' => 'min:0|required|integer',
            'peopleNumber' => 'min:0|max:11|required|integer',
            'amountValidate' => 'min:100000|required|integer',
            'lastWeekAmountValidate' => 'min:100000|required|integer',
            'address' => 'min:0|max:190|required|string',
        ], [], [
            'province' => 'استان',
            'city' => 'شهر',
            'roomNumber' => 'تعداد اتاق',
            'area' => 'متراژ',
            'peopleNumber' => 'تعدادمسافران ',
            'amountValidate' => 'قیمت',
            'lastWeekAmountValidate' => 'قیمت آخر هفته ها',
            'address' => 'آدرس',
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
        $this->validateOnly('image');
        if (sizeof($this->gallery)>8){
            $this->addError('image', 'تعداد تصاویر به حداکثر تعداد مجاز رسیده است.');
            return;
        }
        $size=@getimagesize($this->image->getRealPath());
        if ($size === false){
            $this->addError('image', 'فایل انتخاب‌شده تصویر معتبر نیست.');
            return;
        }
        if ($size[0]<500 or $size[1]<500){
            $this->addError('image', 'حداقل عرض و ارتفاع تصویر باید 500پیکسل باشد.');
            return;
        }
        $imageName = "injaa_" . time() . "." . $this->image->extension();
        $this->image->storeAs('residences', $imageName, 'public');
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
        if (Storage::disk('public')->exists('residences/' . $image)) {
            Storage::disk('public')->delete('residences/' . $image);
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

    public function updatedProvince($province)
    {
        $this->city = City::where('province_id', $province)->value('id');
        $this->resetValidation('city');
    }

    function save()
    {
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
            "title" => "اجاره " . Residence::getResidenceType($this->residenceType) . " " . Residence::convertNumberToString($this->roomNumber) . " خوابه " . $hasPool . " " . Residence::getAreaType($this->areaType) . " " . $city->name,
            "province_id" => $this->province,
            "city_id" => $this->city,
            "user_id" => auth()->user()->id,
            "residence_type" => $this->residenceType,
            "area_type" => $this->areaType,
            "room_number" => $this->roomNumber,
            "area" => $this->area,
            "people_number" => $this->peopleNumber,
            "amount" => str_replace(",","",$this->amountValidate),
            "last_week_amount" => str_replace(",","",$this->lastWeekAmountValidate),
            "address" => $this->address,
            "image" => $this->mainImage,
            "lat" => $this->latLen[0],
            "lng" => $this->latLen[1],
            "status" => true,
        ];
        $residence = null;
        $oldImages = [];
        if ($this->id==null){
            $residence = Residence::create($data);
            session()->put('message', "اقامتگاه با موفقیت ثبت شد");
        }else{
            $residence = Residence::where('id', $this->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$residence) {
                session()->put('message', "اقامتگاه موردنظر یافت نشد");
                Redirect::to("/dashboard");
                return;
            }

            $oldImages = $residence->images()->pluck('url')->toArray();
            $residence->update($data);
            Images::where("residence_id", $this->id)->delete();
            OptionValue::where("residence_id",$this->id)->delete();

            $newImages = array_values($this->gallery);
            foreach (array_diff($oldImages, $newImages) as $deletedImage) {
                if (Storage::disk('public')->exists('residences/' . $deletedImage)) {
                    Storage::disk('public')->delete('residences/' . $deletedImage);
                }
            }

            session()->put('message', "اقامتگاه با موفقیت ویرایش شد");
        }
        foreach ($this->gallery as $item) {
            Images::create([
                "url" => $item,
                "residence_id" => $residence->id
            ]);
        }
        foreach ($this->options as $option) {
            OptionValue::create([
                "option_id" => $option,
                "residence_id" => $residence->id,
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
            $residence = Residence::where('id', $this->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$residence) {
                session()->put('message', "اقامتگاه موردنظر یافت نشد");
                Redirect::to("/dashboard");
                return;
            }

            $this->mainImage=$residence->image;
            $this->title=$residence->title;
            view()->share('title', $this->title);
            $this->roomNumber=$residence->room_number;
            $this->area=$residence->area;
            $this->peopleNumber=$residence->people_number;
            $this->amount=number_format($residence->amount);
            $this->lastWeekAmount=number_format($residence->last_week_amount);
            $this->address=$residence->address;
            $this->latLen=$residence->lat.":".$residence->lng;
            $this->province=$residence->province_id;
            $this->city=$residence->city_id;
            $this->residenceType=$residence->residence_type;
            $this->areaType=$residence->area_type;

            $this->page=1;
            foreach ($residence->images as $image){
                $this->gallery[$image->url]=$image->url;
            }
            foreach ($residence->optionValues as $value){
                $this->options[]=$value->option_id;
            }
        }
    }

}
