<div class="section">
    <h2>
        <i class="fas fa-users-cog"></i> مدیریت کشور ها
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
                    <br>
                    <div wire:click="setForm('add')" class="btn btn-primary">
                        افزودن
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
        <tr style="width: 100%">
            <th colspan="1">ID</th>
            <th colspan="1">نام</th>
            <th colspan="1">عملیات</th>
        </tr>
        </thead>
        <tbody style="width: 100%">
        @foreach($list as $item)

            <tr style="width: 100%">
                <td colspan="1">{{$item->id}}</td>
                <td colspan="1">{{$item->name}}</td>
                @php
                    $gregorianDate = new \DateTime($item["created_at"]);
                    $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                @endphp
                <td colspan="1">
                    <button class="btn btn-sm btn-warning"
                            wire:click="setForm('edit','{{$item->id}}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="{{url("admin/provinces/".$item->id)}}" class="btn btn-sm btn-info"
                       data-id="{{$item->id}}" data-title="{{$item->title}}">
                        <i class="fas fa-link"></i>
                    </a>
                    <button class="btn btn-sm btn-danger" data-id="{{$item->id}}"
                            data-title="{{$item->title}}">
                        <i class="fas fa-trash"></i>
                    </button>
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
                            <input type="text" wire:model="name" class="form-control">
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
