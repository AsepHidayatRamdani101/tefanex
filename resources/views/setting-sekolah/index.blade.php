@extends('adminlte::page')

@section('title', 'Setting Sekolah')

@section('content_header')
    <h1>Setting Sekolah</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('school-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="school_name">Nama Sekolah</label>
                            <input type="text" class="form-control" id="school_name" name="school_name"
                                value="{{ old('school_name', $setting->school_name) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="principal_name">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control" id="principal_name" name="principal_name"
                                value="{{ old('principal_name', $setting->principal_name) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="principal_nip">NIP Kepala Sekolah</label>
                            <input type="text" class="form-control" id="principal_nip" name="principal_nip"
                                value="{{ old('principal_nip', $setting->principal_nip) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="school_logo">Logo Sekolah</label>
                            <input type="file" class="form-control-file" id="school_logo" name="school_logo"
                                accept="image/*">
                            @if ($setting->school_logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $setting->school_logo) }}" alt="Logo Sekolah"
                                        style="max-height: 80px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="certificate_enabled" name="certificate_enabled"
                        value="1" {{ old('certificate_enabled', $setting->certificate_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="certificate_enabled">Aktifkan fitur sertifikat</label>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="certificate_title">Judul Sertifikat</label>
                            <input type="text" class="form-control" id="certificate_title" name="certificate_title"
                                value="{{ old('certificate_title', $setting->certificate_title) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="certificate_number">Nomor Sertifikat</label>
                            <input type="text" class="form-control" id="certificate_number" name="certificate_number"
                                value="{{ old('certificate_number', $setting->certificate_number) }}"
                                placeholder="No. 001/SERT/TEFA/2026">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="certificate_place">Tempat Sertifikat</label>
                            <input type="text" class="form-control" id="certificate_place" name="certificate_place"
                                value="{{ old('certificate_place', $setting->certificate_place) }}" placeholder="Bandung">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="certificate_template">Template Design JPG</label>
                            <input type="file" class="form-control-file" id="certificate_template"
                                name="certificate_template" accept="image/jpeg,image/jpg">
                            @if ($setting->certificate_template)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $setting->certificate_template) }}"
                                        alt="Template Sertifikat"
                                        style="max-width: 100%; max-height: 160px; border: 1px solid #ddd;">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="certificate_subtitle">Deskripsi Sertifikat</label>
                            <textarea class="form-control" id="certificate_subtitle" name="certificate_subtitle" rows="3">{{ old('certificate_subtitle', $setting->certificate_subtitle) }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="certificate_footer">Footer Sertifikat</label>
                            <textarea class="form-control" id="certificate_footer" name="certificate_footer" rows="3">{{ old('certificate_footer', $setting->certificate_footer) }}</textarea>
                        </div>
                    </div>
                </div>

                <a href="{{ route('school-settings.certificate.download') }}" class="btn btn-outline-success"
                    target="_blank">Download Sertifikat Contoh</a>
                <button type="submit" class="btn btn-primary float-right">Simpan Setting</button>
            </form>
        </div>
    </div>
@stop
