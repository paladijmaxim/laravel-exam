@extends('layouts.app')

@section('title', 'Передача вещи')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Передача вещи: {{ $thing->name }}</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('things.transfer', $thing) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Пользователь *</label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Выберите пользователя</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="place_id" class="form-label">Место хранения *</label>
                            <select name="place_id" id="place_id" class="form-select @error('place_id') is-invalid @enderror" required>
                                <option value="">Выберите место хранения</option>
                                @foreach($places as $place)
                                    <option value="{{ $place->id }}" {{ old('place_id') == $place->id ? 'selected' : '' }}>
                                        {{ $place->name }}
                                        @if($place->repair)
                                            <span class="text-danger"> (Ремонт)</span>
                                        @endif
                                        @if($place->work)
                                            <span class="text-warning"> (В работе)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('place_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Количество *</label>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', 1) }}" min="1" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="unit_id" class="form-label">Единица измерения</label>
                                <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                                    <option value="">Выберите единицу</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->abbreviation }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            После передачи вещь будет доступна выбранному пользователю.
                            Вы сможете вернуть ее в любое время.
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('things.show', $thing) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-exchange-alt"></i> Передать вещь
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection