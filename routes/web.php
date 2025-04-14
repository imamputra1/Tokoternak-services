<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-log', function() {
    \Log::info('TEST LOG MESSAGE MANUAL', [
        'time' => now(),
        'status' => 'working'
    ]);
    return 'Check storage/logs/laravel.log';
});
