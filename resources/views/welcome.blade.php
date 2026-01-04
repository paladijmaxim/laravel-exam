<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage of Things</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .feature-icon {
            font-size: 3rem;
            color: #2575fc;
            margin-bottom: 20px;
        }
        .card-hover {
            transition: transform 0.3s;
        }
        .card-hover:hover {
            transform: translateY(-5px);
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
                <a class="nav-link" href="{{ route('login') }}">Вход</a>
                <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
            </div>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 mb-4">Storage of Things</h1>
            <p class="lead mb-4">Организуйте хранение ваших вещей и управляйте ими легко</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">Начать сейчас</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Войти</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-md-4">
                <div class="card card-hover border-0 shadow-sm p-4">
                    <i class="fas fa-cube feature-icon"></i>
                    <h4>Учет вещей</h4>
                    <p>Создавайте и управляйте своими вещами, отслеживайте гарантии и сроки годности</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hover border-0 shadow-sm p-4">
                    <i class="fas fa-warehouse feature-icon"></i>
                    <h4>Места хранения</h4>
                    <p>Организуйте места хранения, отмечайте статусы ремонта и работы</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-hover border-0 shadow-sm p-4">
                    <i class="fas fa-handshake feature-icon"></i>
                    <h4>Передача вещей</h4>
                    <p>Передавайте вещи в пользование другим пользователям системы</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <h2 class="mb-4">Готовы начать?</h2>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-user-plus"></i> Зарегистрироваться бесплатно
            </a>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Storage of Things. Все права защищены.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>