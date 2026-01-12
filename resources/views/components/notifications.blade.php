@php
    // Старые уведомления (для назначения вещей)
    $unreadCount = Auth::user()->unreadNotificationsCount();
    $notifications = Auth::user()->notifications()->latest()->limit(5)->get();
    
    // Новые уведомления (для описаний)
    $unreadDescCount = Auth::user()->unreadDescriptionNotificationsCount();
    $descNotifications = Auth::user()->descriptionNotifications()->latest()->limit(5)->get();
    
    // Общее количество
    $totalUnread = $unreadCount + $unreadDescCount;
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" 
       data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if($totalUnread > 0)
            <span class="badge bg-danger">{{ $totalUnread }}</span>
        @endif
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
        <li><h6 class="dropdown-header">Уведомления</h6></li>
        
        <!-- Уведомления о назначении вещей -->
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
        
        <!-- Уведомления об описаниях -->
        @foreach($descNotifications as $notification)
            <li>
                <a class="dropdown-item {{ !$notification->read ? 'fw-bold' : '' }}" 
                   href="{{ route('things.show', $notification->thing) }}">
                    <div class="small">{{ $notification->title }}</div>
                    <div class="text-muted" style="font-size: 0.8rem;">
                        {{ \Illuminate\Support\Str::limit($notification->message, 40) }}
                    </div>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
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