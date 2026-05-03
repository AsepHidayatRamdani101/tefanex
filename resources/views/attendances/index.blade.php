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
                            <i class="fas fa-plus"></i> Ambil Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filter_kelas">Filter Kelas</label>
                    <select id="filter_kelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="attendance_date">Tanggal</label>
                    <input type="date" id="attendance_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <div id="bulkActionButtons" style="display: none;" class="btn-group btn-group-sm btn-block"
                        role="group">
                        <button type="button" class="btn btn-success bulk-status-btn" data-status="hadir">Hadir</button>
                        <button type="button" class="btn btn-info bulk-status-btn" data-status="sakit">Sakit</button>
                        <button type="button" class="btn btn-warning bulk-status-btn" data-status="izin">Izin</button>
                        <button type="button" class="btn btn-danger bulk-status-btn" data-status="alpha">Alpa</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="attendanceTable">
                    <thead>
                        <tr>
                            <th width="50px">
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th width="150px">Status</th>
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
                responsive: false,
                ajax: {
                    url: "{{ route('attendances.data') }}",
                    data: function(d) {
                        d.kelas_id = $('#filter_kelas').val();
                        d.date = $('#attendance_date').val();
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user',
                        name: 'user',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kelas',
                        name: 'kelas',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_dropdown',
                        name: 'status_dropdown',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#filter_kelas').change(function() {
                table.ajax.reload();
            });

            $('#attendance_date').change(function() {
                table.ajax.reload();
            });

            // Select all checkboxes
            $('#selectAllCheckbox').change(function() {
                const isChecked = $(this).is(':checked');
                $('#attendanceTable').find('.row-checkbox').prop('checked', isChecked);
                updateBulkActionButtons();
            });

            // Handle checkbox change for each row
            $(document).on('change', '.row-checkbox', function() {
                updateBulkActionButtons();
            });

            // Toggle bulk action buttons based on selection
            function updateBulkActionButtons() {
                const checkedCount = $('#attendanceTable').find('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#bulkActionButtons').show();
                } else {
                    $('#bulkActionButtons').hide();
                }
            }

            // Handle bulk status button clicks
            $(document).on('click', '.bulk-status-btn', function() {
                const status = $(this).data('status');
                const date = $('#attendance_date').val();

                if (!date) {
                    Swal.fire('Perhatian!', 'Silakan pilih tanggal terlebih dahulu', 'warning');
                    return;
                }

                const selectedUserIds = [];
                $('#attendanceTable').find('.row-checkbox:checked').each(function() {
                    const $row = $(this).closest('tr');
                    const $statusDropdown = $row.find('.status-dropdown');
                    selectedUserIds.push($statusDropdown.data('user-id'));
                });

                if (selectedUserIds.length === 0) {
                    Swal.fire('Perhatian!', 'Pilih minimal satu siswa', 'warning');
                    return;
                }

                $.ajax({
                    url: '/attendances/bulk',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_ids: selectedUserIds,
                        date: date,
                        status: status,
                    },
                    success: function() {
                        $('#selectAllCheckbox').prop('checked', false);
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan',
                            text: 'Attendance untuk ' + selectedUserIds.length +
                                ' siswa tersimpan',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr) {
                        let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            error = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire('Gagal!', error, 'error');
                    }
                });
            });

            // Handle status dropdown change
            $(document).on('change', '.status-dropdown', function() {
                const status = $(this).val();
                const attendanceId = $(this).data('attendance-id');
                const userId = $(this).data('user-id');
                const date = $('#attendance_date').val();

                if (!status) {
                    return;
                }

                if (!date) {
                    Swal.fire('Perhatian!', 'Silakan pilih tanggal terlebih dahulu', 'warning');
                    $(this).val('');
                    return;
                }

                const data = {
                    _token: "{{ csrf_token() }}",
                    user_id: userId,
                    date: date,
                    status: status,
                };

                // If attendance ID exists (not new record), update it
                if (attendanceId && String(attendanceId).indexOf('new_') !== 0) {
                    $.ajax({
                        url: '/attendances/' + attendanceId,
                        type: 'PUT',
                        data: data,
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                error = Object.values(xhr.responseJSON.errors).flat().join(
                                    '<br>');
                            }
                            Swal.fire('Gagal!', error, 'error');
                            table.ajax.reload();
                        }
                    });
                } else {
                    // Create new attendance record
                    $.ajax({
                        url: '/attendances',
                        type: 'POST',
                        data: data,
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                error = Object.values(xhr.responseJSON.errors).flat().join(
                                    '<br>');
                            }
                            Swal.fire('Gagal!', error, 'error');
                            table.ajax.reload();
                        }
                    });
                }
            });

            $('#addAttendanceBtn').click(function() {
                const date = $('#attendance_date').val();
                if (!date) {
                    Swal.fire('Perhatian!', 'Silakan pilih tanggal terlebih dahulu', 'warning');
                    return;
                }

                const selectedUserIds = [];
                $('#attendanceTable').find('.row-checkbox:checked').each(function() {
                    const $row = $(this).closest('tr');
                    const $statusDropdown = $row.find('.status-dropdown');
                    selectedUserIds.push($statusDropdown.data('user-id'));
                });

                if (selectedUserIds.length === 0) {
                    Swal.fire('Perhatian!', 'Pilih minimal satu siswa', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Pilih Status',
                    input: 'select',
                    inputOptions: {
                        'hadir': 'Hadir',
                        'sakit': 'Sakit',
                        'izin': 'Izin',
                        'alpha': 'Alpa'
                    },
                    inputPlaceholder: 'Pilih status kehadiran',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const status = result.value;
                        $.ajax({
                            url: '/attendances/bulk',
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                user_ids: selectedUserIds,
                                date: date,
                                status: status,
                            },
                            success: function() {
                                $('#selectAllCheckbox').prop('checked', false);
                                table.ajax.reload();
                                Swal.fire('Berhasil!', 'Attendance untuk ' +
                                    selectedUserIds.length + ' siswa tersimpan',
                                    'success');
                            },
                            error: function(xhr) {
                                let error =
                                    'Terjadi kesalahan saat mengirimkan request ke server';
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    error = Object.values(xhr.responseJSON.errors)
                                    .flat().join('<br>');
                                }
                                Swal.fire('Gagal!', error, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
