<x-mail::message>
# {{ $isNew ? 'Добавлено новое описание' : 'Обновлено описание' }}

**Вещь:** {{ $thing->name }}  
**Владелец:** {{ $thing->owner->name }}  
**Автор изменения:** {{ $user->name }}  

<x-mail::panel>
{{ $description }}
</x-mail::panel>

**Дата изменения:** {{ now()->format('d.m.Y H:i') }}

<x-mail::button :url="route('things.show', $thing)">
Посмотреть вещь
</x-mail::button>

</x-mail::message>