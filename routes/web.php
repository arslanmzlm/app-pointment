<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('login');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
