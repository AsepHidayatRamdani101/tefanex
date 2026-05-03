<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="payment_id">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengeluaran</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" id="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Referensi</label>
                        <input type="text" id="reference" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea id="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" id="category" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="text" id="amount" class="form-control" required>
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
