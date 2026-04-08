<div class="modal fade" id="siswaModal" tabindex="-1" aria-labelledby="siswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="siswaForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="siswaModalLabel">Tambah Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="siswa_id" id="siswa_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NIM <span class="text-danger">*</span></label>
                                <input type="text" name="nim" id="nim" class="form-control" placeholder="Contoh: 2024001" required maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Siswa <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama lengkap siswa" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-control">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No. Telepon</label>
                                <input type="text" name="no_telepon" id="no_telepon" class="form-control" placeholder="08xxxxxxxxxx" maxlength="15">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Siswa</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="email@example.com">
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Alamat siswa (opsional)"></textarea>
                    </div>

                    <hr class="my-3">
                    <h6>Akun Pengguna (Opsional)</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username Login</label>
                                <input type="email" name="username" id="username" class="form-control" placeholder="username@example.com">
                                <small class="form-text text-muted">Gunakan format email untuk username</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                        </div>
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
