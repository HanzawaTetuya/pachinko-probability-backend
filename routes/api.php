<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\auth\UserRegistrationController;
use App\Http\Controllers\user\auth\AuthController;
use App\Http\Controllers\user\ProductsController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\user\FavoriteController;
use App\Http\Controllers\user\CartController;
use App\Http\Controllers\user\OrderController;
use App\Http\Controllers\user\auth\PaymentController;
use App\Http\Controllers\user\auth\SystemController;
use App\Http\Controllers\user\HomePageController;
use App\Http\Controllers\user\NewsController;
use App\Http\Controllers\user\PurchaseController;
use App\Http\Controllers\user\ContactController;
use App\Http\Controllers\user\QuestionController;


// アカウント登録
Route::post('/register', [UserRegistrationController::class, 'register']);
Route::post('/verify-code', [UserRegistrationController::class, 'verifyCode']);
Route::post('/add-password', [UserRegistrationController::class, 'addPassword']);

// アカウントログイン
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-login-code', [AuthController::class, 'loginVerifyCode']);

// パスワードをわすれた方（ログイン時）
Route::post('/forgot-password-in-login', [AuthController::class, 'forgotPasswordInLogin']);
Route::post('/verify-reset-password-code', [AuthController::class, 'verifyResetPasswordCode']);
Route::post('/reset-password-in-login', [AuthController::class, 'resetPasswordInLogin']);


// ログアウト
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);



// ホーム画面
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home-data', [HomePageController::class, 'homeData']);
});

Route::middleware('auth:sanctum')->get('/user-info', [UserController::class, 'getUserInfo']);
Route::middleware('auth:sanctum')->post('/edit-username', [UserController::class, 'editUserName']);
Route::middleware('auth:sanctum')->post('/edit-email', [UserController::class, 'editEmail']);
Route::middleware('auth:sanctum')->post('/verify-email-code', [UserController::class, 'verifyEmailCode']);

// お問い合わせ処理
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/contact', [ContactController::class, 'send']);
});


// サポートヘルプ機能
Route::get('/questions', [QuestionController::class, 'getQuestion']);
Route::middleware('auth:sanctum')->get('/getAnswer/{id}', [QuestionController::class, 'getAnswer']);





// パスワードの変更
Route::middleware('auth:sanctum')->post('/edit-password', [UserController::class, 'editPassword']);
Route::middleware('auth:sanctum')->post('/verify-password-code', [UserController::class, 'verifyPasswordCode']);
Route::middleware('auth:sanctum')->post('/update-password', [UserController::class, 'updatePassword']);

// パスワードをお忘れの方
Route::middleware('auth:sanctum')->post('/forgot-password-in-mypage', [UserController::class, 'forgotPasswordInMypage']);

// 商品情報取得API
Route::get('/products', [ProductsController::class, 'showProducts']);


// お気に入り登録機能
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/check', [FavoriteController::class, 'isFavorite']); // お気に入り確認
    Route::get('/favorites', [FavoriteController::class, 'favoriteIndex']);
    Route::post('/favorites', [FavoriteController::class, 'favoriteStore']);
    Route::delete('/favorites', [FavoriteController::class, 'favoriteDestroy']);
});

// カート登録機能
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/check', [CartController::class, 'isInCart']); // カート内に同じ商品があるかチェック
    Route::post('/cart/add', [CartController::class, 'addToCart']); // 商品の追加
    Route::get('/cart/list', [CartController::class, 'cartIndex']); // カート内と計算
    Route::delete('/cart/{product_number}', [CartController::class, 'cartDestroy']); // カート内と計算
});

// システム利用
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get-order-all', [PaymentController::class, 'getOrderAll']);
    Route::post('/verify-license', [SystemController::class, 'verifyLicense']);
    Route::post('/calculate', [SystemController::class, 'calculate']);
    Route::post('/fetch-usage-data', [SystemController::class, 'fetchUsageData']);
    Route::post('/get-data-detail', [SystemController::class, 'getDataDetail']);
});
// 購入履歴取得
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get-order', [OrderController::class, 'orderIndex']);
    Route::post('/order-detail', [OrderController::class, 'orderDetail']);
});

// お知らせの情報取得
Route::get('/news-index', [NewsController::class, 'NewsIndex']);




// ■■　決済関係　■■
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/purchase/start', [PurchaseController::class, 'startPurchase']);
    Route::get('/purchase/checkout', [PurchaseController::class, 'showCheckoutPage']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payment-link', [PaymentController::class, 'generatePaymentLink']);
    Route::post('/check-order-status', [PaymentController::class, 'checkOrderStatus']);
    Route::post('/get-order', [PaymentController::class, 'getOrder']);
});