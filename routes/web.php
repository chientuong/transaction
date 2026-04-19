<?php

use Illuminate\Support\Facades\Route;

$appService = env('APP_SERVICE', 'all');

if (in_array($appService, ['all', 'admin'])) {
    Route::redirect('/', '/admin')->name('home');
    require __DIR__.'/settings.php';
}
