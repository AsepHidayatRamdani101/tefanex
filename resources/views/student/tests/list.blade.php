@extends('adminlte::page')

@section('title', 'Daftar Test')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header with-border">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Daftar Test Pretest & Posttest
                        </h3>
                    </div>

                    <div class="card-body">
                        @if ($tests->count() == 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada test yang tersedia saat ini.
                            </div>
                        @else
                            <div class="row">
                                @foreach ($tests as $test)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <span class="badge badge-{{ $test->type === 'pretest' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($test->type) }}
                                                    </span>
                                                </h5>
                                            </div>

                                            <div class="card-body">
                                                <h6 class="font-weight-bold">{{ $test->material->name ?? 'N/A' }}</h6>
                                                <p class="text-muted small mb-3">
                                                    {{ $test->material->description ?? '' }}
                                                </p>

                                                <div class="test-info mb-3">
                                                    <p class="mb-2">
                                                        <i class="fas fa-question-circle"></i>
                                                        <strong>Jumlah Soal:</strong> {{ $test->questions->count() }}
                                                    </p>
                                                    @if ($test->completed)
                                                        <p class="mb-2">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <strong>Status:</strong> <span class="text-success">Selesai</span>
                                                        </p>
                                                        <p class="mb-0">
                                                            <i class="fas fa-star text-warning"></i>
                                                            <strong>Nilai:</strong> <span class="font-weight-bold">{{ $test->score }}%</span>
                                                        </p>
                                                    @else
                                                        <p class="mb-0">
                                                            <i class="fas fa-hourglass-end text-warning"></i>
                                                            <strong>Status:</strong> <span class="text-warning">Belum Dikerjakan</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card-footer bg-light">
                                                @if ($test->completed)
                                                    <a href="{{ route('student.test.result', $test->testResult->id ?? '#') }}"
                                                       class="btn btn-sm btn-info w-100">
                                                        <i class="fas fa-eye"></i> Lihat Hasil
                                                    </a>
                                                @else
                                                    <a href="{{ route('student.test.show', $test->id) }}"
                                                       class="btn btn-sm btn-success w-100">
                                                        <i class="fas fa-play"></i> Mulai Test
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
