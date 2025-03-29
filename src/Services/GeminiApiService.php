<?php

namespace HuyUDB24CC149\MovieContentGenerator\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiApiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model = 'gemini-1.5-flash-8b'; // Model mới

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function generateContent($prompt)
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('Gemini API key is not set');
            }

            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topP' => 0.8,
                    'topK' => 40,
                    'maxOutputTokens' => 250,
                    'responseMimeType' => 'text/plain',
                ]
            ]);

            if (!$response->successful()) {
                throw new Exception('Gemini API request failed: ' . $response->body());
            }

            $data = $response->json();
            
            // Kiểm tra cấu trúc phản hồi
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                throw new Exception('Unexpected response format from Gemini API: ' . json_encode($data));
            }

            return $data['candidates'][0]['content']['parts'][0]['text'];
        } catch (Exception $e) {
            Log::channel(config('movie-content-generator.log_channel', 'movie-content'))
                ->error('Gemini API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
