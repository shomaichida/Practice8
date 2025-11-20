<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', function () {
    return redirect()->route('products.index');
})->name('home');
Route::middleware('auth')->group(function () {
    Route::get('/products',            [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create',     [ProductController::class, 'create'])->name('products.create');
    Route::post('/products',           [ProductController::class, 'store'])->name('products.store');
    // ★ 編集画面表示
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    // ★ 更新処理
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::get('/products/{product}',  [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');     // 追加
    Route::put('/products/{product}',      [ProductController::class, 'update'])->name('products.update'); // 追加
    Route::delete('/products/{product}',   [ProductController::class, 'destroy'])->name('products.destroy');
});
