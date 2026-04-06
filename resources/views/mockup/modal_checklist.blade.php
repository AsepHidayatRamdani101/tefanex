<div class="modal fade" id="qcFileDesainModal" tabindex="-1" role="dialog" aria-labelledby="qcFileDesainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qcFileDesainModalLabel">QC File Desain (Pre-Production)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="qcFileDesainForm">
                @csrf
                <input type="hidden" id="mockup_id" name="mockup_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <p>Pastikan semua poin di bawah ini sudah dicek sebelum file dikirim ke cetak:</p>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="checkResolusi" name="check_resolusi">
                                <label class="form-check-label" for="checkResolusi">
                                    Resolusi minimal 300 dpi
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="checkModeWarna" name="check_mode_warna">
                                <label class="form-check-label" for="checkModeWarna">
                                    Mode warna CMYK (bukan RGB)
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="checkUkuran" name="check_ukuran">
                                <label class="form-check-label" for="checkUkuran">
                                    Ukuran sesuai pesanan (tidak pecah / resize berlebihan)
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="checkMarginBleed" name="check_margin_bleed">
                                <label class="form-check-label" for="checkMarginBleed">
                                    Margin & bleed aman (tidak kepotong)
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="checkFont" name="check_font">
                                <label class="form-check-label" for="checkFont">
                                    Font sudah di-embed / convert to curve
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="checkTypo" name="check_typo">
                                <label class="form-check-label" for="checkTypo">
                                    Tidak ada typo (nama, tanggal, nomor, dll)
                                </label>
                            </div>

                            <div class="form-group mt-3">
                                <label for="qcFileDesainNote">Catatan / Komentar</label>
                                <textarea class="form-control" id="qcFileDesainNote" name="note" rows="3" placeholder="Tambahkan catatan tambahan jika perlu..."></textarea>
                            </div>
                            <div class="form-group mt-3">
                                <label for="qcFileDesainStatus">Status QC</label>
                                <select class="form-control" id="qcFileDesainStatus" name="status">
                                    <option value="">Pilih Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="revisi">Revisi</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="mb-3">Preview Mockup</h6>
                                 <p class="text-muted mt-3">Gambar mockup akan muncul di sini saat file mockup diambil dari data.</p>
                                <div class="d-flex justify-content-center align-items-center h-100" style="margin-top: -30px">
                                    <img id="mockupPreview" src="" alt="Mockup Preview" class="img-fluid rounded shadow-sm" style="max-height: 460px; width: auto;" />
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Checklist</button>
                </div>
            </form>
        </div>
    </div>
</div>