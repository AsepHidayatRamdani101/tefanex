<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Kelas dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Petunjuk Penggunaan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Download template terlebih dahulu dengan klik tombol "Download Template"</li>
                            <li>Isi data kelas sesuai format yang tersedia di template</li>
                            <li>Pastikan Kode Kelas bersifat unik (tidak ada duplikat)</li>
                            <li>Kapasitas harus antara 1-200 siswa</li>
                            <li>Deskripsi bersifat opsional</li>
                            <li>File yang didukung: .xlsx, .xls, .csv</li>
                            <li>Ukuran file maksimal: 10 MB</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label>Pilih File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                            <label class="custom-file-label" for="importFile">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">Format: Excel (.xlsx, .xls) atau CSV</small>
                    </div>

                    <div id="importErrors" class="alert alert-danger" style="display: none;">
                        <strong>Kesalahan Import:</strong>
                        <ul id="errorList" class="mb-0 mt-2"></ul>
                    </div>

                    <div id="importSuccess" class="alert alert-success" style="display: none;">
                        <strong>Kesuksesan!</strong>
                        <p id="successMessage" class="mb-0 mt-2"></p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <a href="{{ route('kelas.downloadTemplate') }}" class="btn btn-info">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-primary" id="importSubmitBtn">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Update file input label when file is selected
    document.getElementById('importFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Pilih file...';
        document.querySelector('.custom-file-label').textContent = fileName;
    });
</script>
