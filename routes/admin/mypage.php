<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\MypageController;


// マイページ表示のためのルート
Route::get('/mypage/login', [MypageController::class, 'showLoginForm'])->name('admin.mypage.login');

// マイページへのログイン処理
Route::post('/mypage/login', [MypageController::class, 'mypageLogin'])->name('admin.mypage.login.post');

// 二段階認証画面表示のためのルート
Route::get('/mypage/two-factor', [MypageController::class, 'showTwoFactor'])->name('admin.mypage.two_factor');

// 二段階認証コードの処理
Route::post('/mypage/two-factor', [MypageController::class, 'verifyCode'])->name('admin.verify.code');

// マイページの表示
Route::get('/mypage', [MypageController::class, 'showMypage'])->name('admin.mypage');



// アカウント情報編集
// 二段階認証ページ
Route::get('/mypage/edit/two-factor', [MypageController::class, 'showEditTwoFactor'])->name('admin.mypage.edit.two_factor.show');

// コード認証
Route::post('/mypage/edit/two-factor', [MypageController::class, 'verifyEditTwoFactor'])->name('admin.mypage.edit.two_factor');

// 各編集ページにリダイレクト
Route::get('/mypage/edit/{type}', [MypageController::class, 'showEditPage'])->name('admin.mypage.edit');

// 編集処理（name、passwordなどの変更）
Route::post('/mypage/edit/{type}', [MypageController::class, 'updateProfile'])->name('admin.mypage.update');

// emailの保存と認証コードの生成と送信
Route::post('/mypage/edit-email', [MypageController::class, 'verifyEmail'])->name('admin.email.verify');

// 認証コード入力ページの表示
Route::get('/mypage/verify-email', [MypageController::class, 'verifyEmailShow'])->name('admin.email.verify.show');

// 認証コードの認証
Route::post('/mypage/verify-email', [MypageController::class, 'verifyCodeEmail'])->name('admin.email.verify.code');