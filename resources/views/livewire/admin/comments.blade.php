<div class="section">
    <h2>
        <i class="fas fa-users-cog"></i> مدیریت نظرات
    </h2>
    <div class="" id="collapseExample">
        <div class="card card-body">

            <div class="row flex-nowrap flex-row justify-content-between">
                <div style="margin: 4px">
                    <label>امتیاز:
                        <input type="text" wire:model.live="search" class="form-control">
                    </label>
                </div>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>نام</th>
            <th>اقامتگاه</th>
            <th>امتیاز</th>
            <th>تاریخ ثبت</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $item)

            <tr>
                <td>{{$item->id}}</td>
                <td>
                    @if(isset($users[$item->user_id]))
                        {{$users[$item->user_id]->name}} {{$users[$item->user_id]->family}}
                    @endif
                </td>
                <td>
                    @if($item->residence_id!=null)
                        @if(isset($villas[$item->residence_id]))
                            {{$villas[$item->residence_id]->title}}
                        @endif
                    @endif
                    @if($item->store_id!=null)
                        @if(isset($stores[$item->store_id]))
                            {{$stores[$item->residence_id]->title}}
                        @endif
                    @endif
                </td>
                <td>
                    <i class="text-warning fa fa-star"></i>
                    {{$item->point}}
                </td>
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
                    <button class="btn btn-sm btn-warning"
                            wire:click="setForm('edit','{{$item->id}}')">
                        <i class="fas fa-edit"></i>
                    </button>
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
                            افزودن
                        @else
                            ویرایش
                        @endif
                    </h5>
                    <span wire:click="setForm('empty')" type="button" class="close"
                          data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div style="margin: 4px">
                        <label>امتیاز:
                            <input type="number" wire:model="point" min="0" max="5"
                                   class="form-control">
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
                        console.log(data.name); // خروجی: blog-component
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
