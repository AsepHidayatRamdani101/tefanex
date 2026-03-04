@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard TEFA</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box 
                title="120" 
                text="Total Siswa"
                icon="fas fa-users"
                theme="info"/>
        </div>
    </div>
@stop