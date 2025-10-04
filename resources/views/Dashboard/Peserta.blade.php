{{-- Update view Dashboard/Peserta.blade.php --}}

@extends('layouts.dashboard-layouts')

@section('content')
    <div class="">
        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center sm:hidden block">Manajemen Peserta</h1>

        @if (Auth::user()->role === 'admin')
            <div class="flex mb-4 md:flex-row flex-col md:items-center items-end justify-end gap-2">
                <x-fragments.modal-button target="modal-control-peserta" variant="emerald" act="create" :disabled="$activeBatch->isEmpty()">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Tambah Peserta
                </x-fragments.modal-button>
            </div>
        @endif

        <x-fragments.form-modal id="modal-control-peserta" title="Form Peserta" createTitle="Tambah Peserta Baru"
            editTitle="Edit Peserta" action="{{ route('peserta.store') }}">

            @if ($activeBatch->isNotEmpty())
                <div id="batch-info-section" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span class="text-sm text-blue-800">
                            Batch dalam status registration akan otomatis digunakan untuk peserta baru.
                        </span>
                    </div>
                    <ul class="list-disc ml-10">
                        @foreach ($activeBatch as $batch)
                            <li class="text-sm text-blue-800"><strong>{{ $batch->display_name }}</strong></li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div id="batch-info-section" class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-red-600 mr-2"></i>
                        <span class="text-sm text-red-800">
                            tidak ada batch dalam status registrasi
                        </span>
                    </div>

                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <x-fragments.text-field label="Nama User" name="name" placeholder="Masukkan nama untuk login peserta"
                    required />
                <x-fragments.text-field label="Email" name="email" type="email" placeholder="Masukkan email"
                    required />
            </div>

            <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap"
                placeholder="Masukkan nama lengkap (Opsional)" />

            <div id="isMagang" class="hidden">
                <x-fragments.select-field label="Magang" name="ikut_magang" :options="[
                    'belum ditentukan' => 'belum ditentukan',
                    'tidak' => 'tidak',
                    'ikut' => 'ikut',
                ]" />

            </div>

            <div class="mt-4">
                <x-fragments.text-field label="Password" name="password" type="password" placeholder="Masukkan password"
                    required />
                <p id="password-help-text" class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password
                </p>
            </div>


            <div id="kelas-div" class="mt-4 ">
                <x-fragments.select-field label="Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" required />
            </div>
            <p id="text-info" class="text-blue-500 hidden">Kelas yang terdapat pada peserta tidak dapat diubah</p>
        </x-fragments.form-modal>

        <x-reusable-table :searchBar="true" :truncate="false" :headers="['No', 'Avatar', 'Nama Lengkap', 'Email', 'Kelas', 'Batch', 'Status', 'Magang', 'Hasil Ujian']" :data="$pesertaData" :columns="[
            fn($row, $i) => $i + 1,
            fn($row) => $row['avatar'],
            fn($row) => $row['nama_lengkap'],
            fn($row) => $row['email'],
            fn($row) => $row['kelas'],
            fn($row) => $row['batch'],
            fn($row) => $row['status'],
            fn($row) => $row['ikut_magang'],
            fn($row) => count($row['hasil']) . ' ujian',
        ]"
            :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalTarget' => 'modal-control-peserta',
                'editData' => [
                    'id' => $row['id'],
                    'fetchEndpoint' => '/peserta/' . $row['id'],
                    'updateEndpoint' => '/peserta/' . $row['id'],
                    'act' => 'update',
                ],
                'deleteRoute' => route('peserta.destroy', $row['id']),
                'drawerId' => 'drawer-detail-peserta-' . $row['id'],
            ])" :autoFilter="[4 => 'Kelas']" :filterPlaceholder="'Semua'" />
    </div>

    @foreach ($pesertaData as $peserta)
        <x-drawer-layout id="drawer-detail-peserta-{{ $peserta['id'] }}" title="Detail Hasil Ujian: {{ $peserta['name'] }}"
            description="Informasi lengkap tentang hasil ujian {{ $peserta['nama_lengkap'] }}">
            <div class="space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Informasi Peserta</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama User:</p>
                            <p class="font-medium">{{ $peserta['name'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email:</p>
                            <p class="font-medium">{{ $peserta['email'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nama Lengkap:</p>
                            <p class="font-medium">{{ $peserta['nama_lengkap'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Kelas:</p>
                            <p class="font-medium">{{ $peserta['kelas'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Batch:</p>
                            <p class="font-medium">{{ $peserta['batch'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            <p class="font-medium"> {!! Str::of($peserta['status'])->stripTags('<span>')->toString() !!}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Hasil Ujian</h3>
                    @if (count($peserta['hasil']) > 0)
                        <div class="space-y-4">
                            @foreach ($peserta['hasil'] as $hasil)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-semibold text-blue-600">{{ $hasil['judul'] }}</h4>
                                        <span
                                            class="px-3 py-1 rounded-full text-sm font-medium
                                            {{ $hasil['nilai'] >= 80
                                                ? 'bg-green-100 text-green-800'
                                                : ($hasil['nilai'] >= 70
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-red-100 text-red-800') }}">
                                            {{ $hasil['nilai'] }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Waktu Pengerjaan:</p>
                                            <p class="font-medium">{{ $hasil['waktu'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Jawaban Benar:</p>
                                            <p class="font-medium text-green-600">{{ $hasil['benar'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Jawaban Salah:</p>
                                            <p class="font-medium text-red-600">{{ $hasil['salah'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-clipboard-list text-4xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Belum ada hasil ujian yang tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-drawer-layout>
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nameInput = document.querySelector('input[name="name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');
            const namaLengkapInput = document.querySelector('input[name="nama_lengkap"]');
            const kelasSelect = document.querySelector('select[name="kelas_id"]');
            const isMagang = document.getElementById('isMagang');
            const magangInput = document.querySelector('select[name="ikut_magang"]');
            const batchInfoSection = document.getElementById('batch-info-section');
            const passwordHelpText = document.getElementById('password-help-text');
            const kelasDiv = document.getElementById('kelas-div');
            const textInfo = document.getElementById('text-info');


            document.addEventListener('modalCreate', function(e) {
                if (e.detail.modalId === 'modal-control-peserta') {
                    resetForm();
                    if (batchInfoSection) batchInfoSection.style.display = 'block';
                    passwordInput.required = true;
                    passwordHelpText.style.display = 'none';
                }
            });

            document.addEventListener('modalUpdate', function(e) {
                if (e.detail.modalId === 'modal-control-peserta') {
                    const pesertaData = e.detail.data;
                    console.log('Peserta Data:', pesertaData);

                    nameInput.value = pesertaData.name || '';
                    emailInput.value = pesertaData.email || '';
                    namaLengkapInput.value = pesertaData.nama_lengkap || '';
                    kelasSelect.value = pesertaData.kelas_id || '';
                    kelasSelect.disabled = true;
                    isMagang.classList.remove('hidden')
                    isMagang.classList.add('block')
                    magangInput.value = pesertaData.ikut_magang || ''.
                    kelasSelect.removeAttribute('required');
                    textInfo.classList.remove('hidden')
                    kelasDiv.classList.add('opacity-25');
                    kelasSelect.style.cursor = 'not-allowed';
                    passwordInput.value = '';
                    passwordInput.required = false;
                    passwordHelpText.style.display = 'block';

                    if (batchInfoSection) batchInfoSection.style.display = 'none';
                }
            });
            document.addEventListener('modalReset', function(e) {
                if (e.detail.modalId === 'modal-control-peserta') {
                    resetForm();
                }
            });

            function resetForm() {
                nameInput.value = '';
                emailInput.value = '';
                passwordInput.value = '';
                namaLengkapInput.value = '';
                kelasSelect.value = '';
                passwordInput.required = true;
                passwordHelpText.style.display = 'none';
                if (batchInfoSection) batchInfoSection.style.display = 'block';
            }
        });
    </script>
@endsection
