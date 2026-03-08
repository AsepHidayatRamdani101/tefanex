@extends('adminlte::page')

@section('title', 'Project Member Management')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Project Member Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary mb-3" id="addProjectMemberBtn">Tambah Anggota Project</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="projectMemberTable">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

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

            // let table = $('#projectMemberTable').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     responsive: true,
            //     ajax: "{{ route('project-members.data') }}",
            //     columns: [{
            //             data: 'project',
            //             name: 'project'
            //         },
            //         { data:'deskripsi', name:'deskripsi' },
            //         {
            //             data: 'action',
            //             name: 'action',
            //             orderable: false,
            //             searchable: false
            //         }
            //     ]
            // });

            $('#addProjectMemberBtn').click(function() {
                let projectId = $(this).data('id');
                console.log(projectId);
                
                $('#projectMemberForm')[0].reset();
                $('#project_id_member').val(projectId);
                $('#projectMemberModal').modal('show');
            });

            $('#projectMemberForm').submit(function(e) {
                e.preventDefault();
              

                let id = $('#projectMember_id').val();
               if(id){
                    var url = '/project-members/' + id;
                    var method = 'PUT';
                } else {
                    var url = '/project-members';
                    var method = 'POST';
                }
                // console.log(id,url,method);
                
                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        anggota_id: $('#anggota_id').val(),
                        project_id: $('#project_id').val(),
                        tugas: $('#tugas').val()

                    },
                    success: function() {
                        $('#projectMemberModal').modal('hide');
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

            
            $(document).on('click', '.editBtnMember', function() {
                let id = $(this).data('id');
                console.log(id);
                
                // $.get('/project-members/' + id + '/edit', function(data) {
                //     $('#anggota_id').val(data.user_id);
                //     $('#project_id').val(data.project_id);
                //     $('#tugas').val(data.role_in_project);
                //     $('#projectMember_id').val(data.id);
                //     $('#projectMemberModal').modal('show');
                //     // console.log(data);
                    
                // });
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
