@extends('layouts.app')

@section('title', 'Общий список вещей')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-list"></i> Общий список вещей</h1>
        @can('create', App\Models\Thing::class)
        <a href="{{ route('things.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Добавить вещь
        </a>
        @endcan
    </div>

    @if($things->count() > 0)
    <div class="row">
        @foreach($things as $thing)
            <div class="col-md-4 mb-4">
                {{-- ВСЁ В ОДНОЙ ДИРЕКТИВЕ! --}}
                <div class="card h-100" @mything($thing, 'style')>
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cube @mything($thing, 'icon')"></i> 
                            {{ $thing->name }}
                            @mything($thing, 'badge')
                        </h5>
                    </div>
                    
                    <div class="card-body @mything($thing, 'class')">
                        <p class="card-text text-muted">
                            {{ $thing->description ?: 'Нет описания' }}
                        </p>
                        
                        <ul class="list-unstyled">
                            <li>
                                <strong>Владелец:</strong> 
                                @mything($thing)
                                    <span class="text-success fw-bold">
                                        <i class="fas fa-user-check"></i> Вы
                                    </span>
                                @else
                                    {{ $thing->owner->name }}
                                @endmything
                            </li>
                            <li>
                                <strong>Гарантия:</strong> 
                                {{ $thing->wrnt ? $thing->wrnt->format('d.m.Y') : 'нет' }}
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            {{-- Используем как условие (без параметра) --}}
                            @mything($thing)
                                <div class="btn-group">
                                    <a href="{{ route('things.edit', $thing) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('things.destroy', $thing) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Удалить?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endmything
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection