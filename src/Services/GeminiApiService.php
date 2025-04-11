<?php

namespace namhuunam\MovieContentGenerator\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiApiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('movie-content-generator.gemini_api_key');
        $this->model = config('movie-content-generator.gemini_model', 'gemini-1.5-flash-8b');
        $this->baseUrl = config('movie-content-generator.gemini_api_endpoint', 
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent");
    }

    /**
     * Generate content using Google Gemini API
     *
     * @param string $prompt
     * @return string
     * @throws Exception
     */
    public function generateContent(string $prompt): string
    {
        try {
            // Log gọi API
            Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                ->info("Calling Gemini API with prompt: " . substr($prompt, 0, 100) . "...");
            
            // Gọi API Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 350,
                ]
            ]);

            // Kiểm tra response
            if ($response->successful()) {
                $data = $response->json();
                
                // Log phản hồi API
                Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                    ->debug("API response status: success");
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Kiểm tra nội dung trả về
                    if (empty(trim($content))) {
                        Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                            ->warning("API returned empty content");
                        throw new Exception('API returned empty content');
                    }
                    
                    // Log nội dung trả về (một phần)
                    Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                        ->debug("API returned content: " . substr($content, 0, 100) . "...");
                    
                    return $content;
                } else {
                    Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                        ->error('Unexpected API response structure: ' . json_encode($data));
                    throw new Exception('Unexpected API response structure');
                }
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';
                $errorCode = $errorData['error']['code'] ?? 500;
                
                Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                    ->error("API Error ({$errorCode}): {$errorMessage}");
                    
                throw new Exception("API Error: {$errorMessage}", $errorCode);
            }
        } catch (Exception $e) {
            Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                ->error('Error generating content: ' . $e->getMessage());
            throw $e;
        }
    }
}
