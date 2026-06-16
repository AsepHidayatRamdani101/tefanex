@extends('adminlte::page')

@section('title', 'Setting Sekolah')

@section('content')
    <div class="card mt-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Setting Sekolah</h3>
        </div>

        <form action="{{ route('school-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="form-group">
                    <label for="school_name">Nama Sekolah</label>
                    <input type="text" name="school_name" id="school_name"
                        class="form-control @error('school_name') is-invalid @enderror"
                        value="{{ old('school_name', $setting->school_name) }}">
                    @error('school_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="principal_name">Nama Kepala Sekolah</label>
                    <input type="text" name="principal_name" id="principal_name"
                        class="form-control @error('principal_name') is-invalid @enderror"
                        value="{{ old('principal_name', $setting->principal_name) }}">
                    @error('principal_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="principal_nip">NIP Kepala Sekolah</label>
                    <input type="text" name="principal_nip" id="principal_nip"
                        class="form-control @error('principal_nip') is-invalid @enderror"
                        value="{{ old('principal_nip', $setting->principal_nip) }}">
                    @error('principal_nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="school_logo">Logo Sekolah</label>
                    <input type="file" name="school_logo" id="school_logo"
                        class="form-control @error('school_logo') is-invalid @enderror" accept="image/*">
                    @error('school_logo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Preview Logo</label>
                    <div class="border rounded p-3 text-center bg-light">
                        @if ($setting->school_logo)
                            <img src="{{ asset('storage/' . $setting->school_logo) }}" alt="Logo Sekolah"
                                style="max-height: 120px; max-width: 100%;">
                        @else
                            <span class="text-muted">Belum ada logo sekolah</span>
                        @endif
                    </div>
                </div>

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Pengaturan Sertifikat</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="certificate_enabled"
                                    id="certificate_enabled" value="1"
                                    {{ old('certificate_enabled', $setting->certificate_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="certificate_enabled">
                                    Aktifkan fitur sertifikat untuk siswa yang menyelesaikan seluruh alur modul.
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="certificate_title">Judul Sertifikat</label>
                            <input type="text" name="certificate_title" id="certificate_title"
                                class="form-control @error('certificate_title') is-invalid @enderror"
                                value="{{ old('certificate_title', $setting->certificate_title) }}">
                            @error('certificate_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="certificate_subtitle">Deskripsi Sertifikat</label>
                            <textarea name="certificate_subtitle" id="certificate_subtitle" rows="3"
                                class="form-control @error('certificate_subtitle') is-invalid @enderror">{{ old('certificate_subtitle', $setting->certificate_subtitle) }}</textarea>
                            @error('certificate_subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="certificate_footer">Footer Sertifikat</label>
                            <textarea name="certificate_footer" id="certificate_footer" rows="2"
                                class="form-control @error('certificate_footer') is-invalid @enderror">{{ old('certificate_footer', $setting->certificate_footer) }}</textarea>
                            @error('certificate_footer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="certificate_number">Nomor Sertifikat</label>
                            <input type="text" name="certificate_number" id="certificate_number"
                                class="form-control @error('certificate_number') is-invalid @enderror"
                                value="{{ old('certificate_number', $setting->certificate_number) }}"
                                placeholder="Contoh: No. 001/2026">
                            @error('certificate_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Nomor sertifikat yang akan ditampilkan di sertifikat</small>
                        </div>

                        <div class="form-group">
                            <label for="certificate_location">Tempat Pengeluaran Sertifikat</label>
                            <input type="text" name="certificate_location" id="certificate_location"
                                class="form-control @error('certificate_location') is-invalid @enderror"
                                value="{{ old('certificate_location', $setting->certificate_location) }}"
                                placeholder="Contoh: Bandung">
                            @error('certificate_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Nama kota/tempat pengeluaran sertifikat</small>
                        </div>

                        <div class="form-group">
                            <label for="certificate_template">Template Design Sertifikat (JPG/PNG)</label>
                            <input type="file" name="certificate_template" id="certificate_template"
                                class="form-control @error('certificate_template') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/jpg">
                            @error('certificate_template')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Upload template design JPG/PNG sebagai background
                                sertifikat. Ukuran maksimal: 5 MB. Resolusi optimal: 1200x800px (landscape)</small>
                        </div>

                        @if ($setting->certificate_template)
                            <div class="form-group">
                                <label>Preview Template</label>
                                <div class="border rounded p-2 text-center bg-light">
                                    <img src="{{ asset('storage/' . $setting->certificate_template) }}"
                                        alt="Certificate Template" style="max-height: 200px; max-width: 100%;">
                                    <div class="mt-2">
                                        <small class="text-muted">{{ $setting->certificate_template }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <a href="{{ route('school-settings.certificate.download') }}"
                                class="btn btn-outline-success">
                                <i class="fas fa-download"></i> Download Sertifikat Contoh
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Simpan Setting</button>
            </div>
        </form>
    </div>
@endsection
