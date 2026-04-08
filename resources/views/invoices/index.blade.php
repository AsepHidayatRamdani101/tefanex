@extends('adminlte::page')

@section('title', 'Invoice Management')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Invoice Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addInvoiceBtn">
                            <i class="fas fa-plus"></i> Tambah Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="invoiceTable">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Project</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('invoices.modal')
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
            let table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('invoices.data') }}",
                columns: [{
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
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

            $('#addInvoiceBtn').click(function() {
                $('#invoiceForm')[0].reset();
                $('#invoice_id').val('');
                $('#invoiceModalLabel').text('Tambah Invoice');
                $('#invoiceModal').modal('show');
            });



            //convert to rupiah format ketika input
            function convertToRupiah(angka) {
                var rupiah = '';
                var angkarev = angka.toString().split('').reverse().join('');
                for (var i = 0; i < angkarev.length; i++)
                    if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
                return 'Rp. ' + rupiah.split('', rupiah.length - 1).reverse().join('');
            }

            $('#amount').on('input', function() {
                let amount = $(this).val();
                console.log(amount);
                if (amount) {
                    $(this).val(convertToRupiah(amount.replace(/\D/g, '')));
                } else {
                    $(this).val('');
                }
                
            });

            $('#invoiceForm').submit(function(e) {
                e.preventDefault();

                let id = $('#invoice_id').val();
                let url = id ? '/invoices/' + id : '/invoices';
                let method = id ? 'PUT' : 'POST';
                let amount = $('#amount').val().replace(/\D/g, ''); // hapus semua karakter non-digit

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        project_id: $('#project_id').val(),
                        invoice_number: $('#invoice_number').val(),
                        amount: amount,
                        status: $('#status').val(),
                    },
                    success: function() {
                        $('#invoiceModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr) {
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
                $.get('/invoices/' + id + '/edit', function(data) {
                    $('#invoice_id').val(data.id);
                    $('#project_id').val(data.project_id);
                    $('#invoice_number').val(data.invoice_number);
                    $('#amount').val(data.amount);
                    $('#status').val(data.status);
                    $('#invoiceModalLabel').text('Ubah Invoice');
                    $('#invoiceModal').modal('show');
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
                            url: '/invoices/' + id,
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
