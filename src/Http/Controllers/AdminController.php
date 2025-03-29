<?php

namespace namhuunam\MovieContentGenerator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Hiển thị trang quản trị
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->getStats();
        return view('movie-content-generator::admin', compact('stats'));
    }
    
    /**
     * Xử lý yêu cầu tạo nội dung thủ công
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateContent(Request $request)
    {
        $id = $request->input('movie_id');
        
        if ($id) {
            // Gọi lệnh artisan để tạo nội dung cho một bộ phim cụ thể
            $exitCode = \Artisan::call('movies:generate-content', [
                '--batch' => 1,
                '--movie' => $id,
                '--force' => true
            ]);
            
            if ($exitCode === 0) {
                return redirect()->back()->with('success', 'Đã tạo nội dung thành công cho phim ID: ' . $id);
            } else {
                return redirect()->back()->with('error', 'Có lỗi khi tạo nội dung cho phim ID: ' . $id);
            }
        } else {
            // Tạo nội dung cho một batch phim
            $batchSize = $request->input('batch_size', 5);
            $exitCode = \Artisan::call('movies:generate-content', [
                '--batch' => $batchSize,
                '--force' => $request->has('force')
            ]);
            
            if ($exitCode === 0) {
                return redirect()->back()->with('success', 'Đã tạo nội dung thành công cho ' . $batchSize . ' phim');
            } else {
                return redirect()->back()->with('error', 'Có lỗi khi tạo nội dung');
            }
        }
    }
    
    /**
     * Lấy thống kê về trạng thái tạo nội dung
     *
     * @return array
     */
    private function getStats()
    {
        $hasCompleteColumn = $this->checkIfCompleteColumnExists();
        
        $total = DB::table('movies')->count();
        
        $completed = 0;
        $pending = 0;
        $recentMovies = collect();
        
        if ($hasCompleteColumn) {
            $completed = DB::table('movies')
                ->whereNotNull('content')
                ->where('complete', 1)
                ->count();
                
            $pending = DB::table('movies')
                ->where(function($query) {
                    $query->whereNull('content')
                        ->orWhere('content', '')
                        ->orWhere('complete', 0);
                })
                ->count();
                
            $recentMovies = DB::table('movies')
                ->whereNotNull('content')
                ->where('complete', 1)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'updated_at']);
        } else {
            $completed = DB::table('movies')
                ->whereNotNull('content')
                ->where('content', '<>', '')
                ->count();
                
            $pending = DB::table('movies')
                ->where(function($query) {
                    $query->whereNull('content')
                        ->orWhere('content', '');
                })
                ->count();
                
            $recentMovies = DB::table('movies')
                ->whereNotNull('content')
                ->where('content', '<>', '')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'updated_at']);
        }
        
        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'percent_complete' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'recent_movies' => $recentMovies,
            'has_complete_column' => $hasCompleteColumn
        ];
    }
    
    /**
     * Kiểm tra xem cột complete có tồn tại trong bảng movies không
     *
     * @return bool
     */
    private function checkIfCompleteColumnExists()
    {
        try {
            return \Schema::hasColumn('movies', 'complete');
        } catch (\Exception $e) {
            return false;
        }
    }
}