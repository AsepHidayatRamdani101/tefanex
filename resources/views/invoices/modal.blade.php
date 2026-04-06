<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="invoiceForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Tambah Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="invoice_id">
                    <div class="form-group">
                        <label>Project</label>
                        <select name="project_id" id="project_id" class="form-control">
                            <option value="">Pilih Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Invoice Number</label>
                        <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                            placeholder="INV-001">
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="text" name="amount" id="amount" class="form-control" >
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="belum bayar">Belum Bayar</option>
                            <option value="DP">DP</option>
                            <option value="lunas">Lunas</option>
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


