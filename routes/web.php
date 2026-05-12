<?php

use App\Http\Controllers\TelescopeAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Custom Telescope Auth Routes (bukan /telescope/login untuk avoid conflict dengan Telescope routes)
Route::get('/telescope-admin/login', [TelescopeAuthController::class, 'showLogin'])->name('telescope.login');
Route::post('/telescope-admin/login', [TelescopeAuthController::class, 'login'])->name('telescope.login.post');
Route::post('/telescope-admin/logout', [TelescopeAuthController::class, 'logout'])->name('telescope.logout');