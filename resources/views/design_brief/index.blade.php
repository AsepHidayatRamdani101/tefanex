@extends('adminlte::page')

@section('title', 'Design Brief')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Design Brief</h3>
                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="designBriefTable">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th>Deskripsi</th>
                            <th>Klien</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        
    </div>

@include('design_brief.modal')
@include('design_brief.rejectmodal')

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

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {

            let table = $('#designBriefTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('design-brief.data') }}",
                columns: [{
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                    },
                    {
                        data: 'klien',
                        name: 'klien'
                    },
                      {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            $('#designBriefForm').submit(function(e) {
                e.preventDefault();
                console.log($('#project_id').val());
                

                let formData = new FormData();

                let output_file = [];
                $('input[name="output_file[]"]:checked').each(function() {
                    output_file.push($(this).val());
                });

                let desskripsi_array = [
                    $('#dikerjakan').val() === "" ? "-" : $('#dikerjakan').val(),
                    $('#lama_pengerjaan').val() === "" ? "-" : $('#lama_pengerjaan').val(),
                    $('#dimensi').val() === "" ? "-" : $('#dimensi').val(),
                    $('#warna').val() === "" ? "-" : $('#warna').val(),
                    $('#tagline').val() === "" ? "-" : $('#tagline').val(),
                    $('#font').val() === "" ? "-" : $('#font').val(),
                    ...output_file
                ];

                let desskripsi = desskripsi_array.join('\n');

                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('project_id', $('#project_id').val());
                formData.append('description', desskripsi);
                formData.append('target_market', $('#target_market').val());
                formData.append('budget', $('#budget').val());

                let file = $('#gambar')[0].files[0];
                if (file) {
                    formData.append('reference_file', file);
                }

               let id = $('#designBrief_id').val();
                
                let url = '/design-brief';
                let method = 'POST';

                if (id) {
                    url = '/design-brief/' + id;
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#designBriefModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirim request');
                    }
                });
            });


            $(document).on('click', '.editBtnDesignBrief', function() {
                let id = $(this).data('id');
                console.log(id);

                // $.get('/design-brief/' + id + '/edit', function(data) {
                //     $('#name').val(data.name);
                //     $('#description').val(data.description);
                //     $('#designBrief_id').val(data.id);
                //     $('#designBriefModal').modal('show');
                //     // console.log(data);

                // });

                // $("#designBriefForm")[0].reset();
                // $("#designBrief_id").val(id);
                $("#rejectModal").modal("show");
            });

            $(document).on('click', '.tambahBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                $.get('/projects/' + id, function(data) {
                    console.log(data);
                    
                    $('#designBriefForm')[0].reset();
                    $('#name').val(data.judul);
                    $('#client').val(data.client);
                    $('#gambardetail').attr('src', '');
                    $('#designBrief_id').val(data.design_brief_id);
                    $('#project_id').val(data.id);

                    $('#designBriefModal').modal('show');
                });
            });

            $(document).on('click', '.lihatBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                $.get('/design-brief/' + id, function(data) {
                    console.log(data);
                    if (!data.project) {
                        Swal.fire('Error', 'Design Brief belum di isi', 'error');
                        return;
                    }
                    let description = data.description.split('\n');

                    $('#name').val(data.project.judul);
                    $('#client').val(data.project.client);
                    $('#designBrief_id').val(data.id);
                    $('#project_id').val(data.project_id);
                    $('#dikerjakan').val(description[0].trim());
                    $('#lama_pengerjaan').val(description[1]);
                    $('#dimensi').val(description[2]);
                    $('#font').val(description[3]);
                    $('#warna').val(description[4]);
                    $('#tagline').val(description[5]);
                    let checkboxValues = description.slice(6).map(item => item.trim());

                    $('input[name="output_file[]"]').each(function() {
                        if (checkboxValues.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                    $('#target_market').val(data.target_market);
                    $('#budget').val(data.budget);

                    if (data.reference_file) {
                        $('#gambardetail').attr('src', data.reference_file);
                        $('#gambardetail-link').attr('href', data.reference_file);
                    } else {
                        $('#gambardetail').attr('src', '');
                    }
                    
                    $('#designBriefModalLabel').text('Lihat Design Brief');
                    $('#designBriefModal').modal('show');
                });
            });

            $(document).on('click', '.approveBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                $.ajax({
                    url: '/design-brief/' + id + '/status',
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        status: 'approved',
                        keterangan: 'sesuai'
                    },
                    success: function() {
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Design Brief disetujui', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirim request');
                    }
                })
            })

            $(document).on('click', '.rejectBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                $("#designBrief_id").val($(this).data('id'));
                $("#rejectModal").modal("show");
                
            })

            $("#rejectForm").submit(function(e) {
                e.preventDefault();
                let id = $('#designBrief_id').val();
                $.ajax({
                    url: '/design-brief/' + id + '/status',
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        status: 'rejected',
                        keterangan: $('#keterangan').val()
                    },
                    success: function() {
                        table.ajax.reload();
                        $("#rejectModal").modal("hide");
                        Swal.fire('Berhasil!', 'Design Brief disetujui', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirim request');
                    }
                })
            })

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                $.get('/design-brief/' + id + '/edit', function(data) {
                    let description = data.description.split('\n');
                    $('#name').val(data.project.judul);
                    $('#client').val(data.project.client);
                    $('#designBrief_id').val(data.id);
                    $('#project_id').val(data.project_id);
                    $('#dikerjakan').val(description[0].trim());
                    $('#lama_pengerjaan').val(description[1]);
                    $('#dimensi').val(description[2]);
                    $('#font').val(description[3]);
                    $('#warna').val(description[4]);
                    $('#tagline').val(description[5]);
                    let checkboxValues = description.slice(6).map(item => item.trim());

                    $('input[name="output_file[]"]').each(function() {
                        if (checkboxValues.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                    $('#target_market').val(data.target_market);
                    $('#budget').val(data.budget);

                    if (data.reference_file) {
                        $('#gambardetail').attr('src', data.reference_file);
                        $('#gambardetail-link').attr('href', data.reference_file);
                    } else {
                        $('#gambardetail').attr('src', '');
                    }
                    
                    $('#designBriefModalLabel').text('Edit Design Brief');
                    $('#sub').text('Edit');
                    $('#designBriefModal').modal('show');

                });
            });

            let urlParams = new URLSearchParams(window.location.search);
            let designProjectId = urlParams.get('project_id');
            if (designProjectId) {
                $.get('/projects/' + designProjectId, function(data) {
                    $('#designBriefForm')[0].reset();
                    $('#name').val(data.judul);
                    $('#client').val(data.client);
                    $('#gambardetail').attr('src', '');
                    $('#designBrief_id').val(data.design_brief_id || '');
                    $('#project_id').val(data.id);
                    $('#designBriefModal').modal('show');
                });
            }

        });
    </script>
@endsection
