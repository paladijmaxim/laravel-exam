@extends('layouts.app')

@section('title', 'Добавить вещь')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Добавить новую вещь</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('things.store') }}">
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
                        
                        <div class="mb-3">
                            <label for="wrnt" class="form-label">Гарантия/срок годности</label>
                            <input type="date" class="form-control @error('wrnt') is-invalid @enderror" 
                                   id="wrnt" name="wrnt" value="{{ old('wrnt') }}">
                            @error('wrnt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Укажите дату окончания гарантии или срока годности</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            После создания вещи вы сможете передать ее другому пользователю или указать место хранения.
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('things.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад к списку
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить вещь
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection