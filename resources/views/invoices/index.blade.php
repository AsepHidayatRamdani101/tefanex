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
                            <th>Total Budget</th>
                            <th>Jumlah Bayar</th>
                            <th>Sisa Bayar</th>
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
                        data: 'payment_amount',
                        name: 'payment_amount'
                    },
                    {
                        data: 'remaining_amount',
                        name: 'remaining_amount'
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

            function formatRupiah(value) {
                // Convert to integer to properly handle decimal values like "100000.00"
                 // Use parseFloat to properly handle decimal values
                 let numericValue = parseFloat(String(value || '').replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(/,/g, '.')) || 0;
                 numericValue = Math.round(numericValue);
                 return numericValue ? 'Rp ' + numericValue.toLocaleString('id-ID') : '';
            }

            function parseNumeric(value) {
                  // Remove any non-numeric characters (like 'Rp', spaces, dots) and parse
                  let s = String(value || '').replace(/[^0-9,.-]/g, '');
                  // Remove thousand separators (dots) and normalize decimal comma to dot
                  s = s.replace(/\./g, '');
                  s = s.replace(/,/g, '.');
                  let numValue = parseFloat(s) || 0;
                  return Math.round(numValue);
            }

            function updateInvoicePreview() {
                const selectedBudget = $('#project_id option:selected').data('budget');
                const budgetValue = selectedBudget ? parseFloat(selectedBudget) : 0;
                const paymentValue = parseNumeric($('#payment_amount').val());
                const remainingValue = Math.max(budgetValue - paymentValue, 0);

                $('#amount').val(budgetValue ? formatRupiah(budgetValue) : '');
                $('#remaining_amount').val(budgetValue ? formatRupiah(remainingValue) : '');
            }

            $('#addInvoiceBtn').click(function() {
                $('#invoiceForm')[0].reset();
                $('#invoice_id').val('');
                $('#invoiceModalLabel').text('Tambah Invoice');
                $('#invoice_number_display').val('Akan digenerate otomatis');
                updateInvoicePreview();
                $('#invoiceModal').modal('show');
            });

            $('#project_id').on('change', function() {
                updateInvoicePreview();
            });

            $('#payment_amount').on('input', function() {
                const value = $(this).val();
                if (value) {
                    $(this).val(formatRupiah(value));
                }

                updateInvoicePreview();
            });

            $('#invoiceForm').submit(function(e) {
                e.preventDefault();

                let id = $('#invoice_id').val();
                let url = id ? '/invoices/' + id : '/invoices';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        project_id: $('#project_id').val(),
                        payment_amount: parseNumeric($('#payment_amount').val()),
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
                    $('#invoice_number_display').val(data.invoice_number);
                    $('#payment_amount').val(formatRupiah(data.payment_amount));
                    $('#status').val(data.status);
                    $('#amount').val(formatRupiah(data.amount));
                    const currentTotal = parseNumeric(data.amount);
                    const currentPayment = parseNumeric(data.payment_amount);
                    $('#remaining_amount').val(formatRupiah(Math.max(currentTotal - currentPayment, 0)));
                    updateInvoicePreview();
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
