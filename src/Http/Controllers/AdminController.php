<?php

namespace HuyUDB24CC149\MovieContentGenerator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use Exception;

class AdminController extends Controller
{
    public function index()
    {
        return view('movie-content-generator::admin', [
            'apiKey' => config('movie-content-generator.gemini_api_key'),
            'promptTemplate' => config('movie-content-generator.prompt_template'),
            'batchSize' => config('movie-content-generator.batch_size'),
        ]);
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'prompt_template' => 'required|string',
            'batch_size' => 'required|integer|min:1|max:100',
        ]);

        // Update .env file
        $this->updateEnvironmentFile([
            'GEMINI_API_KEY' => $request->api_key,
            'GEMINI_PROMPT_TEMPLATE' => $request->prompt_template,
            'MOVIE_CONTENT_BATCH_SIZE' => $request->batch_size,
        ]);

        // Update config at runtime
        Config::set('movie-content-generator.gemini_api_key', $request->api_key);
        Config::set('movie-content-generator.prompt_template', $request->prompt_template);
        Config::set('movie-content-generator.batch_size', $request->batch_size);

        return redirect()->route('movie-content-generator.index')
            ->with('success', 'Settings saved successfully');
    }

    public function generateContent(Request $request)
    {
        try {
            $exitCode = Artisan::call('movies:generate-content', [
                '--batch' => $request->input('batch_size', config('movie-content-generator.batch_size')),
            ]);

            $output = Artisan::output();

            if ($exitCode === 0) {
                return redirect()->route('movie-content-generator.index')
                    ->with('success', 'Content generation started successfully. ' . $output);
            } else {
                return redirect()->route('movie-content-generator.index')
                    ->with('error', 'Content generation failed. ' . $output);
            }
        } catch (Exception $e) {
            return redirect()->route('movie-content-generator.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    protected function updateEnvironmentFile(array $data)
    {
        $path = app()->environmentFilePath();
        $env = file_get_contents($path);

        foreach ($data as $key => $value) {
            // If key exists, replace it
            if (strpos($env, "{$key}=") !== false) {
                $env = preg_replace(
                    "/{$key}=.*?(\r|\n)/",
                    "{$key}=\"{$value}\"\$1",
                    $env
                );
            } else {
                // If key doesn't exist, add it
                $env .= "\n{$key}=\"{$value}\"";
            }
        }

        file_put_contents($path, $env);
    }
}
