@extends('layouts.app')

@section('title', $thing->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>{{ $thing->name }}</h3>
                </div>
                
                <div class="card-body">
                    <p><strong>Описание:</strong> {{ $thing->description ?? 'Нет описания' }}</p>
                    <p><strong>Гарантия/срок годности:</strong> 
                        {{ $thing->wrnt ? $thing->wrnt->format('d.m.Y') : 'Нет гарантии' }}
                    </p>
                    <p><strong>Владелец:</strong> {{ $thing->owner->name }}</p>
                    
                    @if($currentUsage)
                        <div class="alert alert-warning">
                            <h5>Текущее использование</h5>
                            <p><strong>Пользователь:</strong> {{ $currentUsage->user->name }}</p>
                            <p><strong>Место хранения:</strong> {{ $currentUsage->place->name }}</p>
                            <p><strong>Количество:</strong> {{ $currentUsage->amount }}</p>
                            <p><strong>С:</strong> {{ $currentUsage->created_at->format('d.m.Y H:i') }}</p>
                            
                            @if($thing->master == Auth::id())
                                <form action="{{ route('things.return', $thing) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        Вернуть вещь
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-success">
                            Вещь доступна для передачи
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('things.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад
                    </a>
                    
                    @if($thing->master == Auth::id() && !$currentUsage)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
                            <i class="fas fa-exchange-alt"></i> Передать вещь
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>История использования</h5>
                </div>
                
                <div class="card-body">
                    @if($thing->usages->count() > 0)
                        <div class="list-group">
                            @foreach($thing->usages as $usage)
                                <div class="list-group-item">
                                    <small class="text-muted">
                                        {{ $usage->created_at->format('d.m.Y H:i') }}
                                    </small>
                                    <p class="mb-1">
                                        <strong>{{ $usage->user->name }}</strong> 
                                        взял(а) {{ $usage->amount }} шт.
                                    </p>
                                    <small>
                                        Место: {{ $usage->place->name }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Нет истории использования</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal для передачи вещи -->
@if($thing->master == Auth::id() && !$currentUsage)
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Передать вещь в пользование</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('things.transfer', $thing) }}" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Пользователь</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Выберите пользователя</option>
                            @foreach(\App\Models\User::where('id', '!=', Auth::id())->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="place_id" class="form-label">Место хранения</label>
                        <select name="place_id" id="place_id" class="form-select" required>
                            <option value="">Выберите место</option>
                            @foreach(\App\Models\Place::where('repair', false)
                                ->where('work', false)
                                ->get() as $place)
                                <option value="{{ $place->id }}">{{ $place->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Количество</label>
                        <input type="number" name="amount" id="amount" 
                               class="form-control" value="1" min="1" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Передать</button>
                </div>
                @if($thing->master == Auth::id() && !$thing->isInUse())
    <a href="{{ route('things.transfer.form', $thing) }}" class="btn btn-primary">
        <i class="fas fa-exchange-alt"></i> Передать вещь
    </a>
@endif
            </form>
        </div>
    </div>
</div>
@endif
@endsection