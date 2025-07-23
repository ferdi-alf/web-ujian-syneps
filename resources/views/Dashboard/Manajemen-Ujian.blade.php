@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mt-10">
        <h2 class="text-lg font-semibold mb-2">Data Ujian</h2>
        <x-reusable-table :searchBar="true" :headers="['No', 'Judul', 'Kelas', 'Waktu', 'Status', 'Total Soal']" :data="$dataUjian" :columns="[
            fn($row, $i) => $i + 1,
            fn($row) => $row['judul'],
            fn($row) => $row['kelas'],
            fn($row) => $row['display_waktu'],
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
            <x-drawer-layout id="drawer-detail-ujian-{{ $ujian['id'] }}" title="Detail Soal Ujian: {{ $ujian['judul'] }}"
                description="Daftar soal untuk ujian {{ $ujian['judul'] }}">
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Daftar Soal</h3>
                        @if (count($ujian['soals']) > 0)
                            <div class="space-y-4">
                                @foreach ($ujian['soals'] as $index => $soal)
                                    <div class="border-b pb-4">
                                        <p class="text-sm font-medium mb-2">
                                            {{ $index + 1 }}. {{ $soal['teks'] }}
                                        </p>
                                        <ul class="space-y-2">
                                            @foreach ($soal['jawabans'] as $jawaban)
                                                <li
                                                    class="flex items-center {{ $jawaban['benar'] ? 'bg-green-100 text-green-800 p-2 rounded' : 'p-2' }}">
                                                    <span class="mr-2">{{ $jawaban['pilihan'] }}.
                                                        {{ $jawaban['teks'] }}</span>
                                                    @if ($jawaban['benar'])
                                                        <svg class="w-5 h-5 text-green-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Belum ada soal untuk ujian ini.</p>
                        @endif
                    </div>
                </div>
            </x-drawer-layout>
        @endforeach

        @foreach ($dataUjian as $ujian)
            <x-fragments.form-modal id="modal-update-ujian-{{ $ujian['id'] }}" title="Edit Ujian"
                action="{{ route('manajemen-ujian.update', $ujian['id']) }}" method="PUT">

                <x-fragments.text-field label="Judul" name="judul" :value="$ujian['judul']" required />

                <x-fragments.select-field label="Durasi Ujian" name="waktu" :options="[
                    '30' => '30 Menit',
                    '60' => '60 Menit',
                    '90' => '90 Menit',
                ]" :value="(string) $ujian['waktu']"
                    required />

                <x-fragments.select-field label="Status" name="status" :options="[
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'finished' => 'Finished',
                ]" :value="$ujian['status']['text']" required />
            </x-fragments.form-modal>
        @endforeach
    </div>
@endsection
