@extends('adminlte::page')

@section('title', 'Project Management')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Project Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary mb-3" id="addProjectBtn">Tambah Project</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="projectTable">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th>Deskripsi</th>
                            <th>Klien</th>
                            <th>Status</th>
                            <th>Guru</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    @include('projects.modal')

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

            let table = $('#projectTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('projects.data') }}",
                columns: 
                [
                    { data: 'judul', name: 'judul' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'client', name: 'client' },
                    { data: 'status', name: 'status', render: function(data, type, row) {
                        return '<span class="badge badge-' + (data === 'design_brief' ? 'info' : data === 'timeline' ? 'warning' : data === 'design' ? 'primary' : data === 'produksi' ? 'success' : data === 'qc' ? 'danger' : data === 'mass_production' ? 'dark' : 'light') + '">' + data + '</span>';
                    } },
                    { data: 'guru', name: 'guru' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#addProjectBtn').click(function() {
                $('#projectForm')[0].reset();
                $('#projectModal').modal('show');
            });

            $('#projectForm').submit(function(e) {
                e.preventDefault();

                let id = $('#project_id').val();
                let url = id ? '/projects/' + id : '/projects';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        judul:$('#judul').val(),
                        deskripsi:$('#deskripsi').val(),
                        client:$('#client').val(),
                        status:$('#status').val(),
                        guru_id:$('#guru_id').val(),

                    },
                    success: function() {
                        $('#projectModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirimkan request ke server');
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/projects/' + id + '/edit', function(data) {
                    $('#judul').val(data.judul);
                    $('#deskripsi').val(data.deskripsi);
                    $('#client').val(data.client);
                    $('#status').val(data.status);
                    $('#guru_id').val(data.guru_id);
                    $('#project_id').val(data.id);
                    $('#projectModal').modal('show');
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
                            url: '/projects/' + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', '', 'success');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
