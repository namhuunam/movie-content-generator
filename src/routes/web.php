<?php

use Illuminate\Support\Facades\Route;
use namhuunam\MovieContentGenerator\Http\Controllers\AdminController;

// Kiểm tra xem tính năng admin có được bật hay không
if (config('movie-content-generator.admin.enabled', true)) {
    $adminUrl = config('movie-content-generator.admin.url', 'manager/movie-content');
    $middleware = config('movie-content-generator.admin.middleware', ['web']);
    
    Route::middleware($middleware)->group(function () use ($adminUrl) {
        // Trang admin chính
        Route::get($adminUrl, [AdminController::class, 'index'])
            ->name('movie-content-generator.admin');
            
        // Xử lý yêu cầu tạo nội dung
        Route::post($adminUrl . '/generate', [AdminController::class, 'generateContent'])
            ->name('movie-content-generator.generate');
    });
    Route::get('movie-debug', function () {
        try {
            // Gọi service của package
            $stats = app()->make('namhuunam\MovieContentGenerator\Services\MovieStatsService')->getStats();
            return response()->json(['stats' => $stats, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    });
}
