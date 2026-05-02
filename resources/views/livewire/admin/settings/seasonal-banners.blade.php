@push('head')
    <style>
        .season-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .season-card {
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }

        .season-card-image {
            aspect-ratio: 16 / 7;
            background: #f8fafc;
            border-bottom: 1px solid var(--admin-border);
            overflow: hidden;
        }

        .season-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .season-card-body {
            display: grid;
            gap: 12px;
            padding: 16px;
        }

        .season-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .season-card-head h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        @media (max-width: 900px) {
            .season-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section listing-panel">
    <div class="listing-panel-head">
        <div>
            <h2 class="listing-panel-title">
                <span class="listing-panel-icon"><i class="fa fa-leaf"></i></span>
                بنرهای فصلی
            </h2>
            <p class="admin-page-description">برای هر فصل تصویر، عنوان و توضیح جداگانه ذخیره می‌شود.</p>
        </div>
    </div>

    <div class="season-grid">
        @foreach($seasons as $season => $label)
            <form class="season-card" wire:submit="saveSeason('{{ $season }}')">
                <div class="season-card-image">
                    @if(!empty($images[$season]))
                        <img src="{{ $images[$season]->temporaryUrl() }}" alt="{{ $label }}">
                    @elseif(!empty($storedImages[$season]) && ($url = \App\Support\Admin\AdminFileManager::url($storedImages[$season])))
                        <img src="{{ $url }}" alt="{{ $label }}">
                    @endif
                </div>
                <div class="season-card-body">
                    <div class="season-card-head">
                        <h3>{{ $label }}</h3>
                        <button type="submit" class="toolbar-btn toolbar-btn--success">ذخیره</button>
                    </div>

                    <div class="admin-form-field">
                        <label>تصویر</label>
                        <input type="file" wire:model="images.{{ $season }}" class="form-control" accept="image/*">
                        @error("images.{$season}")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="admin-form-field">
                        <label>عنوان</label>
                        <input type="text" wire:model.defer="titles.{{ $season }}" class="form-control">
                        @error("titles.{$season}")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="admin-form-field">
                        <label>توضیحات</label>
                        <textarea wire:model.defer="descriptions.{{ $season }}" class="form-control"></textarea>
                        @error("descriptions.{$season}")<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
            </form>
        @endforeach
    </div>

    @script
    <script>
        Livewire.on('season-saved', () => {
            Toast.fire({ icon: 'success', title: 'بنر فصل ذخیره شد' });
        });
    </script>
    @endscript
</div>
