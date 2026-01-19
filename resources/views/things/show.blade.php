@extends('layouts.app')

@section('title', $thing->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ $thing->name }}</h3>
                        <div>
                            @can('update', $thing)
                            <a href="{{ route('things.edit', $thing) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <p><strong>Текущее описание:</strong> 
                        @php
                            $currentDesc = $thing->descriptions->where('is_current', true)->first();
                        @endphp
                        
                        {{ $currentDesc ? $currentDesc->description : ($thing->description ?? 'Нет описания') }}
                    </p>
                    <p><strong>Владелец:</strong> {{ $thing->owner->name }}</p>
                    
                    @if($currentUsage)
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-shopping-cart"></i> Текущее использование</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Пользователь:</strong> {{ $currentUsage->user->name }}</p>
                                    <p><strong>Место хранения:</strong> {{ $currentUsage->place->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <!-- ДОБАВИТЬ БЛОК С КОЛИЧЕСТВОМ И ЕДИНИЦЕЙ -->
                                    <p><strong>Количество:</strong></p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary fs-5 me-2">
                                            {{ $currentUsage->amount }}
                                        </span>
                                        <div>
                                            <div class="fw-bold">{{ $currentUsage->unit->abbreviation ?? 'шт' }}</div>
                                            <small class="text-muted">
                                                {{ $currentUsage->unit->name ?? 'Штуки' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0">
                                <strong>С:</strong> {{ $currentUsage->created_at->format('d.m.Y H:i') }}
                            </p>
                            
                            @if($thing->master == Auth::id())
                                <form action="{{ route('things.return', $thing) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-undo"></i> Вернуть вещь
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Вещь доступна для передачи
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('things.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                    
                    @if($thing->master == Auth::id() && !$currentUsage)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
                            <i class="fas fa-exchange-alt"></i> Передать вещь
                        </button>
                    @endif
                </div>
            </div>

            <!-- Блок описаний -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-align-left"></i> История описаний
                        <span class="badge bg-secondary">{{ $thing->descriptions->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($thing->descriptions->count() > 0)
                        <div class="list-group mb-3">
                            @foreach($thing->descriptions as $desc)
                                <div class="list-group-item {{ $desc->is_current ? 'list-group-item-primary' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <p class="mb-1">{{ $desc->description }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> {{ $desc->creator->name }} | 
                                                <i class="fas fa-clock"></i> {{ $desc->created_at->format('d.m.Y H:i') }}
                                                @if($desc->is_current)
                                                    <span class="badge bg-success ms-2">
                                                        <i class="fas fa-star"></i> Текущее
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            @can('update', $thing)
                                                @if(!$desc->is_current)
                                                    <form action="{{ route('things.set-current-description', ['thing' => $thing, 'description' => $desc]) }}" 
                                                          method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-check"></i> Сделать текущим
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <!-- Форма для редактирования описания -->
                                                <button type="button" class="btn btn-sm btn-outline-warning edit-description-btn" 
                                                        data-bs-toggle="modal" data-bs-target="#editDescriptionModal"
                                                        data-id="{{ $desc->id }}" 
                                                        data-description="{{ $desc->description }}">
                                                    <i class="fas fa-edit"></i> Редактировать
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Нет сохраненных описаний
                        </p>
                    @endif

                    @can('update', $thing)
                        <!-- Форма для добавления нового описания -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-plus-circle"></i> Добавить новое описание
                                </h6>
                                <form action="{{ route('things.add-description', $thing) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="new_description" class="form-label">Текст описания</label>
                                        <textarea class="form-control" id="new_description" name="description" 
                                                  rows="3" placeholder="Введите новое описание..." required></textarea>
                                        <div class="form-text">
                                            Новое описание станет текущим. Будет отправлено уведомление владельцу и назначенному пользователю.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Добавить описание
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">

            <!-- Блок гарантии -->
            @if($thing->wrnt)
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Гарантия/срок годности</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <i class="fas fa-calendar-alt"></i> 
                        <strong>До:</strong> {{ $thing->wrnt->format('d.m.Y') }}
                    </p>
                    @php
                        $daysLeft = now()->diffInDays($thing->wrnt, false);
                    @endphp
                    @if($daysLeft > 30)
                        <span class="badge bg-success">Осталось дней: {{ $daysLeft }}</span>
                    @elseif($daysLeft > 0)
                        <span class="badge bg-warning">Осталось дней: {{ $daysLeft }}</span>
                    @else
                        <span class="badge bg-danger">Гарантия истекла</span>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- ДОБАВИТЬ БЛОК ИНФОРМАЦИИ О ЕДИНИЦЕ ИЗМЕРЕНИЯ -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-weight"></i> Единица измерения</h5>
                </div>
                <div class="card-body">
                    @if($currentUsage && $currentUsage->unit)
                        <div class="text-center">
                            <div class="display-4 text-primary mb-2">
                                {{ $currentUsage->unit->abbreviation }}
                            </div>
                            <h4>{{ $currentUsage->unit->name }}</h4>
                            <small class="text-muted">
                                Стандартная единица для этой вещи
                            </small>
                        </div>
                    @else
                        <p class="text-muted">
                            <i class="fas fa-box"></i> 
                            Стандартная единица: Штуки (шт)
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- ДОБАВИТЬ БЛОК ИСТОРИИ ИСПОЛЬЗОВАНИЙ -->
            @if($thing->usages->count() > 1)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history"></i> История количеств</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($thing->usages->take(5) as $usage)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $usage->amount }}</strong>
                                            <small class="text-muted">
                                                {{ $usage->unit->abbreviation ?? 'шт' }}
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-muted">
                                                {{ $usage->created_at->format('d.m.Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    @if($usage->user)
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $usage->user->name }}
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
                    
                    <!-- ДОБАВИТЬ ВЫБОР ЕДИНИЦЫ ИЗМЕРЕНИЯ -->
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Количество</label>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Единица</label>
                                <select name="unit_id" id="unit_id" class="form-select" required>
                                    <option value="">Выберите единицу</option>
                                    @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ $currentUsage && $currentUsage->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->abbreviation }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Передать</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal для редактирования описания -->
@can('update', $thing)
<div class="modal fade" id="editDescriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать описание</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="editDescriptionForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <input type="hidden" id="edit_description_id" name="description_id">
                    <div class="mb-3">
                        <label for="edit_description_text" class="form-label">Текст описания</label>
                        <textarea class="form-control" id="edit_description_text" name="new_description" 
                                  rows="4" required></textarea>
                        <div class="form-text">
                            После сохранения будет отправлено уведомление владельцу и назначенному пользователю.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка кнопок редактирования описания
    const editButtons = document.querySelectorAll('.edit-description-btn');
    const editModal = document.getElementById('editDescriptionModal');
    const editForm = document.getElementById('editDescriptionForm');
    const descriptionIdInput = document.getElementById('edit_description_id');
    const descriptionTextInput = document.getElementById('edit_description_text');
    
    if (editButtons.length > 0 && editModal) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const descriptionId = this.getAttribute('data-id');
                const descriptionText = this.getAttribute('data-description');
                
                descriptionIdInput.value = descriptionId;
                descriptionTextInput.value = descriptionText;
                
                // Устанавливаем action формы - используем things.update-description
                const thingId = {{ $thing->id }};
                editForm.action = `/things/${thingId}/update-description/${descriptionId}`;
            });
        });
    }
    
    // Обработка отправки формы редактирования описания
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            submitButton.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Закрываем модальное окно
                    const modal = bootstrap.Modal.getInstance(editModal);
                    modal.hide();
                    
                    // Обновляем страницу
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || 'Ошибка при сохранении');
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Ошибка при сохранении описания');
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    }
});
</script>
@endpush
@endsection