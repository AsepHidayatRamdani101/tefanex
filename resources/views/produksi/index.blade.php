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
                            <th>waktu</th>
                            <th>Status</th>
                            <th>Revisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    @include('produksi.modal');
    @include('produksi.revisi');


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
                        data: 'project',
                        name: 'project'
                    },
                    {

                        data: 'deskripsi',
                        name: 'deskripsi',
                        data: 'deskripsi',
                        data: function(row) {
                            if (row.deskripsi === '-') {
                                return {
                                    judul: '-',
                                    lama_pengerjaan: '-',
                                    dimensi: '-',
                                    warna: '-',
                                    font: '-',
                                    tagline: '-',
                                };
                            }

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
                        data: 'waktu',
                        name: 'waktu',
                        render: function(data) {
                            let date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            let projectId;


            $(document).on('click', '.tambahBtn', function() {
                projectId = $(this).data('id');
                $('#id').val(projectId);
                $('#uploadModal').modal('show');
            });

            $(document).on('submit', '#uploadForm', function(e) {
                e.preventDefault();
                let id = $('#id').val();
                let formData = new FormData(this);
                formData.append('id', projectId);

                let file = $('#file')[0].files[0];
                if (file) {
                    formData.append('file', file);
                }

                if (id) {
                    url = '/produksi/' + id;
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#uploadModal').modal('hide');
                        Swal.fire(
                            'Success',
                            response.message,
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON.message,
                            'error'
                        );
                    }
                });
            })

            //btn approve
            $(document).on('click', '.approveBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/produksi/' + id + '/status',
                            method: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: 'selesai'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Approved!',
                                    response.message,
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error',
                                    xhr.responseJSON.message,
                                    'error'
                                );
                                console.log(xhr);

                            }
                        });
                    }
                })
            });

            //btn revisi
            $(document).on('click', '.revisiBtn', function() {
                projectId = $(this).data('id');
                $('#revisi_produksi_id').val(projectId);
                $('#revisiModal').modal('show');
            });

            $(document).on('submit', '#revisiForm', function(e) {
                e.preventDefault();
                let id = $('#revisi_produksi_id').val();
                let revisi_note = $('#revisi_note').val();

                $.ajax({
                    url: '/produksi/' + id + '/revisi',
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        revisi_note: revisi_note
                    },
                    success: function(response) {
                        $('#revisiModal').modal('hide');
                        Swal.fire(
                            'Revised!',
                            response.message,
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON.message,
                            'error'
                        );
                        console.log(xhr);
                    }
                });
            });

            //btn lihat detail produksi
            $(document).on('click', '.lihatBtn', function() {
                let id = $(this).data('id');
                console.log(id);

                $.ajax({
                    url: '/produksi/' + id,
                    method: 'GET',
                    success: function(response) {
                        //buka langsung tanpa modal path file produksi
                        window.open(response.file_path, '_blank');

                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON.message,
                            'error'
                        );
                        console.log(xhr);
                    }
                });
            });


        });
    </script>
@endsection
