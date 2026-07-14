<?php

use App\Http\Controllers\KadenController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\LineLoginController;
use App\Http\Controllers\ItemWatchController;
use App\Http\Controllers\LineWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KadenController::class, 'index'])->name('kaden.index');
Route::get('/search', [KadenController::class, 'search'])->name('kaden.search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::view('/about', 'about')->name('about');

Route::post('/reviews', [ReviewController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('reviews.store');

// LINE連携（価格ウォッチの値下がり通知）
Route::get('/line/login', [LineLoginController::class, 'redirect'])->name('line.login');
Route::get('/line/callback', [LineLoginController::class, 'callback'])->name('line.callback');
Route::post('/item-watches', [ItemWatchController::class, 'toggle'])
    ->name('item-watches.toggle')
    ->middleware('throttle:10,1');
Route::post('/line/webhook', [LineWebhookController::class, 'handle'])->name('line.webhook');
