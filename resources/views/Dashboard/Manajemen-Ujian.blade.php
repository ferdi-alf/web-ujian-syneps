@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mt-10">
        <h2 class="text-lg font-semibold mb-2">Data Ujian</h2>
        <x-reusable-table :headers="['No', 'Judul', 'Kelas', 'Waktu', 'Status', 'Total Soal']" :data="$dataUjian" :columns="[
            fn($row, $i) => $i + 1,
            fn($row) => $row['judul'],
            fn($row) => $row['kelas'],
            fn($row) => $row['waktu'],
            fn($row) => '<span class=\'' .
                $row['status']['badge'] .
                '\'>' .
                ucfirst($row['status']['text']) .
                '</span>',
            fn($row) => $row['total_soal'],
        ]" :showActions="true"
            :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-ujian-'.$row['id'],
                'drawerId' => 'drawer-detail-ujian-'.$row['id'],
                'updateRoute' => route('manajemen-ujian.update', $row['id']),
                'deleteRoute' => route('manajemen-ujian.destroy', $row['id']),
            ])" />

        @foreach ($dataUjian as $ujian)
            <x-drawer-layout id="drawer-detail-ujian-{{ $ujian['id'] }}" title="Detail Hasil Ujian: {{ $ujian['judul'] }}"
                description="Informasi lengkap tentang hasil ujian {{ $ujian['judul'] }}">

                <div class="space-y-6">


                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Hasil Ujian</h3>
                        @if (count($ujian['hasil']) > 0)
                            <x-reusable-table :headers="['Nama Lengkap', 'Email', 'Nilai']" :data="$ujian['hasil']" :columns="[
                                fn($row) => $row['nama_lengkap'],
                                fn($row) => $row['email'],
                                fn($row) => $row['nilai'],
                            ]" :showActions="false" />
                        @else
                            <p class="text-gray-500 text-sm">Belum ada hasil ujian.</p>
                        @endif
                    </div>
                </div>
            </x-drawer-layout>
        @endforeach

        @foreach ($dataUjian as $ujian)
            <x-fragments.form-modal id="modal-update-ujian-{{ $ujian['id'] }}" title="Edit Ujian"
                action="{{ route('manajemen-ujian.update', $ujian['id']) }}" method="PUT">

                <x-fragments.text-field label="Judul" name="judul" :value="$ujian['judul']" required />

                <x-fragments.text-field label="Waktu" name="waktu" :value="$ujian['waktu']" placeholder="HH:MM" required />

                <x-fragments.select-field label="Status" name="status" :options="[
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'finished' => 'Finished',
                ]" :value="$ujian['status']['text']" required />
            </x-fragments.form-modal>
        @endforeach

    </div>
@endsection
