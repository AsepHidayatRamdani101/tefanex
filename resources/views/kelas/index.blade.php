@extends('adminlte::page')

@section('title', 'Kelas Management')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Manajemen Kelas</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-success btn-sm" id="importKelasBtn">
                            <i class="fas fa-file-excel"></i> Import Excel
                        </button>
                        <a href="{{ route('kelas.export') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-download"></i> Export Excel
                        </a>
                        <a href="{{ route('kelas.downloadTemplate') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-file-excel"></i> Download Template
                        </a>
                        <button class="btn btn-primary btn-sm" id="addKelasBtn">
                            <i class="fas fa-plus"></i> Tambah Kelas
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="kelasTable">
                    <thead>
                        <tr>
                            <th width="30%">Nama Kelas</th>
                            <th width="15%">Kode</th>
                            <th width="20%">Deskripsi</th>
                            <th width="10%">Kapasitas</th>
                            <th width="15%">Dibuat</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('kelas.modal')
    @include('kelas.import_modal')
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
            let table = $('#kelasTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('kelas.data') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'description', name: 'description' },
                    { data: 'capacity', name: 'capacity' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#addKelasBtn').click(function() {
                $('#kelasForm')[0].reset();
                $('#kelas_id').val('');
                $('#kelasModalLabel').text('Tambah Kelas');
                $('#kelasModal').modal('show');
            });

            $('#importKelasBtn').click(function() {
                $('#importForm')[0].reset();
                $('#importModal').modal('show');
            });

            $('#importForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '{{ route("kelas.import") }}',
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

            $('#kelasForm').submit(function(e) {
                e.preventDefault();

                let id = $('#kelas_id').val();
                let url = id ? '/kelas/' + id : '/kelas';
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
                        $('#kelasModal').modal('hide');
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
                $.get('/kelas/' + id, function(data) {
                    $('#kelas_id').val(data.id);
                    $('#name').val(data.name);
                    $('#code').val(data.code);
                    $('#description').val(data.description);
                    $('#capacity').val(data.capacity);
                    $('#kelasModalLabel').text('Ubah Kelas');
                    $('#kelasModal').modal('show');
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
                            url: '/kelas/' + id,
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
