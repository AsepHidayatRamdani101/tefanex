@extends('adminlte::page')

@section('title', 'Test Management')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Test Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addTestBtn">
                            <i class="fas fa-plus"></i> Tambah Test
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="testTable">
                    <thead>
                        <tr>
                            <th>Materi</th>
                            <th>Tipe Test</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('test.modal')
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
            let table = $('#testTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('test.data') }}",
                columns: [{
                        data: 'material_title',
                        name: 'material_title'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#addTestBtn').click(function() {
                $('#testForm')[0].reset();
                $('#test_id').val('');
                $('#testModalLabel').text('Tambah Test');
                $('#testModal').modal('show');
            });

            $('#testForm').submit(function(e) {
                e.preventDefault();

                let id = $('#test_id').val();
                let url = id ? '/test/' + id : '/test';
                let method = id ? 'POST' : 'POST';

                let formData = new FormData(this);
                // CSRF token
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
                        $('#testModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Test tersimpan', 'success');
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

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/test/' + id + '/edit', function(data) {
                    $('#test_id').val(data.id);
                    $('#material_id').val(data.material_id);
                    $('#type').val(data.type);
                    $('#testModalLabel').text('Ubah Test');
                    $('#testModal').modal('show');
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
                            url: '/test/' + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', '', 'success');
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Tidak dapat menghapus data',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
