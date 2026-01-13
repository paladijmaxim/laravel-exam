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
        
        /* Стиль для активной вкладки */
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
        
        /* Стили для выделения вещей пользователя */
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
        
        /* Бейджи для типов мест */
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
                {{-- Панель --}}
                <li class="nav-item">
                    <a class="nav-link @navactive('dashboard')" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Панель
                    </a>
                </li>
                
                {{-- Архив --}}
                <li class="nav-item">
                    <a class="nav-link @navactive('archived.*')" href="{{ route('archived.index') }}">
                        <i class="fas fa-archive"></i> Архив
                    </a>
                </li>
                
                {{-- Вещи - Dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @navactive('things.*')" 
                       href="#" id="thingsDropdown" role="button" data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <i class="fas fa-cube"></i> Вещи
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="thingsDropdown">
                        {{-- Общий список --}}
                        <li>
                            <a class="dropdown-item @navactive('things.index')" href="{{ route('things.index') }}">
                                <i class="fas fa-list"></i> Общий список
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        {{-- Мои вещи --}}
                        <li>
                            <a class="dropdown-item @navactive('things.my')" href="{{ route('things.my') }}">
                                <i class="fas fa-user"></i> Мои вещи
                            </a>
                        </li>
                        
                        {{-- Мои вещи, используемые другими --}}
                        <li>
                            <a class="dropdown-item @navactive('things.used')" href="{{ route('things.used') }}">
                                <i class="fas fa-users"></i> Мои вещи, используемые другими
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        {{-- Вещи в ремонте/мойке --}}
                        <li>
                            <a class="dropdown-item @navactive('things.repair')" href="{{ route('things.repair') }}">
                                <i class="fas fa-tools"></i> Вещи в ремонте/мойке
                            </a>
                        </li>
                        
                        {{-- Вещи в работе --}}
                        <li>
                            <a class="dropdown-item @navactive('things.work')" href="{{ route('things.work') }}">
                                <i class="fas fa-briefcase"></i> Вещи в работе
                            </a>
                        </li>
                        
                        {{-- Взятые мной вещи --}}
                        <li>
                            <a class="dropdown-item @navactive('things.borrowed')" href="{{ route('things.borrowed') }}">
                                <i class="fas fa-handshake"></i> Взятые мной вещи
                            </a>
                        </li>
                        
                        {{-- Все вещи (админ) --}}
                        @can('viewAll', App\Models\Thing::class)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item @navactive('things.admin.all')" href="{{ route('things.admin.all') }}">
                                <i class="fas fa-eye"></i> Все вещи (админ)
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                
                {{-- Места --}}
                <li class="nav-item">
                    <a class="nav-link @navactive('places.*')" href="{{ route('places.index') }}">
                        <i class="fas fa-warehouse"></i> Места
                    </a>
                </li>
                
                @include('components.notifications')
                
                {{-- Админ --}}
                @can('admin')
                <li class="nav-item dropdown">
                    {{-- Для админ меню проверяем несколько маршрутов через OR --}}
                    <a class="nav-link dropdown-toggle text-warning @navactive('things.admin.all') @navactive('places.create') @navactive('places.index')" 
                       href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <i class="fas fa-crown"></i> Админ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li>
                            <a class="dropdown-item @navactive('things.admin.all')" href="{{ route('things.admin.all') }}">
                                <i class="fas fa-eye"></i> Просмотр всех вещей
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item @navactive('places.create')" href="{{ route('places.create') }}">
                                <i class="fas fa-plus"></i> Добавить место
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item @navactive('places.index')" href="{{ route('places.index') }}">
                                <i class="fas fa-edit"></i> Управление местами
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>
            
            {{-- Пользователь --}}
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
            
            {{-- Гости --}}
            @guest
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link @navactive('login')" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @navactive('register')" href="{{ route('register') }}">
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

    // ============================================
    // КАНАЛ ДЛЯ ВЕЩЕЙ (THINGS)
    // ============================================
    
    // Подписка на канал things
    console.log('📡 Subscribing to channel: things');
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
        showThingNotification(data);
    });
    
    // Слушаем ВСЕ события для дебага
    channel.bind_global(function(eventName, data) {
        if (!eventName.includes('pusher:')) {
            console.log('🌐 Global event (things):', eventName, data);
        }
    });

    // Функция показа уведомления о вещи
    function showThingNotification(data) {
        // Проверяем, создатель ли это текущий пользователь
        const isCreator = CURRENT_USER_ID && data.user_id == CURRENT_USER_ID;
        
        console.log('Is thing creator?', isCreator);
        
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
    
    // ============================================
    // КАНАЛ ДЛЯ МЕСТ ХРАНЕНИЯ (PLACES)
    // ============================================
    
    // Подписка на канал places
    console.log('📡 Subscribing to channel: places');
    const placesChannel = pusher.subscribe('places');
    
    // Проверка подписки на канал places
    placesChannel.bind('pusher:subscription_succeeded', function() {
        console.log('✅ Subscribed to channel: places');
    });
    
    placesChannel.bind('pusher:subscription_error', function(err) {
        console.error('❌ Places subscription error:', err);
    });
    
    // Обработка события создания места
    placesChannel.bind('place.created', function(data) {
        console.log('🏢 EVENT RECEIVED: place.created', data);
        console.log('Creator user_id:', data.user_id);
        console.log('Current user_id:', CURRENT_USER_ID);
        
        // Показываем уведомление всем пользователям
        showPlaceNotification(data);
    });
    
    // Слушаем ВСЕ события на канале places для дебага
    placesChannel.bind_global(function(eventName, data) {
        if (!eventName.includes('pusher:')) {
            console.log('🌐 Places global event:', eventName, data);
        }
    });
    
    // Функция показа уведомления о создании места
    function showPlaceNotification(data) {
        // Проверяем, создатель ли это текущий пользователь
        const isCreator = CURRENT_USER_ID && data.user_id == CURRENT_USER_ID;
        
        console.log('Is place creator?', isCreator);
        
        // Определяем иконку и бейдж в зависимости от типа места
        let iconClass = 'fa-warehouse'; // По умолчанию обычное место
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
        
        // Разный цвет для создателя и других пользователей
        if (isCreator) {
            notification.classList.add('place-creator');
        } else {
            notification.classList.add('place-notification');
        }
        
        // Заголовок в зависимости от того, кто создал
        let title = isCreator ? '✅ Вы создали место!' : '🏢 Новое место хранения!';
        let message = isCreator 
            ? 'Вы успешно создали место хранения:' 
            : `<strong>${data.user_name}</strong> создал(а) место:`;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                <div style="display: flex; align-items: center;">
                    <i class="fas ${isCreator ? 'fa-user-check' : iconClass}" 
                       style="font-size: 22px; margin-right: 12px;"></i>
                    <h5 style="margin: 0; font-weight: bold; font-size: 16px;">
                        ${title}
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
        
        // Добавляем на страницу
        document.body.appendChild(notification);
        
        // Удаляем через 7 секунд
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 7000);
        
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
    
    // Тестовая функция для проверки вещей (только для админов)
    @if(Auth::check() && Auth::user()->isAdmin())
    function testThingNotification() {
        const testData = {
            thing_id: 999,
            thing_name: 'Тестовая вещь',
            user_id: {{ Auth::id() }},
            user_name: '{{ Auth::user()->name }}',
            url: '#',
            time: new Date().toLocaleTimeString()
        };
        showThingNotification(testData);
    }
    
    // Тестовая функция для проверки мест (только для админов)
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
    
    // Добавляем тестовые кнопки для админов
    document.addEventListener('DOMContentLoaded', function() {
        // Кнопка для теста вещей
        const testThingBtn = document.createElement('button');
        testThingBtn.innerHTML = '<i class="fas fa-cube"></i> Тест вещи';
        testThingBtn.className = 'btn btn-warning btn-sm';
        testThingBtn.style.position = 'fixed';
        testThingBtn.style.bottom = '60px';
        testThingBtn.style.right = '20px';
        testThingBtn.style.zIndex = '9998';
        testThingBtn.onclick = testThingNotification;
        document.body.appendChild(testThingBtn);
        
        // Кнопка для теста мест
        const testPlaceBtn = document.createElement('button');
        testPlaceBtn.innerHTML = '<i class="fas fa-warehouse"></i> Тест места';
        testPlaceBtn.className = 'btn btn-info btn-sm';
        testPlaceBtn.style.position = 'fixed';
        testPlaceBtn.style.bottom = '100px';
        testPlaceBtn.style.right = '20px';
        testPlaceBtn.style.zIndex = '9998';
        testPlaceBtn.onclick = testPlaceNotification;
        document.body.appendChild(testPlaceBtn);
    });
    @endif
    
    // Экспортируем функции для глобального использования
    window.showThingNotification = showThingNotification;
    window.showPlaceNotification = showPlaceNotification;
    </script>
    
    @stack('scripts')
</body>
</html>