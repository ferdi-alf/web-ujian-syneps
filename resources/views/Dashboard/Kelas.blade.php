@extends('layouts.dashboard-layouts')

@section('content')
    <div>
        <div class="flex justify-end">
            <x-fragments.modal-button target="modal-control-kelas" variant="emerald" act="create">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kelas
            </x-fragments.modal-button>
        </div>

        <x-fragments.form-modal id="modal-control-kelas" size="xl" title="Form Kelas" createTitle="Tambah Kelas"
            editTitle="Edit Kelas" action="{{ route('kelas.store') }}">
            <div class="overflow-auto p-2">
                <x-fragments.text-field label="Nama Kelas" name="name" placeholder="Masukan Nama Kelas..." required />

                <x-fragments.currency-field label="Harga Kelas" name="price" placeholder="Masukkan harga kelas..."
                    required />

                <div>
                    <x-fragments.text-field label="DP (%)" name="dp_persen" type="number" placeholder="Contoh: 30"
                        required />
                    <small class="text-gray-500">Masukkan angka dalam persen. Contoh: 30 = 30%.</small>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-fragments.select-field label="Type Kelas" name="type" :options="['' => 'Pilih Type', 'intensif' => 'Intensif', 'partime' => 'Partime']" />
                        <small class="text-gray-500">Kosongkan jika kelas tidak ada type spesifik</small>
                    </div>
                    <div>
                        <x-fragments.text-field label="Lama Durasi Belajar (bulan)" name="durasi_belajar" type="number"
                            placeholder="Contoh: 1, 2, 4..." required />
                        <small class="text-gray-500">Masukkan angka dalam satuan bulan. Contoh: 4 = 4 bulan.</small>
                    </div>
                </div>

                <div>
                    <x-fragments.text-field label="Lama Durasi Magang (bulan)" name="waktu_magang" type="number"
                        placeholder="Contoh: 1, 2, 4..." />
                    <small class="text-gray-500">Masukkan angka dalam satuan bulan. Contoh: 2 = 2 bulan. (Kosongkan jika
                        kelas tidak memiliki magang)</small>
                </div>
            </div>
        </x-fragments.form-modal>

        <div class="mt-6">
            <x-reusable-table :headers="['No', 'Nama Kelas', 'Harga', 'DP (%)', 'Total DP', 'Type', 'Durasi']" position="center" :data="$kelas" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->nama,
                fn($row) => 'Rp ' . number_format($row->harga, 0, ',', '.'),
                fn($row) => $row->dp_persen . '%',
                fn($row) => 'Rp ' . number_format(($row->harga * $row->dp_persen) / 100, 0, ',', '.'),
                fn($row) => $row->getFormattedTypeAttribute(),
                fn($row) => $row->getFormattedDurationAttribute(),
            ]" :showActions="true"
                :actionButtons="fn($row) => view('components.action-buttons', [
                    'modalTarget' => 'modal-control-kelas',
                    'editData' => [
                        'id' => $row->id,
                        'fetchEndpoint' => '/kelas/show/' . $row->id,
                        'updateEndpoint' => '/kelas/' . $row->id,
                        'act' => 'update',
                    ],
                
                    'deleteRoute' => route('kelas.destroy', $row->id),
                ])" :searchBar="true" :truncate="true" :rowPerPage="10" position="left" />
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nameInput = document.querySelector('input[name="name"]');
            const priceInput = document.querySelector('input[name="price"]');
            const dpPersenInput = document.querySelector('input[name="dp_persen"]');
            const typeSelect = document.querySelector('select[name="type"]');
            const durasiBelajarInput = document.querySelector('input[name="durasi_belajar"]');
            const waktuMagangInput = document.querySelector('input[name="waktu_magang"]');

            document.addEventListener('modalCreate', function(e) {
                if (e.detail.modalId === 'modal-control-kelas') {
                    resetForm();
                }
            });

            document.addEventListener('modalUpdate', function(e) {
                if (e.detail.modalId === 'modal-control-kelas') {
                    const kelasData = e.detail.data;
                    console.log('Kelas Data:', kelasData);

                    nameInput.value = kelasData.name || '';
                    priceInput.value = kelasData.price ?
                        parseInt(kelasData.price).toString() :
                        '';
                    if (typeof formatCurrency === 'function') {
                        formatCurrency(priceInput);
                    }

                    dpPersenInput.value = kelasData.dp_persen || '';
                    typeSelect.value = kelasData.type || '';
                    durasiBelajarInput.value = kelasData.durasi_belajar || '';
                    waktuMagangInput.value = kelasData.waktu_magang || '';

                    if (priceInput && typeof formatCurrency === 'function') {
                        formatCurrency(priceInput);
                    }
                }
            });

            document.addEventListener('modalReset', function(e) {
                if (e.detail.modalId === 'modal-control-kelas') {
                    resetForm();
                }
            });

            function resetForm() {
                nameInput.value = '';
                priceInput.value = '';
                dpPersenInput.value = '';
                typeSelect.value = '';
                durasiBelajarInput.value = '';
                waktuMagangInput.value = '';
            }
        });
    </script>
@endsection
