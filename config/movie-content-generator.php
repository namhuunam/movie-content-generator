<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình API key và endpoint cho Google Gemini AI
    |
    */
    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    'gemini_api_endpoint' => env('GEMINI_API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-8b:generateContent'),
    'gemini_model' => env('GEMINI_MODEL', 'gemini-1.5-flash-8b'),

    /*
    |--------------------------------------------------------------------------
    | Content Generation Options
    |--------------------------------------------------------------------------
    |
    | Các tùy chọn cho việc tạo nội dung
    |
    */
    'batch_size' => env('MOVIE_CONTENT_MAX_BATCH', 10),
    
    'prompt_template' => env('GEMINI_PROMPT_TEMPLATE', 
        "Dựa trên tiêu đề {name} và mô tả {content}, hãy viết một bài viết về phim chuẩn SEO với độ dài khoảng 150 đến 300 từ tránh trùng lặp nội dung với nội dung các website khác. Ngôn ngữ 100% tiếng việt, tuyệt đối không dùng Markdown, không chèn ảnh, không chèn bất kỳ link, và ký tự đặc biệt nào."
    ),
    
    /*
    |--------------------------------------------------------------------------
    | Admin Route Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho trang quản trị
    |
    */
    'admin' => [
        // Bật/tắt trang quản trị
        'enabled' => env('MOVIE_CONTENT_ADMIN_ENABLED', true),
        
        // Đường dẫn URL cho trang quản trị
        'url' => env('MOVIE_CONTENT_ADMIN_URL', 'admin/movie-content'),
        
        // Middleware kiểm soát quyền truy cập trang quản trị
        'middleware' => env('MOVIE_CONTENT_ADMIN_MIDDLEWARE', ['web', 'auth']),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình ghi log
    |
    */
    'log_channel' => env('MOVIE_CONTENT_LOG_CHANNEL', 'movie-content'),
];
