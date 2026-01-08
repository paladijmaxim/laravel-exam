@php
    $unreadCount = Auth::user()->unreadNotificationsCount();
    $notifications = Auth::user()->notifications()->latest()->limit(5)->get();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" 
       data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger">{{ $unreadCount }}</span>
        @endif
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
        <li><h6 class="dropdown-header">Уведомления</h6></li>
        
        @foreach($notifications as $notification)
            <li>
                <a class="dropdown-item {{ !$notification->read ? 'fw-bold' : '' }}" 
                   href="{{ route('notifications.show', $notification) }}">
                    <div class="small">{{ $notification->title }}</div>
                    <div class="text-muted" style="font-size: 0.8rem;">
                        {{ \Illuminate\Support\Str::limit($notification->message, 40) }}
                    </div>
                </a>
            </li>
        @endforeach
        
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                Все уведомления
            </a>
        </li>
    </ul>
</li>