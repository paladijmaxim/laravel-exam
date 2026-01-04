@extends('layouts.app')

@section('title', 'Добавить место хранения')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Добавить новое место хранения</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('places.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="repair" name="repair" 
                                           {{ old('repair') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="repair">
                                        <i class="fas fa-tools"></i> Место на ремонте/мойке
                                    </label>
                                </div>
                                <div class="form-text">Вещи в этом месте будут отмечены как находящиеся в ремонте</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="work" name="work" 
                                           {{ old('work') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="work">
                                        <i class="fas fa-briefcase"></i> Место в работе
                                    </label>
                                </div>
                                <div class="form-text">Вещи в этом месте будут отмечены как находящиеся в работе</div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Если место отмечено как "ремонт" или "работа", вещи в нем будут попадать 
                            в соответствующие разделы фильтрации.
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('places.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад к списку
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить место
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection