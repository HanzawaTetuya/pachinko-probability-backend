<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


Route::get('/client/{any?}', function () {
    return File::get(public_path('client/index.html'));
})->where('any', '.*');
