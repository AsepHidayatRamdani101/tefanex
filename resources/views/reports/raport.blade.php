@extends('adminlte::page')

@section('title', 'Raport - ' . ($siswa->nama ?? 'Siswa'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Raport Siswa</h1>
        <button onclick="window.print()" class="btn btn-primary no-print">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>
@stop

@section('content')
<div id="raport-container" class="raport-wrapper">
    <!-- Main Report Card -->
    <div class="raport-card">
        <!-- Header with School Info -->
        <div class="raport-header">
            <div class="header-content">
                <!-- Logo Section -->
                <div class="header-logo-section">
                    @if($schoolSetting?->school_logo)
                        <img src="{{ Storage::url($schoolSetting->school_logo) }}" alt="Logo" class="school-logo-header">
                    @else
                        <div class="school-logo-placeholder"></div>
                    @endif
                </div>

                <!-- School Info Section -->
                <div class="header-info-section">
                    <div class="header-line header-department" style="margin-bottom: -5px">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
                    <div class="header-line header-dinas" style="margin-bottom: -5px">DINAS PENDIDIKAN</div>
                    <div class="header-line header-category" style="margin-bottom: -5px">CABANG DINAS PENDIDIKAN WILAYAH XI</div>
                    <h1 class="header-school-name">{{ $schoolSetting?->school_name ?? 'NAMA SEKOLAH' }}</h1>
                    <div class="header-address" style="margin-bottom: -5px">JL. RAYA LIMBANGAN SELAAWI KM 12 SELAAWI GARUT</div>
                    <div class="header-contact" style="margin-bottom: -5px">Website: www.smk8garut.sch.id <=> email: smknegeri8grt@gmail.com</div>
                </div>
            </div>
            <hr class="header-bottom-divider">
        </div>

        <!-- Student Information Section -->
        <div class="student-info-section mt-4 mb-4">
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama Murid</td>
                    <td class="info-separator">:</td>
                    <td class="info-value"><strong>{{ strtoupper($siswa->nama ?? '-') }}</strong></td>
                    <td class="info-spacer"></td>
                    <td class="info-label">Kelas</td>
                    <td class="info-separator">:</td>
                    <td class="info-value"><strong>{{ $siswa->kelas?->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="info-label">NIS/NISN</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $siswa->nim ?? '-' }}</td>
                    <td class="info-spacer"></td>
                    <td class="info-label">Sekolah</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $schoolSetting?->school_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Alamat</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $siswa->alamat ?? '-' }}</td>
                    <td class="info-spacer"></td>
                    <td class="info-label"></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <!-- Academic Grades Section -->
        <div class="grades-section mt-4">
            <h4 class="section-title"><strong>LAPORAN HASIL BELAJAR</strong></h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-project">Nama Project</th>
                        <th class="col-material">Nama Materi</th>
                        <th class="col-score">Nilai Akhir</th>
                        <th class="col-note">Catatan Sikap</th>
                    </tr>
                </thead>
                <tbody>
                    @if($grades->count() > 0)
                        @forelse($grades as $projectName => $materials)
                            @forelse($materials as $index => $grade)
                                <tr>
                                    @if($index === 0)
                                        <td class="col-project" rowspan="{{ $materials->count() }}">
                                            <strong>{{ $projectName ?? '-' }}</strong>
                                        </td>
                                    @endif
                                    <td class="col-material">{{ $grade->material_title ?? '-' }}</td>
                                    <td class="col-score text-center">
                                        <strong>
                                            {{ $grade->average_score !== null ? rtrim(rtrim(number_format((float)$grade->average_score, 1, '.', ''), '0'), '.') : '-' }}
                                        </strong>
                                    </td>
                                    <td class="col-note">{{ $grade->attitude_note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">-</td>
                                </tr>
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada data nilai</td>
                            </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data nilai</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Attendance Section -->
        <div class="attendance-section mt-5">
            <h4 class="section-title"><strong>KEHADIRAN</strong></h4>
            <div class="row">
                <div class="col-md-6">
                    <table class="attendance-table attendance-fixed">
                        <thead>
                            <tr>
                                <th colspan="3">Ketidakhadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="label">Sakit</td>
                                <td class="separator">:</td>
                                <td class="value"><strong>{{ $attendance->sakit ?? 0 }} hari</strong></td>
                            </tr>
                            <tr>
                                <td class="label">Izin</td>
                                <td class="separator">:</td>
                                <td class="value"><strong>{{ $attendance->izin ?? 0 }} hari</strong></td>
                            </tr>
                            <tr>
                                <td class="label">Tanpa Keterangan</td>
                                <td class="separator">:</td>
                                <td class="value"><strong>{{ $attendance->alpa ?? 0 }} hari</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="attendance-table attendance-fixed">
                        <thead>
                            <tr>
                                <th colspan="1">Catatan Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="height: 108px; vertical-align: top;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="signatures-section mt-5">
            <table class="signature-table">
                <tr>
                    <td class="sig-column sig-left">
                        <div class="sig-date-placeholder"></div>
                        <div class="sig-label">Orang Tua/Wali Murid</div>
                        <div class="sig-space"></div>
                        <div class="sig-name">................................</div>
                    </td>
                    <td class="sig-column sig-right">
                        <div class="sig-date-inline" style="margin-bottom: -5px">
                            Garut, 
                            @php
                                setlocale(LC_TIME, 'id_ID.UTF-8');
                                $date = \Carbon\Carbon::now();
                                echo strftime('%d %B %Y', $date->timestamp);
                            @endphp
                        </div>
                        <div class="sig-label">Guru Pengajar</div>
                        <div class="sig-space"></div>
                        <div class="sig-name">
                            @if($guru)
                                <strong>{{ $guru->name }}</strong>
                            @else
                                ................................
                            @endif
                        </div>
                    </td>
                </tr>
            </table>

            <div class="sig-principal-section mt-4">
                <div class="sig-principal">
                    <div class="sig-label">Kepala Sekolah</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">
                        <strong>{{ $schoolSetting?->principal_name ?? '................................' }}</strong>
                    </div>
                    @if($schoolSetting?->principal_nip)
                        <div class="sig-nip">NIP. {{ $schoolSetting->principal_nip }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    :root {
        --primary-color: #333;
        --border-color: #000;
        --text-color: #000;
    }

    .raport-wrapper {
        background: white;
        padding: 20px;
    }

    .raport-card {
        background: white;
        border: 1px solid var(--border-color);
        padding: 40px;
        max-width: 900px;
        margin: 0 auto;
        color: var(--text-color);
    }

    .raport-header {
        margin-bottom: 20px;
    }

    .header-content {
        position: relative;
        min-height: 120px;
    }

    .header-logo-section {
        position: absolute;
        left: 0;
        top: 0;
    }

    .school-logo-header {
        width: 95px;
        height: 95px;
        object-fit: contain;
    }

    .school-logo-placeholder {
        width: 95px;
        height: 95px;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
    }

    .header-info-section {
        text-align: center;
        width: 100%;
        padding-left: 50px;
    }

    .header-line {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        margin: 2px 0;
    }

    .header-department {
        font-size: 15px;
        color: #333;
    }

    .header-dinas {
        font-size: 15px;
        color: #333;
    }

    .header-category {
        font-size: 15px;
        color: #333;
    }

    .header-line-divider {
        border-top: 2px solid #000;
        margin: 8px 0;
    }

    .header-school-name {
        font-size: 20px;
        font-weight: bold;
        margin: 8px 0;
        letter-spacing: 1px;
    }

    .header-address {
        font-size: 11px;
        color: #333;
        margin: 3px 0;
    }

    .header-contact {
        font-size: 10px;
        color: #333;
        margin: 3px 0;
    }

    .header-bottom-divider {
        border: 2px solid #000;
        margin: 10px 0;
    }

    .student-info-section {
        margin: 20px 0;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .info-table td {
        padding: 8px 0;
    }

    .info-label {
        width: 20%;
        font-weight: 500;
    }

    .info-separator {
        width: 3%;
        text-align: center;
    }

    .info-value {
        width: 27%;
    }

    .info-spacer {
        width: 5%;
    }

    .section-title {
        font-size: 14px;
        font-weight: bold;
        margin: 20px 0 15px 0;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 8px;
    }

    .data-table,
    .attendance-table,
    .signature-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        border: 1px solid var(--border-color);
    }

    .data-table th,
    .attendance-table th {
        background-color: #f5f5f5;
        border: 1px solid var(--border-color);
        padding: 10px;
        text-align: center;
        font-weight: bold;
    }

    .data-table td,
    .attendance-table td {
        border: 1px solid var(--border-color);
        padding: 8px;
    }

    .col-project {
        width: 20%;
    }

    .col-material {
        width: 35%;
    }

    .col-score {
        width: 15%;
    }

    .col-note {
        width: 30%;
    }

    .text-center {
        text-align: center;
    }

    .attendance-table {
        border: 1px solid var(--border-color);
    }

    .attendance-table thead {
        background-color: #f5f5f5;
    }

    .attendance-table .label {
        width: 40%;
        font-weight: 500;
    }

    .attendance-table .separator {
        width: 10%;
        text-align: center;
    }

    .attendance-table .value {
        width: 50%;
    }

    .attendance-fixed {
        min-height: 140px;
    }

    .signature-table {
        border: none;
    }

    .signature-table td {
        border: none;
        vertical-align: top;
        padding: 0 15px;
    }

    .sig-column {
        width: 50%;
        text-align: center;
    }

    .sig-left {
        text-align: center;
    }

    .sig-right {
        text-align: center;
    }

    .sig-date-placeholder {
        font-size: 12px;
        margin-bottom: 10px;
        font-weight: 500;
        height: 14px;
        visibility: hidden;
    }

    .sig-date-inline {
        font-size: 12px;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .sig-principal-section {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .sig-principal {
        text-align: center;
        width: auto;
    }

    .sig-label {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .sig-space {
        height: 60px;
        margin: 10px 0;
    }

    .sig-name {
        font-size: 12px;
        margin-top: 10px;
    }

    .sig-nip {
        font-size: 11px;
        margin-top: 2px;
    }

    .location-date {
        font-size: 13px;
        margin-top: 20px;
    }

    .text-muted {
        color: #999;
    }

    .no-print {
        margin-bottom: 20px;
    }

    /* Print Styles */
    @media print {
        body {
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .content-wrapper {
            background-color: white !important;
            padding: 0 !important;
        }

        .main-header,
        .main-sidebar,
        .no-print {
            display: none !important;
        }

        .content-header {
            display: none !important;
        }

        .raport-wrapper {
            padding: 0;
            background: white;
        }

        .raport-card {
            border: none;
            padding: 0;
            box-shadow: none;
            page-break-inside: avoid;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        .data-table,
        .attendance-table {
            page-break-inside: avoid;
        }

        .signatures-section {
            page-break-inside: avoid;
        }

        .sig-principal-section {
            page-break-inside: avoid;
        }
    }

    @media (max-width: 768px) {
        .raport-card {
            padding: 20px;
        }

        .signature-table td {
            padding: 10px 5px;
        }
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Auto-print on load (commented out for now)
        // window.print();
    });
</script>
@stop
