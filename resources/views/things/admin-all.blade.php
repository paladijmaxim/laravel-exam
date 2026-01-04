@extends('layouts.app')

@section('title', 'Все вещи (админ)')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-eye"></i> Все вещи (административный просмотр)</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Владелец</th>
                    <th>Гарантия до</th>
                    <th>Текущий пользователь</th>
                    <th>Место</th>
                    <th>Количество</th>
                    <th>Создано</th>
                </tr>
            </thead>
            <tbody>
                @forelse($things as $thing)
                    <tr>
                        <td>{{ $thing->id }}</td>
                        <td>{{ $thing->name }}</td>
                        <td>{{ Str::limit($thing->description, 50) }}</td>
                        <td>{{ $thing->owner->name }}</td>
                        <td>{{ $thing->wrnt ? $thing->wrnt->format('d.m.Y') : '—' }}</td>
                        <td>
                            @if($thing->currentUser())
                                {{ $thing->currentUser()->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($thing->currentPlace())
                                {{ $thing->currentPlace()->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($thing->currentUsage())
                                {{ $thing->currentUsage()->formatted_amount }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $thing->created_at->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Нет вещей</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $things->links() }}
    </div>
</div>
@endsection