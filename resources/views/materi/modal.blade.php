<div class="modal fade" id="materiModal" tabindex="-1" aria-labelledby="materiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="materiForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="materiModalLabel">Tambah Materi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Project <span class="text-muted">(Opsional)</span></label>
                        <select name="project_id" id="project_id" class="form-control">
                            <option value="">Pilih Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->judul }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Materi <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Tipe Materi <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Pilih Tipe</option>
                            <option value="text">Text (Deskripsi Singkat)</option>
                            <option value="video">Video (Link)</option>
                            <option value="pdf">PDF (Upload)</option>
                        </select>
                    </div>

                    <div class="form-group" id="contentGroup" style="display: none;">
                        <label>Konten <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="5" placeholder="Masukkan konten materi..."></textarea>
                    </div>

                    <div class="form-group" id="fileGroup" style="display: none;">
                        <label for="file">Upload File 
                            <span class="badge badge-info">PDF: .pdf</span>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" accept=".pdf">
                            <label class="custom-file-label" for="file">Pilih File</label>
                        </div>
                        <small class="form-text text-muted">Ukuran maksimal 100MB</small>
                    </div>

                    <div class="form-group" id="videoGroup" style="display: none;">
                        <label for="video_link">Link Video 
                            <span class="badge badge-info">YouTube</span>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="url" class="form-control" id="video_link" name="video_link" placeholder="https://youtube.com/watch?v=dQw4w9WgXcQ atau https://youtu.be/dQw4w9WgXcQ">
                        <small class="form-text text-muted">Dukung format: https://youtube.com/watch?v=... atau https://youtu.be/... atau embed URL</small>
                    </div>

                    <div id="fileInfo"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-file-label::after {
        content: "Pilih File";
    }

    .custom-file-input:focus ~ .custom-file-label {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .custom-file-input:lang(en) ~ .custom-file-label::after {
        content: "Pilih File";
    }
</style>

<script>
    // Update file label
    $('#file').on('change', function() {
        let fileName = $(this).val().split('\\\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
