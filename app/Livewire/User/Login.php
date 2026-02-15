<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    public $phone;

    public $page=1;
    public function render()
    {
        return view('livewire.user.login')
            ->extends("app")
            ->section("content");
    }

    function mount(){

        if (\auth()->check()){
            if (session("detail-id")){
                Redirect::to("detail/".session("detail-id"));
                session()->forget("detail-id");
                return;
            }else{
                Redirect::to("");
                return;
            }
        }
        view()->share('title', "ورود یا ثبت نام");
    }
    function login(){
        $this->validate([
            'phone' => 'min:10|max:11|required',
        ], [], [
            'phone' => 'شماره تماس',
        ]);
        $templateId = 645580;
        $randNumber=rand(1000,9999);
        $parameters = [
            [
                "name" => "LOGIN",
                "value" => $randNumber
            ]
        ];
        // dd(env("LOG_CHANNEL"));
        // SMS gateway disabled (no network). Log the verification code instead.
        Log::error('SMSIR disabled - verification code generated', [
            'phone' => $this->phone,
            'code' => $randNumber,
        ]);
        VerificationCode::create([
            "phone"=>convertPersianToEnglishNumbers($this->phone),
            "code"=>$randNumber,
            "is_use"=>false
        ]);
        $this->dispatch("start-timer");
        $this->page=2;
    }

    public $code;
    public $code1;
    public $code2;
    public $code3;
    public $code4;
    function verify_code(){
        $this->code=$this->code1.$this->code2.$this->code3.$this->code4;
        $this->validate([
            'phone' => 'min:3|max:11|required',
        ], [], [
            'phone' => 'شماره تماس',
        ]);
        if ($this->code==""){
            $this->addError('code', 'کد وارد شده صحیح نیست.');
            return;
        }
        $verifyModel = VerificationCode::where("code", $this->code)
            ->where("phone", $this->phone)
            ->where("is_use", false)
            ->get()
            ->sortByDesc("id")
            ->first();
        if ($verifyModel==null){
            $this->addError('code', 'کد وارد شده صحیح نیست.');
            return;
        }
        $verifyModel->is_use=true;
        $verifyModel->save();
        $user=User::where("phone",$this->phone)->get()->first();
        if ($user==null){
            $user=User::create([
                "name"=>"",
                "family"=>"",
                "national_code"=>"",
                "birth_day"=>"",
                "phone"=>convertPersianToEnglishNumbers($this->phone),
                "profile_image"=>"",
            ]);
            Auth::login($user,true);
        }else{
            Auth::login($user,true);
        }
        if (session("detail-id")){
            return Redirect::to("detail/".session("detail-id"));
            session()->forget("detail-id");
            return;
        }else{
            return Redirect::to("");
        }
    }
    public function back(){
        $this->page=1;
    }


}
