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
                            <label for="wrnt" class="form-label">Гарантия (дата окончания)</label>
                            <input type="date" class="form-control @error('wrnt') is-invalid @enderror" 
                                   id="wrnt" name="wrnt" value="{{ old('wrnt') }}">
                            @error('wrnt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="place_id" class="form-label">Место хранения</label>
                            <select class="form-control @error('place_id') is-invalid @enderror" 
                                    id="place_id" name="place_id">
                                <option value="">-- Не указывать место --</option>
                                @foreach(App\Models\Place::all() as $place)
                                    <option value="{{ $place->id }}" 
                                        {{ old('place_id') == $place->id ? 'selected' : '' }}>
                                        {{ $place->name }}
                                        @if($place->repair) 
                                            <span class="text-danger">(Ремонт)</span>
                                        @elseif($place->work)
                                            <span class="text-warning">(Работа)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Можно добавить вещь сразу в место хранения</div>
                            @error('place_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Единица измерения</label>
                            <select class="form-control @error('unit_id') is-invalid @enderror" 
                                    id="unit_id" name="unit_id">
                                <option value="">-- Выберите единицу измерения --</option>
                                @foreach(App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}" 
                                        {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->display }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">В чем измеряется эта вещь</div>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                                
                        <div class="mb-3">
                            <label for="amount" class="form-label">Количество</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', 1) }}" min="1" step="0.01">
                            <div class="form-text">Сколько единиц этой вещи</div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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