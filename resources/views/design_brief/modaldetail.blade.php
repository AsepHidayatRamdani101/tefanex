<div class="modal fade" id="designBriefModalDetail" tabindex="-1" aria-labelledby="designBriefModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl w-100">
        <div class="modal-content">
            <form id="designBriefForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="designBriefModalLabel">Lihat Design Brief</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="designBrief_id" id="designBrief_id">
                    <input type="hidden" name="project_id" id="project_id">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Project</label>
                                <input type="text" name="name" id="name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Klien</label>
                                <input type="text" name="client" id="client" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Apa Yang dikerjakan</label>
                                <select name="dikerjakan" id="dikerjakan" class="form-control">
                                    <option value="Mug">Mug</option>
                                    <option value="Map">Map</option>
                                    <option value="Foto Ijazah">Foto Ijazah</option>
                                    <option value="ID Card">ID Card</option>
                                    <option value="Kaos">Kaos</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Lama Pengerjaan</label>
                                <div class="input-group">
                                    <input type="number" name="lama_pengerjaan" id="lama_pengerjaan" class="form-control" min="1" step="1">
                                    <div class="input-group-append">
                                        <select name="lama_pengerjaan_satuan" id="lama_pengerjaan_satuan" class="form-control">
                                            <option value="hari" selected>Hari</option>
                                            <option value="minggu">Minggu</option>
                                            <option value="bulan">Bulan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Dimensi File (cm/pixel/meter)</label>
                                <input type="text" name="dimensi" id="dimensi" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Warna</label>
                                <input type="text" name="warna" id="warna" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tagline</label>
                                <input type="text" name="tagline" id="tagline" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Font</label>
                                <input type="text" name="font" id="font" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Target Market</label>
                                <input type="text" name="target_market" id="target_market" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Satuan</label>
                                <input type="text" id="harga_satuan_display" class="form-control" inputmode="numeric" autocomplete="off">
                                <input type="hidden" name="harga_satuan" id="harga_satuan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jumlah yang Akan Dicetak</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Budget</label>
                                <input type="text" id="budget_display" class="form-control" readonly>
                                <input type="hidden" name="budget" id="budget">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Output File</label>
                                <div class="form-check">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]" value="pdf" id="output_file_pdf">
                                        <label class="form-check-label" for="output_file_pdf">
                                            PDF
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]" value="jpg" id="output_file_jpg">
                                        <label class="form-check-label" for="output_file_jpg">
                                            JPG
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]" value="cdr" id="output_file_cdr">
                                        <label class="form-check-label" for="output_file_cdr">
                                            CDR
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]" value="ai" id="output_file_ai">
                                        <label class="form-check-label" for="output_file_ai">
                                            AI
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]" value="psd" id="output_file_psd">
                                        <label class="form-check-label" for="output_file_psd">
                                            PSD
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gambar Referensi</label>
                                <input type="file" name="reference_files[]" id="reference_files" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Maksimal 3 gambar.</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div id="referenceFilePreview" class="d-flex flex-wrap"></div>
                        </div>

