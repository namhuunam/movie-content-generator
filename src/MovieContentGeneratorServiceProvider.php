<?php

namespace namhuunam\MovieContentGenerator;

use Illuminate\Support\ServiceProvider;
use namhuunam\MovieContentGenerator\Commands\GenerateMovieContent;
use namhuunam\MovieContentGenerator\Commands\InstallCommand;
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
        // Publish config
        $this->publishes([
            __DIR__.'/../config/movie-content-generator.php' => config_path('movie-content-generator.php'),
        ], 'config');
        
        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/movie-content-generator'),
        ], 'views');
        
        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');
        
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'movie-content-generator');
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateMovieContent::class,
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/movie-content-generator.php', 'movie-content-generator'
        );
        
        // Bind GeminiApiService to the container
        $this->app->singleton(GeminiApiService::class, function ($app) {
            return new GeminiApiService();
        });
    }
}