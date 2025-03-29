<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | This is the API key for the Gemini AI service. You can get this from
    | the Google AI Studio dashboard.
    |
    */
    'gemini_api_key' => env('GEMINI_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Content Generation Prompt
    |--------------------------------------------------------------------------
    |
    | This is the prompt template that will be used for generating content.
    | Use {name} and {content} placeholders which will be replaced with
    | the movie's name and existing content.
    |
    */
    'prompt_template' => env('GEMINI_PROMPT_TEMPLATE', 'Dựa trên tiêu đề {name} và mô tả {content}, hãy viết một bài viết về phim chuẩn SEO với độ dài khoảng 150 đến 300 từ tránh trùng lặp nội dung với nội dung các website khác. Ngôn ngữ 100% tiếng việt, tuyệt đối không dùng Markdown, không chèn ảnh, không chèn bất kỳ link, và ký tự đặc biệt nào.'),

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Configure error logging for the package.
    |
    */
    'log_channel' => env('MOVIE_CONTENT_LOG_CHANNEL', 'movie-content'),
    'log_path' => storage_path('logs/movie-content.log'),

    /*
    |--------------------------------------------------------------------------
    | Processing Settings
    |--------------------------------------------------------------------------
    |
    | Configure how many movies to process in a single run.
    |
    */
    'batch_size' => env('MOVIE_CONTENT_BATCH_SIZE', 10),
];
