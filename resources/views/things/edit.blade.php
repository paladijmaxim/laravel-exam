@extends('layouts.app')

@section('title', 'Редактировать вещь: ' . $thing->name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @can('update', $thing)
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Редактировать вещь: {{ $thing->name }}
                    </h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('things.update', $thing) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $thing->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Основное описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $thing->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Изменение этого поля также отправит уведомление владельцу и назначенному пользователю.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="wrnt" class="form-label">Гарантия/срок годности</label>
                            <input type="date" class="form-control @error('wrnt') is-invalid @enderror" 
                                   id="wrnt" name="wrnt" value="{{ old('wrnt', $thing->wrnt ? $thing->wrnt->format('Y-m-d') : '') }}">
                            @error('wrnt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Укажите дату окончания гарантии или срока годности</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Владелец</label>
                            <input type="text" class="form-control" value="{{ $thing->owner->name }}" disabled>
                            <div class="form-text">Владелец вещи не может быть изменен</div>
                        </div>
                        
                        @if($thing->isInUse())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Эта вещь сейчас используется пользователем: 
                            <strong>
                                @php
                                    $currentUsage = $thing->currentUsage();
                                    $user = $currentUsage ? $currentUsage->user : null;
                                @endphp
                                {{ $user ? $user->name : 'Неизвестно' }}
                            </strong>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад к вещи
                            </a>
                            
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    @if($thing->master == Auth::id() || Auth::user()->isAdmin())
                    <hr>
                    
                    <div class="mt-3">
                        <h5 class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Опасная зона
                        </h5>
                        <form action="{{ route('things.destroy', $thing) }}" method="POST" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить эту вещь? Это действие нельзя отменить.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Удалить вещь
                            </button>
                            <div class="form-text mt-2">
                                Удаление вещи также удалит всю историю использования и описания.
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="alert alert-danger">
                <i class="fas fa-ban"></i>
                <strong>Доступ запрещен!</strong>
                У вас нет прав для редактирования этой вещи. 
                Редактировать может только владелец вещи.
            </div>
            <a href="{{ route('things.show', $thing) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Назад к вещи
            </a>
            @endcan
        </div>
    </div>
</div>
@endsection