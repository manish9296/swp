<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/login-user', [LoginController::class, 'loginUser'])->name('loginuser');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/state/{state}', [DashboardController::class, 'state'])->name('dashboard.state')->where('state', '.*');
    Route::resource('farmers', FarmerController::class);
    Route::get('/generation-summary', [GenerationController::class, 'summary'])->name('generation.summary');
    Route::get('/farmers/{farmer}/generation', [GenerationController::class, 'farmer'])->name('generation.farmer');
    Route::any('/logout', [LoginController::class, 'logout'])->name('logout');

});
