@extends('layouts.app')

@section('title', 'Взятые мной вещи')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-handshake"></i> Взятые мной вещи</h1>
    </div>

    <div class="row">
        @forelse($usages as $usage)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $usage->thing->name }}</h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($usage->thing->description, 100) }}
                        </p>
                        
                        <ul class="list-unstyled">
                            <li><strong>Владелец:</strong> {{ $usage->thing->owner->name }}</li>
                            <li><strong>Гарантия:</strong> 
                                {{ $usage->thing->wrnt ? $usage->thing->wrnt->format('d.m.Y') : 'нет' }}
                            </li>
                            <li><strong>Место хранения:</strong> {{ $usage->place->name }}</li>
                            <li><strong>Количество:</strong> {{ $usage->formatted_amount }}</li>
                            <li><strong>Взято:</strong> {{ $usage->created_at->format('d.m.Y H:i') }}</li>
                        </ul>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{ route('things.show', $usage->thing) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Подробнее
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Вы пока не взяли ни одной вещи.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $usages->links() }}
    </div>
</div>
@endsection