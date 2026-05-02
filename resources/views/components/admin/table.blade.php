@props([
    'items',
    'columns',
    'resource',
    'config',
])

@if($items->count())
    @php
        $canShow = \App\Support\Admin\AdminResourceRegistry::allows($resource, 'show');
        $canEdit = \App\Support\Admin\AdminResourceRegistry::allows($resource, 'edit');
        $canDelete = \App\Support\Admin\AdminResourceRegistry::allows($resource, 'delete');
        $quickActions = $config['quick_actions'] ?? [];
        $hasActions = $canShow || $canEdit || $canDelete || !empty($config['status_column']) || $quickActions;
    @endphp
    <div class="listing-table-wrap">
        <table class="table responsive-table listing-table">
            <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
                @if($hasActions)
                    <th>عملیات</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    @foreach($columns as $column)
                        <td data-label="{{ $column['label'] }}">
                            @if(($column['type'] ?? null) === 'status')
                                <x-admin.status-badge :value="data_get($item, $column['key'])" />
                            @elseif(($column['type'] ?? null) === 'image')
                                @php($imageValue = \App\Support\Admin\AdminResourceRegistry::displayValue($item, $column))
                                @php($imageUrl = \App\Support\Admin\AdminFileManager::url($imageValue, $column['disk'] ?? 'public', $column['directory'] ?? null))
                                @if($imageUrl && !str_ends_with(strtolower($imageValue), '.pdf'))
                                    <img src="{{ $imageUrl }}" alt="{{ $column['label'] }}" class="admin-table-thumb">
                                @elseif($imageUrl)
                                    <a href="{{ $imageUrl }}" target="_blank" class="toolbar-btn toolbar-btn--light toolbar-btn--sm">مشاهده رسید</a>
                                @else
                                    -
                                @endif
                            @else
                                {{ \App\Support\Admin\AdminResourceRegistry::displayValue($item, $column) }}
                            @endif
                        </td>
                    @endforeach
                    @if($hasActions)
                        <td data-label="عملیات">
                            <div class="listing-actions">
                                @if($canShow)
                                    <a href="{{ route('admin.resources.show', [$resource, $item->id]) }}" class="listing-icon-btn" title="نمایش" aria-label="نمایش">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                @endif
                                @if($canEdit)
                                    <a href="{{ route('admin.resources.edit', [$resource, $item->id]) }}" class="listing-icon-btn" title="ویرایش" aria-label="ویرایش">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                @endif
                                @if($quickActions)
                                    @foreach($quickActions as $action)
                                        <form method="POST" action="{{ route('admin.resources.status', [$resource, $item->id]) }}" class="inline-delete-form" data-confirm="{{ $action['confirm'] ?? 'از انجام این عملیات اطمینان دارید؟' }}" data-confirm-button="{{ $action['label'] }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="field" value="{{ $action['field'] }}">
                                            <input type="hidden" name="status" value="{{ $action['status'] }}">
                                            <button type="submit" class="listing-action-btn listing-action-btn--{{ $action['class'] ?? 'dark' }}">
                                                {{ $action['label'] }}
                                            </button>
                                        </form>
                                    @endforeach
                                @elseif(!empty($config['status_column']))
                                    <form method="POST" action="{{ route('admin.resources.status', [$resource, $item->id]) }}" class="inline-delete-form" data-confirm="وضعیت این رکورد تغییر کند؟" data-confirm-button="تغییر وضعیت">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="listing-action-btn listing-action-btn--info">
                                            تغییر وضعیت
                                        </button>
                                    </form>
                                @endif
                                @if($canDelete)
                                    <x-admin.delete-button :action="route('admin.resources.destroy', [$resource, $item->id])" />
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="listing-pagination">
        <div class="card">
            <div class="card-body">
                {{ $items->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
@else
    <x-admin.empty-state />
@endif
