@extends('adminlte::page')

@section('title', 'Detail Materi')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header with-border bg-primary">
                        <h3 class="card-title text-white">
                            <i class="fas fa-book"></i> {{ $material->title }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Project:</strong>
                                    {{ $material->project->judul ?? 'N/A' }}
                                </p>
                                <p class="mb-2">
                                    <strong>Diajarkan oleh:</strong>
                                    {{ $material->creator->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Tipe Materi:</strong>
                                    {{ $material->type ?? 'N/A' }}
                                </p>
                                <p class="mb-2">
                                    <strong>Tanggal Upload:</strong>
                                    {{ $material->created_at->format('d-m-Y H:i') }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="content-section">
                            <h5 class="mb-3"><strong>Konten Materi</strong></h5>
                            <div class="border p-3 bg-light rounded">
                                {!! $material->content !!}
                            </div>
                        </div>

                        @if($material->file_path)
                            <hr>
                            <div class="file-section">
                                <h5 class="mb-3"><strong>File Lampiran</strong></h5>
                                @php
                                    $filePath = str_replace('storage/', '', $material->file_path);
                                @endphp
                                <a href="{{ Storage::url($filePath) }}" class="btn btn-success" download>
                                    <i class="fas fa-download"></i> Unduh File
                                </a>
                            </div>
                        @endif

                        @if($material->video_link)
                            <hr>
                            <div class="video-section">
                                <h5 class="mb-3"><strong>Video Pembelajaran</strong></h5>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="{{ $material->embed_video_link }}" allowfullscreen></iframe>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('student.materials.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Materi
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
