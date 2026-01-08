@extends('layouts.app')

@section('title', 'Уведомления')

@section('content')
<div class="container py-4">
    <h1>Мои уведомления</h1>
    
    @foreach($notifications as $notification)
        <div class="card mb-3 {{ !$notification->read ? 'border-primary' : '' }}">
            <div class="card-body">
                <h5 class="card-title">
                    {{ $notification->title }}
                    @if(!$notification->read)
                        <span class="badge bg-primary">Новое</span>
                    @endif
                </h5>
                <p class="card-text">{{ $notification->message }}</p>
                <small class="text-muted">
                    От: {{ $notification->fromUser->name }} • 
                    {{ $notification->created_at->format('d.m.Y H:i') }}
                </small>
                <a href="{{ route('notifications.show', $notification) }}" class="btn btn-sm btn-primary float-end">
                    Подробнее
                </a>
            </div>
        </div>
    @endforeach
    
    {{ $notifications->links() }}
</div>
@endsection