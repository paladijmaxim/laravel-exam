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
            {{-- ДИРЕКТИВА ДЛЯ СПЕЦИАЛЬНЫХ МЕСТ --}}
            <div class="card h-100 @specialthing($thing)" @mything($thing, 'style')>
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cube @mything($thing, 'icon')"></i> 
                        {{ $thing->name }}
                        @mything($thing, 'badge')
                        
                        {{-- Бейдж для специальных мест --}}
                        @php
                            $status = $thing->isInSpecialPlace();
                        @endphp
                        @if($status === 'repair')
                            <span class="badge bg-danger ms-2"><i class="fas fa-tools"></i> Ремонт</span>
                        @elseif($status === 'work')
                            <span class="badge bg-warning ms-2"><i class="fas fa-briefcase"></i> Работа</span>
                        @endif
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
                            <strong>Место:</strong>
                            @if($thing->isInUse() && $thing->currentPlace())
                                {{ $thing->currentPlace()->name }}
                            @else
                                <span class="text-muted">Не используется</span>
                            @endif
                        </li>
                    </ul>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('things.show', $thing) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        
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

<style>
    /* ДОБАВЛЯЕМ СТИЛИ ДЛЯ НОВОЙ ДИРЕКТИВЫ */
    .thing-repair {
        border: 2px solid #dc3545 !important;
        background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
    }
    
    .thing-work {
        border: 2px solid #ffc107 !important;
        background: linear-gradient(135deg, #fff9db 0%, #ffec99 100%);
    }
    
    .thing-repair .card-header {
        background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
        color: white;
    }
    
    .thing-work .card-header {
        background: linear-gradient(135deg, #ffc107 0%, #fab005 100%) !important;
        color: #212529;
    }
</style>
@endsection