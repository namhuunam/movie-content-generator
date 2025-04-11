<?php

namespace namhuunam\MovieContentGenerator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use namhuunam\MovieContentGenerator\Services\GeminiApiService;

class DebugController extends Controller
{
    protected $geminiService;
    
    public function __construct(GeminiApiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    
    public function index()
    {
        // Kiểm tra cấu hình API key
        $apiKey = config('movie-content-generator.gemini_api_key');
        $apiStatus = !empty($apiKey) ? 'Configured' : 'Missing';
        
        // Lấy thống kê
        $stats = [
            'total' => DB::table('movies')->count(),
            'completed' => DB::table('movies')->where('complete', 1)->count(),
            'pending' => DB::table('movies')->where('complete', 0)->count(),
        ];
        
        // Lấy mẫu phim đã hoàn thành và chưa hoàn thành
        $completedMovies = DB::table('movies')
            ->where('complete', 1)
            ->limit(5)
            ->get();
            
        $pendingMovies = DB::table('movies')
            ->where('complete', 0)
            ->limit(5)
            ->get();
        
        // Kiểm tra API bằng cách gọi thử
        $apiTestResult = 'Not tested';
        try {
            $testPrompt = 'Say hello in a short sentence.';
            $apiResponse = $this->geminiService->generateContent($testPrompt);
            $apiTestResult = 'Success: ' . $apiResponse;
        } catch (\Exception $e) {
            $apiTestResult = 'Error: ' . $e->getMessage();
        }
        
        return view('movie-content-generator::debug', [
            'apiStatus' => $apiStatus,
            'stats' => $stats,
            'completedMovies' => $completedMovies,
            'pendingMovies' => $pendingMovies,
            'apiTestResult' => $apiTestResult
        ]);
    }
    
    public function testMovie($id)
    {
        try {
            $movie = DB::table('movies')->where('id', $id)->first();
            
            if (!$movie) {
                return response()->json(['error' => 'Movie not found'], 404);
            }
            
            $promptTemplate = config('movie-content-generator.prompt_template');
            
            $prompt = str_replace(
                ['{name}', '{content}'],
                [$movie->name, $movie->content],
                $promptTemplate
            );
            
            $generatedContent = $this->geminiService->generateContent($prompt);
            
            return response()->json([
                'movie' => $movie,
                'generated_content' => $generatedContent,
                'is_different' => trim($movie->content) != trim('<p>' . $generatedContent . '</p>')
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
