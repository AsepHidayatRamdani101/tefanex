@extends('adminlte::page')

@section('title', 'Mengerjakan Test')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header with-border bg-primary">
                        <h3 class="card-title text-white">
                            <i class="fas fa-pencil-alt"></i>
                            {{ ucfirst($test->type) }}: {{ $test->material->title ?? 'Test' }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Pastikan Anda telah membaca semua soal dengan teliti sebelum menjawab.
                            Jawaban tidak dapat diubah setelah submit.
                        </div>

                        <form id="testForm" method="POST" action="{{ route('student.test.submit', $test->id) }}">
                            @csrf

                            @foreach ($questions as $index => $question)
                                <div class="question-card mb-4 p-3 border rounded">
                                    <div class="question-number mb-2">
                                        <h5 class="font-weight-bold">
                                            Soal {{ $index + 1 }} dari {{ $questions->count() }}
                                        </h5>
                                    </div>

                                    <div class="question-text mb-3">
                                        <p class="lead">{{ $question->question_text }}</p>
                                    </div>

                                    @if ($question->type === 'multiple_choice')
                                        <div class="answer-options">
                                            @if ($question->options && is_array($question->options))
                                                @foreach ($question->options as $optionIndex => $option)
                                                    <div class="custom-control custom-radio mb-2">
                                                        @php
                                                            $answer = $question->answers->get($optionIndex);
                                                            $optionValue = $answer ? $answer->id : $option;
                                                        @endphp
                                                        <input type="radio" class="custom-control-input"
                                                               id="answer_{{ $question->id }}_{{ $optionIndex }}"
                                                               name="answer_{{ $question->id }}"
                                                               value="{{ $optionValue }}" required>
                                                        <label class="custom-control-label"
                                                               for="answer_{{ $question->id }}_{{ $optionIndex }}">
                                                            {{ chr(65 + $optionIndex) }}. {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">Opsi tidak tersedia</p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="answer-essay">
                                            <textarea class="form-control" name="answer_{{ $question->id }}"
                                                      rows="5" placeholder="Ketikkan jawaban Anda di sini..."
                                                      required></textarea>
                                            <small class="form-text text-muted">
                                                Jawaban essay akan dikoreksi secara manual oleh guru.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="form-group mt-4">
                                <a href="{{ route('student.tests.list') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success float-right">
                                    <i class="fas fa-check"></i> Submit Test
                                </button>
                            </div>
                        </form>
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

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#testForm').submit(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin submit?',
                text: 'Pastikan semua soal sudah dijawab. Jawaban tidak dapat diubah setelah submit.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Submit',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengsubmit...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: '<p>Test berhasil disubmit</p><p class="lead"><strong>Nilai: ' + response.score + '%</strong></p><p>Jawab Benar: ' + response.correct_answers + ' dari ' + response.total_questions + '</p>',
                                icon: 'success',
                                confirmButtonText: 'Lihat Hasil'
                            }).then(() => {
                                window.location.href = '{{ route('student.test.result', '') }}/' + response.test_result_id;
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat submit. Silahkan coba lagi.', 'error');
                        }
                    });
                }
            });

            return false;
        });
    </script>
@endsection
