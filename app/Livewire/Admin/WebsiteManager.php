<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Livewire\WithFileUploads;

class WebsiteManager extends Component
{
    use WithFileUploads;
    public function render()
    {
        return view('livewire.admin.website-manager')
            ->extends("app")
            ->section("content");
    }




    protected $rules = [
        'icon' => 'nullable|string|max:40',
        'title' => 'required|string|max:160',
        'words' => 'required|string',
        'description' => 'required|string',
    ];

//    public function updated($key,$value){
//        $this->dispatchBrowserEvent("updatedConfig",[]);
//    }

    function updateGeneral(){
        Config::where("title","website-title")->update([
            "value" =>$this->title
        ]);
        Config::where("title","website-description")->update([
            "value" =>$this->description
        ]);
        Config::where("title","website-main-title")->update([
            "value" =>$this->mainTitle
        ]);
        Config::where("title","website-words")->update([
            "value" =>$this->words
        ]);
        Config::where("title","website-words")->update([
            "value" =>$this->words
        ]);
        Config::where("title","website-titleEn")->update([
            "value" =>$this->titleEn
        ]);
        $this->dispatch("edited");
    }
    public function updateSkin()
    {
        if ($this->iconInput) {
            $extenstion = $this->iconInput->getClientOriginalExtension();
            $this->iconInput->storeAs("", $this->icon, 'public');
            $this->iconInput=null;
            Config::where("title","website-icon")->update([
                "value" =>$this->icon
            ]);
        }
        if ($this->bannerSeasonImageTemp) {
            $extenstion = $this->bannerSeasonImageTemp->getClientOriginalExtension();
            $this->bannerSeasonImageTemp->storeAs("", $this->bannerSeasonImage, 'public');
            $this->bannerSeasonImageTemp=null;
            Config::where("title","bannerSeasonImage")->update([
                "value" =>$this->bannerSeasonImage
            ]);
        }
        if ($this->markerMapIconTemp) {
            $this->markerMapIconTemp->storeAs("", $this->markerMapIcon, 'public');
            $this->markerMapIconTemp=null;
            Config::where("title","markerMapIcon")->update([
                "value" =>$this->markerMapIcon
            ]);
        }
        if ($this->markerMapFoodstoreIconTemp) {
            $this->markerMapFoodstoreIconTemp->storeAs("", $this->markerMapFoodstoreIcon, 'public');
            $this->markerMapFoodstoreIconTemp=null;
            Config::where("title","markerMapFoodstoreIcon")->update([
                "value" =>$this->markerMapFoodstoreIcon
            ]);
        }
        if ($this->offlineModeIconTemp) {
            $this->offlineModeIconTemp->storeAs("", $this->offlineModeIcon, 'public');
            $this->offlineModeIconTemp=null;
            Config::where("title","offlineModeIcon")->update([
                "value" =>$this->offlineModeIcon
            ]);
        }
        if ($this->page404IconTemp) {
            $this->page404IconTemp->storeAs("", $this->page404Icon, 'public');
            $this->page404IconTemp=null;
            Config::where("title","page404Icon")->update([
                "value" =>$this->page404Icon
            ]);
        }
        Config::where("title","mainColor")->update([
            "value" =>$this->mainColor
        ]);
        Config::where("title","secondaryColor")->update([
            "value" =>$this->secondaryColor
        ]);
        $this->dispatch("edited");

    }


    public function updateContactUs(){
        Config::where("title","phone1")->update([
            "value" =>$this->phone1
        ]);
        Config::where("title","phone2")->update([
            "value" =>$this->phone2
        ]);
        Config::where("title","address")->update([
            "value" =>$this->address
        ]);
        Config::where("title","email")->update([
            "value" =>$this->email
        ]);
        $this->dispatch("edited");
    }
    public function updateBanners(){
        Config::where("title","adminMessagesPhone")->update([
            "value" =>$this->adminMessagesPhone
        ]);
        Config::where("title","mainBannerText")->update([
            "value" =>$this->mainBannerText
        ]);
        $this->dispatch("edited");
    }
    public function updateFinance(){
        Config::where("title","commissionReserve")->update([
            "value" =>$this->commissionReserve
        ]);
        $this->dispatch("edited");
    }
    public function updateFiltersControls(){
        Config::where("title","paginationItemCount")->update([
            "value" =>$this->paginationItemCount
        ]);
        $this->dispatch("edited");
    }
    public function updateSocialMedia(){
        Config::where("title","instagramLink")->update([
            "value" =>$this->instagramLink
        ]);
        Config::where("title","telegramLink")->update([
            "value" =>$this->telegramLink
        ]);
        Config::where("title","whatsappLink")->update([
            "value" =>$this->whatsappLink
        ]);
        $this->dispatch("edited");
    }
    public function updateConfig(){
        Config::where("title","websiteStatus")->update([
            "value" =>$this->websiteStatus
        ]);
        Config::where("title","OfflineModeText")->update([
            "value" =>$this->OfflineModeText
        ]);
        $this->dispatch("edited");
    }

    //general info
    public $configs;
    public $title = "";
    public $mainTitle = "";
    public $titleEn = "";
    public $words = "";
    public $description = "";
    public $status;


    //skin info
    public $mainColor;
    public $secondaryColor;
    public $iconInput;
    public $icon;

    public $bannerSeasonImageTemp;
    public $bannerSeasonImage;

    public $markerMapIconTemp;
    public $markerMapIcon;

    public $markerMapFoodstoreIconTemp;
    public $markerMapFoodstoreIcon;

    public $offlineModeIconTemp;
    public $offlineModeIcon;

    public $page404IconTemp;
    public $page404Icon;
    public function updated($type,$value){
        if ($type=="iconInput" or $type=="markerMapIconTemp" or $type=="markerMapFoodstoreIconTemp" or $type=="offlineModeIconTemp" or $type=="page404IconTemp" or $type=="bannerSeasonImageTemp"){
            if ($this->iconInput) {
                $extenstion = $this->iconInput->getClientOriginalExtension();
                $filename =  "injaa_".$type."_".time()."." . $extenstion;
                $this->icon = $filename;
            }
            if ($this->markerMapIconTemp) {
                $extenstion = $this->markerMapIconTemp->getClientOriginalExtension();
                $filename =  "injaa_".$type."_".time()."." . $extenstion;
                $this->markerMapIcon = $filename;
            }
            if ($this->markerMapFoodstoreIconTemp) {
                $extenstion = $this->markerMapFoodstoreIconTemp->getClientOriginalExtension();
                $filename =  "injaa_".$type."_".time()."." . $extenstion;
                $this->markerMapFoodstoreIcon = $filename;
            }
            if ($this->offlineModeIconTemp) {
                $extenstion = $this->offlineModeIconTemp->getClientOriginalExtension();
                $filename =  "injaa_".$type."_".time()."." . $extenstion;
                $this->offlineModeIcon = $filename;
            }
            if ($this->page404IconTemp) {
                $extenstion = $this->page404IconTemp->getClientOriginalExtension();
                $filename =  "injaa_".$type."_".time()."." . $extenstion;
                $this->page404Icon = $filename;
            }
            if ($this->bannerSeasonImageTemp) {
                $extenstion = $this->bannerSeasonImageTemp->getClientOriginalExtension();
                $filename =  "injaa_".$type."_".time()."." . $extenstion;
                $this->bannerSeasonImage = $filename;
            }
        }
    }

    //contact-us info
    public $address="";
    public $phone1="";
    public $phone2="";
    public $email="";


    //banners info
    public $mainBannerText="";
    public $adminMessagesPhone="";

    //finance info
    public $commissionReserve="";
    //filter info
    public $paginationItemCount="";
    //social media info
    public $instagramLink="";
    public $telegramLink="";
    public $whatsappLink="";

    //config info
    public $websiteStatus="";
    public $OfflineModeText="";

    function mount(){
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
        $this->configs=Config::all()->keyBy("title");

        $this->title=$this->configs->get("website-title")->value;
        $this->titleEn=$this->configs->get("website-titleEn")->value;
        $this->mainTitle=$this->configs->get("website-main-title")->value;
        $this->description=$this->configs->get("website-description")->value;
        $this->words=$this->configs->get("website-words")->value;

        $this->icon=$this->configs->get("website-icon")->value;
        $this->bannerSeasonImage=$this->configs->get("bannerSeasonImage")->value;
        $this->markerMapIcon=$this->configs->get("markerMapIcon")->value;
        $this->markerMapFoodstoreIcon=$this->configs->get("markerMapFoodstoreIcon")->value;
        $this->offlineModeIcon=$this->configs->get("offlineModeIcon")->value;
        $this->page404Icon=$this->configs->get("page404Icon")->value;
        $this->mainColor=$this->configs->get("mainColor")->value;
        $this->secondaryColor=$this->configs->get("secondaryColor")->value;

        $this->address=$this->configs->get("address")->value;
        $this->phone1=$this->configs->get("phone1")->value;
        $this->phone2=$this->configs->get("phone2")->value;
        $this->email=$this->configs->get("email")->value;

        $this->mainBannerText=$this->configs->get("mainBannerText")->value;
        $this->adminMessagesPhone=$this->configs->get("adminMessagesPhone")->value;

        $this->commissionReserve=$this->configs->get("commissionReserve")->value;

        $this->paginationItemCount=$this->configs->get("paginationItemCount")->value;

        $this->instagramLink=$this->configs->get("instagramLink")->value;
        $this->telegramLink=$this->configs->get("telegramLink")->value;
        $this->whatsappLink=$this->configs->get("whatsappLink")->value;

        $this->websiteStatus=$this->configs->get("websiteStatus")->value;
        $this->OfflineModeText=$this->configs->get("OfflineModeText")->value;
    }


}
