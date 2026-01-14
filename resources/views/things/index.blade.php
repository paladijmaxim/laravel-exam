@extends('layouts.app')

@section('title', 'Доступные вещи')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-boxes"></i> Все вещи</h1>
        @auth
            <a href="{{ route('things.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Добавить вещь
            </a>
        @endauth
    </div>

    @guest
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> Для управления вещами необходимо 
            <a href="{{ route('login') }}">войти</a> или 
            <a href="{{ route('register') }}">зарегистрироваться</a>
        </div>
    @endguest

    <div class="row">
        @forelse($things as $thing)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $thing->name }}</h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($thing->description, 100) }}
                        </p>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> Владелец: {{ $thing->owner->name }}
                            </small>
                        </div>
                        @if($thing->usages->first() && $thing->usages->first()->user)
                            <div class="alert alert-warning py-1 mb-2">
                                <small>
                                    <i class="fas fa-user-check"></i> 
                                    Используется: {{ $thing->usages->first()->user->name }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent d-flex justify-content-between">
                        <div>
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @auth
                                @if(Auth::id() == $thing->master)
                                    <a href="{{ route('things.edit', $thing) }}" class="btn btn-outline-success btn-sm ms-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            @endauth
                        </div>
                        
                        <!-- КНОПКА УДАЛЕНИЯ -->
                        @auth
                            @if(Auth::id() == $thing->master)
                                <form action="{{ route('things.destroy', $thing) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                            onclick="return confirm('Вы уверены, что хотите удалить эту вещь?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        @endauth
                        
                        @guest
                            <button class="btn btn-outline-secondary btn-sm" disabled title="Требуется вход">
                                <i class="fas fa-handshake"></i>
                            </button>
                        @endguest
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Пока нет доступных вещей
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $things->links() }}
    </div>
    
</div>
@endsection