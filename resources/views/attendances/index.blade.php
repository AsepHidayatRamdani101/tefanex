@extends('adminlte::page')

@section('title', 'Attendance Management')


@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Attendance Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addAttendanceBtn">
                            <i class="fas fa-plus"></i> Tambah Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('attendances.modal')
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
@section('plugins.Select2', true)

@section('js')
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.js') }}"></script>
    <script>
        $(function() {
            let table = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('attendances.data') }}",
                columns: [{
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#addAttendanceBtn').click(function() {
                $('#attendanceForm')[0].reset();
                $('#attendance_id').val('');
                $('#attendanceModalLabel').text('Tambah Attendance');
                $('#attendanceModal').modal('show');
            });

            // Inisialisasi Select2 untuk dropdown user (multiple)
            $('#user_id').select2({
                minimumInputLength: 0,
                width: '100%',
                dropdownParent: $('#attendanceModal'),
                allowClear: true,
                placeholder: 'Cari dan pilih user (bisa lebih dari 1)',
                language: {
                    noResults: function() {
                        return 'User tidak ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });
            

            $('#attendanceForm').submit(function(e) {
                e.preventDefault();

                let id = $('#attendance_id').val();
                let userIds = $('#user_id').val();
                let date = $('#date').val();
                let status = $('#status').val();

                if (!userIds || userIds.length === 0) {
                    Swal.fire('Perhatian!', 'Pilih minimal satu user', 'warning');
                    return;
                }

                // Jika edit single attendance
                if (id) {
                    $.ajax({
                        url: '/attendances/' + id,
                        type: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            user_id: userIds[0],
                            date: date,
                            status: status,
                        },
                        success: function() {
                            $('#attendanceModal').modal('hide');
                            table.ajax.reload();
                            Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                        },
                        error: function(xhr) {
                            let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                error = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                            Swal.fire('Gagal!', error, 'error');
                        }
                    });
                } else {
                    // Jika tambah untuk multiple users
                    $.ajax({
                        url: '/attendances/bulk',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            user_ids: userIds,
                            date: date,
                            status: status,
                        },
                        success: function() {
                            $('#attendanceModal').modal('hide');
                            table.ajax.reload();
                            Swal.fire('Berhasil!', 'Attendance untuk ' + userIds.length + ' siswa tersimpan', 'success');
                        },
                        error: function(xhr) {
                            let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                error = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                            Swal.fire('Gagal!', error, 'error');
                        }
                    });
                }
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/attendances/' + id + '/edit', function(data) {
                    $('#attendance_id').val(data.id);
                    $('#user_id').val([data.user_id]).trigger('change');
                    $('#date').val(data.date);
                    $('#status').val(data.status);
                    $('#attendanceModalLabel').text('Ubah Attendance');
                    $('#attendanceModal').modal('show');
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
                            url: '/attendances/' + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', '', 'success');
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Tidak dapat menghapus data',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
