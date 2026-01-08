@extends('layouts.app')

@section('title', 'Уведомление')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>{{ $notification->title }}</h4>
            <small class="text-muted">
                {{ $notification->created_at->format('d.m.Y H:i') }}
            </small>
        </div>
        <div class="card-body">
            <p>{{ $notification->message }}</p>
            
            <p><strong>От:</strong> {{ $notification->fromUser->name }}</p>
            <p><strong>Вещь:</strong> 
                <a href="{{ route('things.show', $notification->thing) }}">
                    {{ $notification->thing->name }}
                </a>
            </p>
        </div>
        <div class="card-footer">
            @if(!$notification->read)
            <form action="{{ route('notifications.read', $notification) }}" method="POST" id="mark-read-form">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check me-1"></i> Отметить как ознакомлен
                </button>
                <small class="d-block text-muted mt-1">
                    После нажатия страница обновится и уведомление исчезнет из списка новых
                </small>
            </form>
            @else
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>Вы уже ознакомились с этим уведомлением</strong>
                    <div class="small">
                        {{ $notification->read_at->format('d.m.Y в H:i') }}
                    </div>
                </div>
            @endif
            
            <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Назад к списку
            </a>
        </div>
    </div>
</div>

@if(!$notification->read)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mark-read-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = form.querySelector('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Обработка...';
            button.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Показываем успех
                    button.innerHTML = '<i class="fas fa-check-circle me-1"></i> Готово!';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-secondary');
                    
                    // Обновляем страницу через 1 секунду
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error('Ошибка сервера');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                alert('Ошибка: ' + error.message);
            });
        });
    }
});
</script>
@endif
@endsection