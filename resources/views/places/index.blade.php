@extends('layouts.app')

@section('title', 'Места хранения')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Места хранения</h1>
        <a href="{{ route('places.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Добавить место
        </a>
    </div>

    <div class="row">
        @foreach($places as $place)
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
                                Вещей в хранении: {{ $place->usages()->count() }}
                            </small>
                        </p>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('places.show', $place) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="{{ route('places.edit', $place) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('places.destroy', $place) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Удалить это место хранения?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $places->links() }}
    </div>
</div>
@endsection