@extends('layouts.app')

@section('title', 'Архив: ' . $archivedThing->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center {{ $archivedThing->restored ? 'bg-success text-white' : 'bg-warning' }}">
                    <h4 class="mb-0">
                        <i class="fas fa-archive"></i> Архивная запись: {{ $archivedThing->name }}
                        @if($archivedThing->restored)
                            <span class="badge bg-light text-dark ms-2">Восстановлена</span>
                        @endif
                    </h4>
                    <a href="{{ route('archived.index') }}" class="btn btn-sm {{ $archivedThing->restored ? 'btn-light' : 'btn-secondary' }}">
                        <i class="fas fa-arrow-left"></i> Назад
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Основная информация</h5>
                            <ul class="list-unstyled">
                                <li><strong>Название:</strong> {{ $archivedThing->name }}</li>
                                <li><strong>Описание:</strong> {{ $archivedThing->description ?? 'Нет описания' }}</li>
                                <li><strong>Гарантия до:</strong> {{ $archivedThing->wrnt ? $archivedThing->wrnt->format('d.m.Y') : 'Нет гарантии' }}</li>
                                <li><strong>Количество:</strong> {{ $archivedThing->formatted_amount }}</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Владелец</h5>
                            <ul class="list-unstyled">
                                <li><strong>Имя:</strong> {{ $archivedThing->owner_name }}</li>
                                <li><strong>Email:</strong> {{ $archivedThing->owner_email }}</li>
                            </ul>
                            
                            @if($archivedThing->last_user_name)
                            <h5 class="mt-3">Последний пользователь</h5>
                            <ul class="list-unstyled">
                                <li><strong>Имя:</strong> {{ $archivedThing->last_user_name }}</li>
                                <li><strong>Email:</strong> {{ $archivedThing->last_user_email }}</li>
                            </ul>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Место хранения</h5>
                            <ul class="list-unstyled">
                                <li><strong>Название:</strong> {{ $archivedThing->place_name ?? 'Не указано' }}</li>
                                <li><strong>Описание:</strong> {{ $archivedThing->place_description ?? 'Нет описания' }}</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Даты</h5>
                            <ul class="list-unstyled">
                                <li><strong>Удалена:</strong> {{ $archivedThing->formatted_deleted_at }}</li>
                                @if($archivedThing->restored)
                                    <li><strong>Восстановлена:</strong> {{ $archivedThing->formatted_restored_at }}</li>
                                    <li><strong>Кем:</strong> {{ $archivedThing->restored_by_name }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                    @if($archivedThing->metadata)
                    <div class="mt-3">
                        <h5>Техническая информация</h5>
                        <pre class="bg-light p-3 small">{{ json_encode($archivedThing->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('archived.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> К списку архива
                        </a>
                        
                        <div>
                            @if(!$archivedThing->restored)
                                <form action="{{ route('archived.restore', $archivedThing) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Восстановить эту вещь? Вы станете ее новым владельцем.')">
                                        <i class="fas fa-undo"></i> Восстановить вещь
                                    </button>
                                </form>
                            @endif
                            
                            @can('admin')
                                <form action="{{ route('archived.force-delete', $archivedThing) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Удалить эту запись из архива навсегда?')">
                                        <i class="fas fa-times"></i> Удалить навсегда
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Статус</h5>
                </div>
                <div class="card-body">
                    @if($archivedThing->restored)
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Вещь восстановлена</h6>
                            <p class="mb-0">
                                Эта вещь была восстановлена {{ $archivedThing->formatted_restored_at }}<br>
                                Новым владельцем стал: <strong>{{ $archivedThing->restored_by_name }}</strong>
                            </p>
                        </div>
                        <p>
                            <i class="fas fa-lightbulb"></i> Восстановленная вещь доступна в общем списке вещей.
                        </p>
                    @else
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-trash"></i> Вещь в архиве</h6>
                            <p class="mb-0">
                                Удалена: {{ $archivedThing->formatted_deleted_at }}<br>
                                Можно восстановить
                            </p>
                        </div>
                        <p>
                            <i class="fas fa-lightbulb"></i> При восстановлении вы станете новым владельцем этой вещи.
                        </p>
                    @endif
                    
                    <hr>
                    
                    <h6>Что происходит при восстановлении:</h6>
                    <ul class="small">
                        <li>Создается новая вещь с теми же данными</li>
                        <li>Вы становитесь владельцем восстановленной вещи</li>
                        <li>Запись в архиве помечается как восстановленная</li>
                        <li>Оригинальная информация об архиве сохраняется</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection