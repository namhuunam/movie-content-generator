@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1>Movie Content Generator</h1>
                </div>

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

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-header">Tổng số phim</div>
                                <div class="card-body">
                                    <h2 class="card-title">{{ $stats['total'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-header">Đã tạo nội dung</div>
                                <div class="card-body">
                                    <h2 class="card-title">{{ $stats['completed'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-header">Cần tạo nội dung</div>
                                <div class="card-body">
                                    <h2 class="card-title">{{ $stats['pending'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-header">Tiến độ</div>
                                <div class="card-body">
                                    <h2 class="card-title">{{ $stats['percent_complete'] }}%</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h3>Tạo nội dung tự động</h3>
                            <form method="POST" action="{{ route('movie-content-generator.generate') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="batch_size">Số lượng phim xử lý:</label>
                                    <input type="number" class="form-control" id="batch_size" name="batch_size" value="5" min="1" max="50">
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="force" name="force">
                                    <label class="form-check-label" for="force">Tạo lại nội dung cho phim đã có</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Tạo nội dung</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h3>Tạo nội dung cho phim cụ thể</h3>
                            <form method="POST" action="{{ route('movie-content-generator.generate') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="movie_id">ID Phim:</label>
                                    <input type="number" class="form-control" id="movie_id" name="movie_id" required min="1">
                                </div>
                                <button type="submit" class="btn btn-primary">Tạo nội dung</button>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <h3>Phim được tạo gần đây</h3>
                    @if($stats['recent_movies']->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Ngày cập nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_movies'] as $movie)
                                    <tr>
                                        <td>{{ $movie->id }}</td>
                                        <td>{{ $movie->name }}</td>
                                        <td>{{ $movie->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">
                            Chưa có phim nào được tạo nội dung.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection