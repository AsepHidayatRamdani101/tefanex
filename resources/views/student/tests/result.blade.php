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
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box bg-blue">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Materi</span>
                                        <span class="info-box-number">{{ $testResult->test->material->title ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-green">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Nilai {{ ucfirst($testResult->test->type) }}</span>
                                        <span class="info-box-number">{{ $finalScore }}%</span>
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

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box bg-warning">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Nilai Tugas</span>
                                        <span class="info-box-number">{{ $taskScore !== null ? $taskScore . '%' : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-info">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Catatan Sikap</span>
                                        <span class="info-box-number">{{ $testResult->attitude_note ? 'Tersedia' : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        @if($testResult->attitude_note)
                            <div class="card card-secondary mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Catatan Sikap</h5>
                                    <p class="mb-0">{{ $testResult->attitude_note }}</p>
                                </div>
                            </div>
                        @endif

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
                                                $selectedAnswer = is_numeric($studentAnswer->selected_option)
                                                    ? $question->answers->find($studentAnswer->selected_option)
                                                    : null;
                                                $answerIndex = $selectedAnswer
                                                    ? $question->answers->search(fn($a) => $a->id == $studentAnswer->selected_option)
                                                    : false;
                                                $selectedText = $selectedAnswer
                                                    ? $selectedAnswer->answer_text
                                                    : $studentAnswer->selected_option;
                                            @endphp
                                            <p class="ml-3 mb-3">
                                                @if ($answerIndex !== false)
                                                    {{ chr(65 + $answerIndex) }}.
                                                @endif
                                                {{ $selectedText }}
                                            </p>
                                        @else
                                            <p class="ml-3 mb-3 text-muted">Tidak ada jawaban</p>
                                        @endif

                                        @if (!$studentAnswer->is_correct)
                                            <p class="mb-2"><strong>Jawaban Benar:</strong></p>
                                            @php
                                                $correctAnswer = $question->correct_answer
                                                    ? $question->correct_answer
                                                    : optional($question->answers->where('is_correct', true)->first())->answer_text;
                                                $correctIndex = $question->answers->search(fn($a) => $a->is_correct);
                                            @endphp
                                            @if ($correctAnswer)
                                                <p class="ml-3 text-success">
                                                    @if ($correctIndex !== false)
                                                        {{ chr(65 + $correctIndex) }}.
                                                    @endif
                                                    {{ $correctAnswer }}
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

                    @if($isTeacher)
                        <div class="card mt-4">
                            <div class="card-header bg-primary">
                                <h5 class="card-title text-white mb-0">Evaluasi Guru</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('student.test.result.update', $testResult->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="manual_score">Nilai Manual / Final (%)</label>
                                        <input type="number" name="manual_score" id="manual_score" class="form-control" min="0" max="100" value="{{ old('manual_score', $testResult->manual_score) }}">
                                        <small class="form-text text-muted">Gunakan nilai ini jika tugas essay perlu koreksi manual.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="task_score">Nilai Tugas (%)</label>
                                        <input type="number" name="task_score" id="task_score" class="form-control" min="0" max="100" value="{{ old('task_score', $testResult->task_score ?? $taskScore) }}">
                                        <small class="form-text text-muted">Nilai tugas dihitung otomatis dari status approved.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="attitude_note">Catatan Sikap</label>
                                        <textarea name="attitude_note" id="attitude_note" rows="4" class="form-control">{{ old('attitude_note', $testResult->attitude_note) }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Simpan Evaluasi Guru
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

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
