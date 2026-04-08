@section('title', 'Dashboard Guru')
@extends('adminlte::page')

@section('content')
    <div class="container pt-4">
        <div>
            <h1>Selamat Datang di Dashboard Guru</h1>
            <p>Ini adalah halaman dashboard untuk guru. Di sini Anda dapat melihat informasi terkait proyek, materi, tugas, dan absensi siswa Anda.</p>
        </div>
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ \App\Models\Project_Member::where('project_id', auth()->user()->id)->count() }}</h3>

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
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ \App\Models\Project::whereHas('project_members', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })->where('status', '<>', 'awal')
                    ->count() }}</h3>

                <p>Progress Project </p>
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
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>44</h3>

                <p>Aktivitas siswa</p>
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
          
          <!-- ./col -->
        </div>
    </div>
@stop
