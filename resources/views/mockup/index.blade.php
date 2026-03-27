@extends('adminlte::page')

@section('title', 'Mockup Design')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Mockup Design</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="projectTable">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th width="10%">Deskripsi</th>
                            <th>File Referensi</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Hasil Design</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>


    </div>

    @include('mockup.modal');


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
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.js') }}"></script>
    <script>
        $(function() {

            let table = $('#projectTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('mockup.data') }}",
                columns: [{
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'deskripsi',
                        data: function(row) {
                            let deskripsiArray = row.deskripsi.split('\n');
                            return {
                                judul: deskripsiArray[0],
                                lama_pengerjaan: deskripsiArray[1],
                                dimensi: deskripsiArray[2],
                                warna: deskripsiArray[3],
                                font: deskripsiArray[4],
                                tagline: deskripsiArray[5],
                            };
                        },
                        render: function(data) {
                            return `
                                <p><b>Judul</b> : ${data.judul} </p>
                                <p><b>Lama Pengerjaan</b> : ${data.lama_pengerjaan}</p>
                                <p><b>Dimensi</b> : ${data.dimensi} </p>
                                <p><b>Font</b> : ${data.font} </p>
                                <p><b>Warna</b> : ${data.warna} </p>
                                <p><b>Tagline</b> : ${data.tagline} </p>
                            `;
                        }
                    },
                    {
                        data: 'file',
                        name: 'file',
                        render: function(data) {
                            return `
                                <a href="${data}" class="btn btn-sm btn-primary" target="_blank">Lihat File</a>
                            `;
                        }
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'waktu',
                        name: 'waktu'
                    },
                    {
                        data: 'hasil',
                        name: 'hasil',
                        render: function(data) {
                            return `
                                <a href="${data}" class="btn btn-sm btn-primary" target="_blank">Lihat Hasil</a>
                            `;
                        }
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            let projectId;


            $("#uploadForm").submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                let file = $('#file')[0].files[0];
                if (file) {
                    formData.append('file', file);
                }

                let id = $('#mockup_id').val();
                let url = '/mockup';
                let method = 'POST';

                if (id) {
                    url = '/mockup/' + id;
                    formData.append('_method', 'PUT');
                }


                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        
                        $('#uploadModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'File berhasil diupload', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirim request', 'error');
                    }
                });
            });


            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/projects/' + id + '/edit', function(data) {
                    $('#judul').val(data.judul);
                    $('#deskripsi').val(data.deskripsi);
                    $('#client').val(data.client);
                    $('#status').val(data.status);
                    $('#guru_id').val(data.guru_id);
                    $('#project_id').val(data.id);
                    $('#projectModal').modal('show');
                });
            });

            $(document).on('click', '.addBtn', function() {
                let id = $(this).data('id');
                $("#mockup_id").val(id);
                $("#uploadForm")[0].reset();
                $("#uploadModal").modal("show");

            });




            

        });
    </script>
@endsection
