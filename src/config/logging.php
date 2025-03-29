// Add this to your existing config/logging.php channels array
'movie-content' => [
    'driver' => 'single',
    'path' => storage_path('logs/movie-content.log'),
    'level' => 'debug',
],