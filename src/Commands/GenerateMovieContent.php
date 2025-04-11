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
            // Lấy danh sách phim cần xử lý
            $query = DB::table('movies');
            
            if (!$force) {
                $query->where('complete', 0);
            }
            
            $movies = $query->take($batchSize)->get();

            if ($movies->isEmpty()) {
                $this->info('No movies found to process. All done!');
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

                    // Gọi API Gemini để sinh nội dung
                    $generatedContent = $this->geminiService->generateContent($prompt);
                    
                    // Kiểm tra nội dung sinh ra có hợp lệ không
                    if (empty($generatedContent) || strlen(trim($generatedContent)) < 50) {
                        Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                            ->warning("Movie ID {$movie->id}: Generated content is too short or empty. Not updating.");
                        
                        $bar->advance();
                        sleep(2);
                        continue;
                    }
                    
                    // Định dạng nội dung sinh ra
                    $formattedContent = '<p>' . $generatedContent . '</p>';
                    
                    // Kiểm tra nếu nội dung mới giống nội dung cũ
                    $currentContent = $movie->content;
                    if (trim($currentContent) == trim($formattedContent)) {
                        Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                            ->warning("Movie ID {$movie->id}: Generated content is identical to existing content. Not updating.");
                            
                        $bar->advance();
                        sleep(2);
                        continue;
                    }
                    
                    // Cập nhật nội dung mới và đánh dấu đã hoàn thành
                    DB::table('movies')
                        ->where('id', $movie->id)
                        ->update([
                            'content' => $formattedContent,
                            'complete' => 1
                        ]);
                    
                    Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                        ->info("Movie ID {$movie->id}: Content updated successfully.");

                    $bar->advance();
                    
                    // Thêm độ trễ nhỏ để tránh rate limiting
                    sleep(2);
                    
                } catch (Exception $e) {
                    $bar->finish();
                    $this->newLine();
                    $this->error("Error processing movie ID {$movie->id}: {$e->getMessage()}");
                    
                    Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                        ->error("Error processing movie ID {$movie->id}: {$e->getMessage()}");
                    
                    // Không đánh dấu phim là đã hoàn thành khi có lỗi xảy ra
                    // Nếu không dùng --force thì dừng lại
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
