<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\SalesController;

// 売上管理
Route::get('index', [SalesController::class, 'showSalesPage'])->name('show.sales.index');

Route::get('company-store', [SalesController::class, 'showStorePage'])->name('companies.store.form');
Route::post('company/confirm', [SalesController::class, 'storeCompanyConfirm'])->name('companies.store.confirm');
Route::post('company/store', [SalesController::class, 'storeCompany'])->name('companies.store');

Route::get('dailySales/show', [SalesController::class, 'showDailySales'])->name('show.daily.sales');
Route::get('monthSales/show', [SalesController::class, 'showMonthlySales'])->name('show.monthly.sales');

Route::post('detail-info', [SalesController::class, 'showSalesDetail'])->name('show.sales.detail');

// 企業詳細
Route::get('company/detail-{referralCode}', [SalesController::class, 'showCompanyDetail'])->name('show.company.detailsale');

// 報酬額修正
Route::post('company/edit', [SalesController::class, 'editCompany'])->name('edit.company');
Route::post('/company/edit/confirm', [SalesController::class, 'editCompanyConfirm'])->name('editCompanyConfirm');
