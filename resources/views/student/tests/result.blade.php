@extends('adminlte::page')

@section('title', 'Hasil Test')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header with-border bg-success">
                        <h3 class="card-title text-white">
                            <i class="fas fa-check-circle"></i> Hasil {{ ucfirst($testResult->test->type) }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box bg-blue">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Materi</span>
                                        <span class="info-box-number">{{ $testResult->test->material->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-green">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Nilai Anda</span>
                                        <span class="info-box-number">{{ $testResult->score }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Soal</span>
                                        <span class="info-box-number">{{ $testResult->test->questions->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Jawab Benar</span>
                                        <span class="info-box-number">
                                            {{ $testResult->studentAnswers->where('is_correct', true)->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-danger">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Jawab Salah</span>
                                        <span class="info-box-number">
                                            {{ $testResult->studentAnswers->where('is_correct', false)->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h4 class="mb-3">Detail Jawaban</h4>

                        @foreach ($testResult->test->questions as $index => $question)
                            @php
                                $studentAnswer = $testResult->studentAnswers
                                    ->where('question_id', $question->id)
                                    ->first();
                            @endphp
                            <div class="card mb-3 {{ $studentAnswer->is_correct ? 'border-success' : 'border-danger' }}">
                                <div class="card-header {{ $studentAnswer->is_correct ? 'bg-success' : 'bg-danger' }} text-white">
                                    <h6 class="mb-0">
                                        <strong>Soal {{ $index + 1 }}</strong>
                                        <span class="float-right">
                                            @if ($studentAnswer->is_correct)
                                                <i class="fas fa-check-circle"></i> Benar
                                            @else
                                                <i class="fas fa-times-circle"></i> Salah
                                            @endif
                                        </span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Pertanyaan:</strong></p>
                                    <p class="ml-3 mb-3">{{ $question->question_text }}</p>

                                    @if ($question->type === 'multiple_choice')
                                        <p class="mb-2"><strong>Pilihan Anda:</strong></p>
                                        @if ($studentAnswer->selected_option)
                                            @php
                                                $selectedAnswer = $question->answers->find($studentAnswer->selected_option);
                                                $answerIndex = $question->answers->search(
                                                    fn($a) => $a->id == $studentAnswer->selected_option
                                                );
                                            @endphp
                                            <p class="ml-3 mb-3">
                                                @if ($answerIndex !== false)
                                                    {{ chr(65 + $answerIndex) }}.
                                                @endif
                                                {{ $selectedAnswer->answer_text ?? 'N/A' }}
                                            </p>
                                        @else
                                            <p class="ml-3 mb-3 text-muted">Tidak ada jawaban</p>
                                        @endif

                                        @if (!$studentAnswer->is_correct)
                                            <p class="mb-2"><strong>Jawaban Benar:</strong></p>
                                            @php
                                                $correctAnswer = $question->answers->where('is_correct', true)->first();
                                                $correctIndex = $question->answers->search(
                                                    fn($a) => $a->is_correct
                                                );
                                            @endphp
                                            @if ($correctAnswer)
                                                <p class="ml-3 text-success">
                                                    @if ($correctIndex !== false)
                                                        {{ chr(65 + $correctIndex) }}.
                                                    @endif
                                                    {{ $correctAnswer->answer_text }}
                                                </p>
                                            @endif
                                        @endif
                                    @else
                                        <p class="mb-2"><strong>Jawaban Anda:</strong></p>
                                        <div class="ml-3 mb-3 p-2 bg-light rounded">
                                            {{ $studentAnswer->answer_text ?? 'Tidak ada jawaban' }}
                                        </div>
                                        <p class="text-warning small">
                                            <i class="fas fa-info-circle"></i> Jawaban essay akan dikoreksi secara manual oleh guru.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('student.tests.list') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Test
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
