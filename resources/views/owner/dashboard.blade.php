@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
    {{-- Transaction Carousel --}}
    <div class="row justify-content-md-between justify-content-center w-full">
        <div class="card border-0 zoom-in bg-light-primary shadow-none col-auto" style="width: 300px">
            <div class="card-body d-flex flex-column p-0">
                <select class="form-select ms-auto border-0 text-primary fw-bold" data-type="omset" id="omsetFilter"
                    style="width:auto" aria-label="Default select example">
                    <option value="all" selected>Semua</option>
                    <option value="day">Hari ini</option>
                    <option value="month">Bulan ini</option>
                    <option value="year">Tahun ini</option>
                </select>
                <div class="text-center mx-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-cash mb-3 text-primary">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                        <path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    </svg>
                    <p class="fw-semibold fs-3 text-primary mb-1"> Total Omset </p>
                    <h5 class="fw-semibold text-primary mb-0" id="omsetDisplay">
                        {{ 'Rp. ' . number_format($data['omset'], 2, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="card border-0 zoom-in bg-light-danger shadow-none col-auto" style="width: 300px">
            <div class="card-body d-flex flex-column p-0">
                <select class="form-select ms-auto border-0 text-danger fw-bold" data-type="expense" id="expenseFilter"
                    style="width:auto" aria-label="Default select example">
                    <option value="all" selected>Semua</option>
                    <option value="day">Hari ini</option>
                    <option value="month">Bulan ini</option>
                    <option value="year">Tahun ini</option>
                </select>
                <div class="text-center mx-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-cash-minus mb-3 text-danger">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                        <path d="M12 19h-4a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v5" />
                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M16 19h6" />
                    </svg>
                    <p class="fw-semibold fs-3 text-danger mb-1"> Pembelanjaan </p>
                    <h5 class="fw-semibold text-danger mb-0" id="expenseDisplay">
                        {{ 'Rp. ' . number_format($data['expense'], 2, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="card border-0 zoom-in bg-light-success shadow-none col-auto" style="width: 300px">
            <div class="card-body d-flex flex-column p-0">
                <select class="form-select ms-auto border-0 text-success fw-bold" data-type="income" id="incomeFilter"
                    style="width:auto" aria-label="Default select example">
                    <option value="all" selected>Semua</option>
                    <option value="day">Hari ini</option>
                    <option value="month">Bulan ini</option>
                    <option value="year">Tahun ini</option>
                </select>
                <div class="text-center mx-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icon-tabler-cash-plus mb-3 text-success">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                        <path d="M12 19h-4a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v2.5" />
                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M16 19h6" />
                        <path d="M19 16v6" />
                    </svg>
                    <p class="fw-semibold fs-3 text-success mb-1"> Pendapatan </p>
                    <h5 class="fw-semibold text-success mb-0" id="incomeDisplay">
                        {{ 'Rp. ' . number_format($data['income'], 2, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            function fetchFilteredData(type, filter) {
                $.ajax({
                    url: "{{ route('owner.dashboard.data') }}",
                    method: "GET",
                    data: {
                        type: type,
                        filter: filter
                    },
                    success: function(response) {
                        console.log(response);
                        if (type === 'omset') {
                            $('#omsetDisplay').html('Rp. ' + response.omset);
                        } else if (type === 'expense') {
                            $('#expenseDisplay').html('Rp. ' + response.expense);
                        } else if (type === 'income') {
                            $('#incomeDisplay').html('Rp. ' + response.income);
                        }
                    },
                    error: function(xhr) {
                        console.log('Error fetching omset:', xhr.responseText);
                        alert('Gagal memuat data omset. Silakan coba lagi.');
                    }
                });
            }

            // Event listener untuk perubahan pada selectbox
            $('#omsetFilter').on('change', function() {
                var type = $(this).data('type');
                var filter = $(this).val(); // Dapatkan nilai filter yang dipilih
                fetchFilteredData(type, filter); // Panggil fungsi AJAX
            });
            $('#expenseFilter').on('change', function() {
                var type = $(this).data('type');
                var filter = $(this).val(); // Dapatkan nilai filter yang dipilih
                fetchFilteredData(type, filter); // Panggil fungsi AJAX
            });
            $('#incomeFilter').on('change', function() {
                var type = $(this).data('type');
                var filter = $(this).val(); // Dapatkan nilai filter yang dipilih
                fetchFilteredData(type, filter); // Panggil fungsi AJAX
            });

        });
    </script>
@endpush
