@extends('adminlte::page')

@section('title', 'Nilai Siswa')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Daftar Nilai & Evaluasi</h3>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-success" id="exportGradesBtn">Export Nilai</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label for="filterKelas" class="mb-1">Filter Kelas</label>
                    <select id="filterKelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="filterProject" class="mb-1">Filter Project</label>
                    <select id="filterProject" class="form-control">
                        <option value="">Semua Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->judul }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary mr-2" id="resetFilters">Reset Filter</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="gradesTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="checkAllRows">
                            </th>
                            <th>Siswa</th>
                            <th>Project & Materi</th>
                            <th>Nilai Pretest</th>
                            <th>Nilai Posttest</th>
                            <th>Nilai Tugas</th>
                            <th>Rata-rata Nilai</th>
                            <th>Catatan Sikap</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
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
            const gradesTable = $('#gradesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                drawCallback: function() {
                    $('#checkAllRows').prop('checked', false);
                },
                ajax: {
                    url: "{{ route('grades.data') }}",
                    data: function(d) {
                        d.kelas_id = $('#filterKelas').val();
                        d.project_id = $('#filterProject').val();
                    }
                },
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<input type="checkbox" class="grade-row-check" data-id="' + data + '">';
                        }
                    },
                    { data: 'student_name', name: 'student_name' },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'pretest_score', name: 'pretest_score', orderable: false, searchable: false },
                    { data: 'posttest_score', name: 'posttest_score', orderable: false, searchable: false },
                    {
                        data: 'task_score_value',
                        name: 'task_score',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const value = row.task_score_value ?? '';
                            return '<input type="number" min="0" max="100" class="form-control form-control-sm grade-task-score" value="' + value + '" disabled>';
                        }
                    },
                    { data: 'average_score', name: 'average_score', orderable: false, searchable: false },
                    {
                        data: 'attitude_note',
                        name: 'attitude_note',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const value = row.attitude_note ?? '';
                            return '<textarea rows="2" class="form-control form-control-sm grade-attitude-note" disabled>' + escapeHtml(value) + '</textarea>';
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            function escapeHtml(value) {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function setRowEditable($row, editable) {
                $row.find('.grade-task-score, .grade-attitude-note').prop('disabled', !editable);
                $row.find('.saveGradeBtn').prop('disabled', !editable);
            }

            function getRowData($button) {
                const $row = $button.closest('tr');
                return {
                    resultIds: String($button.data('result-ids') || ''),
                    row: $row,
                    taskScore: $row.find('.grade-task-score').val(),
                    attitudeNote: $row.find('.grade-attitude-note').val(),
                };
            }

            $('#filterKelas, #filterProject').on('change', function() {
                gradesTable.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#filterKelas').val('');
                $('#filterProject').val('');
                gradesTable.ajax.reload();
            });

            $('#exportGradesBtn').on('click', function() {
                const params = new URLSearchParams();
                const kelasId = $('#filterKelas').val();
                const projectId = $('#filterProject').val();

                if (kelasId) {
                    params.set('kelas_id', kelasId);
                }

                if (projectId) {
                    params.set('project_id', projectId);
                }

                const query = params.toString();
                const exportUrl = '{{ route('grades.export') }}' + (query ? '?' + query : '');
                window.location.href = exportUrl;
            });

            $(document).on('change', '#checkAllRows', function() {
                const checked = $(this).is(':checked');
                $('#gradesTable tbody .grade-row-check').prop('checked', checked).trigger('change');
            });

            $(document).on('change', '.grade-row-check', function() {
                const $row = $(this).closest('tr');
                const checked = $(this).is(':checked');
                $row.toggleClass('table-active', checked);
                $row.find('.editGradeBtn, .saveGradeBtn, .deleteGradeBtn').prop('disabled', !checked);
                if (!checked) {
                    setRowEditable($row, false);
                }
            });

            $(document).on('click', '.editGradeBtn', function() {
                const data = getRowData($(this));
                setRowEditable(data.row, true);
                data.row.find('.grade-manual-score').trigger('focus');
            });

            $(document).on('click', '.saveGradeBtn', function() {
                const data = getRowData($(this));

                $.ajax({
                    url: '{{ route('grades.update') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        result_ids: data.resultIds,
                        task_score: data.taskScore,
                        attitude_note: data.attitudeNote,
                    },
                    success: function() {
                        Swal.fire('Berhasil!', 'Nilai siswa berhasil disimpan.', 'success');
                        gradesTable.ajax.reload(null, false);
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

            $(document).on('click', '.deleteGradeBtn', function() {
                const data = getRowData($(this));

                Swal.fire({
                    title: 'Hapus nilai ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '{{ route('grades.destroy') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            result_ids: data.resultIds
                        },
                        success: function() {
                            Swal.fire('Berhasil!', 'Nilai siswa berhasil dihapus.', 'success');
                            gradesTable.ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus nilai.', 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection
