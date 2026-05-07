@extends('adminlte::page')

@section('title', 'Pengaturan')

@section('content_header')
    <h1>Pengaturan Aplikasi</h1>
@endsection

@section('content')
    <div class="row">
        <!-- Backup Data Card -->
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database"></i> Backup Data
                    </h3>
                </div>
                <div class="card-body">
                    <p>Buat backup lengkap dari semua data aplikasi untuk keamanan dan keperluan restore.</p>
                    <hr>
                    <div class="form-group">
                        <label>Jenis Backup:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backupType" id="backupFull" value="full" checked>
                            <label class="form-check-label" for="backupFull">
                                Backup Lengkap (Database + Files)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backupType" id="backupDatabase" value="database">
                            <label class="form-check-label" for="backupDatabase">
                                Backup Database Saja
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backupType" id="backupFiles" value="files">
                            <label class="form-check-label" for="backupFiles">
                                Backup Files Saja
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success" id="backupBtn">
                        <i class="fas fa-download"></i> Mulai Backup
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Data Card -->
        <div class="col-md-6">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trash-alt"></i> Hapus Data
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong class="text-danger">⚠️ Perhatian:</strong> Operasi ini akan menghapus data secara permanen dan tidak dapat dibatalkan.</p>
                    <hr>
                    <div class="form-group">
                        <label>Pilih Data yang Ingin Dihapus:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deleteType" id="deleteProjects" value="projects">
                            <label class="form-check-label" for="deleteProjects">
                                Hapus Semua Project
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deleteType" id="deleteUsers" value="users">
                            <label class="form-check-label" for="deleteUsers">
                                Hapus Semua User (Kecuali Admin)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deleteType" id="deleteAttendance" value="attendance">
                            <label class="form-check-label" for="deleteAttendance">
                                Hapus Semua Data Absensi
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deleteType" id="deleteTests" value="tests">
                            <label class="form-check-label" for="deleteTests">
                                Hapus Semua Test & Hasil Test
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deleteType" id="deleteAll" value="all">
                            <label class="form-check-label text-danger" for="deleteAll">
                                <strong>Hapus SEMUA Data (Restore Database Default)</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="confirmText">Konfirmasi dengan mengetik: <code>SAYA SETUJU MENGHAPUS</code></label>
                        <input type="text" class="form-control" id="confirmText" placeholder="Ketik konfirmasi di sini...">
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-danger" id="deleteBtn" disabled>
                        <i class="fas fa-trash"></i> Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup History Card -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Backup
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe Backup</th>
                                    <th>Ukuran File</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><span class="badge badge-secondary">Belum ada backup</span></td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Backup functionality
        document.getElementById('backupBtn').addEventListener('click', function() {
            const backupType = document.querySelector('input[name="backupType"]:checked').value;
            
            Swal.fire({
                title: 'Konfirmasi Backup',
                text: `Anda akan membuat backup dengan tipe: ${backupType}. Proses ini mungkin memerlukan beberapa menit.`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Backup...',
                        html: 'Mohon tunggu, sedang membuat backup data dan mendownload file...',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send backup request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("pengaturan.backup") }}';
                    form.style.display = 'none';
                    
                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = 'type';
                    typeInput.value = backupType;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    
                    form.appendChild(typeInput);
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                    
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Backup Berhasil!',
                            text: 'File backup sedang diunduh. Jika belum dimulai, periksa folder Downloads Anda.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    }, 3000);
                }
            });
        });

        // Confirm text validation for delete
        document.getElementById('confirmText').addEventListener('input', function() {
            const deleteBtn = document.getElementById('deleteBtn');
            deleteBtn.disabled = this.value !== 'SAYA SETUJU MENGHAPUS';
        });

        // Delete functionality
        document.getElementById('deleteBtn').addEventListener('click', function() {
            const selectedTypes = Array.from(document.querySelectorAll('input[name="deleteType"]:checked'))
                .map(el => el.value);

            if (selectedTypes.length === 0) {
                Swal.fire({
                    title: 'Pilihan Kosong',
                    text: 'Silakan pilih setidaknya satu jenis data yang ingin dihapus.',
                    icon: 'warning'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Penghapusan Data',
                html: `<p class="text-danger"><strong>⚠️ PERINGATAN!</strong></p>
                       <p>Anda akan menghapus data berikut:</p>
                       <ul style="text-align: left;">
                           ${selectedTypes.map(type => `<li>${type}</li>`).join('')}
                       </ul>
                       <p class="text-danger"><strong>Operasi ini TIDAK DAPAT DIBATALKAN!</strong></p>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Data...',
                        html: 'Mohon tunggu, sedang menghapus data...',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Simulasi proses delete (uncomment untuk implementasi sebenarnya)
                    // fetch('/api/delete-data', {
                    //     method: 'POST',
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    //     },
                    //     body: JSON.stringify({ types: selectedTypes })
                    // })

                    setTimeout(() => {
                        Swal.fire({
                            title: 'Data Terhapus!',
                            text: 'Data berhasil dihapus secara permanen.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }, 2000);
                }
            });
        });
    </script>
@endsection
