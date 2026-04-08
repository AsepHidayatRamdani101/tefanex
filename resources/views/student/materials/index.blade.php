@extends('adminlte::page')

@section('title', 'Materi Saya')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header with-border bg-primary">
                        <h3 class="card-title text-white">
                            <i class="fas fa-book"></i> Materi Pembelajaran
                        </h3>
                    </div>

                    <div class="card-body">
                        @if($materials->count() > 0)
                            <div class="row">
                                @foreach($materials as $material)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title font-weight-bold">
                                                    {{ $material->title }}
                                                </h5>
                                                <p class="card-text text-muted small mb-2">
                                                    <i class="fas fa-project-diagram"></i>
                                                    {{ $material->project->judul ?? 'N/A' }}
                                                </p>
                                                <p class="card-text text-muted small mb-2">
                                                    <i class="fas fa-user"></i>
                                                    {{ $material->creator->name ?? 'N/A' }}
                                                </p>
                                                <p class="card-text" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ strip_tags($material->content) }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $material->created_at->format('d-m-Y') }}
                                                </small>
                                            </div>
                                            <div class="card-footer bg-white border-top">
                                                <a href="{{ route('student.material.detail', $material) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    {{ $materials->links() }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i>
                                Belum ada materi untuk project Anda. Silakan hubungi guru untuk mendapatkan materi.
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('student.tasks.index') }}" class="btn btn-info float-right">
                            <i class="fas fa-tasks"></i> Lihat Tugas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <div class="float-right d-none d-sm-inline">Versi 1.0</div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">TEFANEX</a>.</strong> All rights reserved.
@endsection
