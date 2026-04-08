@extends('adminlte::page')

@section('title', 'User Management')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">User Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addUserBtn">
                            <i class="fas fa-plus"></i> Tambah User
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="userTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>



    @include('users.modal')

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

            let table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('users.data') }}",
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#addUserBtn').click(function() {
                $('#userForm')[0].reset();
                $('#userModal').modal('show');
            });

            $('#userForm').submit(function(e) {
                e.preventDefault();

                let id = $('#user_id').val();
                let url = id ? '/users/' + id : '/users';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: $('#name').val(),
                        email: $('#email').val(),
                        password: $('#password').val(),
                        role: $('#role').val()
                    },
                    success: function() {
                        $('#userModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/users/' + id + '/edit', function(data) {
                    $('#user_id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#userModal').modal('show');
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
                            url: '/users/' + id,
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

    