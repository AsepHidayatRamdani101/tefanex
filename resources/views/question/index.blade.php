@extends('adminlte::page')

@section('title', 'Question Management')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Question Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-success btn-sm" id="importExcelBtn">
                            <i class="fas fa-file-excel"></i> Import Excel
                        </button>
                        <button class="btn btn-info btn-sm" id="importWordBtn">
                            <i class="fas fa-file-word"></i> Import Word
                        </button>
                        <button class="btn btn-primary btn-sm" id="addQuestionBtn">
                            <i class="fas fa-plus"></i> Tambah Manual
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="questionTable">
                    <thead>
                        <tr>
                            <th>Test</th>
                            <th>Tipe</th>
                            <th>Pertanyaan</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('question.modal')
    @include('question.import_modal')
@stop

@section('footer')
    <div class="float-right d-none d-sm-inline">
        Versi 1.0
    </div>
    <strong>
        Copyright &copy; {{ date('Y') }}
        <a href="#">TEFANEX</a>.
    </strong> All rights reserved.
@endsection

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.js') }}"></script>
    <script>
        $(function() {
            let table = $('#questionTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('question.data') }}",
                columns: [
                    { data: 'test_info', name: 'test_info' },
                    { data: 'type', name: 'type' },
                    { data: 'question_text', name: 'question_text' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Handle question type change
            $('#type').change(function() {
                let type = $(this).val();
                if (type === 'multiple_choice') {
                    $('#multipleChoiceSection').show();
                    $('#essaySection').hide();
                    updateCorrectAnswerOptions();
                } else if (type === 'essay') {
                    $('#multipleChoiceSection').hide();
                    $('#essaySection').show();
                } else {
                    $('#multipleChoiceSection').hide();
                    $('#essaySection').hide();
                }
            });

            // Add option button
            $('#addOption').click(function() {
                let optionCount = $('.option-item').length;
                if (optionCount >= 5) {
                    Swal.fire('Batas Maksimal!', 'Maksimal 5 pilihan untuk soal pilihan ganda', 'warning');
                    return;
                }
                let nextLetter = String.fromCharCode(65 + optionCount); // A, B, C, etc.
                let optionHtml = `
                    <div class="input-group mb-2 option-item">
                        <input type="text" name="options[]" class="form-control" placeholder="Pilihan ${nextLetter}" maxlength="255">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-option">&times;</button>
                        </div>
                    </div>
                `;
                $('#optionsContainer').append(optionHtml);
                updateCorrectAnswerOptions();
                updateRemoveButtons();
                updateAddButtonVisibility();
            });

            // Remove option button
            $(document).on('click', '.remove-option', function() {
                if ($('.option-item').length > 2) {
                    $(this).closest('.option-item').remove();
                    updateCorrectAnswerOptions();
                    updateRemoveButtons();
                }
            });

            // Update options when typing
            $(document).on('input', 'input[name="options[]"]', function() {
                updateCorrectAnswerOptions();
            });

            function updateCorrectAnswerOptions() {
                let options = $('input[name="options[]"]').map(function() {
                    return $(this).val().trim();
                }).get().filter(val => val !== '');

                let $correctAnswer = $('#correct_answer');
                $correctAnswer.empty();
                $correctAnswer.append('<option value="">Pilih Jawaban Benar</option>');

                options.forEach(function(option, index) {
                    let letter = String.fromCharCode(65 + index);
                    $correctAnswer.append(`<option value="${option}">${letter}. ${option}</option>`);
                });
            }

            function updateRemoveButtons() {
                let optionCount = $('.option-item').length;
                if (optionCount > 2) {
                    $('.remove-option').show();
                } else {
                    $('.remove-option').hide();
                }
                updateAddButtonVisibility();
            }

            function updateAddButtonVisibility() {
                let optionCount = $('.option-item').length;
                if (optionCount >= 5) {
                    $('#addOption').hide();
                } else {
                    $('#addOption').show();
                }
            }

            $('#addQuestionBtn').click(function() {
                $('#questionForm')[0].reset();
                $('#question_id').val('');
                $('#questionModalLabel').text('Tambah Pertanyaan');
                $('#multipleChoiceSection').hide();
                $('#essaySection').hide();
                $('#optionsContainer').html(`
                    <div class="input-group mb-2 option-item">
                        <input type="text" name="options[]" class="form-control" placeholder="Pilihan A" maxlength="255">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-option" style="display: none;">&times;</button>
                        </div>
                    </div>
                    <div class="input-group mb-2 option-item">
                        <input type="text" name="options[]" class="form-control" placeholder="Pilihan B" maxlength="255">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-option" style="display: none;">&times;</button>
                        </div>
                    </div>
                `);
                $('#correct_answer').html('<option value="">Pilih Jawaban Benar</option>');
                updateAddButtonVisibility();
                $('#questionModal').modal('show');
            });

            // Import Excel functionality
            $('#importExcelBtn').click(function() {
                $('#importExcelForm')[0].reset();
                $('#importExcelModal').modal('show');
            });

            $('#importExcelForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '{{ route("question.import.excel") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mengimport...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        $('#importExcelModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', response.message, 'success');
                    },
                    error: function(xhr) {
                    console.log(xhr.responseText);
                        Swal.close();
                        let error = 'Terjadi kesalahan saat import';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            error = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', error, 'error');
                    }
                });
            });

            // Import Word functionality
            $('#importWordBtn').click(function() {
                $('#importWordForm')[0].reset();
                $('#importWordModal').modal('show');
            });

            $('#importWordForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '{{ route("question.import.word") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mengimport...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        $('#importWordModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.close();
                        let error = 'Terjadi kesalahan saat import';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            error = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', error, 'error');
                    }
                });
            });

            // Download template functions
            window.downloadTemplate = function(type) {
                if (type === 'excel') {
                    window.open('{{ asset("templates/template_pertanyaan.csv") }}', '_blank');
                } else if (type === 'word') {
                    window.open('{{ asset("templates/template_pertanyaan.docx") }}', '_blank');
                }
            };

            $('#questionForm').submit(function(e) {
                e.preventDefault();

                let id = $('#question_id').val();
                let url = id ? '/question/' + id : '/question';
                let method = id ? 'POST' : 'POST';

                let formData = new FormData(this);
                // CSRF token
                formData.append('_token', "{{ csrf_token() }}");
                if (id) {
                    formData.append('_method', 'PUT');
                }

                // Handle essay correct answer
                if ($('#type').val() === 'essay') {
                    formData.set('correct_answer', $('#essay_correct_answer').val());
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#questionModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Pertanyaan tersimpan', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);

                        let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            error = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            error = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', error, 'error');
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/question/' + id, function(data) {
                    $('#question_id').val(data.id);
                    $('#test_id').val(data.test_id);
                    $('#question_text').val(data.question_text);
                    $('#type').val(data.type).trigger('change');

                    if (data.type === 'multiple_choice' && data.options) {
                        $('#optionsContainer').empty();
                        data.options.forEach(function(option, index) {
                            let letter = String.fromCharCode(65 + index);
                            let showRemove = data.options.length > 2 ? '' : 'style="display: none;"';
                            let optionHtml = `
                                <div class="input-group mb-2 option-item">
                                    <input type="text" name="options[]" class="form-control" placeholder="Pilihan ${letter}" maxlength="255" value="${option}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-option" ${showRemove}>&times;</button>
                                    </div>
                                </div>
                            `;
                            $('#optionsContainer').append(optionHtml);
                        });
                        updateCorrectAnswerOptions();
                        $('#correct_answer').val(data.correct_answer);
                    } else if (data.type === 'essay') {
                        $('#essay_correct_answer').val(data.correct_answer || '');
                    }

                    $('#questionModalLabel').text('Ubah Pertanyaan');
                    updateAddButtonVisibility();
                    $('#questionModal').modal('show');
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/question/' + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', '', 'success');
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Tidak dapat menghapus data', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection