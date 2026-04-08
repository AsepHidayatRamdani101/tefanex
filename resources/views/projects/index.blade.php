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
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addProjectBtn">
                            <i class="fas fa-plus"></i> Tambah Project
                        </button>
                    </div>
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
    @include('projects.lihatanggota')
    @include('project_members.modal')



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
                columns: [{
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'client',
                        name: 'client'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return '<span class="badge badge-' + (data === 'awal' ? 'light' :
                                    data === 'design_brief' ? 'info' :
                                    data === 'timeline' ? 'warning' : data === 'design' ?
                                    'primary' : data === 'produksi' ? 'success' : data === 'qc' ?
                                    'danger' : data === 'mass_production' ? 'dark' : 'light') +
                                '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'guru',
                        name: 'guru'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            let projectId;
            let tableAnggota;

            $('#addProjectBtn').click(function() {
                $('#projectForm')[0].reset();
                $('#projectModal').modal('show');
            });

            $('#projectForm').submit(function(e) {
                e.preventDefault();

                let id = $('#project_id').val();
                let url = id ? '/projects/' + id : '/projects';
                let method = id ? 'PUT' : 'POST';

                console.log(id, url, method);


                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        judul: $('#judul').val(),
                        deskripsi: $('#deskripsi').val(),
                        client: $('#client').val(),
                        status: $('#status').val(),
                        guru_id: $('#guru_id').val(),

                    },
                    success: function() {
                        $('#projectModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Gagal!',
                            'Terjadi kesalahan saat mengirimkan request ke server');
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


            //lihat anggota
            $(document).on('click', '.lihatAnggota', function() {
                let id = $(this).data('id');
                console.log(id);
                projectId = id;

                $('#lihatAnggotaModal').modal('show');
                if ($.fn.DataTable.isDataTable('#tableAnggota')) {
                    $('#tableAnggota').DataTable().destroy();
                }
                
                tableAnggota = $('#tableAnggota').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('projects.members.data', ':projectId') }}".replace(
                            ':projectId',
                            id),
                        type: 'GET',
                    },
                    columns: [{
                            data: 'anggota',
                            name: 'anggota'
                        },
                        {
                            data: 'tugas',
                            name: 'tugas'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
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

            $('#projectMemberForm').submit(function(e) {
                e.preventDefault();

                let id = $.trim($('#projectMember_id').val());
                let projectId = $.trim($('#project_id_member').val());
                let url = id ? '/project-members/' + id : '/project-members';
                let ajaxType = id ? 'POST' : 'POST';
                let payload = {
                    _token: "{{ csrf_token() }}",
                    anggota_id: $('#anggota_id').val(),
                    project_id: projectId,
                    tugas: $('#tugas').val()
                };

                if (id) {
                    payload._method = 'PUT';
                }

                console.log('projectMember submit', {id, url, payload});

                $.ajax({
                    url: url,
                    type: ajaxType,
                    data: payload,
                    success: function() {
                        $('#projectMemberModal').modal('hide');
                        if (tableAnggota) {
                            tableAnggota.ajax.reload(null, false);
                        }
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Gagal!',
                            'Terjadi kesalahan saat mengirimkan request ke server');
                        console.log(xhr.responseText);
                    }
                });
            });



            $(document).on('click', '.editBtnMember', function() {
                let id = $(this).data('id');
                console.log('edit member id', id);

                $.get('/project-members/' + id + '/edit', function(data) {
                    console.log('edit member data', data);

                    $('#anggota_id').val(data.user_id);
                    $('#project_id_member').val(data.project_id);
                    $('#tugas').val(data.role_in_project);
                    $('#projectMember_id').val(data.id);
                    $('#lihatAnggotaModal').modal('hide');
                    $('#projectMemberModal').modal('show');
                });
            });

            $('#tambahAnggota').click(function() {
                console.log(projectId);

                $('#projectMemberForm')[0].reset();
                $('#project_id_member').val(projectId);
                $('#lihatAnggotaModal').modal('hide');
                $('#projectMemberModal').modal('show');
            });

            $(document).on('click', '.deleteBtnMember', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/project-members/' + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {

                                $('#lihatAnggotaModal').modal('hide');
                                tableAnggota.ajax.reload();
                                Swal.fire('Terhapus!', '', 'success');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
