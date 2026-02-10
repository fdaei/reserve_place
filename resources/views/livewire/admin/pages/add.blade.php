<div >
    <h4 class="col-12">
        @if($id==0)
            افزودن صفحه جدید
        @else
            ویرایش صفحه {{$title}}
        @endif
    </h4>
    <form wire:submit="update" class="row">
        <div wire:ignore.self class="col-8" id="nav-socialmedia" role="tabpanel"
             aria-labelledby="nav-socialmedia-tab">
            <div class="form-group">
                <label>محتوای صفحه</label>
                <textarea style="min-height: 500px" wire:model="text" type="text" class="form-control" required></textarea>
                @error('text')
                <div class="text-danger text-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div wire:ignore.self class="col-4" id="nav-socialmedia" role="tabpanel"
             aria-labelledby="nav-socialmedia-tab">
                <div class="form-group">
                    <label>عنوان صفحه</label>
                    <input wire:model="title" type="text" class="form-control" required>
                    @error('title')
                    <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>آدرس url صفحه</label>
                    <input placeholder="مثال: aboutus" wire:model="urlTitle" type="text" class="form-control"  required>
                    @error('urlTitle')
                    <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>وضعیت </label>
                    <select wire:model="status" type="text" class="form-control" name="status" required>
                        <option value="1">فعال</option>
                        <option value="0">غیرفعال</option>
                    </select>
                    @error('status')
                    <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save"></i>
                    ذخیره تغییرات
                </button>
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
