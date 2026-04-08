@extends('adminlte::page')

@section('title', 'Materi Management')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Materi Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addMateriBtn">
                            <i class="fas fa-plus"></i> Tambah Materi
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="materiTable">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Project</th>
                            <th>Dibuat Oleh</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('materi.modal')
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
            let table = $('#materiTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('materi.data') }}",
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'type', name: 'type' },
                    { data: 'project', name: 'project' },
                    { data: 'creator', name: 'creator' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#addMateriBtn').click(function() {
                $('#materiForm')[0].reset();
                $('#materi_id').val('');
                $('#project_id').val('');
                $('#video_link').val('');
                $('#materiModalLabel').text('Tambah Materi');
                $('#fileInfo').hide();
                $('#materiModal').modal('show');
            });

            $('#materiForm').submit(function(e) {
                e.preventDefault();

                let id = $('#materi_id').val();
                let url = id ? '/materi/' + id : '/materi';
                let method = id ? 'POST' : 'POST';

                let formData = new FormData(this);
                //crsf token
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
                    success: function() {
                        $('#materiModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Materi tersimpan', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        
                        let error = 'Terjadi kesalahan saat mengirimkan request ke server';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            error = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire('Gagal!', error, 'error');
                    }
                });
            });

            // Update content visibility based on type
            $('#type').change(function() {
                let type = $(this).val();
                if (type === 'text') {
                    $('#contentGroup').show();
                    $('#fileGroup').hide();
                    $('#videoGroup').hide();
                    $('#content').prop('required', true);
                } else if (type === 'file' || type === 'pdf') {
                    $('#contentGroup').hide();
                    $('#fileGroup').show();
                    $('#videoGroup').hide();
                    $('#content').prop('required', false);
                } else if (type === 'video') {
                    $('#contentGroup').hide();
                    $('#fileGroup').hide();
                    $('#videoGroup').show();
                    $('#content').prop('required', false);
                } else {
                    $('#contentGroup').hide();
                    $('#fileGroup').hide();
                    $('#videoGroup').hide();
                    $('#content').prop('required', false);
                }
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/materi/' + id + '/edit', function(data) {
                    $('#materi_id').val(data.id);
                    $('#title').val(data.title);
                    $('#type').val(data.type).trigger('change');
                    $('#content').val(data.content);
                    $('#project_id').val(data.project_id || '');
                    $('#video_link').val(data.video_link || '');
                    
                    if (data.file_path) {
                        $('#fileInfo').show().html('<p><small>File: <a href="/' + data.file_path + '" target="_blank">Lihat File</a></small></p>');
                    } else {
                        $('#fileInfo').hide();
                    }
                    
                    $('#materiModalLabel').text('Ubah Materi');
                    $('#materiModal').modal('show');
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
                            url: '/materi/' + id,
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
