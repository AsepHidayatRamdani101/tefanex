@extends('adminlte::page')

@section('title', 'Rekap Attendance')

@section('content')
    <div class="card mt-2">
        <div class="card-header">
            <h3 class="card-title">Rekap Laporan Attendance</h3>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="alert-heading">Error!</h4>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="alert-heading">Error!</h4>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session('success') }}

                            <!-- Debug Info -->
                            <div class="alert alert-info">
                                <small>
                                    <strong>Active Filters:</strong>
                                    Kelas: <code>{{ $filters['kelas_id'] ?: 'Semua' }}</code> |
                                    Dari: <code>{{ $filters['date_from'] ?: 'Tidak diset' }}</code> |
                                    Sampai: <code>{{ $filters['date_to'] ?: 'Tidak diset' }}</code>
                                </small>
                            </div>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('attendances.rekap') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="kelas_id">Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ (string) $filters['kelas_id'] === (string) $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from">Tanggal Dari</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to">Tanggal Sampai</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                            </div>
                            <div class="col-md-5 d-flex align-items-end gap-2">
                                <button id="apply-btn" type="button" class="btn btn-primary btn-sm">Terapkan</button>
                                <button id="reset-btn" class="btn btn-secondary btn-sm">Reset</button>
                            </div>
                        </div>
                    </form>
                    <div class="row mb-3">
                        <div class="col-md-12 d-flex align-items-end gap-2">
                            <form id="export-form" method="GET" action="{{ route('attendances.rekap.export') }}" style="display: inline;">
                                <input id="export_kelas_id" type="hidden" name="kelas_id" value="{{ $filters['kelas_id'] }}">
                                <input id="export_date_from" type="hidden" name="date_from" value="{{ $filters['date_from'] }}">
                                <input id="export_date_to" type="hidden" name="date_to" value="{{ $filters['date_to'] }}">
                                <button id="export-btn" type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="rekap-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Total</th>
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
    <script>
        $(function() {
            let table = $('#rekap-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ route('attendances.rekap.data') }}",
                    data: function(d) {
                        d.kelas_id = $('#kelas_id').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kelas',
                        name: 'kelas',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'hadir',
                        name: 'hadir',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge badge-success">' + data + '</span>';
                        }
                    },
                    {
                        data: 'sakit',
                        name: 'sakit',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge badge-info">' + data + '</span>';
                        }
                    },
                    {
                        data: 'izin',
                        name: 'izin',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge badge-warning">' + data + '</span>';
                        }
                    },
                    {
                        data: 'alpha',
                        name: 'alpha',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge badge-danger">' + data + '</span>';
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#apply-btn').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#reset-btn').on('click', function(e) {
                e.preventDefault();
                $('#kelas_id').val('');
                $('#date_from').val('');
                $('#date_to').val('');
                table.ajax.reload();
            });

            $('#export-btn').on('click', function(e) {
                $('#export_kelas_id').val($('#kelas_id').val());
                $('#export_date_from').val($('#date_from').val());
                $('#export_date_to').val($('#date_to').val());
            });
        });
    </script>
@endsection
