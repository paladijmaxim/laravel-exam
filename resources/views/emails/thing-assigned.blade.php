<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вам назначена вещь</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .card h3 {
            color: #495057;
            margin-top: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .info-item {
            margin-bottom: 10px;
            display: flex;
        }
        .info-label {
            font-weight: bold;
            min-width: 150px;
            color: #495057;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .icon {
            margin-right: 8px;
            color: #667eea;
        }
        .status {
            display: inline-block;
            background-color: #e7f4e4;
            color: #2e7d32;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Storage of Things</h1>
            <p>Система управления хранением вещей</p>
        </div>
        
        <div class="content">
            <h2>Здравствуйте, {{ $recipient->name }}!</h2>
            
            <p>Мы рады сообщить, что вам была назначена вещь в системе управления хранением.</p>
            
            <div class="card">
                <h3>Детали назначения</h3>
                
                <div class="info-item">
                    <span class="info-label">Вещь:</span>
                    <span><strong>{{ $thing->name }}</strong></span>
                </div>
                
                @if($thing->description)
                <div class="info-item">
                    <span class="info-label">Описание:</span>
                    <span>{{ $thing->description }}</span>
                </div>
                @endif
                
                <div class="info-item">
                    <span class="info-label">Владелец:</span>
                    <span>{{ $owner->name }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Количество:</span>
                    <span>{{ $formattedAmount }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Место хранения:</span>
                    <span>{{ $place->name }}</span>
                </div>
                
                @if($thing->wrnt)
                <div class="info-item">
                    <span class="info-label">Гарантия до:</span>
                    <span>{{ $thing->wrnt->format('d.m.Y') }}</span>
                </div>
                @endif
                
                <div class="info-item">
                    <span class="info-label">Дата назначения:</span>
                    <span>{{ now()->format('d.m.Y H:i') }}</span>
                </div>
                
                <div class="status">
                     Вещь успешно назначена вам
                </div>
            </div>
            
            <p>Для просмотра деталей этой вещи и управления ей, перейдите по ссылке:</p>
            
            <a href="{{ url('/things/' . $thing->id) }}" class="btn">
                Перейти к вещи
            </a>
            
            <p>Также вы можете найти эту вещь в разделе <strong>"Взятые мной вещи"</strong> в вашем личном кабинете.</p>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <strong>Важно:</strong>
                <p style="margin: 5px 0 0 0;">Пожалуйста, бережно относитесь к переданной вам вещи и своевременно возвращайте ее владельцу по необходимости.</p>
            </div>
        </div>
    </div>
</body>
</html>