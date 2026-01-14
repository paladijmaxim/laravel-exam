<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('things', function ($user) {
    return !is_null($user);
});

Broadcast::channel('places', function ($user) {
    return !is_null($user);
});