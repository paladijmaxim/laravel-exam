import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});

// Слушаем канал вещей
window.Echo.channel('things')
    .listen('.thing.created', (e) => {
        console.log('Новая вещь создана:', e);
        
        // Показываем уведомление
        showNotification(e.message);
        
        // Обновляем счетчик уведомлений если нужно
        updateNotificationCount();
    });

function showNotification(message) {
    // Используем браузерные уведомления
    if (Notification.permission === "granted") {
        new Notification("Новая вещь", {
            body: message,
            icon: '/favicon.ico'
        });
    }
    
    // Или показываем Toast уведомление
    showToast(message);
}

function showToast(message) {
    // Создаем HTML элемент для уведомления
    const toast = document.createElement('div');
    toast.className = 'toast-notification alert alert-info alert-dismissible fade show';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Удаляем через 5 секунд
    setTimeout(() => toast.remove(), 5000);
}