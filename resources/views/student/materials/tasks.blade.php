@extends('adminlte::page')

@section('title', 'Tugas Saya')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header with-border bg-success">
                        <h3 class="card-title text-white">
                            <i class="fas fa-tasks"></i> Tugas dan Project
                        </h3>
                    </div>

                    <div class="card-body">
                        @if($tasks->count() > 0)
                            <div class="row">
                                @php
                                    $colors = [
                                        'Design Brief' => '#3498db',
                                        'Mockup' => '#9b59b6',
                                        'Produksi' => '#e67e22',
                                        'Quality Control' => '#1abc9c'
                                    ];
                                    $icons = [
                                        'Design Brief' => 'fa-pencil-alt',
                                        'Mockup' => 'fa-drafting-compass',
                                        'Produksi' => 'fa-industry',
                                        'Quality Control' => 'fa-check-circle'
                                    ];
                                @endphp

                                @foreach($tasks as $task)
                                    @php
                                        $bgColor = $colors[$task['type']] ?? '#2ecc71';
                                        $icon = $icons[$task['type']] ?? 'fa-tasks';
                                        $statusClass = $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in_progress' ? 'warning' : 'info');
                                    @endphp
                                    <div class="col-md-6 mb-4">
                                        <div class="card card-outline" style="border-top: 3px solid {{ $bgColor }};">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="row">
                                                        <div class="col-auto">
                                                            <span class="badge badge-secondary mr-1">
                                                                <i class="fas fa-user"></i> {{ $task['role'] }}
                                                            </span>
                                                            <span class="badge badge-{{ $statusClass }}">
                                                                {{ ucfirst($task['status']) }}
                                                            </span>
                                                        </div>
                                                        <div class="col text-right">
                                                            <span class="badge badge-pill" style="background-color: {{ $bgColor }}; color: #fff;">
                                                                {{ $task['created_at']->format('d-m-Y') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <h5 class="card-title mt-2 mb-0 font-weight-bold">
                                                        <i class="fas {{ $icon }}"></i> {{ $task['type'] }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted mb-2"><strong>Instruksi Tugas:</strong> {{ $task['task_description'] }}</p>
                                                <p class="text-muted mb-3">{{ $task['description'] ?: '-' }}</p>

                                                <div class="mb-1">
                                                    @if($task['type'] === 'Design Brief')
                                                        <a href="{{ route('design-brief.index', ['project_id' => $task['project_id']]) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-pencil-alt"></i> Kerjakan Design Brief
                                                        </a>
                                                    @elseif($task['type'] === 'Mockup')
                                                        <a href="{{ route('mockup.index', ['project_id' => $task['project_id']]) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-file-upload"></i> Upload Mockup
                                                        </a>
                                                    @elseif($task['type'] === 'Produksi')
                                                        <a href="{{ route('produksi.index', ['project_id' => $task['project_id']]) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-industry"></i> Upload Produksi
                                                        </a>
                                                    @elseif($task['type'] === 'Quality Control')
                                                        <a href="{{ route('quality-control.index', ['project_id' => $task['project_id']]) }}" class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-check-circle"></i> Lihat QC
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer text-right text-muted small">
                                                <i class="fas fa-clock"></i> {{ $task['created_at']->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i>
                                Belum ada tugas untuk Anda. Nantikan tugas dari guru sesuai dengan role Anda di project.
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('student.materials.index') }}" class="btn btn-info float-right">
                            <i class="fas fa-book"></i> Lihat Materi
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
