<?php

namespace namhuunam\MovieContentGenerator\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'movie-content:install';
    protected $description = 'Install Movie Content Generator package';

    public function handle()
    {
        $this->info('Installing Movie Content Generator...');
        
        // Chạy migration
        $this->call('migrate');
        
        // Xuất config và view
        $this->call('vendor:publish', [
            '--provider' => 'namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider',
            '--tag' => 'config'
        ]);
        
        $this->call('vendor:publish', [
            '--provider' => 'namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider',
            '--tag' => 'views'
        ]);
        
        // Xóa cache
        $this->call('config:clear');
        $this->call('cache:clear');
        
        $this->info('Movie Content Generator has been installed successfully!');
        $this->info('You can now use the package by accessing /admin/movie-content or running `php artisan movies:generate-content`');
        
        return 0;
    }
}