<?php

namespace namhuunam\MovieContentGenerator;

use Illuminate\Support\ServiceProvider;
use namhuunam\MovieContentGenerator\Commands\GenerateMovieContent;
use namhuunam\MovieContentGenerator\Services\GeminiApiService;

class MovieContentGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Đăng ký migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'movie-content-generator');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        
        // Publish config
        $this->publishes([
            __DIR__.'/config/movie-content-generator.php' => config_path('movie-content-generator.php'),
        ], 'config');

        // Publish views
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/movie-content-generator'),
        ], 'views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateMovieContent::class,
            ]);
        }
    }

    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/config/movie-content-generator.php', 'movie-content-generator'
        );

        // Register services
        $this->app->singleton(GeminiApiService::class, function ($app) {
            return new GeminiApiService(config('movie-content-generator.gemini_api_key'));
        });
        // Đăng ký lệnh artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                \namhuunam\MovieContentGenerator\Console\Commands\GenerateContentCommand::class,
                \namhuunam\MovieContentGenerator\Console\Commands\InstallCommand::class,
            ]);
        }
    }
}
