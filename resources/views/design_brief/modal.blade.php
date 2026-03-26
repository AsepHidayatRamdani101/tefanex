<div class="modal fade" id="designBriefModal" tabindex="-1" aria-labelledby="designBriefModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl w-100">
        <div class="modal-content">
            <form id="designBriefForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="designBriefModalLabel">Tambah Design Brief</h5>
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
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Lama Pengerjaan</label>
                                <input type="text" name="lama_pengerjaan" id="lama_pengerjaan" class="form-control">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Target Market</label>
                                <input type="text" name="target_market" id="target_market" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Budget</label>
                                <input type="number" name="budget" id="budget" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Output File</label>
                                <div class="form-check">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]"
                                            value="pdf" id="output_file_pdf">
                                        <label class="form-check-label" for="output_file_pdf">
                                            PDF
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]"
                                            value="jpg" id="output_file_jpg">
                                        <label class="form-check-label" for="output_file_jpg">
                                            JPG
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]"
                                            value="cdr" id="output_file_cdr">
                                        <label class="form-check-label" for="output_file_cdr">
                                            CDR
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]"
                                            value="ai" id="output_file_ai">
                                        <label class="form-check-label" for="output_file_ai">
                                            AI
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="output_file[]"
                                            value="psd" id="output_file_psd">
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
                                <input type="file" name="gambar" id="gambar" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <a href="#" id="gambardetail-link" target="_blank">
                                <img src="" alt="" id="gambardetail" width="100px">
                            </a>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="sub">Tambah</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
