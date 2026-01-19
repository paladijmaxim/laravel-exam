@extends('layouts.app')

@section('title', 'Мои вещи')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Мои вещи</h1>
        <a href="{{ route('things.create') }}" class="btn btn-primary">
            Добавить вещь
        </a>
    </div>

    <div class="row">
        @forelse($things as $thing)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $thing->name }}</h5>
                        <p class="card-text text-muted">
                            @if($thing->currentDescription)
                                {{ \Illuminate\Support\Str::limit($thing->currentDescription->description, 100) }}
                            @elseif($thing->description)
                                {{ \Illuminate\Support\Str::limit($thing->description, 100) }}
                            @else
                                Нет описания
                            @endif
                        </p>
                        
                        <ul class="list-unstyled">
                            <li><strong>Гарантия:</strong> 
                                {{ $thing->wrnt ? $thing->wrnt->format('d.m.Y') : 'нет' }}
                            </li>
                            <li>
                                <strong>Статус:</strong>
                                @if($thing->isInUse())
                                    @php
                                        $usage = $thing->currentUsage();
                                    @endphp
                                    <span class="badge bg-warning">
                                        У пользователя: {{ $usage->user->name }}
                                    </span>
                                @else
                                    <span class="badge bg-success">Доступна</span>
                                @endif
                            </li>
                            @if($thing->isInUse())
                                <li><strong>Место:</strong> {{ $thing->currentPlace()->name }}</li>
                                <li><strong>Количество:</strong> {{ $thing->currentUsage()->formatted_amount }}</li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-sm btn-info">
                                Просмотр
                            </a>
                            
                            <a href="{{ route('things.edit', $thing) }}" class="btn btn-sm btn-warning">
                                Редактировать
                            </a>
                            
                            <form action="{{ route('things.destroy', $thing) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Удалить эту вещь?')">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    У вас пока нет вещей. <a href="{{ route('things.create') }}">Создать первую вещь</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $things->onEachSide(1)->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection