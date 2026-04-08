<div class="modal fade" id="testModal" tabindex="-1" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="testForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="testModalLabel">Tambah Test</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="test_id" id="test_id">

                    <div class="form-group">
                        <label>Materi <span class="text-danger">*</span></label>
                        <select name="material_id" id="material_id" class="form-control" required>
                            <option value="">Pilih Materi</option>
                            @foreach ($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipe Test <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Pilih Tipe</option>
                            <option value="pretest">Pre Test</option>
                            <option value="posttest">Post Test</option>
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