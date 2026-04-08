@extends('adminlte::page')

@section('title', 'Manajemen Role')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="btn-group" role="group">
                <button class="btn btn-primary btn-sm" id="createNewRole">
                    <i class="fas fa-plus"></i> Tambah Role
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Role</th>
                            <th width="150px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('roles.modal')
@endsection
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

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('roles.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#createNewRole').click(function() {
                $('#roleForm').trigger("reset");
                $('#ajaxModel').modal('show');
            });

            $('#roleForm').submit(function(e) {
                e.preventDefault();

                var id = $('#role_id').val();
                var url = id ? '/roles/' + id : '/roles';

                $.ajax({
                    data: {
                            _token: "{{ csrf_token() }}",
                            name: $('#name').val()
                    },
                    url: url,
                    type: id ? "PUT" : "POST",
                    success: function() {
                        $('#roleForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.ajax.reload();
                        Swal.fire(
                            'Sukses!',
                            'Data berhasil disimpan.',
                            'success'
                        );
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                var id = $(this).data('id');
                $.get("/roles/" + id + "/edit", function(data) {
                    $('#role_id').val(data.id);
                    $('#name').val(data.name);
                    $('#ajaxModel').modal('show');
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/roles/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
