@extends('adminlte::page')

@section('title', 'Pengeluaran')

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Pengeluaran - Pembelian Barang / Jasa</h3>
        <button id="addPaymentBtn" class="btn btn-primary btn-sm">Tambah Pengeluaran</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="paymentsTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Referensi</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@include('payments.modal')
@endsection

@section('plugins.Datatables', true)
@section('js')
<script>
$(function(){
    let table = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('payments.data') }}",
        columns: [
            { data: 'date', name: 'date' },
            { data: 'reference', name: 'reference' },
            { data: 'description', name: 'description' },
            { data: 'category', name: 'category' },
            { data: 'amount', name: 'amount' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#addPaymentBtn').click(function(){
        $('#paymentForm')[0].reset();
        $('#payment_id').val('');
        $('#paymentModal').modal('show');
    });

    $('#paymentForm').submit(function(e){
        e.preventDefault();
        let id = $('#payment_id').val();
        let url = id ? '/payments/' + id : '/payments';
        let method = id ? 'PUT' : 'POST';
        let data = {
            _token: '{{ csrf_token() }}',
            date: $('#date').val(),
            reference: $('#reference').val(),
            description: $('#description').val(),
            category: $('#category').val(),
            amount: $('#amount').val()
        };

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function() {
                $('#paymentModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Berhasil', 'Data tersimpan', 'success');
            },
            error: function(xhr) {
                let msg = 'Gagal menyimpan';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    $(document).on('click', '.editPayment', function(){
        let id = $(this).data('id');
        $.get('/payments/' + id + '/edit', function(data){
            $('#payment_id').val(data.id);
            $('#date').val(data.date);
            $('#reference').val(data.reference);
            $('#description').val(data.description);
            $('#category').val(data.category);
            $('#amount').val(data.amount);
            $('#paymentModal').modal('show');
        });
    });

    $(document).on('click', '.deletePayment', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            showCancelButton: true,
            icon: 'warning'
        }).then((res)=>{
            if(res.isConfirmed){
                $.ajax({
                    url: '/payments/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(){
                        table.ajax.reload();
                        Swal.fire('Terhapus','', 'success');
                    }
                });
            }
        });
    });
});
</script>
@endsection
