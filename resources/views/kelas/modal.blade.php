<div class="modal fade" id="kelasModal" tabindex="-1" aria-labelledby="kelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="kelasForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="kelasModalLabel">Tambah Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="kelas_id" id="kelas_id">

                    <div class="form-group">
                        <label>Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: X IPA 1" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label>Kode Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: X-IPA-1" required maxlength="50">
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Deskripsi kelas (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Kapasitas Siswa <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" id="capacity" class="form-control" value="30" min="1" max="200" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
