<?php

namespace HuyUDB24CC149\MovieContentGenerator;

use Illuminate\Support\ServiceProvider;
use HuyUDB24CC149\MovieContentGenerator\Commands\GenerateMovieContent;
use HuyUDB24CC149\MovieContentGenerator\Services\GeminiApiService;

class MovieContentGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        
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
    }
}
