@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard TEFA</h1>
@stop

@section('content')

{{-- ================= GURU ================= --}}
@role('guru')
<div class="row">
    <div class="col-md-4">
        <x-adminlte-small-box title="12" text="Total Project" icon="fas fa-project-diagram" theme="info"/>
    </div>
    <div class="col-md-4">
        <x-adminlte-small-box title="30" text="Total Siswa Aktif" icon="fas fa-users" theme="success"/>
    </div>
</div>
@endrole


{{-- ================= MARKETING ================= --}}
@role('marketing')
<div class="row">
    <div class="col-md-4">
        <x-adminlte-small-box title="5" text="Design Brief Masuk" icon="fas fa-lightbulb" theme="warning"/>
    </div>
</div>
@endrole


{{-- ================= KEPALA PRODUKSI ================= --}}
@role('kepala_produksi')
<div class="row">
    <div class="col-md-4">
        <x-adminlte-small-box title="3" text="Produksi Berjalan" icon="fas fa-industry" theme="primary"/>
    </div>
    <div class="col-md-4">
        <x-adminlte-small-box title="2" text="Menunggu QC" icon="fas fa-check-circle" theme="danger"/>
    </div>
</div>
@endrole


{{-- ================= BENDAHARA ================= --}}
@role('bendahara')
<div class="row">
    <div class="col-md-4">
        <x-adminlte-small-box title="7" text="Invoice Pending" icon="fas fa-file-invoice" theme="secondary"/>
    </div>
</div>
@endrole


{{-- ================= SISWA ================= --}}
@role('siswa')
<div class="row">
    <div class="col-md-4">
        <x-adminlte-small-box title="1" text="Project Aktif" icon="fas fa-tasks" theme="info"/>
    </div>
    <div class="col-md-4">
        <x-adminlte-small-box title="80%" text="Progress" icon="fas fa-chart-line" theme="success"/>
    </div>
</div>
@endrole

@role('super_admin')
<div class="row"> 
    <div class="col-md-4">
        <x-adminlte-small-box title="{{ $totalUsers }}" text="Total Users" icon="fas fa-users" theme="primary"/>
    </div>
    <div class="col-md-4">
        <x-adminlte-small-box title="{{ $totalRoles }}" text="Total Roles" icon="fas fa-user-shield" theme="secondary"/>
    </div>
</div>
@endrole

@stop