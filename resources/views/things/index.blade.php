@extends('layouts.app')

@section('title', 'Доступные вещи')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Все вещи</h1>
        @auth
            <a href="{{ route('things.create') }}" class="btn btn-primary">
                Добавить вещь
            </a>
        @endauth
    </div>

    @guest
        <div class="alert alert-info mb-4">
            Для управления вещами необходимо 
            <a href="{{ route('login') }}">войти</a> или 
            <a href="{{ route('register') }}">зарегистрироваться</a>
        </div>
    @endguest

    <div class="row">
        @forelse($things as $thing)
            <div class="col-md-4 mb-4">
                @if(Auth::check() && $thing->master == Auth::id())
                    <div class="card h-100 my-thing-card">
                @else
                    <div class="card h-100">
                @endif
                    
                    <div class="card-body" @mything($thing, 'style')>
                        <h5 class="card-title">
                            {{ $thing->name }}
                            @mything($thing, 'badge')
                        </h5>
                        
                        <p class="card-text text-muted">
                            {{ Str::limit($thing->description, 100) }}
                        </p>
                        
                        <!-- ДОБАВИТЬ БЛОК С КОЛИЧЕСТВОМ И ЕДИНИЦЕЙ -->
                        <div class="mb-3">
                            @if($thing->usages->isNotEmpty())
                                @php
                                    $usage = $thing->usages->first();
                                @endphp
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary fs-6 me-2">
                                        {{ $usage->amount ?? 1 }}
                                    </span>
                                    <span class="text-muted">
                                        {{ $usage->unit->abbreviation ?? 'шт' }}
                                    </span>
                                    <small class="text-muted ms-2">
                                        ({{ $usage->unit->name ?? 'Штуки' }})
                                    </small>
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-box"></i> 1 шт (Штуки)
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                Владелец: 
                                @if(Auth::check() && $thing->master == Auth::id())
                                    <span class="text-success fw-bold">Вы</span>
                                @else
                                    {{ $thing->owner->name }}
                                @endif
                            </small>
                        </div>
                        
                        @if($thing->usages->first() && $thing->usages->first()->user)
                            <div class="alert alert-warning py-1 mb-2">
                                <small>
                                    <i class="fas fa-user"></i>
                                    Используется: {{ $thing->usages->first()->user->name }}
                                </small>
                            </div>
                        @endif
                        
                        <!-- ДОБАВИТЬ ИНФОРМАЦИЮ О МЕСТЕ -->
                        @if($thing->usages->first() && $thing->usages->first()->place)
                            <div class="alert alert-info py-1 mb-2">
                                <small>
                                    <i class="fas fa-map-marker-alt"></i>
                                    Место: {{ $thing->usages->first()->place->name }}
                                </small>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-footer bg-transparent d-flex justify-content-between">
                        <div>
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Просмотр
                            </a>
                            
                            @mything($thing)
                                <a href="{{ route('things.edit', $thing) }}" class="btn btn-outline-success btn-sm ms-1">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>
                            @endmything
                        </div>
                        
                        @mything($thing)
                            <form action="{{ route('things.destroy', $thing) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                        onclick="return confirm('Вы уверены, что хотите удалить эту вещь?')">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </form>
                        @endmything
                        
                        @guest
                            <button class="btn btn-outline-secondary btn-sm" disabled title="Требуется вход">
                                <i class="fas fa-hand-paper"></i> Взять
                            </button>
                        @endguest
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Пока нет доступных вещей
                </div>
            </div>
        @endforelse
    </div>

    @if($things->hasPages())
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    @for ($page = 1; $page <= $things->lastPage(); $page++)
                        <li class="page-item {{ $things->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $things->url($page) }}">{{ $page }}</a>
                        </li>
                    @endfor
                </ul>
            </nav>
        </div>
    @endif
</div>
@endsection