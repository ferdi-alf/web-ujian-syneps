{{-- Update view Dashboard/Peserta.blade.php --}}

@extends('layouts.dashboard-layouts')

@section('content')
    <div class="">
        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center sm:hidden block">Manajemen Peserta</h1>

        <div class="flex mb-4 md:flex-row flex-col md:items-center items-end justify-between gap-2">
            <div class="flex items-center gap-4 md:w-auto w-full">

                @if ($activeBatch->isNotEmpty())
                    <div class="bg-green-50 border md:w-auto w-full border-green-200 rounded-lg px-3 py-2">
                        <div class="flex items-center">
                            <i class="fas fa-layer-group text-green-600 mr-2"></i>
                            <span class="text-sm text-green-800">
                                <strong>Batch Aktif:</strong>
                            </span>
                        </div>
                        <ul class="space-y-1 list-disc text-green-800 ml-6">
                            @foreach ($activeBatch as $batch)
                                <li>{{ $batch->display_name }}</li>
                            @endforeach
                        </ul>

                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="text-sm text-yellow-800">
                                <strong>Peringatan:</strong> Tidak ada batch aktif
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <x-fragments.modal-button target="modal-add-peserta" variant="emerald" :disabled="!isset($activeBatch)">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Peserta
            </x-fragments.modal-button>
        </div>

        <x-reusable-table :searchBar="true" :truncate="true" :headers="['No', 'Avatar', 'Nama Lengkap', 'Email', 'Kelas', 'Batch', 'Status', 'Hasil Ujian']" :data="$pesertaData" :columns="[
            fn($row, $i) => $i + 1,
            fn($row) => $row['avatar'],
            fn($row) => $row['nama_lengkap'],
            fn($row) => $row['email'],
            fn($row) => $row['kelas'],
            fn($row) => $row['batch'],
            fn($row) => $row['status'],
            fn($row) => count($row['hasil']) . ' ujian',
        ]"
            :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-edit-peserta-' . $row['id'],
                'drawerId' => 'drawer-detail-peserta-' . $row['id'],
                'updateRoute' => route('peserta.update', $row['id']),
                'deleteRoute' => route('peserta.destroy', $row['id']),
            ])" :autoFilter="[
                5 => 'Batch', // Filter by column index 5 (Batch)
            ]" :filterPlaceholder="'Semua'" />
    </div>


    <x-fragments.form-modal id="modal-add-peserta" title="Tambah Peserta Baru" action="{{ route('peserta.store') }}">
        @if ($activeBatch->isNotEmpty())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <span class="text-sm text-blue-800">
                        Batch yang aktif:
                    </span>
                </div>
                <ul class="list-disc ml-10">
                    @foreach ($activeBatch as $batch)
                        <li class="text-sm text-blue-800"><strong>{{ $batch->display_name }}</strong></li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="grid grid-cols-2 gap-4">
            <x-fragments.text-field label="Nama User" name="name" placeholder="Masukkan nama untuk login peserta"
                required />
            <x-fragments.text-field label="Email" name="email" type="email" placeholder="Masukkan email" required />
        </div>
        <x-fragments.text-field label="Password" name="password" type="password" placeholder="Masukkan password" required
            class="mt-4" />
        <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap"
            placeholder="Masukkan nama lengkap, (Opsional) bisa dikosongkan" />
        <div class="mt-4">
            <x-fragments.select-field label="Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" required />
        </div>
    </x-fragments.form-modal>


    @foreach ($pesertaData as $peserta)
        <x-fragments.form-modal id="modal-edit-peserta-{{ $peserta['id'] }}" title="Edit Peserta"
            action="{{ route('peserta.update', $peserta['id']) }}" method="PUT">
            <div class="grid grid-cols-2 gap-4">
                <x-fragments.text-field label="Nama User" name="name" value="{{ $peserta['name'] }}"
                    placeholder="Masukkan nama user" required />
                <x-fragments.text-field label="Email" name="email" type="email" value="{{ $peserta['email'] }}"
                    placeholder="Masukkan email" required />
            </div>
            <x-fragments.text-field label="Password" name="password" type="password"
                placeholder="Kosongkan jika tidak ingin mengubah" class="mt-4" />
            <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap" value="{{ $peserta['nama_lengkap'] }}"
                placeholder="Masukkan nama lengkap, (Opsional) bisa dikosongkan" />
            <div class="mt-4">
                <x-fragments.select-field label="Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()"
                    value="{{ $peserta['kelas_id'] }}" required />
            </div>


            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-4">
                <div class="flex items-center">
                    <i class="fas fa-layer-group text-gray-600 mr-2"></i>
                    <span class="text-sm text-gray-700">
                        <strong>Batch:</strong> {{ $peserta['batch'] }}
                    </span>
                </div>
            </div>
        </x-fragments.form-modal>
    @endforeach


    @foreach ($pesertaData as $peserta)
        <x-drawer-layout id="drawer-detail-peserta-{{ $peserta['id'] }}"
            title="Detail Hasil Ujian: {{ $peserta['name'] }}"
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
@endsection
