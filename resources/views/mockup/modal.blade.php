<div class="modal fade" id="uploadModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
               
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     <input type="hidden" id="mockup_id" name="mockup_id">
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" name="file" id="file" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

