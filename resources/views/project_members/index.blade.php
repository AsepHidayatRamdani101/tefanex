@extends('adminlte::page')

@section('title', 'Project Member Management')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Project Member Management</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm" id="addProjectMemberBtn">
                            <i class="fas fa-plus"></i> Tambah Anggota Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="projectFilter">Pilih Project:</label>
                    <select id="projectFilter" class="form-control">
                        <option value="">-- Pilih Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->judul }} ({{ $project->client ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="projectMemberTable">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th>Deskripsi</th>
                            <th>Anggota</th>
                            <th>Tugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>

    @include('project_members.modal')

@stop
@section('footer')
    <div class="float-right d-none d-sm-inline">
        Versi 1.0
    </div>
    <strong>
        Copyright &copy; {{ date('Y') }}
        <a href="#">TEFANEX</a>.
    </strong> All rights reserved.
@endsection

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let table;
        let originalUsers = {!! json_encode($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toArray()) !!};
        
        $(function() {
            // Initialize DataTable
            table = $('#projectMemberTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('project-members.data') }}",
                    data: function(d) {
                        d.project = $('#projectFilter').val();
                    }
                },
                columns: [
                    {
                        data: 'project',
                        name: 'project'
                    },
                    { 
                        data:'deskripsi', 
                        name:'deskripsi' 
                    },
                    {
                        data: 'anggota',
                        name: 'anggota'
                    },
                    {
                        data: 'tugas',
                        name: 'tugas'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Reload table when project changes
            $('#projectFilter').change(function() {
                if($(this).val()) {
                    table.ajax.reload();
                } else {
                    table.clear().draw();
                }
            });

            // Handle modal show event to initialize Select2
            $('#projectMemberModal').on('shown.bs.modal', function() {
                if (!$('#anggota_id').hasClass('select2-hidden-accessible')) {
                    initializeSelect2();
                }
            });

            // Handle Kelas filter change
            $('#kelas_filter').change(function() {
                let kelasId = $(this).val();
                console.log('Kelas selected:', kelasId);

                if (!kelasId) {
                    // Reset to original users
                    populateAnggotaDropdown(originalUsers);
                    return;
                }

                // Fetch students by class
                $.ajax({
                    url: "{{ route('project-members.get-students') }}",
                    type: 'GET',
                    data: { kelas_id: kelasId },
                    dataType: 'json',
                    success: function(students) {
                        console.log('Students loaded:', students);
                        if (Array.isArray(students)) {
                            populateAnggotaDropdown(students);
                        } else {
                            console.error('Invalid response format:', students);
                            Swal.fire('Error!', 'Format data tidak valid', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        let errorMsg = 'Gagal memuat data siswa';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            });

            $('#addProjectMemberBtn').click(function() {
                let projectId = $('#projectFilter').val();
                
                if(!projectId) {
                    Swal.fire('Perhatian!', 'Silahkan pilih project terlebih dahulu', 'warning');
                    return;
                }
                
                $('#projectMemberForm')[0].reset();
                $('#project_id').val(projectId);
                $('#anggota_id').val(null).trigger('change');
                $('#projectMemberModal').modal('show');
            });

            $('#projectMemberForm').submit(function(e) {
                e.preventDefault();
              
                let id = $('#projectMember_id').val();
               if(id){
                    var url = '/project-members/' + id;
                    var method = 'PUT';
                } else {
                    var url = '/project-members';
                    var method = 'POST';
                }
                
                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        anggota_id: $('#anggota_id').val(),
                        project_id: $('#project_id').val(),
                        tugas: $('#tugas').val()

                    },
                    success: function() {
                        $('#projectMemberModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Gagal!',
                            'Terjadi kesalahan saat mengirimkan request ke server');
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.editBtnMember', function() {
                let id = $(this).data('id');
                
                $.get('/project-members/' + id + '/edit', function(data) {
                    $('#anggota_id').val(data.user_id).trigger('change');
                    $('#project_id').val(data.project_id);
                    $('#tugas').val(data.role_in_project);
                    $('#projectMember_id').val(data.id);
                    $('#kelas_filter').val('').trigger('change');
                    $('#projectMemberModal').modal('show');
                });
            });

            $(document).on('click', '.deleteBtnMember', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/project-members/' + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', '', 'success');
                            }
                        });
                    }
                });
            });

        });

        function populateAnggotaDropdown(users) {
            let anggotaSelect = $('#anggota_id');
            let currentValue = anggotaSelect.val();
            
            // Destroy existing Select2
            if (anggotaSelect.hasClass('select2-hidden-accessible')) {
                anggotaSelect.select2('destroy');
            }
            
            anggotaSelect.empty();
            anggotaSelect.append('<option value="">-- Pilih Anggota --</option>');
            
            if (users.length > 0) {
                users.forEach(user => {
                    let option = $('<option>')
                        .val(user.id)
                        .text(user.name);
                    anggotaSelect.append(option);
                });
            } else {
                anggotaSelect.append('<option value="" disabled>Tidak ada data</option>');
            }
            
            // Reinitialize Select2
            initializeSelect2();
        }

        function initializeSelect2() {
            $('#anggota_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Cari anggota...',
                dropdownParent: $('#projectMemberModal'),
                language: {
                    noResults: function() {
                        return 'Anggota tidak ditemukan';
                    }
                }
            });
        }
    </script>
@endsection
