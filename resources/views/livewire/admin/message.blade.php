<div class="row">
    @vite(['resources/css/user/tickets.less'])

    <div class="col-12" style="text-align: center" id="btns">
        <p class="font-weight-bold pt-2 text-left">
            {{$ticket->title}}
        </p>
    </div>
    <ul class="tickets mb-5">
        @foreach($list as $item)
            <li class="{{$item->user_id==auth()->id()?"me":"user"}}">
                <div>
                    <span class="op-5 {{$item->user_id==auth()->id()?"text-light":""}}">
                        {{$item->user_id==auth()->id()?"من:":$userPhone}}
                    </span>
                    <br>
                    {{$item->message}}
                    <br>
                    <span class="op-5  {{$item->user_id==auth()->id()?"text-light":""}}">
                          @php
                              $gregorianDate = new \DateTime($item["created_at"]);
                              $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                          @endphp
                        {{$jalaliDate->format('H:i')}}
                        {{$jalaliDate->format('%Y/%m/%d')}}
                    </span>
                </div>
            </li>
        @endforeach
    </ul>
    <form  wire:submit.prevent="save" class="card col-12 mb-5">
        <div class="card-body">
            <div class="col-12 mt-2">
                <p class="m-0">بخش</p>
                <select wire:model="area" class="form-control form-control-sm" style="height: 40px">
                    @foreach(\App\Models\SupportAreaTickets::all() as $item)
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
                text: `پاسخ شما برای تیکت شماره {{$ticket->id}} ثبت شد.`,
                confirmButtonText: "بستن",
                confirmButtonColor: '#007bff',
            }).then(res => {
            })
        })
    </script>
    @endscript
</div>
