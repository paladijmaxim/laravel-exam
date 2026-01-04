@extends('layouts.app')

@section('title', 'Место: ' . $place->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $place->name }}</h4>
                    <div>
                        @if($place->repair)
                            <span class="badge bg-danger">Ремонт/мойка</span>
                        @endif
                        @if($place->work)
                            <span class="badge bg-warning">В работе</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <h5>Описание</h5>
                    <p class="card-text">{{ $place->description ?? 'Нет описания' }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Статистика</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-cube"></i> Вещей в хранении: {{ $place->usages()->count() }}</li>
                                <li><i class="fas fa-calendar"></i> Создано: {{ $place->created_at->format('d.m.Y') }}</li>
                                <li><i class="fas fa-sync"></i> Обновлено: {{ $place->updated_at->format('d.m.Y') }}</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Статус</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-tools"></i> Ремонт/мойка: 
                                    <span class="badge bg-{{ $place->repair ? 'danger' : 'success' }}">
                                        {{ $place->repair ? 'Да' : 'Нет' }}
                                    </span>
                                </li>
                                <li>
                                    <i class="fas fa-briefcase"></i> В работе: 
                                    <span class="badge bg-{{ $place->work ? 'warning' : 'success' }}">
                                        {{ $place->work ? 'Да' : 'Нет' }}
                                    </span>
                                </li>
                                <li>
                                    <i class="fas fa-check-circle"></i> Доступность: 
                                    <span class="badge bg-{{ $place->isAvailable() ? 'success' : 'danger' }}">
                                        {{ $place->isAvailable() ? 'Доступно' : 'Недоступно' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('places.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                        
                        @can('update', $place)
                        <a href="{{ route('places.edit', $place) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            
            <!-- Вещи в этом месте -->
            @if($place->usages()->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Вещи в этом месте</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Вещь</th>
                                    <th>Владелец</th>
                                    <th>Пользователь</th>
                                    <th>Количество</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($place->usages as $usage)
                                <tr>
                                    <td>{{ $usage->thing->name }}</td>
                                    <td>{{ $usage->thing->owner->name }}</td>
                                    <td>{{ $usage->user->name }}</td>
                                    <td>{{ $usage->formatted_amount }}</td>
                                    <td>
                                        <a href="{{ route('things.show', $usage->thing) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    @if($place->isAvailable())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Это место доступно для хранения вещей.
                    </div>
                    @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Это место недоступно для хранения новых вещей.
                    </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('things.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Найти вещи
                        </a>
                        
                        @can('update', $place)
                        <a href="{{ route('places.edit', $place) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Изменить статус
                        </a>
                        @endcan
                        
                        @can('delete', $place)
                        <form action="{{ route('places.destroy', $place) }}" method="POST" 
                              onsubmit="return confirm('Удалить это место хранения?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Удалить место
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection