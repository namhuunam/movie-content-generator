<?php

namespace namhuunam\MovieContentGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    /**
     * Tên và chữ ký của lệnh.
     *
     * @var string
     */
    protected $signature = 'movie-content:install';

    /**
     * Mô tả của lệnh.
     *
     * @var string
     */
    protected $description = 'Cài đặt và cấu hình Movie Content Generator';

    /**
     * Thực thi lệnh.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Đang cài đặt Movie Content Generator...');

        // Kiểm tra bảng movies
        $this->checkMoviesTable();

        // Xuất file cấu hình
        $this->publishConfig();

        // Xuất view
        $this->publishViews();

        // Xuất migration
        $this->publishMigrations();

        // Chạy migration
        $this->runMigrations();

        $this->info('Cài đặt hoàn tất!');
        
        // Hiển thị thông tin bổ sung
        $this->info('');
        $this->info('THÔNG TIN BỔ SUNG:');
        $this->info('1. Nhớ cấu hình GEMINI_API_KEY trong file .env của bạn');
        $this->info('2. Truy cập trang quản trị tại: ' . config('movie-content-generator.admin.url', 'admin/movie-content'));
        $this->info('3. Tham khảo tài liệu tại: https://github.com/namhuunam/movie-content-generator');

        return 0;
    }

    /**
     * Kiểm tra và thông báo về bảng movies
     *
     * @return void
     */
    private function checkMoviesTable()
    {
        $this->info('Kiểm tra bảng movies...');

        if (!Schema::hasTable('movies')) {
            $this->warn('Bảng movies không tồn tại. Tạo bảng movies trước khi sử dụng package này.');
            $this->info('Bảng movies cần có các cột: id, name, content, complete (tùy chọn)');
        } else {
            $this->info('Đã tìm thấy bảng movies.');
            
            // Kiểm tra cột complete
            if (!Schema::hasColumn('movies', 'complete')) {
                $this->warn('Không tìm thấy cột "complete" trong bảng movies.');
                $this->info('Bạn nên thêm cột "complete" bằng cách chạy migration:');
                $this->info('  php artisan migrate');
            } else {
                $this->info('Cấu trúc bảng movies đã đúng.');
            }
        }
    }

    /**
     * Xuất file cấu hình
     *
     * @return void
     */
    private function publishConfig()
    {
        $this->info('Xuất file cấu hình...');
        $this->call('vendor:publish', [
            '--provider' => 'namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider',
            '--tag' => 'config'
        ]);
    }

    /**
     * Xuất file view
     *
     * @return void
     */
    private function publishViews()
    {
        $this->info('Xuất file view...');
        $this->call('vendor:publish', [
            '--provider' => 'namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider',
            '--tag' => 'views'
        ]);
    }

    /**
     * Xuất migration
     *
     * @return void
     */
    private function publishMigrations()
    {
        $this->info('Xuất migration...');
        $this->call('vendor:publish', [
            '--provider' => 'namhuunam\MovieContentGenerator\MovieContentGeneratorServiceProvider',
            '--tag' => 'migrations'
        ]);
    }

    /**
     * Chạy migration
     *
     * @return void
     */
    private function runMigrations()
    {
        $this->info('Chạy migration...');
        $this->call('migrate');
    }
}