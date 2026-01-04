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
        .status-badge { font-size: 0.8em; padding: 3px 8px; border-radius: 12px; }
        .dropdown-menu { max-height: 400px; overflow-y: auto; }
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
                    
                    <!-- ВКЛАДКА С ВЫПАДАЮЩИМ СПИСКОМ (ЗАДАНИЕ 1) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="thingsDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cube"></i> Вещи
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="thingsDropdown">
                            <!-- Общий список -->
                            <li><a class="dropdown-item" href="{{ route('things.index') }}">
                                <i class="fas fa-list"></i> Общий список
                            </a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Вещи аутентифицированного пользователя -->
                            <li><a class="dropdown-item" href="{{ route('things.my') }}">
                                <i class="fas fa-user"></i> Мои вещи
                            </a></li>
                            
                            <!-- Личные вещи, которые используются другими пользователями -->
                            <li><a class="dropdown-item" href="{{ route('things.used') }}">
                                <i class="fas fa-users"></i> Мои вещи, используемые другими
                            </a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Вещи в специальных местах (Repair things) -->
                            <li><a class="dropdown-item" href="{{ route('things.repair') }}">
                                <i class="fas fa-tools"></i> Вещи в ремонте/мойке
                            </a></li>
                            
                            <!-- Вещи в работе (Work) -->
                            <li><a class="dropdown-item" href="{{ route('things.work') }}">
                                <i class="fas fa-briefcase"></i> Вещи в работе
                            </a></li>
                            
                            <!-- Дополнительно: взятые мной вещи -->
                            <li><a class="dropdown-item" href="{{ route('things.borrowed') }}">
                                <i class="fas fa-handshake"></i> Взятые мной вещи
                            </a></li>
                            
                            <!-- Для администратора -->
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
                    
                    <!-- Панель администратора -->
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
    @stack('scripts')
</body>
</html>