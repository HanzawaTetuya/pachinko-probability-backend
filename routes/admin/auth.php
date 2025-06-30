<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\authController;

Route::middleware(['web'])->group(function () {
    Route::get('/login', [authController::class, 'showLoginForm'])->name('showLogin');
});

Route::post('/login', [authController::class, 'login'])->name('admin.login'); // ← ここを変更！


Route::get('/two-factor', function () {
    return view('admin.login.two_factor');
})->name('admin.two_factor');

Route::post('/two-factor', [authController::class, 'verifyTwoFactor'])->name('verify.two_factor');

Route::get('/dashboard', [authController::class, 'showDashboard'])->name('admin.dashboard');

// ログアウトのルート
Route::post('/logout', [authController::class, 'logout'])->name('admin.logout');
