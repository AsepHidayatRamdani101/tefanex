@extends('adminlte::page')

@section('title', 'Quality Control')

@section('content')

    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Quality Control</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="qualityTable">
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

    @include('quality.modal');


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

            let table = $('#qualityTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('quality.data') }}",
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

            //btn checklist
            $('#qualityTable').on('click', '.checklistBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                
                //get data file mockup dan file produksi berdasarkan id
                $.get('/quality-control/' + id, function(data) {
                 
                    //reset form
                    $('#qcForm')[0].reset();

                    $('#qcCetakModal').modal('show');
                    $('#qc_id').val(data.id);
                    $('#referensiFile').attr('href', data.mockup_file);
                    $('#produksiFile').attr('href', data.produksi_file);
                })
                
            });

            // save checklist
            $('#saveQcBtn').on('click', function() {
                let qc_id = $('#qc_id').val();
                let status = $('#status').val();
                let note = $('#note').val();
                let checklist_result = JSON.stringify({
                    warnaProof: $('#warnaProof').is(':checked'),
                    tidakAdaGaris: $('#tidakAdaGaris').is(':checked'),
                    tintaMerata: $('#tintaMerata').is(':checked'),
                    kertasSpesifikasi: $('#kertasSpesifikasi').is(':checked'),
                    
                })
                
                $.ajax({
                    url: '/quality-control/' + qc_id,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        note: note,
                        checklist_result: checklist_result
                    },
                    success: function(response) {
                        $('#qcCetakModal').modal('hide');
                        Swal.fire(
                            'Success!',
                            'Checklist berhasil disimpan.',
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON.message);
                        
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat menyimpan checklist.',
                            'error'
                        );
                    }
                });

                
            });

            //lihatbtn
            $(document).on('click', '.lihatBtn', function() {
                let id = $(this).data('id');
                $.get('/quality-control/' + id, function(data) {
                    $('#qcCetakModal').modal('show');
                    $('#qc_id').val(data.id);
                    $('#referensiFile').attr('href', data.mockup_file);
                    $('#produksiFile').attr('href', data.produksi_file);

                    let checklist_result = JSON.parse(data.checklist_result);
                    $('#warnaProof').prop('disabled', true);
                    $('#tidakAdaGaris').prop('disabled', true);
                    $('#tintaMerata').prop('disabled', true);
                    $('#kertasSpesifikasi').prop('disabled', true);
                    
                    $('#warnaProof').prop('checked', checklist_result.warnaProof);
                    $('#tidakAdaGaris').prop('checked', checklist_result.tidakAdaGaris);
                    $('#tintaMerata').prop('checked', checklist_result.tintaMerata);
                    $('#kertasSpesifikasi').prop('checked', checklist_result.kertasSpesifikasi);
                   

                    $('#note').prop('disabled', true);
                    $('#status').prop('disabled', true);
                    $('#note').val(data.note);
                    $('#status').val(data.status);

                    // sembunyikan tombol save
                    $('#saveQcBtn').hide();
                });
            });

            //editbtn
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                console.log(id);
                $.get('/quality-control/' + id , function(data) {
                    $('#qc_id').val(data.id);
                    $('#status').val(data.status);
                    let checklist_result = JSON.parse(data.checklist_result);  
                    $('#warnaProof').prop('checked', checklist_result.warnaProof);
                    $('#tidakAdaGaris').prop('checked', checklist_result.tidakAdaGaris);
                    $('#tintaMerata').prop('checked', checklist_result.tintaMerata);
                    $('#kertasSpesifikasi').prop('checked', checklist_result.kertasSpesifikasi);
                    $('#note').val(data.note);
                    $('#qcCetakModal').modal('show');
                });
            });




        });
    </script>
@endsection

