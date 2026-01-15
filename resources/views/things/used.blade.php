@extends('layouts.app')

@section('title', 'Мои вещи, используемые другими')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users"></i> Мои вещи, используемые другими</h1>
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
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($thing->master == Auth::id())
                                <a href="{{ route('things.edit', $thing) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('things.return', $thing) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Вернуть эту вещь?')">
                                        <i class="fas fa-undo"></i> Вернуть
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    У вас нет вещей, которые используются другими пользователями.
                </div>
            </div>
        @endforelse
    </div>

    @if($things->hasPages())
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    {{-- Показываем только номера страниц --}}
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