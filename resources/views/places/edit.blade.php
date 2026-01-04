@extends('layouts.app')

@section('title', 'Редактировать место: ' . $place->name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Редактировать место: {{ $place->name }}</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('places.update', $place) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $place->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $place->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="repair" name="repair" 
                                           {{ old('repair', $place->repair) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="repair">
                                        <i class="fas fa-tools"></i> Место на ремонте/мойке
                                    </label>
                                </div>
                                <div class="form-text">Вещи в этом месте будут отмечены как находящиеся в ремонте</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="work" name="work" 
                                           {{ old('work', $place->work) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="work">
                                        <i class="fas fa-briefcase"></i> Место в работе
                                    </label>
                                </div>
                                <div class="form-text">Вещи в этом месте будут отмечены как находящиеся в работе</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Статистика</label>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-cube"></i> Вещей в этом месте: {{ $place->usages()->count() }}</li>
                                <li><i class="fas fa-calendar"></i> Создано: {{ $place->created_at->format('d.m.Y') }}</li>
                            </ul>
                        </div>
                        
                        @if($place->usages()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            В этом месте находятся вещи. Изменение статуса "ремонт/работа" повлияет 
                            на отображение этих вещей в соответствующих разделах.
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('places.show', $place) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад
                            </a>
                            
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    @can('delete', $place)
                    <hr>
                    
                    <div class="mt-3">
                        <form action="{{ route('places.destroy', $place) }}" method="POST" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить это место хранения?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Удалить место
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection