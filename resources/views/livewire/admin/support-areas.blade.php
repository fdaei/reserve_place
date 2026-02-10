<div class="section">
    <h2>
        <i class="fas fa-users-cog"></i>  دسته بندی پیام ها
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
            <th colspan="1">وضعیت</th>
            <th colspan="1">عملیات</th>
        </tr>
        </thead>
        <tbody style="width: 100%">
        @foreach($list as $item)

            <tr style="width: 100%">
                <td colspan="1">{{$item->id}}</td>
                <td colspan="1">{{$item->title}}</td>
                <td colspan="1">{{$item->status?"فعال":"غیرفعال"}}</td>
                @php
                    $gregorianDate = new \DateTime($item["created_at"]);
                    $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                @endphp
                <td colspan="1">
                    <button class="btn btn-sm btn-warning"
                            wire:click="setForm('edit','{{$item->id}}')">
                        <i class="fas fa-edit"></i>
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
                            افزودن دسته بندی
                        @else
                            ویرایش دسته بندی
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
                    <div class="col-12 mt-2">
                        <p class="m-0">وضعیت</p>
                        <select wire:model="status" class="form-control form-control-sm " style="height: 40px">
                            <option value="1">فعال</option>
                            <option value="0">غیرفعال</option>
                        </select>
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
