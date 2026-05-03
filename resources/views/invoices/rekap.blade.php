@extends('adminlte::page')

@section('title', 'Rekap Invoice')

@section('content')
<div class="card mt-2">
    <div class="card-header">
        <h3 class="card-title">Rekap Invoice - Histori Pembayaran</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="row mb-2">
                <div class="col-md-3">
                    <input type="date" id="start_date" class="form-control" placeholder="Start date">
                </div>
                <div class="col-md-3">
                    <input type="date" id="end_date" class="form-control" placeholder="End date">
                </div>
                <div class="col-md-2">
                    <button id="applyFilter" class="btn btn-primary">Apply</button>
                    <button id="resetFilter" class="btn btn-secondary">Reset</button>
                </div>
            </div>
            <table class="table table-bordered" id="rekapTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Referensi</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Saldo Kumulatif</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('js')
<script>
$(function() {
    // No client-side formatter needed; server returns formatted amount

    let table = $('#rekapTable').DataTable({
        processing: true,
        serverSide: true,
            ajax: {
                url: "{{ route('invoices.rekap.data') }}",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'type', name: 'type' },
            { data: 'reference', name: 'reference' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'balance', name: 'balance', render: function(data) {
                if (data === null || data === undefined) return '-';
                let n = Number(String(data)) || 0;
                return 'Rp ' + Math.abs(Math.round(n)).toLocaleString('id-ID');
            }}
        ]
    });

    $('#applyFilter').on('click', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#resetFilter').on('click', function(e) {
        e.preventDefault();
        $('#start_date').val('');
        $('#end_date').val('');
        table.ajax.reload();
    });
});
</script>
@endsection
