@extends('adminlte::page')

@section('title', 'Timeline')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Timeline</h3>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary mb-3" id="addTimelineBtn">Tambah Timeline</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="timelineTable">
                    <thead>
                        <tr>
                            <th>Nama Timeline</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    @include('timeline.modal')

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

            let table = $('#timelineTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('timeline.data') }}",
                columns: [{
                        data: 'judul',
                        name: 'judul'
                    },
                    { data:'deskripsi', name:'deskripsi' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#addTimelineBtn').click(function() {
                let id = $(this).data('id');
                console.log(id);
                
                $('#timelineForm')[0].reset();
                $('#timeline_id').val(id);
                $('#timelineModal').modal('show');
            });

            $('#timelineForm').submit(function(e) {
                e.preventDefault();
              

                let id = $('#timeline_id').val();
               if(id){
                    var url = '/timeline/' + id;
                    var method = 'PUT';
                } else {
                    var url = '/timeline';
                    var method = 'POST';
                }
                // console.log(id,url,method);
                
                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: $('#name').val(),
                        deskripsi: $('#deskripsi').val()

                    },
                    success: function() {
                        $('#timelineModal').modal('hide');
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

            
            $(document).on('click', '.editBtnTimeline', function() {
                let id = $(this).data('id');
                console.log(id);
                
                // $.get('/timeline/' + id + '/edit', function(data) {
                //     $('#name').val(data.name);
                //     $('#deskripsi').val(data.deskripsi);
                //     $('#timeline_id').val(data.id);
                //     $('#timelineModal').modal('show');
                //     // console.log(data);
                    
                // });
            });

            $(document).on('click', '.deleteBtnTimeline', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/timeline/' + id,
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
    </script>
@endsection

