@extends('layouts.app')

@section('title', 'Вещи в работе')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1> Вещи в работе</h1>
    </div>

    <div class="row">
        @forelse($things as $thing)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $thing->name }}</h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($thing->description, 100) }}
                        </p>
                        
                        <ul class="list-unstyled">
                            <li><strong>Владелец:</strong> {{ $thing->owner->name }}</li>
                            <li><strong>Гарантия:</strong> 
                                {{ $thing->wrnt ? $thing->wrnt->format('d.m.Y') : 'нет' }}
                            </li>
                            @if($thing->currentUsage())
                                <li><strong>Пользователь:</strong> {{ $thing->currentUser()->name }}</li>
                                <li><strong>Место:</strong> {{ $thing->currentPlace()->name }}</li>
                                <li><strong>Количество:</strong> {{ $thing->currentUsage()->formatted_amount }}</li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{ route('things.show', $thing) }}" class="btn btn-sm btn-info">
                             Подробнее
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Нет вещей в работе.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
    {{ $things->onEachSide(1)->links('pagination::simple-bootstrap-5') }}

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
</div>
@endsection