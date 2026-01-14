<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage of Things - Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 60px 0 40px 0;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
            color: white;
            border-bottom: none;
        }
        .welcome-message {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-left: 5px solid #2575fc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .login-required {
            opacity: 0.7;
            position: relative;
        }
        .login-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-box"></i> Storage of Things
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i> Вход
                </a>
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="fas fa-user-plus"></i> Регистрация
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"> Последние добавленные вещи в системе</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> В системе зарегистрировано <strong>{{ $totalThings }}</strong> вещей
                        </div>
                        
                        @if($recentThings->count() > 0)
                            <div class="list-group">
                                @foreach($recentThings as $thing)
                                    <a href="{{ route('things.show', $thing) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $thing->name }}</h6>
                                            <small class="text-muted">{{ $thing->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">
                                            <small class="text-muted">{{ Str::limit($thing->description, 80) }}</small>
                                        </p>
                                        <small>
                                            <i class="fas fa-user"></i> Владелец: {{ $thing->owner->name }}
                                            @if($thing->currentUsage())
                                                • <span class="badge bg-warning">В использовании</span>
                                            @else
                                                • <span class="badge bg-success">Доступна</span>
                                            @endif
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                            
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5>В системе пока нет вещей</h5>
                                <p class="text-muted">Станьте первым, кто добавит вещь!</p>
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Присоединиться
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} by Maxim Paladii</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>