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
                            @if($thing->latest_user)
                                {{ $thing->latest_user->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($thing->latest_place)
                                {{ $thing->latest_place->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($thing->latest_usage)
                                {{ $thing->latest_usage->amount }}
                                @if($thing->latest_usage->unit)
                                    {{ $thing->latest_usage->unit->abbreviation }}
                                @endif
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

    @if($things->hasPages())
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    {{-- Показываем только номера страниц --}}
                    @for ($page = 1; $page <= $things->lastPage(); $page++)
                        <li class="page-item {{ $things->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $things->url($page) }}">{{ $page }}</a>
                        </li>
                    @endfor
                </ul>
            </nav>
        </div>
    @endif
</div>
@endsection
