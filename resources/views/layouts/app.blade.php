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
        
        /* Стиль для уведомлений */
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
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Панель
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('archived.index') }}">
                            <i class="fas fa-archive"></i> Архив
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="thingsDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cube"></i> Вещи
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="thingsDropdown">
                            <li><a class="dropdown-item" href="{{ route('things.index') }}">
                                <i class="fas fa-list"></i> Общий список
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('things.my') }}">
                                <i class="fas fa-user"></i> Мои вещи
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('things.used') }}">
                                <i class="fas fa-users"></i> Мои вещи, используемые другими
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('things.repair') }}">
                                <i class="fas fa-tools"></i> Вещи в ремонте/мойке
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('things.work') }}">
                                <i class="fas fa-briefcase"></i> Вещи в работе
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('things.borrowed') }}">
                                <i class="fas fa-handshake"></i> Взятые мной вещи
                            </a></li>
                            @can('viewAll', App\Models\Thing::class)
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('things.admin.all') }}">
                                <i class="fas fa-eye"></i> Все вещи (админ)
                            </a></li>
                            @endcan
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('places.index') }}">
                            <i class="fas fa-warehouse"></i> Места
                        </a>
                    </li>
                    
                    @include('components.notifications')
                    
                    @can('admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning" href="#" id="adminDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-crown"></i> Админ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="{{ route('things.admin.all') }}">
                                <i class="fas fa-eye"></i> Просмотр всех вещей
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('places.create') }}">
                                <i class="fas fa-plus"></i> Добавить место
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('places.index') }}">
                                <i class="fas fa-edit"></i> Управление местами
                            </a></li>
                        </ul>
                    </li>
                    @endcan
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-warning">Admin</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Выйти
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
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Войти
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Регистрация
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
    
    <!-- Подключаем Pusher -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    
    <script>
    // Получаем ID текущего пользователя из Laravel
    const CURRENT_USER_ID = {{ Auth::id() ?? 'null' }};
    
    // Инициализация Pusher
    const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true,
        enabledTransports: ['ws', 'wss'] // Явно указываем транспорт
    });
    
    // Дебаг логирование
    console.log('=== PUSHER INIT ===');
    console.log('Current User ID:', CURRENT_USER_ID);
    console.log('Pusher Key:', '{{ env("PUSHER_APP_KEY") }}');
    console.log('Pusher Cluster:', '{{ env("PUSHER_APP_CLUSTER") }}');
    
    // Обработчики состояния подключения
    pusher.connection.bind('connecting', function() {
        console.log('🔌 Pusher: Connecting...');
    });
    
    pusher.connection.bind('connected', function() {
        console.log('✅ Pusher: Connected! Socket ID:', pusher.connection.socket_id);
    });
    
    pusher.connection.bind('disconnected', function() {
        console.log('❌ Pusher: Disconnected');
    });
    
    pusher.connection.bind('error', function(err) {
        console.error('⚠️ Pusher Error:', err);
    });

    // Подписка на канал
    const channel = pusher.subscribe('things');
    
    // Проверка подписки
    channel.bind('pusher:subscription_succeeded', function() {
        console.log('✅ Subscribed to channel: things');
    });
    
    channel.bind('pusher:subscription_error', function(err) {
        console.error('❌ Subscription error:', err);
    });

    // Обработка события создания вещи
    channel.bind('thing.created', function(data) {
        console.log('🎯 EVENT RECEIVED: thing.created', data);
        console.log('Creator user_id:', data.user_id);
        console.log('Current user_id:', CURRENT_USER_ID);
        
        // ВСЕГДА показываем уведомление, проверяем кто создатель
        showNotification(data);
    });
    
    // Слушаем ВСЕ события для дебага
    channel.bind_global(function(eventName, data) {
        if (!eventName.includes('pusher:')) {
            console.log('🌐 Global event received:', eventName, data);
        }
    });

    // Функция показа уведомления
    function showNotification(data) {
        // Проверяем, создатель ли это текущий пользователь
        const isCreator = CURRENT_USER_ID && data.user_id == CURRENT_USER_ID;
        
        console.log('Is creator?', isCreator);
        
        const notification = document.createElement('div');
        notification.className = 'pusher-notification';
        
        // Добавляем класс creator если это создатель
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
            <p style="margin: 0 0 10px 0; font-weight: bold; font-size: 16px;">
                "${data.thing_name}"
            </p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <small>${data.time || 'Только что'}</small>
                <a href="${data.url}" class="btn btn-sm ${isCreator ? 'btn-info' : 'btn-light'}" 
                   style="text-decoration: none;">
                    ${isCreator ? 'Перейти к вещи' : 'Посмотреть'} 
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        `;
        
        // Добавляем на страницу
        document.body.appendChild(notification);
        
        // Удаляем через 5 секунд
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 5000);
        
        // Воспроизводим звук уведомления
        playNotificationSound();
    }
    
    // Функция воспроизведения звука уведомления
    function playNotificationSound() {
        try {
            // Создаем короткий звук уведомления
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
    
    // Тестовая функция для проверки (только для админов)
    @if(Auth::check() && Auth::user()->isAdmin())
    function testNotification() {
        const testData = {
            thing_id: 999,
            thing_name: 'Тестовая вещь',
            user_id: {{ Auth::id() }},
            user_name: '{{ Auth::user()->name }}',
            url: '#',
            time: new Date().toLocaleTimeString()
        };
        showNotification(testData);
    }
    
    // Добавляем тестовую кнопку для админов
    document.addEventListener('DOMContentLoaded', function() {
        const testBtn = document.createElement('button');
        testBtn.innerHTML = '<i class="fas fa-bell"></i> Тест уведомления';
        testBtn.className = 'btn btn-warning btn-sm';
        testBtn.style.position = 'fixed';
        testBtn.style.bottom = '20px';
        testBtn.style.right = '20px';
        testBtn.style.zIndex = '9998';
        testBtn.onclick = testNotification;
        document.body.appendChild(testBtn);
    });
    @endif
    
    // Экспортируем функции для глобального использования
    window.showNotification = showNotification;
    </script>
    
    @stack('scripts')
</body>
</html>