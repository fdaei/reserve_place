<?php

namespace App\Livewire\User\Tour;

use App\Models\City;
use App\Models\Province;
use App\Models\Images;
use App\Models\Tour;
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

        return view('livewire.user.tour.add')
            ->extends("app")
            ->section("content");
    }

    public $province = 27;
    public $city = 384;
    public $address;
    public $amount;
    public $tourType = 1;
    public $residenceType = 1;
    public $minPeople = 3;
    public $maxPeople = 3;
    public $tourDuration = 1;
    public $tourTimeFrame = "one";
    public $openTourTime = "";
    public $description = "";
    public $page = 1;



    public $gallery = [
    ];
    public $mainImage = "";

    #[Validate('image|max:5120|mimes:jpg,jpeg,png,webp,gif')]
    public $image;

    public function updatedTourTimeFrame(){
        if ($this->tourTimeFrame=="one")
        $this->openTourTime="";
        elseif ($this->tourTimeFrame=="weekly")
        $this->openTourTime="saturday";
        elseif ($this->tourTimeFrame=="monthly")
        $this->openTourTime="1";
    }

    public function updatedProvince($province)
    {
        $this->city = City::where('province_id', $province)->value('id');
        $this->resetValidation('city');
    }

    public function updatedImage()
    {
        $this->validateOnly('image');
        if (sizeof($this->gallery)>3){
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
        $this->image->storeAs('tours', $imageName, 'public');
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
        if (Storage::disk('public')->exists('tours/' . $image)) {
            Storage::disk('public')->delete('tours/' . $image);
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
    public $amountValidation;
    public $openTourTimeValidate;

    function save()
    {
        $this->amountValidation=str_replace(",","",$this->amount);
        $openTourTimeValidation="min:0|max:190|required|string";
        if ($this->tourTimeFrame=="one"){
            if ($this->openTourTime) {
                try {
                    $this->openTourTimeValidate = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat(
                        'Y/m/d',
                        $this->openTourTime
                    );
                }catch (\Exception $e){
                }
            }
            $openTourTimeValidation='required|date|after:today';
        }elseif($this->tourTimeFrame=="weekly"){
            $openTourTimeValidation="nullable|string";
        }elseif($this->tourTimeFrame=="monthly"){
            $openTourTimeValidation="nullable|integer";
        }
        $city = City::find($this->city);

        $this->validate([
            'province' => 'min:0|required|exists:provinces,id',
            'city' => 'min:0|required|integer|exists:cities,id',
            'address' => 'required|string',
            'amountValidation' => 'min:100000|required|integer',
            'tourType' => 'min:1|max:'.sizeof(Tour::getTourType()).'|required|integer',
            'residenceType' => 'min:1|max:'.sizeof(Tour::getResidenceType()).'|required|integer',
            'minPeople' => 'min:1|required|integer',
            'maxPeople' => 'min:1|required|integer',
            'tourDuration' => 'min:1|max:190|required|integer',
            'tourTimeFrame' => 'required|string',
            'openTourTimeValidate' => $openTourTimeValidation,
            'description' => 'min:0|max:190|required|string',
        ], [
            'openTourTimeValidate.after' => 'تاریخ برگزاری تور باید تاریخی بعد از امروز باشد.',
        ], [
            'province' => 'استان',
            'city' => 'شهر',
            'address' => 'نقطه شروع ',
            'amountValidation' => 'قیمت',
            'tourType' => 'نوع تور',
            'residenceType' => 'محل اقامت',
            'minPeople' => 'حداقل ظرفیت برای اجرا',
            'maxPeople' => 'حداکثر ظرفیت',
            'tourDuration' => 'مدت تور',
            'tourTimeFrame' => 'بازه زمانی تور',
            'openTourTimeValidate' => 'تاریخ برگزاری تور',
            'description' => 'توضیحات',
        ]);


        if ($city && $city->province_id != $this->province) {
            $this->addError('city', 'شهر انتخاب‌شده با استان هماهنگ نیست.');
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
            "title" => "تور " . Tour::getTourType($this->tourType) . " " . $this->tourDuration . " روزه "  . " " . $city->name,
            "province_id" => $this->province,
            "city_id" => $this->city,
            "address" => $this->address,
            "user_id" => auth()->user()->id,
            "tour_type" => $this->tourType,
            "residence_type" => $this->residenceType,
            "tour_duration" => $this->tourDuration,
            "min_people" => $this->minPeople,
            "max_people" => $this->maxPeople,
            "amount" => $this->amountValidation,
            "image" => $this->mainImage,
            "description" => $this->description,
            "tour_time_frame"=>$this->tourTimeFrame,
            "open_tour_time"=>$this->openTourTime,
            "expire_date"=>$this->tourTimeFrame=="one"?$this->openTourTime:null,
            "status" => true,
        ];
        $tour = null;
        $oldImages = [];
        if ($this->id==null){
            $tour = Tour::create($data);
            session()->put('message', "تور با موفقیت ثبت شد");
        }else{
            $tour = Tour::where('id', $this->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$tour) {
                session()->put('message', "تور موردنظر یافت نشد");
                Redirect::to("/dashboard");
                return;
            }

            $oldImages = $tour->images()->pluck('url')->toArray();
            $tour->update($data);
            Images::where("tour_id", $this->id)->delete();

            $newImages = array_values($this->gallery);
            foreach (array_diff($oldImages, $newImages) as $deletedImage) {
                if (Storage::disk('public')->exists('tours/' . $deletedImage)) {
                    Storage::disk('public')->delete('tours/' . $deletedImage);
                }
            }

            session()->put('message', "تور با موفقیت ویرایش شد");
        }
        foreach ($this->gallery as $item) {
            Images::create([
                "url" => $item,
                "tour_id" => $tour->id
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
            $tour = Tour::where('id', $this->id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$tour) {
                session()->put('message', "تور موردنظر یافت نشد");
                Redirect::to("/dashboard");
                return;
            }

            $this->title=$tour->title;
            $this->province=$tour->province_id;
            $this->city=$tour->city_id;
            $this->address=$tour->address;
            $this->tourType=$tour->tour_type;
            $this->residenceType=$tour->residence_type;
            $this->tourDuration=$tour->tour_duration;
            $this->minPeople=$tour->min_people;
            $this->maxPeople=$tour->max_people;
            $this->amountValidation=$tour->amount;
            $this->amount=number_format($tour->amount);
            $this->mainImage=$tour->image;
            $this->description=$tour->description;
            $this->tourTimeFrame=$tour->tour_time_frame;
            $this->openTourTime=$tour->open_tour_time;
            view()->share('title', $this->title);

            foreach ($tour->images as $image){
                $this->gallery[$image->url]=$image->url;
            }
        }
    }

}
