<div class="section">
    <link href="{{asset("plugin/datepicker/jalalidatepicker.min.css")}}" rel="stylesheet">
    <script src="{{asset("plugin/datepicker/jalalidatepicker.min.js")}}"></script>
    <h2>
        <i class="fas fa-users-cog"></i> مدیریت کاربران
    </h2>
        <div class="" id="collapseExample">
            <div class="card card-body">

                <div class="row flex-nowrap flex-row ">
                    <div style="margin: 4px">
                        <label>نام:
                            <input type="text" wire:model.live="name"  class="form-control">
                        </label>
                    </div>
                    <div style="margin: 4px">
                        <label>نام خانوادگی:
                            <input  wire:model.live="family"  type="text"  class="form-control">
                        </label>
                    </div>
                    <div style="margin: 4px">
                        <label>کد ملی:
                            <input type="text" name="national_code"  wire:model.live="national_code" class="form-control">
                        </label>
                    </div>
                    <div style="margin: 4px">
                        <label>تلفن:
                            <input type="tel" name="phone" class="form-control"  wire:model.live="phone">
                        </label>
                    </div>
                    <div style="margin: 4px">
                        <label>تاریخ ثبت نام:
                            <input wire:model.live="createdAt" data-jdp  data-jdp-max-date="today"  name="birth_day" class="form-control">
                        </label>
                    </div>
                </div>
                @script
                <script>
                    $(function (){
                        jalaliDatepicker.startWatch({
                            minDate: "attr",
                            maxDate: "attr",
                            time: false,
                            autoClose: true,
                        });

                    })

                </script>
                @endscript
            </div>
        </div>

    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>نام</th>
            <th>نام خانوادگی</th>
            <th>کد ملی</th>
            <th>تلفن</th>
            <th>تاریخ ثبت</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $item)

        <tr>
            <td>{{$item->id}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->family}}</td>
            <td>{{$item->national_code}}</td>
            <td>{{$item->phone}}</td>
            @php
                $gregorianDate = new \DateTime($item["created_at"]);
                $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
            @endphp
            <td>
                {{$jalaliDate->format('%Y/%m/%d')}}
                <br>
                <span class="op-5">
                            {{$jalaliDate->format('H:i')}}
                            </span>
            </td>
            <td>
                <button class="btn btn-sm btn-warning" wire:click="login('{{$item->id}}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" data-id="{{$item->id}}" data-title="{{$item->title}}">
                    <i class="fas fa-trash" ></i>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
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
           $(document).on("click", ".btn-danger", function () {
            let id=$(this).attr("data-id")
            let title=$(this).attr("data-title")
            Swal.fire({
                icon: "warning",
                title: 'هشدار',
                text: `از حذف کردن  ${title} اطمینان دارید؟`,
                confirmButtonText: "لغو",
                denyButtonText: "حذف کردن",
                showDenyButton: true,
                background: '#333',
                color: '#fff',
                confirmButtonColor: '#3085d6',
            }).then(res => {
                if (res.isDenied) {
                    Livewire.dispatch("remove", { id: id });
                    Livewire.on('componentName', (data) => {
                        console.log(data.name); // خروجی: blog-component
                    });
                }
            })
        });
        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'سطر با موفقیت حذف شد'
            })
        })
    </script>
    @endscript
    <div class="card">
        <div class="card-body">
            {{$list->links('vendor.pagination.default')}}
        </div>
    </div>
</div>
