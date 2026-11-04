<?php

use App\Http\Controllers\SantaController;
use App\Http\Controllers\WishController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/santa');
});

Route::get('/santa', [SantaController::class, 'index']);

Route::post('/santa/transcribe', [SantaController::class, 'transcribe'])
    ->middleware('throttle:60,1');

Route::post('/santa/reply', [SantaController::class, 'reply'])
    ->middleware('throttle:60,1');

Route::middleware(['wish.secret', 'throttle:60,1'])->group(function () {
    Route::get('/santa/wishes', [WishController::class, 'index']);
    Route::post('/santa/wishes', [WishController::class, 'store']);
    Route::post('/santa/wishes/{wish}/deliver', [WishController::class, 'deliver']);
});
