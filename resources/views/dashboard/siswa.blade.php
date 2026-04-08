@section('title', 'Dashboard Siswa')
@extends('adminlte::page')

@section('content')
    <div class="container pt-4">
        <div>
            <h1>Selamat Datang di Dashboard Siswa</h1>
            <p>Ini adalah halaman dashboard untuk siswa. Di sini Anda dapat melihat informasi terkait proyek, materi, tugas, dan absensi Anda.</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small card -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ \App\Models\Attendance::where('user_id', auth()->user()->id)->count() }}</h3>

                        <p>Absensi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small card -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ \App\Models\Project::whereHas('project_members', function (\Illuminate\Database\Eloquent\Builder $builder) {
                            $builder->where('user_id', '=', auth()->user()->id);
                        })->count() }}</h3>

                        <p>Project Saya</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small card -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ \App\Models\Material::whereHas('project', function (\Illuminate\Database\Eloquent\Builder $builder) {
                            $builder->whereHas('project_members', function (\Illuminate\Database\Eloquent\Builder $builder2) {
                                $builder2->where('user_id', '=', auth()->user()->id);
                            });
                        })->count() }}</h3>

                        <p>Materi Saya</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small card -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ \App\Models\Project_Member::where('user_id', auth()->user()->id)->count() }}</h3>

                        <p>Tugas Hari ini </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <!-- ./col -->
            
            <!-- ./col -->
            
            <!-- ./col -->
        </div>
    </div>
@stop
