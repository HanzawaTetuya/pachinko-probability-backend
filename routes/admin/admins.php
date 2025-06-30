<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\UsersController;
use App\Http\Controllers\admin\AdminsController;
use App\Http\Controllers\admin\SalesController;



Route::get('/login', [AdminsController::class, 'showLogin'])->name('show.admins.login');

Route::post('/login-request', [AdminsController::class, 'loginRequest'])->name('admins.login.request');

Route::get('/two-factor', [AdminsController::class, "showTwoFactor"])->name('admins.two.factor');

Route::post('/two-factor', [AdminsController::class, 'verifyTwoFactor'])->name('admins.verify.two.factor');

Route::get('/admins', [AdminsController::class, 'index'])->name('admins.show');

Route::get('/{id}', [AdminsController::class, 'show'])->name('admin.show');

// editボタンをクリックした際の二段階認証コードの発行
Route::get('/{id}/verifyCode', [AdminsController::class, 'editTwoFactorCode'])->name('admin.edit.button');

// 二段階認証コード入力画面の表示
Route::get('/{id}/verify', [AdminsController::class, 'showEditTwoFactor'])->name('admin.edit.two_factor');



// 二段階認証コードの認証
Route::post('/{id}/verify', [AdminsController::class, 'verifyEditTwoFactor'])->name('admin.verify.edit.two.factor');

// ユーザー編集画面を表示するルート
Route::get('/{id}/edit', [AdminsController::class, 'edit'])->name('admin.edit');

Route::put('/{id}/edit', [AdminsController::class, 'update'])->name('admin.update');



// ユーザー登録画面表示
// 開発途中
Route::get('/admin/create', [AdminsController::class, 'create'])->name('admin.create');

Route::post('admin/store',[AdminsController::class, 'store'])->name('admin.store');

Route::get('admin/store',[AdminsController::class, 'storeTwoFactor'])->name('admin.store.two.factor');

Route::post('admin/store/verifyTwoFactor',[AdminsController::class, 'verifyStoreTwoFactor'])->name('admin.verify.store.two.factor');