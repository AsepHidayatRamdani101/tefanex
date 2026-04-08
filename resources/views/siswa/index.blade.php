@extends('adminlte::page')

@section('title', 'Siswa Management')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Manajemen Siswa</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-success btn-sm" id="importSiswaBtn">
                            <i class="fas fa-file-excel"></i> Import Excel
                        </button>
                        <a href="{{ route('siswa.export') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-download"></i> Export Excel
                        </a>
                        <a href="{{ route('siswa.downloadTemplate') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-file-excel"></i> Download Template
                        </a>
                        <button class="btn btn-primary btn-sm" id="addSiswaBtn">
                            <i class="fas fa-plus"></i> Tambah Siswa
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="siswaTable">
                    <thead>
                        <tr>
                            <th width="12%">NIM</th>
                            <th width="18%">Nama Siswa</th>
                            <th width="12%">Kelas</th>
                            <th width="18%">Email</th>
                            <th width="15%">Username</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('siswa.modal')
    @include('siswa.import_modal')
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
    <script>
        $(function() {
            let table = $('#siswaTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('siswa.data') }}",
                columns: [
                    { data: 'nim', name: 'nim' },
                    { data: 'nama', name: 'nama' },
                    { data: 'kelas_name', name: 'kelas_name' },
                    { data: 'email', name: 'email' },
                    { data: 'username', name: 'username' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#addSiswaBtn').click(function() {
                $('#siswaForm')[0].reset();
                $('#siswa_id').val('');
                $('#username').val('');
                $('#password').val('');
                $('#siswaModalLabel').text('Tambah Siswa');
                $('#siswaModal').modal('show');
            });

            $('#importSiswaBtn').click(function() {
                $('#importForm')[0].reset();
                $('#importModal').modal('show');
            });

            $('#importForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '{{ route("siswa.import") }}',
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
                        $('#importModal').modal('hide');
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

            $('#siswaForm').submit(function(e) {
                e.preventDefault();

                let id = $('#siswa_id').val();
                let url = id ? '/siswa/' + id : '/siswa';
                let method = id ? 'POST' : 'POST';

                let formData = new FormData(this);
                formData.append('_token', "{{ csrf_token() }}");
                if (id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#siswaModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', response.message, 'success');
                    },
                    error: function(xhr) {
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
                $.get('/siswa/' + id, function(data) {
                    $('#siswa_id').val(data.id);
                    $('#nim').val(data.nim);
                    $('#nama').val(data.nama);
                    $('#email').val(data.email);
                    $('#no_telepon').val(data.no_telepon);
                    $('#kelas_id').val(data.kelas_id);
                    $('#alamat').val(data.alamat);
                    $('#username').val(data.user ? data.user.email : '');
                    $('#password').val('');
                    $('#siswaModalLabel').text('Ubah Siswa');
                    $('#siswaModal').modal('show');
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data ini tidak dapat dikembalikan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/siswa/' + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', response.message, 'success');
                            },
                            error: function(xhr) {
                                let error = 'Tidak dapat menghapus data';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    error = xhr.responseJSON.message;
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
