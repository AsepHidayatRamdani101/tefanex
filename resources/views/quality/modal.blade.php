
<div class="modal fade" id="qcCetakModal" tabindex="-1" role="dialog" aria-labelledby="qcCetakModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qcCetakModalLabel">QC Proses Cetak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="qcForm">
                @csrf
                <input type="hidden" id="qc_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>File Referensi</h6>
                            <a href="#" id="referensiFile" target="_blank">Referensi</a>
                        </div>
                        <div class="col-md-6">
                            <h6>File Produksi</h6>
                            <a href="#" id="produksiFile" target="_blank">Produksi</a>
                        </div>
                    </div>
                    <p class="mt-3">Saat produksi:</p>
                    <form id="qcCetakForm">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="warnaProof">
                            <label class="form-check-label" for="warnaProof">
                                Warna sesuai proof (tidak terlalu gelap/pucat)
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tidakAdaGaris">
                            <label class="form-check-label" for="tidakAdaGaris">
                                Tidak ada garis (banding) pada hasil print
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tintaMerata">
                            <label class="form-check-label" for="tintaMerata">
                                Tinta merata
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="kertasSpesifikasi">
                            <label class="form-check-label" for="kertasSpesifikasi">
                                Kertas sesuai spesifikasi (jenis & gramasi)
                            </label>
                        </div>
                       
                        <div class="form-group mt-3">
                            <label>Note</label>
                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <label>Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Pilih Status</option>
                                <option value="lulus">Lulus</option>
                                <option value="revisi">Revisi</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveQcBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
