@extends('adminlte::page')

@section('title', 'Mockup Design')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Mockup Design</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="projectTable">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th width="10%">Deskripsi</th>
                            <th>File Referensi</th>
                            <th>Status</th>
                            <th>Waktu</th>
                             <th>Hasil Design</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>


    </div>

    @include('mockup.modal');


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

            let table = $('#projectTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('mockup.data') }}",
                columns: [{
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                         data: 'deskripsi',
                        data: function(row) {
                            let deskripsiArray = row.deskripsi.split('\n');
                            return {
                                judul: deskripsiArray[0],
                                lama_pengerjaan: deskripsiArray[1],
                                dimensi: deskripsiArray[2],
                                warna: deskripsiArray[3],
                                font: deskripsiArray[4],
                                tagline: deskripsiArray[5],
                            };
                        },
                        render: function(data) {
                            return `
                                <p><b>Judul</b> : ${data.judul} </p>
                                <p><b>Lama Pengerjaan</b> : ${data.lama_pengerjaan}</p>
                                <p><b>Dimensi</b> : ${data.dimensi} </p>
                                <p><b>Font</b> : ${data.font} </p>
                                <p><b>Warna</b> : ${data.warna} </p>
                                <p><b>Tagline</b> : ${data.tagline} </p>
                            `;
                        }
                    },
                    {
                        data: 'file',
                        name: 'file',
                        render: function(data) {
                            return `
                                <a href="${data}" class="btn btn-sm btn-primary" target="_blank">Lihat File</a>
                            `;
                        }
                    },
                   
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'waktu',
                        name: 'waktu'
                    },
                    {
                        data: 'hasil',
                        name: 'hasil',
                        render: function(data) {
                            return `
                                <a href="${data}" class="btn btn-sm btn-primary" target="_blank">Lihat Hasil</a>
                            `;
                        }
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
                $('#tableAnggota').DataTable().destroy();

                let tableAnggota = $('#tableAnggota').DataTable({
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
                let projectId = $('#project_id').val();

                let id = $('#projectMember_id').val();
                let url = id ? '/project-members/' + id : '/project-members';
                let method = id ? 'PUT' : 'POST';
                console.log(projectId);

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        anggota_id: $('#anggota_id').val(),
                        project_id: $('#project_id_member').val(),
                        tugas: $('#tugas').val()

                    },
                    success: function() {
                        $('#projectMemberModal').modal('hide');
                        // tableAnggota.ajax.reload();
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


                $.get('/project-members/' + id + '/edit', function(data) {
                    console.log(data);

                    $('#anggota_id').val(data.user_id);
                    $('#project_id_member').val(data.project_id);
                    $('#tugas').val(data.role_in_project);
                    $('#projectMember_id').val(data.id);
                    tableAnggota.ajax.reload();
                    $('#lihatAnggotaModal').modal('hide');
                    $('#projectMemberModal').modal('show');
                    // console.log(data);

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
