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
                            <th>Budget</th>
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

            // Define formatRupiah and parseNumber functions early, before DataTable init
            function parseNumber(value) {
                if (value === null || value === undefined) return 0;
                if (typeof value === 'number') return value;
                let s = String(value).trim();
                // Remove currency label and spaces
                s = s.replace(/Rp\s?/gi, '').replace(/\s+/g, '');

                // Keep only digits and separators
                s = s.replace(/[^0-9.,-]/g, '');
                if (s === '') return 0;

                const hasDot = s.indexOf('.') !== -1;
                const hasComma = s.indexOf(',') !== -1;

                if (hasDot && hasComma) {
                    // Both present -> decide by last occurrence
                    const lastDot = s.lastIndexOf('.');
                    const lastComma = s.lastIndexOf(',');
                    if (lastDot > lastComma) {
                        // dot is decimal separator
                        s = s.replace(/,/g, '');
                    } else {
                        // comma is decimal separator
                        s = s.replace(/\./g, '').replace(/,/g, '.');
                    }
                } else if (hasDot) {
                    // Only dot present: decide if dot is thousand separator (e.g., 25.000)
                    const parts = s.split('.');
                    const lastPart = parts[parts.length - 1];
                    if (lastPart.length === 3) {
                        // likely thousand separators: remove all dots
                        s = s.replace(/\./g, '');
                    } else {
                        // likely decimal separator: remove commas (none) and keep dot
                        s = s.replace(/,/g, '');
                    }
                } else if (hasComma) {
                    // Only comma present: decide if comma is thousand separator (e.g., 25,000)
                    const parts = s.split(',');
                    const lastPart = parts[parts.length - 1];
                    if (lastPart.length === 3) {
                        // thousand separators: remove all commas
                        s = s.replace(/,/g, '');
                    } else {
                        // decimal separator: replace comma with dot
                        s = s.replace(/\./g, '').replace(/,/g, '.');
                    }
                }

                const f = parseFloat(s);
                return isNaN(f) ? 0 : f;
            }

            function formatRupiah(value) {
                const number = parseNumber(value);
                if (!number) return '';
                // Format without decimal places for display
                return 'Rp ' + Math.round(number).toLocaleString('id-ID');
            }

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
                        data: 'budget',
                        name: 'budget',
                        render: function(data) {
                            return formatRupiah(data);
                        }
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
                    $('#lama_pengerjaan').val() === "" ? "-" : ($('#lama_pengerjaan').val() + ' ' + $('#lama_pengerjaan_satuan').val()),
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
                formData.append('harga_satuan', parseNumber($('#harga_satuan').val()));
                formData.append('quantity', parseInt($('#quantity').val()) || 0);
                formData.append('budget', parseNumber($('#budget').val()));

                const files = $('#reference_files')[0].files;
                if (files.length > 3) {
                    Swal.fire('Gagal!', 'Maksimal 3 gambar referensi');
                    return;
                }

                Array.from(files).forEach(function(file) {
                    formData.append('reference_files[]', file);
                });

               let id = $('#designBrief_id').val();
                
                let url = '/design-brief';
                let method = 'POST';

                if (id) {
                    url = '/design-brief/' + id;
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
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        Swal.close();
                        $('#designBriefModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', 'Data tersimpan', 'success');
                    },
                    error: function(xhr) {
                        Swal.close();
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
                    $('#lama_pengerjaan').val('');
                    $('#lama_pengerjaan_satuan').val('hari');
                    $('#harga_satuan').val('');
                    $('#harga_satuan_display').val('');
                    $('#quantity').val('');
                    $('#budget').val('');
                    $('#budget_display').val('');
                    $('#referenceFilePreview').empty();
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
                    {
                        const lamaParts = (description[1] || '').trim().split(/\s+/);
                        $('#lama_pengerjaan').val(lamaParts[0] || '');
                        $('#lama_pengerjaan_satuan').val(lamaParts[1] || 'hari');
                    }
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
                    $('#harga_satuan').val(data.harga_satuan);
                    $('#harga_satuan_display').val(formatRupiah(data.harga_satuan));
                    $('#quantity').val(data.quantity);
                    $('#budget').val(data.budget);
                    $('#budget_display').val(formatRupiah(data.budget));
                    renderReferencePreviews(data.reference_files_list || (data.reference_file ? [data.reference_file] : []));
                    
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
                        keterangan: $('#alasan').val()
                    },
                    success: function() {
                        table.ajax.reload();
                        $("#rejectModal").modal("hide");
                        Swal.fire('Berhasil!', 'Design Brief ditolak', 'success');
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
                    {
                        const lamaParts = (description[1] || '').trim().split(/\s+/);
                        $('#lama_pengerjaan').val(lamaParts[0] || '');
                        $('#lama_pengerjaan_satuan').val(lamaParts[1] || 'hari');
                    }
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
                    $('#harga_satuan').val(data.harga_satuan);
                    $('#harga_satuan_display').val(formatRupiah(data.harga_satuan));
                    $('#quantity').val(data.quantity);
                    $('#budget').val(data.budget);
                    $('#budget_display').val(formatRupiah(data.budget));
                    renderReferencePreviews(data.reference_files_list || (data.reference_file ? [data.reference_file] : []));
                    
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
                    $('#referenceFilePreview').empty();
                    $('#designBrief_id').val(data.design_brief_id || '');
                    $('#project_id').val(data.id);
                    $('#designBriefModal').modal('show');
                });
            }

            function syncBudget() {
                let hargaSatuan = parseNumber($('#harga_satuan_display').val() || 0);
                let quantity = parseInt($('#quantity').val() || 0, 10);
                let budget = (hargaSatuan * quantity) || 0;
                $('#harga_satuan').val(hargaSatuan);
                $('#budget').val(budget);
                $('#budget_display').val(formatRupiah(budget));
            }

            $(document).on('input', '#harga_satuan_display, #quantity', syncBudget);

            $(document).on('blur', '#harga_satuan_display', function() {
                let raw = parseNumber($(this).val() || 0);
                $(this).val(formatRupiah(raw));
                $('#harga_satuan').val(raw);
                syncBudget();
            });

            function renderReferencePreviews(files) {
                const container = $('#referenceFilePreview');
                container.empty();

                if (!files || !files.length) {
                    return;
                }

                files.slice(0, 3).forEach(function(file) {
                    container.append(
                        '<div class="mr-2 mb-2 text-center">' +
                            '<a href="' + file + '" target="_blank">' +
                                '<img src="' + file + '" alt="Referensi" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">' +
                            '</a>' +
                        '</div>'
                    );
                });
            }

        });
    </script>
@endsection
