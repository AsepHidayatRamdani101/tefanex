
@extends('adminlte::page')

@push('css')
    <style>
        .file-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .file-list a {
            display: inline-block;
            padding: 5px 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 240px;
        }
    </style>
@endpush

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

    @include('produksi.modal')
    @include('produksi.revisi')


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
                columns: [
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                        render: function(data) {
                            return data ? data.replace(/\n/g, '<br>') : '-';
                        }
                    },
                    {
                        data: 'file',
                        name: 'file',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '<span class="badge badge-secondary">Tidak ada file</span>';
                        }
                    },
                    {
                        data: 'waktu',
                        name: 'waktu',
                        render: function(data) {
                            if (!data) {
                                return '-';
                            }

                            let date = new Date(data);
                            if (isNaN(date.getTime())) {
                                return data;
                            }

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

            let params = new URLSearchParams(window.location.search);
            let produksiProjectId = params.get('project_id');
            if (produksiProjectId) {
                projectId = produksiProjectId;
                $('#id').val(produksiProjectId);
                $('#uploadForm')[0].reset();
                $('#uploadModal').modal('show');
            }

            $(document).on('submit', '#uploadForm', function(e) {
                e.preventDefault();
                let id = $('#id').val();
                let formData = new FormData(this);
                formData.append('id', id);

                let file = $('#file')[0].files[0];
                if (file) {
                    formData.append('file', file);
                }

                let url = '/produksi';
                let method = 'POST';

                if (id) {
                    url = '/produksi/' + id;
                    formData.append('_method', 'PUT');
                }

                Swal.fire({
                    title: 'Mengupload file...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.close();
                        $('#uploadModal').modal('hide');
                        Swal.fire(
                            'Success',
                            response.message,
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.close();
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
