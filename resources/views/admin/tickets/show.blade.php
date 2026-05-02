@extends('layouts.admin')

@section('title', 'تیکت #' . $ticket->id)

@section('content')
    <x-admin.page-shell :title="'تیکت #' . $ticket->id" icon="fa-ticket">
        <x-slot:actions>
            <a href="{{ route('admin.tickets.index') }}" class="toolbar-btn toolbar-btn--light">بازگشت</a>
        </x-slot:actions>

        <div class="ticket-thread">
            <div class="ticket-summary">
                <div>
                    <span>عنوان</span>
                    <strong>{{ $ticket->title }}</strong>
                </div>
                <div>
                    <span>کاربر</span>
                    <strong>{{ data_get($ticket, 'user.name', 'کاربر') }} {{ data_get($ticket, 'user.family') }}</strong>
                </div>
                <div>
                    <span>وضعیت</span>
                    <x-admin.status-badge :value="$ticket->status" />
                </div>
            </div>

            <div class="ticket-messages">
                @foreach($ticket->chats as $chat)
                    <article @class(['ticket-message', 'ticket-message--admin' => $chat->user_id === auth()->id()])>
                        <div class="ticket-message-head">
                            <strong>{{ data_get($chat, 'user.name', 'کاربر') }} {{ data_get($chat, 'user.family') }}</strong>
                            <span>{{ $chat->created_at?->format('Y/m/d H:i') }}</span>
                        </div>
                        <p>{{ $chat->message }}</p>
                    </article>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="ticket-reply-form">
                @csrf
                <label for="message">پاسخ مدیر</label>
                <textarea id="message" name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                <div class="admin-form-actions">
                    <button class="btn btn-primary">ثبت پاسخ</button>
                </div>
            </form>
        </div>
    </x-admin.page-shell>
@endsection
