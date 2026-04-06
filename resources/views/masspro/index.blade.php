@extends('adminlte::page')

@section('title', 'Mass Production')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Mass Production</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="massproTable">
                    <thead>
                        <tr>
                            <th>Nama Produksi</th>
                            <th>Jumlah</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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
            let table = $('#massproTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('masspro.data') }}",
                columns: [
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        render: function(data) {
                            return data !== null ? data : '-';
                        }
                    },
                    {
                        data: 'waktu',
                        name: 'waktu',
                        render: function(data) {
                            if (!data) {
                                return '-';
                            }
                            let date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('click', '.approveBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Approve mass production?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, approve!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/masspro/' + id + '/status',
                            method: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: 'selesai'
                            },
                            success: function(response) {
                                Swal.fire('Success', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.revisiBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Kirim ke revisi?',
                    text: 'Status akan diubah menjadi revisi.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, revisi',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/masspro/' + id + '/status',
                            method: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: 'revisi'
                            },
                            success: function(response) {
                                Swal.fire('Success', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
