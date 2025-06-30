<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\ProductController;

Route::get('/login', [ProductController::class, 'showLogin'])
    ->name('show.products.login');

Route::post('/login-request', [ProductController::class, 'loginRequest'])
    ->name('products.login.request');

Route::get('/two-factor', [ProductController::class, "showTwoFactor"])
    ->name('products.two.factor');

Route::post('/two-factor', [ProductController::class, 'verifyTwoFactor'])
    ->name('products.verify.two.factor');

Route::get('/products', [ProductController::class, 'showProducts'])
    ->name('products.show');



// 商品登録
// 認証コードの発行
Route::get('/store', [ProductController::class, 'storeProduct'])
    ->name('products.store.two.factor');

// 二段階認証フォームの表示
Route::get('/store/two-factor', [ProductController::class, 'showTwoFactorForm'])
    ->name('products.store.show.two.factor');

// 二段階認証コードの検証
Route::post('/store/verify-two-factor', [ProductController::class, 'verifyTwoFactorCode'])
    ->name('products.store.verify.code');

// 商品登録フォームの表示
Route::get('/store', [ProductController::class, 'showStoreForm'])
    ->name('products.store.show');

// 入力内容の保存
Route::post('/temporary', [ProductController::class, 'storeTemporary'])
    ->name('products.store.temporary');

// 商品登録の確認ページの表示
Route::get('/store/confirm', [ProductController::class, 'confirmProduct'])
    ->name('products.store.confirm');

// 商品登録の処理
Route::post('/create', [ProductController::class, 'createProduct'])
    ->name('products.store');


// 商品の詳細閲覧
Route::get('/{id}', [ProductController::class, 'productShow'])
    ->name('product.show');


/**
 * 商品の編集関係のルーティング
 * ２段階認証はupdate機能実装後に実装
 */

// 認証コードの発行
Route::get('/{id}/edit/two_factor/test', [ProductController::class, 'editTwoFactor'])
    ->name('product.edit.two.factor');

// コード入力画面
Route::get('/{id}/edit/two_factor', [ProductController::class, 'showEditTwoFactor'])
    ->name('product.edit.show.two.factor');

// コード検証
Route::post('/{id}/edit/two_factor', [ProductController::class, 'editVerifyCode'])
    ->name('product.edit.verify');

#ここからedit画面のルーティング    

// エディット画面の表示
Route::get('/{id}/edit', [ProductController::class, 'showEdit'])
    ->name('product.edit.show');

// 変更内容の一時保存
Route::post('/{id}/edit/temporary', [ProductController::class, 'editTemporary'])
    ->name('product.edit.temporary');

// 変更内容の確認画面の表示
Route::get('/{id}/edit/confirm', [ProductController::class, 'editConfirm'])
    ->name('product.edit.confirm');

// 変更内容の更新
Route::post('/{id}/edit/update', [ProductController::class, 'editProduct'])
    ->name('product.update');
