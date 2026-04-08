@section('title', 'Dashboard Admin')
@extends('adminlte::page')

@section('content')
    <div class="container pt-4">
        <h1 class="mb-4">Selamat Datang di Dashboard Admin</h1>
        <p class="mb-8">Ini adalah halaman dashboard untuk admin. Di sini Anda dapat melihat informasi terkait proyek, materi, tugas, dan absensi siswa Anda.</p>

        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ \App\Models\Project::count() }}</h3>

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
                <h3>{{ number_format(\App\Models\Project::where('status', '!=', 'done')->count() / \App\Models\Project::count() * 100, 2) }}<sup style="font-size: 20px">%</sup></h3>

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
                <h3>{{ \App\Models\Student::count() }}</h3>

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

