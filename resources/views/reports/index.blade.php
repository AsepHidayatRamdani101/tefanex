@extends('adminlte::page')

@section('title', 'Cetak Laporan')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('content_header')
    <h1>Cetak Laporan Siswa</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pilih Siswa untuk Cetak Laporan</h3>
    </div>
    <div class="card-body">
        <!-- Filter Section -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="filterKelas" class="form-label"><strong>Filter Kelas</strong></label>
                <select id="filterKelas" class="form-control">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="siswaTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 25%;">Nama Siswa</th>
                        <th style="width: 15%;">NIM</th>
                        <th style="width: 20%;">Kelas</th>
                        <th style="width: 35%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $index => $siswa)
                        <tr data-kelas-id="{{ $siswa->kelas_id ?? '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $siswa->nama ?? '-' }}</strong>
                            </td>
                            <td>{{ $siswa->nim ?? '-' }}</td>
                            <td>{{ $siswa->kelas?->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('report.show', $siswa->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data siswa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
<script>
    $(function() {
        const kelasFilter = document.getElementById('filterKelas');

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'siswaTable') {
                return true;
            }

            const selectedKelas = kelasFilter.value;
            if (!selectedKelas) {
                return true;
            }

            const row = settings.aoData[dataIndex].nTr;
            return row.getAttribute('data-kelas-id') === selectedKelas;
        });

        const table = $('#siswaTable').DataTable({
            pageLength: 10,
            lengthChange: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: 0, orderable: false, searchable: false },
                { targets: 4, orderable: false, searchable: false },
            ]
        });

        $('#filterKelas').on('change', function() {
            table.draw();
        });
    });
</script>
@stop
