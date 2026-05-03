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
                    <input type="text" name="school_name" id="school_name" class="form-control @error('school_name') is-invalid @enderror" value="{{ old('school_name', $setting->school_name) }}">
                    @error('school_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="principal_name">Nama Kepala Sekolah</label>
                    <input type="text" name="principal_name" id="principal_name" class="form-control @error('principal_name') is-invalid @enderror" value="{{ old('principal_name', $setting->principal_name) }}">
                    @error('principal_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="principal_nip">NIP Kepala Sekolah</label>
                    <input type="text" name="principal_nip" id="principal_nip" class="form-control @error('principal_nip') is-invalid @enderror" value="{{ old('principal_nip', $setting->principal_nip) }}">
                    @error('principal_nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="school_logo">Logo Sekolah</label>
                    <input type="file" name="school_logo" id="school_logo" class="form-control @error('school_logo') is-invalid @enderror" accept="image/*">
                    @error('school_logo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Preview Logo</label>
                    <div class="border rounded p-3 text-center bg-light">
                        @if ($setting->school_logo)
                            <img src="{{ asset('storage/' . $setting->school_logo) }}" alt="Logo Sekolah" style="max-height: 120px; max-width: 100%;">
                        @else
                            <span class="text-muted">Belum ada logo sekolah</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Simpan Setting</button>
            </div>
        </form>
    </div>
@endsection
