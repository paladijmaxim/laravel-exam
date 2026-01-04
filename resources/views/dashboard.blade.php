@extends('layouts.app')

@section('title', 'Панель управления')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Мои вещи</h5>
                <h2 class="card-text">{{ $stats['my_things'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Взятые вещи</h5>
                <h2 class="card-text">{{ $stats['borrowed_things'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Всего мест</h5>
                <h2 class="card-text">{{ $stats['total_places'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Доступные места</h5>
                <h2 class="card-text">{{ $stats['available_places'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Мои вещи</h5>
            </div>
            <div class="card-body">
                @if($myThings->count() > 0)
                    <div class="list-group">
                        @foreach($myThings as $thing)
                            <a href="{{ route('things.show', $thing) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $thing->name }}</h6>
                                    <small>
                                        @if($thing->usages->count() > 0)
                                            <span class="badge bg-warning">В использовании</span>
                                        @else
                                            <span class="badge bg-success">Доступна</span>
                                        @endif
                                    </small>
                                </div>
                                <small class="text-muted">
                                    {{ Str::limit($thing->description, 50) }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">У вас пока нет вещей</p>
                @endif
                
                <div class="mt-3">
                    <a href="{{ route('things.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Добавить вещь
                    </a>
                    <a href="{{ route('things.my') }}" class="btn btn-sm btn-outline-primary">
                        Все мои вещи
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Взятые мной вещи</h5>
            </div>
            <div class="card-body">
                @if($borrowedThings->count() > 0)
                    <div class="list-group">
                        @foreach($borrowedThings as $usage)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $usage->thing->name }}</h6>
                                    <small>{{ $usage->amount }} шт.</small>
                                </div>
                                <p class="mb-1">
                                    <small>Владелец: {{ $usage->thing->owner->name }}</small><br>
                                    <small>Место: {{ $usage->place->name }}</small>
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Вы пока не взяли ни одной вещи</p>
                @endif
                
                <div class="mt-3">
                    <a href="{{ route('things.index') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-search"></i> Найти вещи
                    </a>
                    <a href="{{ route('things.borrowed') }}" class="btn btn-sm btn-outline-success">
                        Все взятые вещи
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection