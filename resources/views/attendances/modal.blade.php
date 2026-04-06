<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="attendanceForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Tambah Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="attendance_id">
                    <div class="form-group">
                        <label>User (Pilih satu atau lebih)</label>
                        <select name="user_id[]" id="user_id" class="form-control select2" style="width: 100%;" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" id="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="alpha">Alpha</option>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Enhanced Select2 Styling for AdminLTE */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d2d6de;
        border-radius: 4px;
        min-height: 38px;
        padding: 4px 8px;
        outline: none;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: block;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border: 1px solid #0056b3;
        border-radius: 3px;
        color: white;
        display: inline-block;
        float: left;
        margin-right: 5px;
        margin-bottom: 5px;
        padding: 4px 8px;
        font-size: 13px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
        cursor: pointer;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffeb3b;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__input {
        background: transparent;
        border: none;
        margin-top: 5px;
        font-size: 14px;
        color: #333;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: #999;
        margin-top: 5px;
    }

    /* Dropdown menu styling */
    .select2-container--default.select2-container--open .select2-dropdown {
        border: 1px solid #d2d6de;
        border-top: none;
        border-radius: 0 0 4px 4px;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Search field in dropdown */
    .select2-dropdown .select2-search__field {
        border: none;
        border-bottom: 1px solid #e9ecef;
        padding: 8px 12px;
        font-size: 13px;
        outline: none;
    }

    .select2-dropdown .select2-search__field:focus {
        box-shadow: none;
        border-bottom: 1px solid #007bff;
    }

    /* Results styling */
    .select2-results__options {
        max-height: 300px;
        overflow-y: auto;
    }

    .select2-results__option {
        padding: 10px 12px;
        font-size: 14px;
        color: #333;
        line-height: 1.4;
    }

    .select2-results__option:hover,
    .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff !important;
        color: white !important;
    }

    .select2-results__option[aria-selected="true"] {
        background-color: #e3f2fd;
        color: #0056b3;
    }

    /* Remove default styling */
    .select2-container--default .select2-results__option--highlighted {
        background-color: transparent;
    }
</style>

