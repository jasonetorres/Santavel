<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SantaController;

Route::get('/', function () {
    return redirect('/santa');
});

Route::get('/santa', [SantaController::class, 'index']);
