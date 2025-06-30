<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\UsersController;

Route::get('/login', [UsersController::class, 'showLogin'])->name('show.users.login');

Route::post('/login-request', [UsersController::class, 'loginRequest'])->name('users.login.request');

Route::get('/two-factor', [UsersController::class, "showTwoFactor"])->name('users.two.factor');

Route::post('/two-factor', [UsersController::class, 'verifyTwoFactor'])->name('users.verify.two.factor');

Route::get('/users', [UsersController::class, 'index'])->name('users.show');

Route::get('/{id}', [UsersController::class, 'show'])->name('admin.user.show');

// editボタンをクリックした際の二段階認証コードの発行
Route::get('/{id}/verifyCode', [UsersController::class, 'editTwoFactorCode'])->name('admin.user.edit.button');

// 二段階認証コード入力画面の表示
Route::get('/{id}/verify', [UsersController::class, 'showEditTwoFactor'])->name('admin.user.edit.two_factor');

// 二段階認証コードの認証
Route::post('/{id}/verify', [UsersController::class, 'verifyEditTwoFactor'])->name('admin.user.verify.edit.two.factor');

// ユーザー編集画面を表示するルート
Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('admin.user.edit');

Route::put('/{id}/edit', [UsersController::class, 'update'])->name('admin.user.update');



// ユーザー登録画面表示
// 開発途中
Route::get('/create', [UsersController::class, 'create'])->name('admin.user.create');

Route::post('/store',[UsersController::class, 'store'])->name('admin.user.store');