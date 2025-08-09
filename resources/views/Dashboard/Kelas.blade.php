@extends('layouts.dashboard-layouts')

@section('content')
    <div>
        <div class="flex justify-end">
            <x-fragments.modal-button target="add-jurusan-modal" variant="emerald">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kelas
            </x-fragments.modal-button>
        </div>

        <x-fragments.form-modal id="add-jurusan-modal" size="xl" title="Tambah Kelas" action="{{ route('kelas.store') }}">
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
                        <x-fragments.select-field label="Type Kelas" name="type" :options="['intensif' => 'Intensif', 'partime' => 'Partime']" />
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
                    'modalId' => 'modal-update-kelas-' . $row->id,
                    'updateRoute' => route('kelas.update', $row->id),
                    'deleteRoute' => route('kelas.destroy', $row->id),
                ])" :searchBar="true" :truncate="true" :rowPerPage="10" position="left" />

        </div>

        @foreach ($kelas as $row)
            <x-fragments.form-modal id="modal-update-kelas-{{ $row->id }}" title="Edit Kelas"
                action="{{ route('kelas.update', $row->id) }}" method="PUT">
                <div class="overflow-auto p-2">
                    <x-fragments.text-field label="Nama Kelas" name="name" :value="$row->nama" required />

                    <x-fragments.currency-field label="Harga Kelas" name="price" :value="number_format($row->harga, 0, '', '')"
                        placeholder="Masukkan harga kelas..." required />

                    <div>
                        <x-fragments.text-field label="DP (%)" name="dp_persen" type="number" :value="$row->dp_persen"
                            placeholder="Contoh: 30" required />
                        <small class="text-gray-500">Masukkan angka dalam persen. Contoh: 30 = 30%.</small>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-fragments.select-field label="Type Kelas" name="type" :options="['intensif' => 'Intensif', 'partime' => 'Partime']"
                                :value="$row->type" />
                            <small class="text-gray-500">Kosongkan jika kelas tidak ada type spesifik</small>
                        </div>
                        <div>
                            <x-fragments.text-field label="Lama Durasi Belajar (bulan)" name="durasi_belajar" type="number"
                                :value="$row->durasi_belajar" placeholder="Contoh: 1, 2, 4..." required />
                            <small class="text-gray-500">Masukkan angka dalam satuan bulan. Contoh: 4 = 4 bulan.</small>
                        </div>
                    </div>

                    <div>
                        <x-fragments.text-field label="Lama Durasi Magang (bulan)" name="waktu_magang" type="number"
                            :value="$row->waktu_magang" placeholder="Contoh: 1, 2, 4..." />
                        <small class="text-gray-500">Masukkan angka dalam satuan bulan. Contoh: 2 = 2 bulan. (Kosongkan jika
                            kelas tidak memiliki magang)</small>
                    </div>
                </div>
            </x-fragments.form-modal>
        @endforeach
    </div>
@endsection
