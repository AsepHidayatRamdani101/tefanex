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

                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> Anda dapat menambahkan materi dalam berbagai format sekaligus: teks, link video, atau PDF</small>
                    </div>

                    <div class="form-group">
                        <label>Konten Teks <span class="text-muted">(Opsional)</span></label>
                        <textarea name="content" id="content" class="form-control" rows="4" placeholder="Masukkan konten materi dalam bentuk teks..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="video_link">Link Video YouTube <span class="text-muted">(Opsional)</span></label>
                        <input type="url" class="form-control" id="video_link" name="video_link" placeholder="https://youtube.com/watch?v=dQw4w9WgXcQ atau https://youtu.be/dQw4w9WgXcQ">
                        <small class="form-text text-muted">Format: https://youtube.com/watch?v=... atau https://youtu.be/...</small>
                    </div>

                    <div class="form-group">
                        <label for="file">Upload File PDF <span class="text-muted">(Opsional)</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" accept=".pdf">
                            <label class="custom-file-label" for="file">Pilih File PDF</label>
                        </div>
                        <small class="form-text text-muted">Ukuran maksimal 100MB</small>
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
