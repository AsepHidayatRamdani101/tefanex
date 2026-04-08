<!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importExcelForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">
                        <i class="fas fa-file-excel text-success"></i> Import Pertanyaan dari Excel
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Test <span class="text-danger">*</span></label>
                        <select name="test_id" id="import_excel_test_id" class="form-control" required>
                            <option value="">Pilih Test</option>
                            @foreach ($tests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->material->title ?? 'Unknown Material' }} ({{ ucfirst($test->type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="form-text text-muted">
                            Format: .xlsx, .xls, atau .csv. Maksimal 10MB.
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <strong>Format Excel yang Diharapkan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Kolom pertama: <code>pertanyaan</code> atau <code>question</code></li>
                            <li>Kolom pilihan: <code>pilihan_1</code>, <code>pilihan_2</code>, dst. (maksimal 5)</li>
                            <li>Kolom jawaban: <code>jawaban_benar</code> atau <code>correct_answer</code></li>
                        </ul>
                        <a href="#" class="btn btn-sm btn-outline-primary mt-2" onclick="downloadTemplate('excel')">
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Word Modal -->
<div class="modal fade" id="importWordModal" tabindex="-1" aria-labelledby="importWordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importWordForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="importWordModalLabel">
                        <i class="fas fa-file-word text-primary"></i> Import Pertanyaan dari Word
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Test <span class="text-danger">*</span></label>
                        <select name="test_id" id="import_word_test_id" class="form-control" required>
                            <option value="">Pilih Test</option>
                            @foreach ($tests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->material->title ?? 'Unknown Material' }} ({{ ucfirst($test->type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>File Word <span class="text-danger">*</span></label>
                        <input type="file" name="word_file" id="word_file" class="form-control" accept=".doc,.docx" required>
                        <small class="form-text text-muted">
                            Format: .doc atau .docx. Maksimal 10MB.
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <strong>Format Word yang Diharapkan:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Format Teks:</strong> 1. Pertanyaan A. Pilihan 1 B. Pilihan 2 Jawaban: A</li>
                            <li><strong>Format Tabel:</strong> Setiap baris berisi pertanyaan dan pilihan</li>
                        </ul>
                        <a href="#" class="btn btn-sm btn-outline-primary mt-2" onclick="downloadTemplate('word')">
                            <i class="fas fa-download"></i> Download Template Word
                        </a>
                    </div>
                    <div class="alert alert-secondary mt-3">
                        <strong>Catatan Format Word:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Gunakan nomor soal di awal baris: <code>1. </code>, <code>2. </code>, dst.</li>
                            <li>Untuk pilihan ganda, tulis opsi dengan <code>A.</code>, <code>B.</code>, <code>C.</code>, dst.</li>
                            <li>Tuliskan jawaban benar dengan baris <code>Jawaban: ...</code>.</li>
                            <li>Soal essay tidak memerlukan pilihan, cukup isi pertanyaan dan jawaban.</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload"></i> Import Word
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>