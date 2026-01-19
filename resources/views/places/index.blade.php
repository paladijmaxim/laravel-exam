@extends('layouts.app')

@section('title', 'Места хранения')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Места хранения</h1>
        <a href="{{ route('places.create') }}" class="btn btn-primary">
            Добавить место
        </a>
    </div>

    <div class="row">
        @forelse($places as $place)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            {{ $place->name }}
                            <div>
                                @if($place->repair)
                                    <span class="badge bg-danger">Ремонт</span>
                                @endif
                                @if($place->work)
                                    <span class="badge bg-warning">В работе</span>
                                @endif
                            </div>
                        </h5>
                        
                        <p class="card-text">{{ $place->description ?? 'Нет описания' }}</p>
                        
                        <p class="card-text">
                            <small class="text-muted">
                                Вещей в хранении: {{ $place->usages_count ?? $place->usages()->count() }}
                            </small>
                        </p>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('places.show', $place) }}" class="btn btn-sm btn-info">
                                Просмотр
                            </a>
                            
                            <a href="{{ route('places.edit', $place) }}" class="btn btn-sm btn-warning">
                                Редактировать
                            </a>
                            
                            <form action="{{ route('places.destroy', $place) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Удалить это место хранения?')">
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
                    Пока нет мест хранения
                </div>
            </div>
        @endforelse
    </div>

    @if($places->hasPages())
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    @for ($page = 1; $page <= $places->lastPage(); $page++)
                        <li class="page-item {{ $places->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $places->url($page) }}">{{ $page }}</a>
                        </li>
                    @endfor
                </ul>
            </nav>
        </div>
    @endif
</div>
@endsection