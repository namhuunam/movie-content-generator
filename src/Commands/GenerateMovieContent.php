<?php

namespace namhuunam\MovieContentGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use namhuunam\MovieContentGenerator\Services\GeminiApiService;
use Exception;

class GenerateMovieContent extends Command
{
    protected $signature = 'movies:generate-content {--batch=} {--force}';
    protected $description = 'Generate content for movies using Gemini API';

    protected $geminiService;

    public function __construct(GeminiApiService $geminiService)
    {
        parent::__construct();
        $this->geminiService = $geminiService;
    }

    public function handle()
    {
        $batchSize = $this->option('batch') ?: config('movie-content-generator.batch_size', 10);
        $force = $this->option('force');

        if (empty(config('movie-content-generator.gemini_api_key'))) {
            $this->error('Gemini API key is not set. Please configure it in the admin panel or .env file.');
            return 1;
        }

        try {
            $movies = DB::table('movies')
                ->where('complete', 0)
                ->take($batchSize)
                ->get();

            if ($movies->isEmpty()) {
                $this->info('No movies found with complete = 0. All done!');
                return 0;
            }

            $this->info("Found {$movies->count()} movies to process");
            $bar = $this->output->createProgressBar($movies->count());
            $bar->start();

            $promptTemplate = config('movie-content-generator.prompt_template');
            
            foreach ($movies as $movie) {
                try {
                    $prompt = str_replace(
                        ['{name}', '{content}'],
                        [$movie->name, $movie->content],
                        $promptTemplate
                    );

                    $generatedContent = $this->geminiService->generateContent($prompt);

                    DB::table('movies')
                        ->where('id', $movie->id)
                        ->update([
                            'content' => $generatedContent,
                            'complete' => 1,
                            'updated_at' => now()
                        ]);

                    $bar->advance();
                    
                    // Add a small delay to avoid rate limiting
                    sleep(1);
                    
                } catch (Exception $e) {
                    $bar->finish();
                    $this->newLine();
                    $this->error("Error processing movie ID {$movie->id}: {$e->getMessage()}");
                    
                    Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                        ->error("Error processing movie ID {$movie->id}: {$e->getMessage()}");
                    
                    if (!$force) {
                        return 1;
                    }
                }
            }

            $bar->finish();
            $this->newLine(2);
            $this->info('Content generation completed successfully!');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error("An unexpected error occurred: {$e->getMessage()}");
            
            Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                ->error("Unexpected error: {$e->getMessage()}");
            
            return 1;
        }
    }
}