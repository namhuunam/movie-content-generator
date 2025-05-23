# Cài đặt Movie Content Generator
## 1. Cài đặt package
```bash
# Thêm repository vào composer.json
composer config repositories.movie-content-generator vcs https://github.com/namhuunam/movie-content-generator.git

# Cài đặt package
composer require namhuunam/movie-content-generator:dev-main

# Chạy lệnh cài đặt tự động
php artisan movie-content:install

# Xuất file cấu hình và view
php artisan vendor:publish --provider="namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider" --tag="config"
php artisan vendor:publish --provider="namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider" --tag="views"
php artisan vendor:publish --provider="namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider" --force
```
## 2. Cấu hình môi trường
Thêm các dòng sau vào file .env:
```bash
# API key cho Google Gemini
GEMINI_API_KEY=your_gemini_api_key_here

# Tùy chỉnh prompt template
GEMINI_PROMPT_TEMPLATE="Dựa trên tiêu đề {name} và mô tả {content}, hãy viết một bài viết về phim chuẩn SEO với độ dài khoảng 150 đến 300 từ tránh trùng lặp nội dung với nội dung các website khác. Ngôn ngữ 100% tiếng việt, tuyệt đối không dùng Markdown, không chèn ảnh, không chèn bất kỳ link, và ký tự đặc biệt nào."

# Cấu hình sinh nội dung
MOVIE_CONTENT_MAX_BATCH=10

# Cấu hình logging
MOVIE_CONTENT_LOG_CHANNEL=movie-content

# Cấu hình trang quản trị
MOVIE_CONTENT_ADMIN_ENABLED=true
MOVIE_CONTENT_ADMIN_URL=admin/movie-content
```
## 3. Cấu hình quyền hạn và xóa cache
```bash
# Đặt quyền hạn cho thư mục storage
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Xóa cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```
## 4. Kiểm tra cài đặt
```bash
# Kiểm tra lệnh artisan
php artisan list | grep movies

# Kiểm tra route
php artisan route:list | grep movie-content

# Thử chạy lệnh tạo nội dung
php artisan movies:generate-content --batch=1
```
## 5. Thiết lập Cron Job
Thêm dòng sau vào crontab:
```bash
crontab -e
```
```bash
# Chạy tự động mỗi giờ
*/15 * * * * cd /path/to/your/laravel/project && php artisan movies:generate-content --force >> /dev/null 2>&1
*/15 * * * * cd /path/to/your/laravel/project && php artisan movies:generate-content --batch=50 >> /dev/null 2>&1
*/15 * * * * cd /path/to/your/laravel/project && php artisan movies:generate-content >> /dev/null 2>&1
```
## 6 Kiểm tra debug
```bash
https://vlxxz.pro/movie-debug
website.com/movie-debug
```
# Xử lý sự cố
## Sửa lỗi Gemini API không hoạt động
Chỉnh sửa file GeminiApiService.php:
```bash
nano vendor/namhuunam/movie-content-generator/src/Services/GeminiApiService.php
```
Cập nhật model và phiên bản API:
```bash
protected $model = 'gemini-1.5-flash-8b';

public function __construct($apiKey)
{
    $this->apiKey = $apiKey;
    $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
}
```
## Lỗi quyền truy cập vào log file
```bash
mkdir -p storage/logs
touch storage/logs/movie-content.log
chmod -R 775 storage/logs
chown -R www-data:www-data storage/logs
```
# Lỗi không thể tìm thấy Service Provider
Thêm Service Provider vào file config/app.php:
```bash
'providers' => [
    // Các providers khác...
    namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider::class,
],
```
# Lỗi không tìm thấy package
```bash
composer clearcache
composer update
```
# Sử dụng
1. Truy cập trang quản trị tại đường dẫn đã cấu hình (mặc định: /admin/movie-content)
2. Sử dụng trang quản trị để tạo nội dung thủ công hoặc tự động
3. Hoặc sử dụng lệnh artisan để tạo nội dung:
```bash
# Tạo nội dung cho 5 phim đầu tiên với complete=0
php artisan movies:generate-content --batch=5

# Tạo lại nội dung cho tất cả phim (kể cả đã có nội dung)
php artisan movies:generate-content --force
```
# đặt complete content dưới 500 ký tự
```bash
UPDATE movies
SET complete = 0
WHERE LENGTH(content) < 500;
```
