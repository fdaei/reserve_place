<div class="section">
    <h2>
        <i class="fas fa-users-cog"></i>
        پیام ها
    </h2>
    <div class="" id="collapseExample">
        <div class="card card-body">

            <div class="row flex-nowrap flex-row justify-content-between">
                <div style="margin: 4px">
                    <label>نام:
                        <input type="text" wire:model.live="search" class="form-control">
                    </label>
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>

    <table class="table responsive-table">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>شماره تماس</th>
            <th>عنوان</th>
            <th>واحد</th>
            <th>وضعیت</th>
            <th>تاریخ ثبت</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @php
            $areas=\App\Models\SupportAreaTickets::all()->keyBy("id");
        @endphp
        @foreach($list as $item)

            <tr>
                <td data-label="Id">{{$item->id}}</td>
                <td data-label="شماره تماس">{{\App\Models\User::find($item->user_id)->phone}}</td>
                <td data-label="عنوان">{{$item->title}}</td>
                <td data-label="بخش">{{$areas[$item->area]->title}}</td>
                <td data-label="وضعیت">
                                <span class="   {{$item->status==0?"text-warning":"text-success"}}">
                                    {{$item->status==0?"درحال بررسی":""}}
                                    {{$item->status==1?"پاسخ داده شده":""}}
                                </span>
                </td>
                @php
                    $gregorianDate = new \DateTime($item["created_at"]);
                    $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                @endphp
                <td data-label="تاریخ ثبت">
                    {{$jalaliDate->format('%Y/%m/%d')}}
                    <br>
                    <span class="op-5">
                            {{$jalaliDate->format('H:i')}}
                            </span>
                </td>
                <td data-label="">
                    <a href="{{\Illuminate\Support\Facades\URL::to("admin/message/".$item->id)}}" class="btn btn-sm btn-primary" >
                        مشاهده
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="card">
        <div class="card-body">
            {{$list->links('vendor.pagination.default')}}
        </div>
    </div>
    <div class="modal fade {{$form!="empty"?"show":""}}" id="exampleModal" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true"
         style="{{$form!="empty"?"display: block;":""}}">
        <div class="modal-dialog">
            <form wire:submit="{{$form}}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        @if($form=="add")
                            افزودن استان
                        @else
                            ویرایش استان
                        @endif
                    </h5>
                    <span wire:click="setForm('empty')" type="button" class="close"
                          data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div style="margin: 4px">
                        <label>نام:
                            <input type="text" wire:model="title" class="form-control">
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <span wire:click="setForm('empty')" type="button" class="btn btn-secondary"
                          data-dismiss="modal">لغو</span>
                    <button class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>

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
            let id = $(this).attr("data-id")
            let title = $(this).attr("data-title")
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
                    Livewire.dispatch("remove", {id: id});
                    Livewire.on('componentName', (data) => {
                        console.log(data.title); // خروجی: blog-component
                    });
                }
            })
        });

        Livewire.on("edited", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات با موفقیت بروز شد'
            })
        })
        Livewire.on("create", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات موفقیت ثبت شد'
            })
        })

        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'سطر با موفقیت حذف شد'
            })
        })
    </script>
    @endscript

</div>
