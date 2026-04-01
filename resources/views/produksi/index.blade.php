@extends('adminlte::page')

@section('title', 'Produksi')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Produksi</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="projectTable">
                    <thead>
                        <tr>
                            <th>Nama Produksi</th>
                            <th width="10%">Deskripsi</th>
                            <th>File Referensi</th>
                            <th>Status</th>
                            <th>Revisi</th>
                            <th>Hasil Produksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    @include('produksi.modal');
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
    <script src="{{ asset('vendor/adminlte/dist/js/jquery.js') }}"></script>
    <script>
        $(function() {

            let table = $('#projectTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('produksi.data') }}",
                columns: [{
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'deskripsi',
                        data: function(row) {
                            let deskripsiArray = row.deskripsi.split('\n');
                            return {
                                nama: deskripsiArray[0],
                                lama_pengerjaan: deskripsiArray[1],
                                dimensi: deskripsiArray[2],
                                warna: deskripsiArray[3],
                                font: deskripsiArray[4],
                                tagline: deskripsiArray[5],
                            };
                        },
                        render: function(data) {
                            return `
                                        <p><b>Nama</b> : ${data.nama} </p>
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
                        data: 'revisi',
                        name: 'revisi'
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

                let id = $('#produksi_id').val();
                let url = '/produksi';
                let method = 'POST';

                if (id) {
                    url = '/produksi/' + id;
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
                $.get('/produksi/' + id + '/edit', function(data) {
                    $('#nama').val(data.nama);
                    $('#deskripsi').val(data.deskripsi);
                    $('#client').val(data.client);
                    $('#status').val(data.status);
                    $('#guru_id').val(data.guru_id);
                    $('#produksi_id').val(data.id);
                    $('#produksiModal').modal('show');
                });
            });

            $(document).on('click', '.addBtn', function() {
                let id = $(this).data('id');
                $("#produksi_id").val(id);
                $("#uploadForm")[0].reset();
                $("#uploadModal").modal("show");

            });

            $(document).on('click', '.approveBtn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: '/produksi/' + id + '/status',
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: 'approved',
                        id: id,
                        revisi: "-",
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Produksi berhasil disetujui', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirim request', 'error');
                    }
                })

            });

            $(document).on('click', '.rejectBtn', function() {
                let id = $(this).data('id');
                $("#produksi_id").val(id);
                $("#rejectForm")[0].reset();
                $("#rejectModal").modal("show");
            });

            $("#rejectForm").submit(function(e) {
                e.preventDefault();
                let id = $('#produksi_id').val();
                $.ajax({
                    url: '/produksi/' + id + '/status',
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        status: 'revisi',
                        revisi: $('#alasan').val()
                    },
                    success: function() {
                        table.ajax.reload();
                        $("#rejectModal").modal("hide");
                        Swal.fire('Berhasil!', 'Produksi Design disetujui', 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengirim request');
                    }
                })
            })
        })
    </script>
@endsection
