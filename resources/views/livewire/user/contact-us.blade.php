<div class="container">
    <br>
    <h4 class="text-center">
        <i class="fa fa-phone"></i>
        تماس با سایت
    </h4>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <form  wire:submit.prevent="save" class="card">
                <div class="card-header">
                    ارتباط با ما
                </div>
                <div class="card-body">
                        @if(!auth()->check())
                    <div class="alert alert-info text-center">
                        برای پر کردن فرم تماس باما باید ابتدا وارد حساب خود شوید.
                        <br>
                        <a class="btn btn-primary mt-2" href="{{\Illuminate\Support\Facades\URL::to("login")}}">
                            ورود یا ایجاد حساب
                        </a>
                    </div>
                    @endif
                    <div class="col-12">
                        <p class="m-0">نام</p>
                        <input disabled type="text" value="{{$name}}" id="title" placeholder=""
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-12 mt-2">
                        <p class="m-0">شماره تماس</p>
                        <input disabled type="text" value="{{$phone}}" id="title" placeholder=""
                               class="form-control form-control-sm">
                    </div>
                            <div class="col-12">
                                <p class="m-0">عنوان</p>
                                <input type="text" wire:model="title" id="title" placeholder=""
                                       class="form-control form-control-sm">
                                @error("title")
                                <span class="text-danger">
                                {{$message}}
                            </span>
                                @enderror
                            </div>
                    <div class="col-12 mt-2">
                        <p class="m-0">بخش</p>
                        <select wire:model="area" class="form-control form-control-sm">
                            @foreach(\App\Models\SupportAreaTickets::where("status",true)->get() as $item)
                                <option value="{{$item->id}}">{{$item->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <p class="m-0">متن پیام</p>
                        <textarea type="text" id="" wire:model="message"  style="min-height: 160px" placeholder=""
                                  class="form-control form-control-sm"></textarea>
                        @error("message")
                        <span class="text-danger">
                            {{$message}}
                        </span>
                        @enderror
                    </div>
                    <div class="col-12 mt-2">
                        <button {{!auth()->check()?"disabled":""}} class="btn btn-primary w-100">ارسال پیام</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
           <div class="card">
               <div class="card-header">تماس با ما</div>
               <div class="card-body text-left">
                   <div class="contact-info">
                       <div class="contact-item">
                           <i class="fa fa-map-marker text-dark"></i>
                           <span>{{getConfigs("address")}}</span>
                       </div>
                       <div class="contact-item">
                           <i class="fa fa-phone text-dark"></i>
                           <span>{{getConfigs("phone1")}}</span>
                       </div>
                       <div class="contact-item">
                           <i class="fa fa-phone text-dark"></i>
                           <span>{{getConfigs("phone2")}}</span>
                       </div>
                       <div class="contact-item">
                           <i class="fa fa-envelope text-dark"></i>
                           <span>{{getConfigs("email")}}</span>
                       </div>
                   </div>
               </div>
           </div>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    @script
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Livewire.on("create", event => {
            Swal.fire({
                icon: "success",
                title: 'ثبت موفقیت آمیز',
                text: `درخواست شما با موفقیت ثبت شد و میتوانید پاسخ آن را از طریق پروفایل خود پیگیری کنید.`,
                confirmButtonText: "بستن",
                confirmButtonColor: '#007bff',
            }).then(res => {
            })
        })
    </script>
    @endscript

</div>
