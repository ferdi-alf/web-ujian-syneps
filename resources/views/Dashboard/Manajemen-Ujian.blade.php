@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mt-10">
        <h2 class="text-lg font-semibold mb-2">Data Ujian</h2>
        <x-reusable-table :searchBar="true" :truncate="true" :headers="['No', 'Judul', 'Kelas', 'Batch', 'Waktu', 'Status', 'Total Soal']" :data="$dataUjian" :columns="[
            fn($row, $i) => $i + 1,
            fn($row) => $row['judul'],
            fn($row) => $row['kelas'],
            fn($row) => $row['batch'],
            fn($row) => $row['display_waktu'],
            fn($row) => '<span class=\'' .
                $row['status']['badge'] .
                '\'>' .
                ucfirst($row['status']['text']) .
                '</span>',
            fn($row) => $row['total_soal'],
        ]"
            :showActions="true"
            :actionButtons="fn($row) => view('components.action-buttons', [
                'modalTarget' => 'modal-control-ujian',
                'editData' => [
                    'id' => $row['id'],
                    'fetchEndpoint' => '/manajemen-ujian/'.$row['id'],
                    'updateEndpoint' => '/manajemen-ujian/'.$row['id'],
                    'act' => 'update',
                ],
                'viewData' => [
                    'id' => $row['id'],
                    'fetchEndpoint' => '/manajemen-ujian/'.$row['id'],
                    'drawerTarget' => 'drawer-detail-ujian',
                    'type' => 'slideOver',
                    'title' => 'Detail Soal Ujian',
                    'description' => 'Daftar soal untuk ujian',
                ],
                'deleteRoute' => route('manajemen-ujian.destroy', $row['id']),
            ])"
            :autoFilter="[
                2 => 'Kelas',
                5 => 'Status'
            ]"
            :filterPlaceholder="'Semua'" />

        {{-- Modal untuk Update Ujian Only --}}
        <x-fragments.form-modal id="modal-control-ujian" title="Edit Ujian" editTitle="Edit Ujian" :updateOnly="true">
            <x-fragments.text-field label="Judul" name="judul" placeholder="Masukkan judul ujian..." required />
            <x-fragments.select-field label="Durasi Ujian" name="waktu" :options="[
                '30' => '30 Menit',
                '60' => '60 Menit',
                '90' => '90 Menit',
            ]" required />
            <x-fragments.select-field label="Status" name="status" :options="[
                'pending' => 'Pending',
                'active' => 'Active',
                'finished' => 'Finished',
            ]" required />
        </x-fragments.form-modal>

        {{-- Universal Drawer untuk Detail Soal --}}
        <x-drawer-layout type="slideOver" id="drawer-detail-ujian" title="Detail Soal Ujian"
            description="Daftar soal untuk ujian">
            <div x-data="{
                ujianData: null,
                soals: [],
            }"
                x-on:drawerDataLoaded.window="
                    if ($event.detail.drawerId === 'drawer-detail-ujian') {
                        ujianData = $event.detail.data
                        soals = ujianData.soals || []
                        console.log('Ujian data diterima:', ujianData)
                    }
                "
                class="space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Daftar Soal</h3>
                    <template x-if="soals.length > 0">
                        <div class="space-y-4">
                            <template x-for="(soal, index) in soals" :key="soal.id">
                                <div class="border-b pb-4">
                                    <p class="text-sm font-medium mb-2" x-text="`${index + 1}. ${soal.teks}`"></p>
                                    <ul class="space-y-2">
                                        <template x-for="jawaban in soal.jawabans" :key="jawaban.id">
                                            <li
                                                :class="jawaban.benar ?
                                                    'flex items-center bg-green-100 text-green-800 p-2 rounded' :
                                                    'flex items-center p-2'">
                                                <span class="mr-2" x-text="`${jawaban.pilihan}. ${jawaban.teks}`"></span>
                                                <template x-if="jawaban.benar">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </template>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="soals.length === 0">
                        <p class="text-gray-500 text-sm">Belum ada soal untuk ujian ini.</p>
                    </template>
                </div>
            </div>
        </x-drawer-layout>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const judulInput = document.querySelector('input[name="judul"]');
            const waktuSelect = document.querySelector('select[name="waktu"]');
            const statusSelect = document.querySelector('select[name="status"]');

            document.addEventListener('modalCreate', function(e) {
                if (e.detail.modalId === 'modal-control-ujian') {
                    console.log('Modal Create - Resetting form');
                    resetForm();
                }
            });

            document.addEventListener('modalUpdate', function(e) {
                if (e.detail.modalId === 'modal-control-ujian') {
                    const ujianData = e.detail.data;
                    console.log('Modal Update - Ujian Data:', ujianData);

                    judulInput.value = ujianData.judul || '';
                    waktuSelect.value = ujianData.waktu || '';
                    statusSelect.value = ujianData.status || '';
                }
            });

            document.addEventListener('modalReset', function(e) {
                if (e.detail.modalId === 'modal-control-ujian') {
                    console.log('Modal Reset');
                    resetForm();
                }
            });

            function resetForm() {
                judulInput.value = '';
                waktuSelect.value = '';
                statusSelect.value = '';
            }
        });
    </script>
@endsection
