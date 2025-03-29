<?php

use Illuminate\Support\Facades\Route;
use namhuunam\MovieContentGenerator\Http\Controllers\AdminController;

Route::middleware(['web', 'auth'])
    ->prefix('admin/movie-content')
    ->name('movie-content-generator.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/settings', [AdminController::class, 'saveSettings'])->name('settings.save');
        Route::post('/generate', [AdminController::class, 'generateContent'])->name('generate');
    });
