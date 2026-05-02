@props([
    'title' => 'رکوردی پیدا نشد',
    'description' => 'فیلترها را تغییر دهید یا رکورد جدید ثبت کنید.',
])

<div class="admin-empty-state">
    <h4>{{ $title }}</h4>
    <p>{{ $description }}</p>
</div>
