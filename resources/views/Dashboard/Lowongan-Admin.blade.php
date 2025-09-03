@extends('layouts.dashboard-layouts')

@section('content')
    <div class="flex flex-col gap-y-4">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Lowongan</h1>

        <div class="flex justify-end">
            <x-fragments.modal-button target="add-lowongan-modal" variant="emerald">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Lowongan
            </x-fragments.modal-button>
        </div>

        <x-reusable-table
            :searchBar="true"
            :headers="['Posisi', 'Perusahaan', 'Lokasi', 'Deadline', 'Gaji', 'Pelamar', 'Status']"
            :data="$lowonganForTable"
            :columns="[
                                fn($row) => $row['posisi'],
                fn($row) => $row['perusahaan'],
                fn($row) => $row['lokasi'],
                fn($row) => $row['deadline'],
                fn($row) => $row['gaji'],
                fn($row) => $row['pelamar'],
                fn($row) => $row['status'],
            ]"
            :showActions="true"
        />
    </div>

    <x-fragments.form-modal id="add-lowongan-modal" title="Tambah Lowongan Baru" action="{{ route('lowongan.store') }}">
        <x-fragments.text-field label="Posisi" name="posisi" placeholder="Masukkan posisi yang dibuka" required />

        <div class="grid grid-cols-2 gap-4 mt-4">
            <x-fragments.text-field label="Perusahaan" name="perusahaan" placeholder="Masukkan nama perusahaan" required />
            <x-fragments.text-field label="Lokasi" name="lokasi" placeholder="Masukkan lokasi kerja" required />
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <x-fragments.text-field label="Deadline" name="deadline" type="date" required />
            <x-fragments.currency-field label="Gaji" name="gaji" placeholder="Masukkan nominal gaji" />
        </div>

        <div class="mt-4">
            <label for="deskripsi" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                placeholder="Masukkan deskripsi pekerjaan" required></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <x-fragments.select-field label="Status" name="status" :options="['Aktif' => 'Aktif', 'Ditutup' => 'Ditutup']" required />
        </div>
    </x-fragments.form-modal>

    {{-- Edit and Delete Modals --}}
    @foreach ($lowonganData as $item)
        <x-fragments.form-modal id="edit-lowongan-modal-{{ $item['id'] }}" title="Edit Lowongan" action="{{ route('lowongan.update', $item['id']) }}" method="PUT">
            <x-fragments.text-field label="Posisi" name="posisi" value="{{ $item['posisi'] }}" required />

            <div class="mt-4">
                <label for="deskripsi-{{ $item['id'] }}" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                <textarea id="deskripsi-{{ $item['id'] }}" name="deskripsi" rows="4"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required>{{ $item['deskripsi'] }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-fragments.text-field label="Perusahaan" name="perusahaan" value="{{ $item['perusahaan'] }}" required />
                <x-fragments.text-field label="Lokasi" name="lokasi" value="{{ $item['lokasi'] }}" required />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-fragments.text-field label="Deadline" name="deadline" type="date" value="{{ $item['deadline'] }}" required />
                <x-fragments.currency-field label="Gaji" name="gaji" value="{{ $item['gaji'] }}" />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-fragments.select-field label="Status" name="status" :options="['Aktif' => 'Aktif', 'Ditutup' => 'Ditutup']" :value="$item['status']" required />
            </div>
        </x-fragments.form-modal>
    @endforeach

    {{-- Detail Drawer --}}
    @foreach ($lowonganData as $item)
        <x-drawer-layout id="drawer-detail-lowongan-{{ $item->id }}" title="Detail Lowongan: {{ $item->posisi }}"
            description="Informasi lengkap tentang lowongan pekerjaan.">
            <div class="space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Informasi Lowongan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Posisi:</p>
                            <p class="font-medium">{{ $item->posisi }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Perusahaan:</p>
                            <p class="font-medium">{{ $item->perusahaan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Lokasi:</p>
                            <p class="font-medium">{{ $item->lokasi }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Gaji:</p>
                            <p class="font-medium">{{ 'Rp ' . number_format($item->gaji, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Deadline:</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($item->deadline)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            <p class="font-medium"><span
                                    class="px-2 py-1 text-sm font-semibold rounded-full {{ $item->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $item->status }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Deskripsi Pekerjaan</h3>
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($item->deskripsi)) !!}
                    </div>
                </div>
            </div>
        </x-drawer-layout>
    @endforeach
@endsection
