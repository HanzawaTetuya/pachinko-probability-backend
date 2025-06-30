<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\PurchaseController;
use App\Http\Controllers\user\auth\RobotPaymentController;
use App\Http\Controllers\user\DebugPaymentController;

Route::get('/purchase/checkout', [PurchaseController::class, 'showCheckoutPage']);

// 決済の最初の送信部分
Route::post('/payment/confirm', [RobotPaymentController::class, 'confirm'])->name('robot.confirm');

// 決済成功失敗の場合の処理
Route::post('/payment/notify', [RobotPaymentController::class, 'handleResult']);

// 決済完了画面表示


// ダミー決済
Route::post('/payment/debug/notify', [DebugPaymentController::class, 'handleResult'])->name('debug.notify');