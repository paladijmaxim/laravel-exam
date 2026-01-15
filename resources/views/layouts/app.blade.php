<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Storage of Things')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding-top: 20px; background-color: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .dropdown-menu { max-height: 400px; overflow-y: auto; }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.2) !important;
            border-radius: 5px;
            font-weight: bold;
            position: relative;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 15px;
            right: 15px;
            height: 3px;
            background: linear-gradient(90deg, #4dabf7, #228be6);
            border-radius: 3px;
        }
        
        .dropdown-item.active {
            background-color: #007bff !important;
            color: white !important;
            font-weight: bold;
        }
        
        .my-thing-row {
            background-color: #e8f5e9 !important;
            border-left: 4px solid #28a745 !important;
        }

        .my-thing-row:hover {
            background-color: #d4edda !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
        }

        .my-thing-card {
            border: 2px solid #28a745 !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }

        .my-thing-highlight {
            position: relative;
        }

        .my-thing-highlight::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #28a745, #20c997);
            border-radius: 3px 0 0 3px;
        }

        .my-thing-icon {
            color: #28a745;
            animation: pulse 2s infinite;
        }

        @keyframes myThingPulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        .my-thing-pulse {
            animation: myThingPulse 2s infinite;
        }

        .pusher-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 9999;
            max-width: 350px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        }
        
        .pusher-notification.creator {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .pusher-notification.place-notification {
            background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
        }
        
        .pusher-notification.place-creator {
            background: linear-gradient(135deg, #20c997 0%, #17a589 100%);
        }
        
        .pusher-notification.fade-out {
            animation: fadeOut 0.5s ease forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; transform: translateX(100%); }
        }
        
        .place-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
            font-weight: bold;
        }
        .badge-repair { background: #dc3545; }
        .badge-work { background: #ffc107; color: #000; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-box"></i> Storage of Things
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link @navactive('dashboard')" href="{{ route('dashboard') }}">
                         Главная
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link @navactive('archived.*')" href="{{ route('archived.index') }}">
                         Архив
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @navactive('things.*')" 
                       href="#" id="thingsDropdown" role="button" data-bs-toggle="dropdown" 
                       aria-expanded="false">
                         Вещи
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="thingsDropdown">
                        <li>
                            <a class="dropdown-item @navactive('things.index')" href="{{ route('things.index') }}">
                                 Общий список
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li>
                            <a class="dropdown-item @navactive('things.my')" href="{{ route('things.my') }}">
                                 Мои вещи
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item @navactive('things.used')" href="{{ route('things.used') }}">
                                 Мои вещи, используемые другими
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li>
                            <a class="dropdown-item @navactive('things.repair')" href="{{ route('things.repair') }}">
                                 Вещи в ремонте/мойке
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item @navactive('things.work')" href="{{ route('things.work') }}">
                                 Вещи в работе
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item @navactive('things.borrowed')" href="{{ route('things.borrowed') }}">
                                 Взятые мной вещи
                            </a>
                        </li>
                        
                        @can('viewAll', App\Models\Thing::class)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item @navactive('things.admin.all')" href="{{ route('things.admin.all') }}">
                                Все вещи (админ)
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link @navactive('places.*')" href="{{ route('places.index') }}">
                         Места
                    </a>
                </li>
                
                @include('components.notifications')
                
                @can('admin')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning @navactive('things.admin.all') @navactive('places.create') @navactive('places.index')" 
                       href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" 
                       aria-expanded="false">
                         Админ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li>
                            <a class="dropdown-item @navactive('things.admin.all')" href="{{ route('things.admin.all') }}">
                                 Просмотр всех вещей
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item @navactive('places.create')" href="{{ route('places.create') }}">
                                 Добавить место
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item @navactive('places.index')" href="{{ route('places.index') }}">
                                 Управление местами
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                         {{ Auth::user()->name }}
                        @if(Auth::user()->isAdmin())
                            <span class="badge bg-warning">Admin</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                     Выйти
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            @endauth
            
            @guest
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link @navactive('login')" href="{{ route('login') }}">
                         Войти
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @navactive('register')" href="{{ route('register') }}">
                         Регистрация
                    </a>
                </li>
            </ul>
            @endguest
        </div>
    </div>
</nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    
    <script>
    const CURRENT_USER_ID = {{ Auth::id() ?? 'null' }};
    
    const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true,
        enabledTransports: ['ws', 'wss']
    });
    
    pusher.connection.bind('connected', function() {
        console.log('Pusher connected');
    });
    
    pusher.connection.bind('error', function(err) {
        console.error('Pusher error:', err);
    });

    const channel = pusher.subscribe('things');
    
    channel.bind('pusher:subscription_succeeded', function() {
        console.log('Subscribed to things');
    });
    
    channel.bind('thing.created', function(data) {
        showThingNotification(data);
    });
    
    function showThingNotification(data) {
        const isCreator = CURRENT_USER_ID && data.user_id == CURRENT_USER_ID;
        
        const notification = document.createElement('div');
        notification.className = 'pusher-notification';
        
        if (isCreator) {
            notification.classList.add('creator');
        }
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                <i class="fas ${isCreator ? 'fa-user-check' : 'fa-check-circle'}" 
                   style="font-size: 20px; margin-right: 10px;"></i>
                <h5 style="margin: 0; font-weight: bold;">
                    ${isCreator ? '✅ Вы создали вещь!' : '🎉 Новая вещь!'}
                </h5>
            </div>
            <p style="margin: 0 0 5px 0;">
                ${isCreator ? 
                    'Вы успешно создали вещь:' : 
                    `<strong>${data.user_name}</strong> создал(а) вещь:`
                }
            </p>
            <p style="margin: 0 0 10px 0; font-weight: bold; font-size: 16px; background: rgba(255,255,255,0.1); padding: 8px; border-radius: 5px;">
                "${data.thing_name}"
            </p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <small><i class="far fa-clock"></i> ${data.time || 'Только что'}</small>
                <a href="${data.url}" class="btn btn-sm ${isCreator ? 'btn-info' : 'btn-light'}" 
                   style="text-decoration: none;">
                    ${isCreator ? 'Перейти к вещи' : 'Посмотреть'} 
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 5000);
        
        playNotificationSound();
    }
    
    const placesChannel = pusher.subscribe('places');
    
    placesChannel.bind('pusher:subscription_succeeded', function() {
        console.log('Subscribed to places');
    });
    
    placesChannel.bind('pusher:subscription_error', function(err) {
        console.error('Places subscription error:', err);
    });
    
    placesChannel.bind('place.created', function(data) {
        showPlaceNotification(data);
    });
    
    function showPlaceNotification(data) {
        const isCreator = CURRENT_USER_ID && data.user_id == CURRENT_USER_ID;
        
        let iconClass = 'fa-warehouse';
        let badgeHTML = '';
        
        if (data.is_repair) {
            iconClass = 'fa-tools';
            badgeHTML = '<span class="place-badge badge-repair">🔧 Ремонт</span>';
        } else if (data.is_work) {
            iconClass = 'fa-briefcase';
            badgeHTML = '<span class="place-badge badge-work">💼 Работа</span>';
        }
        
        const notification = document.createElement('div');
        notification.className = 'pusher-notification';
        
        if (isCreator) {
            notification.classList.add('place-creator');
        } else {
            notification.classList.add('place-notification');
        }
        
        let message = isCreator 
            ? 'Вы успешно создали место хранения:' 
            : `<strong>${data.user_name}</strong> создал(а) место:`;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                <div style="display: flex; align-items: center;">
                    <i class="fas ${isCreator ? 'fa-user-check' : iconClass}" 
                       style="font-size: 22px; margin-right: 12px;"></i>
                    <h5 style="margin: 0; font-weight: bold; font-size: 16px;">
                        ${isCreator ? 'Вы создали место!' : 'Новое место!'}
                    </h5>
                </div>
                ${badgeHTML}
            </div>
            
            <p style="margin: 0 0 8px 0; font-size: 14px;">
                ${message}
            </p>
            
            <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 8px; margin: 10px 0; border-left: 4px solid rgba(255,255,255,0.3);">
                <p style="margin: 0; font-weight: bold; font-size: 16px;">
                    "${data.place_name}"
                </p>
            </div>
            
            ${data.description && data.description !== 'Без описания' 
                ? `<div style="margin: 10px 0; padding: 8px 12px; background: rgba(255,255,255,0.1); border-radius: 6px; font-size: 13px; display: flex; align-items: flex-start;">
                    <i class="fas fa-info-circle mt-1" style="margin-right: 8px;"></i>
                    <span>${data.description}</span>
                   </div>`
                : ''
            }
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.2);">
                <div style="font-size: 12px; opacity: 0.9;">
                    <i class="far fa-clock"></i> ${data.time || 'Только что'}
                </div>
                <a href="${data.url}" class="btn btn-sm ${isCreator ? 'btn-success' : 'btn-light'}" 
                   style="text-decoration: none; font-weight: 600; padding: 5px 15px;">
                    Перейти <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 7000);
        
        playNotificationSound();
    }
    
    function playNotificationSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.15);
        } catch (e) {
            console.log('Не удалось воспроизвести звук:', e);
        }
    }
    
    @if(Auth::check() && Auth::user()->isAdmin())
    function testPlaceNotification() {
        const testData = {
            place_id: 999,
            place_name: 'Тестовое складское помещение',
            user_id: {{ Auth::id() }},
            user_name: '{{ Auth::user()->name }}',
            description: 'Тестовое описание для проверки уведомлений',
            url: '#',
            time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
            is_repair: false,
            is_work: true
        };
        showPlaceNotification(testData);
    }
    @endif
    
    window.showThingNotification = showThingNotification;
    window.showPlaceNotification = showPlaceNotification;
    </script>
    
    @stack('scripts')
</body>
</html>