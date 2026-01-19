<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('things', function ($user) {
    return !is_null($user);
});

Broadcast::channel('places', function ($user) {
    return !is_null($user); // пользователь не null т е аутентифицирован (не уверен что слово написано правильно)
});