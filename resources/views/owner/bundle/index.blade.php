@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Analisis FP-Growth</h5>
                    <form method="POST" action="{{ route('owner.bundle.analyze') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Range Waktu</label>
                                <select class="form-select" name="range">
                                    <option value="all">Semua Waktu</option>
                                    <option value="this_month">Bulan ini</option>
                                    <option value="this_year">Tahun ini</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Brand (opsional)</label>
                                <select class="form-select" name="brand_id">
                                    <option value="">Semua Brand</option>
                                    @foreach ($data['brands'] as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Kirim untuk Analisis</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- LIST BUNDLE --}}
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Daftar Bundle</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="bundleTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5%">No</th>
                            <th class="text-center" style="width:20%">Nama Bundle</th>
                            <th class="text-center" style="width:25%">Periode</th>
                            <th class="text-center" style="width:15%">Harga Asli</th>
                            <th class="text-center" style="width:15%">Harga Spesial</th>
                            <th class="text-center" style="width:10%">Aktif</th>
                            <th class="text-center" style="width:10%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let bundleDT;
        $(document).ready(function() {
            bundleDT = $('#bundleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('owner.bundle.data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'bundle_name',
                        name: 'bundle_name',
                        className: 'text-center'
                    },
                    {
                        data: 'period',
                        name: 'period',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'original_price',
                        name: 'original_price',
                        className: 'text-center'
                    },
                    {
                        data: 'special_bundle_price',
                        name: 'special_bundle_price',
                        className: 'text-center'
                    },
                    {
                        data: 'active_switch',
                        name: 'active_switch',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'options',
                        name: 'options',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                order: [
                    [6, 'desc']
                ],
                layout: {
                    topStart: 'search',
                    topEnd: 'pageLength',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                pageLength: 5,
                lengthMenu: [
                    [5, 10, -1],
                    ['5', '10', 'Semua']
                ],
                language: {
                    info: 'Menampilkan halaman _PAGE_ dari _PAGES_ Halaman',
                    infoEmpty: 'Tidak ada data tersedia',
                    infoFiltered: '(disaring dari total _MAX_ data)',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Data tidak ditemukan',
                    search: 'Cari :'
                },
                search: {
                    return: true
                }
            });

            // Toggle aktif/nonaktif
            $(document).on('change', '.toggle-active', function() {
                const id = $(this).data('id');
                const is_active = $(this).is(':checked') ? 1 : 0;
                $.ajax({
                    url: "{{ url('owner/bundle') }}/" + id + "/toggle",
                    type: 'PATCH',
                    data: {
                        is_active,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        toastr.success('Status bundle diperbarui');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Gagal ubah status');
                    }
                });
            });

            // Hapus bundle
            $(document).on('click', '.delete-bundle', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (!confirm('Yakin hapus bundle ini?')) return;
                $.ajax({
                    url: "{{ url('owner/bundle') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(resp) {
                        if (resp.success) {
                            toastr.success(resp.success);
                            bundleDT.ajax.reload(null, false);
                        } else {
                            toastr.error('Gagal menghapus bundle');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Gagal menghapus bundle');
                    }
                });
            });

        });
    </script>
@endpush
