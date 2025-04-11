@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1>Movie Content Generator Debug</h1>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Cấu hình hệ thống</div>
                                <div class="card-body">
                                    <p><strong>API Key Status:</strong> {{ $apiStatus }}</p>
                                    <p><strong>API Test:</strong><br>{{ $apiTestResult }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Thống kê</div>
                                <div class="card-body">
                                    <p><strong>Tổng số phim:</strong> {{ $stats['total'] }}</p>
                                    <p><strong>Đã hoàn thành:</strong> {{ $stats['completed'] }}</p>
                                    <p><strong>Đang chờ:</strong> {{ $stats['pending'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Phim đã hoàn thành (5 mẫu)</div>
                                <div class="card-body">
                                    @if(count($completedMovies) > 0)
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tên</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($completedMovies as $movie)
                                                    <tr>
                                                        <td>{{ $movie->id }}</td>
                                                        <td>{{ $movie->name }}</td>
                                                        <td>
                                                            <a href="{{ route('movie-content-generator.debug.movie', $movie->id) }}" 
                                                               class="btn btn-sm btn-primary" target="_blank">
                                                                Test API
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info">
                                            Không có phim nào đã hoàn thành.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Phim chưa hoàn thành (5 mẫu)</div>
                                <div class="card-body">
                                    @if(count($pendingMovies) > 0)
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tên</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pendingMovies as $movie)
                                                    <tr>
                                                        <td>{{ $movie->id }}</td>
                                                        <td>{{ $movie->name }}</td>
                                                        <td>
                                                            <a href="{{ route('movie-content-generator.debug.movie', $movie->id) }}" 
                                                               class="btn btn-sm btn-primary" target="_blank">
                                                                Test API
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info">
                                            Không có phim nào cần hoàn thành.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
