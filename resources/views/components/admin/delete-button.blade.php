@props([
    'action',
    'label' => 'حذف',
    'message' => 'از حذف این رکورد اطمینان دارید؟',
])

<form method="POST" action="{{ $action }}" class="inline-delete-form" data-confirm="{{ $message }}" data-confirm-button="{{ $label }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="listing-icon-btn listing-icon-btn--danger" title="{{ $label }}" aria-label="{{ $label }}">
        <i class="fa fa-trash"></i>
    </button>
</form>
