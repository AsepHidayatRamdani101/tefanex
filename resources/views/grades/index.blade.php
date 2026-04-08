@extends('adminlte::page')

@section('title', 'Nilai Siswa')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Daftar Nilai & Evaluasi</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="gradesTable">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Project</th>
                            <th>Materi</th>
                            <th>Tipe Test</th>
                            <th>Nilai</th>
                            <th>Nilai Tugas</th>
                            <th>Catatan Sikap</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="gradeForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="gradeModalLabel">Nilai Siswa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="gradeResultId">
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <p id="gradeStudentName" class="font-weight-bold"></p>
                        </div>
                        <div class="form-group">
                            <label>Materi</label>
                            <p id="gradeMaterialTitle" class="font-weight-bold"></p>
                        </div>
                        <div class="form-group">
                            <label for="manual_score">Nilai Manual / Final (%)</label>
                            <input type="number" name="manual_score" id="manual_score" class="form-control" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="task_score">Nilai Tugas (%)</label>
                            <input type="number" name="task_score" id="task_score" class="form-control" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="attitude_note">Catatan Sikap</label>
                            <textarea name="attitude_note" id="attitude_note" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            $('#gradesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('grades.data') }}",
                columns: [
                    { data: 'student_name', name: 'student_name' },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'material_title', name: 'material_title' },
                    { data: 'test_type', name: 'test_type' },
                    { data: 'score', name: 'score' },
                    { data: 'task_score', name: 'task_score' },
                    { data: 'attitude_note', name: 'attitude_note' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $(document).on('click', '.editGradeBtn', function() {
                const resultId = $(this).data('id');
                const studentName = $(this).data('student');
                const materialTitle = $(this).data('material');
                const manualScore = $(this).data('manual-score');
                const taskScore = $(this).data('task-score');
                const attitudeNote = $(this).data('attitude-note');

                $('#gradeResultId').val(resultId);
                $('#gradeStudentName').text(studentName);
                $('#gradeMaterialTitle').text(materialTitle);
                $('#manual_score').val(manualScore);
                $('#task_score').val(taskScore);
                $('#attitude_note').val(attitudeNote);
                $('#gradeModal').modal('show');
            });

            $('#gradeForm').submit(function(e) {
                e.preventDefault();

                const resultId = $('#gradeResultId').val();
                const formData = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    manual_score: $('#manual_score').val(),
                    task_score: $('#task_score').val(),
                    attitude_note: $('#attitude_note').val(),
                };

                $.ajax({
                    url: '/student/test/result/' + resultId,
                    type: 'POST',
                    data: formData,
                    success: function() {
                        $('#gradeModal').modal('hide');
                        $('#gradesTable').DataTable().ajax.reload();
                        Swal.fire('Berhasil!', 'Nilai siswa berhasil diperbarui.', 'success');
                    },
                    error: function(xhr) {
                        let error = 'Terjadi kesalahan saat menyimpan nilai.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            error = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire('Gagal!', error, 'error');
                    }
                });
            });
        });
    </script>
@endsection
