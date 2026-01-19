@extends('layouts.app')

@section('title', 'Архив удаленных вещей')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1> Архив удаленных вещей</h1>
        <a href="{{ route('things.index') }}" class="btn btn-secondary">
            Назад к вещам
        </a>
    </div>

    @if($archivedThings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Название</th>
                        <th>Бывший владелец</th>
                        <th>Последний пользователь</th>
                        <th>Место хранения</th>
                        <th>Количество</th>
                        <th>Дата удаления</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archivedThings as $archived)
                        <tr class="{{ $archived->restored ? 'table-success' : 'table-light' }}">
                            <td>
                                <strong>{{ $archived->name }}</strong>
                                @if($archived->description)
                                    <br><small class="text-muted">{{ Str::limit($archived->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $archived->owner_name }}<br>
                                <small class="text-muted">{{ $archived->owner_email }}</small>
                            </td>
                            <td>
                                @if($archived->last_user_name)
                                    {{ $archived->last_user_name }}<br>
                                    <small class="text-muted">{{ $archived->last_user_email }}</small>
                                @else
                                    <span class="text-muted">Не использовалась</span>
                                @endif
                            </td>
                            <td>{{ $archived->place_full }}</td>
                            <td>{{ $archived->formatted_amount }}</td>
                            <td>{{ $archived->formatted_deleted_at }}</td>
                            <td>
                                @if($archived->restored)
                                    <span class="badge bg-success">
                                         Восстановлена
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $archived->restored_by_name }}<br>
                                        {{ $archived->formatted_restored_at }}
                                    </small>
                                @else
                                    <span class="badge bg-warning">
                                         В архиве
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('archived.show', $archived) }}" 
                                       class="btn btn-info" title="Подробнее">
                                        Подробнее
                                    </a>
                                    
                                    @if(!$archived->restored)
                                        <form action="{{ route('archived.restore', $archived) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" 
                                                    title="Восстановить"
                                                    onclick="return confirm('Восстановить эту вещь? Вы станете ее новым владельцем.')">
                                                Восстановить
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @can('admin')
                                    <form action="{{ route('archived.force-delete', $archived) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                title="Удалить навсегда"
                                                onclick="return confirm('Удалить эту запись из архива навсегда? Это действие нельзя отменить.')">
                                            
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $archivedThings->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Архив пуст. Здесь будут появляться удаленные вещи.
        </div>
    @endif
</div>
@endsection