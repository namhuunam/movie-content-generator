@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Movie Content Generator Settings</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('movie-content-generator.settings.save') }}">
                        @csrf
                        <div class="form-group">
                            <label for="api_key">Gemini API Key</label>
                            <input type="password" class="form-control" id="api_key" name="api_key" 
                                   value="{{ $apiKey }}" required>
                            <small class="form-text text-muted">
                                Get your API key from Google AI Studio dashboard
                            </small>
                        </div>

                        <div class="form-group mt-3">
                            <label for="prompt_template">Prompt Template</label>
                            <textarea class="form-control" id="prompt_template" name="prompt_template" 
                                      rows="5" required>{{ $promptTemplate }}</textarea>
                            <small class="form-text text-muted">
                                Use {name} and {content} placeholders for movie name and content
                            </small>
                        </div>

                        <div class="form-group mt-3">
                            <label for="batch_size">Batch Size</label>
                            <input type="number" class="form-control" id="batch_size" name="batch_size"
                                   value="{{ $batchSize }}" min="1" max="100" required>
                            <small class="form-text text-muted">
                                Number of movies to process in one run
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">Save Settings</button>
                    </form>

                    <hr>

                    <h5 class="mt-4">Run Content Generation</h5>
                    <p>Click the button below to start generating content for movies with <code>complete = 0</code>.</p>

                    <form method="POST" action="{{ route('movie-content-generator.generate') }}">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Generate Movie Content
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">Log Viewer</div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Logs are stored at: <code>{{ config('movie-content-generator.log_path') }}</code>
                    </div>

                    <pre style="max-height: 400px; overflow-y: auto">
@php
    $logPath = config('movie-content-generator.log_path');
    if (file_exists($logPath)) {
        echo htmlspecialchars(file_get_contents($logPath, false, null, -8192));
    } else {
        echo "No logs found.";
    }
@endphp
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection