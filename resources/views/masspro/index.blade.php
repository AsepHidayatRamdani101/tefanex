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

    @include('masspro.modal')
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
                columns: [{
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

            $('#massproTable').on('click', '.tambahBtn', function() {
                let id = $(this).data('id');
                $.get('/masspro/' + id, function(data) {
                    console.log(data);
                    $('#massForm')[0].reset();
                    $('#massproId').val(data.id);
                    $('#nama').val(data.project.judul);
                    $('#jumlah').val(data.design_brief ? data.design_brief.quantity : '');
                    $('#status').val(data.status);
                    $('#massproModal').modal('show');
                });
                
            });

            $('#massForm').submit(function(e) {
                e.preventDefault();
                let id = $('#massproId').val();
                if(id){
                    var url = '/masspro/' + id;
                    var method = 'PUT';
                } else {
                    var url = '/masspro';
                    var method = 'POST';
                }
                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        id: id,
                        quantity: $('#jumlah').val(),
                        status: $('#status').val(),
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        $('#massproModal').modal('hide');
                        Swal.fire(
                            'Berhasil!',
                            'Data mass production berhasil diperbarui.',
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat memperbarui data.',
                            'error'
                        );
                    }
                });
            });


        });
    </script>
@endsection
