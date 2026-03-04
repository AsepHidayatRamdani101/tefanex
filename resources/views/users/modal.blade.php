<div class="modal fade" id="userModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id">

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" id="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Password (kosongkan jika tidak diubah)</label>
                        <input type="text" id="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select id="role" class="form-control">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
