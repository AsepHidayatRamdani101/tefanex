<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="questionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="questionModalLabel">Tambah Pertanyaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="question_id" id="question_id">

                    <div class="form-group">
                        <label>Test <span class="text-danger">*</span></label>
                        <select name="test_id" id="test_id" class="form-control" required>
                            <option value="">Pilih Test</option>
                            @foreach ($tests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->material->title ?? 'Unknown Material' }} ({{ ucfirst($test->type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipe Pertanyaan <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Pilih Tipe</option>
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Teks Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="question_text" id="question_text" class="form-control" rows="4" required
                                  placeholder="Masukkan teks pertanyaan..."></textarea>
                        <small class="form-text text-muted">Maksimal 1000 karakter</small>
                    </div>

                    <!-- Multiple Choice Options -->
                    <div id="multipleChoiceSection" style="display: none;">
                        <div class="form-group">
                            <label>Pilihan Jawaban <span class="text-danger">*</span></label>
                            <div id="optionsContainer">
                                <div class="input-group mb-2 option-item">
                                    <input type="text" name="options[]" class="form-control" placeholder="Pilihan A" maxlength="255">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-option" style="display: none;">&times;</button>
                                    </div>
                                </div>
                                <div class="input-group mb-2 option-item">
                                    <input type="text" name="options[]" class="form-control" placeholder="Pilihan B" maxlength="255">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-option" style="display: none;">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addOption" class="btn btn-sm btn-secondary">Tambah Pilihan</button>
                            <small class="form-text text-muted">Minimal 2 pilihan, maksimal 5 pilihan, maksimal 255 karakter per pilihan</small>
                        </div>

                        <div class="form-group">
                            <label>Jawaban Benar <span class="text-danger">*</span></label>
                            <select name="correct_answer" id="correct_answer" class="form-control">
                                <option value="">Pilih Jawaban Benar</option>
                            </select>
                            <small class="form-text text-muted">Jawaban benar harus salah satu dari pilihan di atas</small>
                        </div>
                    </div>

                    <!-- Essay Correct Answer -->
                    <div id="essaySection" style="display: none;">
                        <div class="form-group">
                            <label>Jawaban Benar (Opsional)</label>
                            <textarea name="essay_correct_answer" id="essay_correct_answer" class="form-control" rows="3"
                                      placeholder="Masukkan jawaban benar untuk pertanyaan essay..."></textarea>
                            <small class="form-text text-muted">Maksimal 1000 karakter</small>
                        </div>
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