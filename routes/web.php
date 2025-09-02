<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth; // ← 追加

Route::get('/', function () {
    return view('welcome');
});

// ここで認証ルートを有効化（/login, /register など）
Auth::routes();

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class);
});
