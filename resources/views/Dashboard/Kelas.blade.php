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
                        <x-fragments.select-field label="Type Kelas" name="type" :options="[
                            'intensif' => 'Intensif',
                            'partime' => 'Partime',
                        ]" />
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
                fn($row) => $row->normal_nama,
            
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
            const priceDisplayInput = document.querySelector('input[name="price_display"]');
            const priceHiddenInput = document.querySelector('input[name="price"]');
            const dpPersenInput = document.querySelector('input[name="dp_persen"]');
            const typeSelect = document.querySelector('select[name="type"]');
            const durasiBelajarInput = document.querySelector('input[name="durasi_belajar"]');
            const waktuMagangInput = document.querySelector('input[name="waktu_magang"]');

            function setCurrencyValue(value) {
                if (!priceDisplayInput || !priceHiddenInput) return;

                const numericValue = value.toString().replace(/[^\d]/g, '');

                const formattedValue = numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');


                priceDisplayInput.value = formattedValue;
                priceHiddenInput.value = numericValue;

                console.log('Currency set - Display:', formattedValue, 'Hidden:', numericValue);
            }

            document.addEventListener('modalCreate', function(e) {
                if (e.detail.modalId === 'modal-control-kelas') {
                    console.log('Modal Create - Resetting form');
                    resetForm();
                }
            });

            document.addEventListener('modalUpdate', function(e) {
                if (e.detail.modalId === 'modal-control-kelas') {
                    const kelasData = e.detail.data;
                    console.log('Modal Update - Kelas Data:', kelasData);

                    nameInput.value = kelasData.name || '';
                    dpPersenInput.value = kelasData.dp_persen || '';
                    typeSelect.value = kelasData.type || '';
                    durasiBelajarInput.value = kelasData.durasi_belajar || '';
                    waktuMagangInput.value = kelasData.waktu_magang || '';

                    if (kelasData.price) {
                        const cleanPrice = kelasData.price.toString().replace(/[^\d]/g, '');
                        console.log('Setting price:', cleanPrice);
                        setCurrencyValue(cleanPrice);

                        if (typeof window.formatCurrency === 'function' && priceDisplayInput) {
                            setTimeout(() => {
                                window.formatCurrency(priceDisplayInput);
                            }, 100);
                        }
                    }
                }
            });

            document.addEventListener('modalReset', function(e) {
                if (e.detail.modalId === 'modal-control-kelas') {
                    console.log('Modal Reset');
                    resetForm();
                }
            });

            function resetForm() {
                nameInput.value = '';
                dpPersenInput.value = '';
                typeSelect.value = '';
                durasiBelajarInput.value = '';
                waktuMagangInput.value = '';
                if (priceDisplayInput) priceDisplayInput.value = '';
                if (priceHiddenInput) priceHiddenInput.value = '';
            }
        });
    </script>
@endsection
