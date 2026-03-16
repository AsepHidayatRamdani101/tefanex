<div class="modal fade" id="projectMemberModal" tabindex="-1" aria-labelledby="projectMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="projectMemberForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectMemberModalLabel">Tambah Anggota Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" name="id" id="projectMember_id">
                    <input type="text" name="project_id_member" id="project_id_member">
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <select name="anggota_id" id="anggota_id" class="form-control">
                            <option value="">Pilih Anggota</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tugas</label>


                        <select name="tugas" id="tugas" class="form-control">
                            <option value="">Pilih Tugas</option>
                            <option value="designer">Designer</option>
                            <option value="kepala_tefa">Kepala TEFA</option>
                            <option value="marketing">Marketing</option>
                            <option value="operator_produksi">Operator Produksi</option>
                            <option value="pembimbing">Pembimbing</option>
                            <option value="qc">QC</option>
                        </select>

                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
        </form>
    </div>
</div>
</div>
