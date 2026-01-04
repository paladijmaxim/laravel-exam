@extends('layouts.app')

@section('title', 'Вещи в ремонте')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tools"></i> Вещи в ремонте/мойке</h1>
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
                            <i class="fas fa-eye"></i> Подробнее
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Нет вещей в ремонте/мойке.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $things->links() }}
    </div>
</div>
@endsection