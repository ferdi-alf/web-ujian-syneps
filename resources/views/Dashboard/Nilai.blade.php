@extends('layouts.dashboard-layouts')

@section('content')
    <div class="">
        <div class="flex justify-end">
            @if (in_array($user->role, ['admin', 'pengajar']))
                <div class="mb-4">
                    <button data-modal-target="download-modal" data-modal-toggle="download-modal"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fa-solid fa-file-pdf"></i>
                        Download Hasil Ujian
                    </button>
                </div>
            @endif
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center sm:hidden block">
            @switch($user->role)
                @case('admin')
                    Manajemen Nilai Ujian
                @break

                @case('pengajar')
                    Nilai Ujian Kelas
                @break

                @default
                    Nilai Ujian Saya
            @endswitch
        </h1>

        <div class="flex md:flex-row flex-col sm:justify-between justify-end items-end sm:items-center mb-6">
            <h1 class="text-2xl sm:block hidden font-bold text-gray-800">
                @switch($user->role)
                    @case('admin')
                        Manajemen Nilai Ujian
                    @break

                    @case('pengajar')
                        Nilai Ujian Kelas
                    @break

                    @default
                        Nilai Ujian Saya
                @endswitch
            </h1>
        </div>

        @switch($user->role)
            @case('siswa')
                @if (count($data) > 0)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul Ujian</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Batch</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total Soal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Benar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Salah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($data as $index => $ujian)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $ujian['judul'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ujian['batch_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $ujian['batch_nama'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ujian['total_soal'] }}
                                                soal</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ujian['nilai'] >= 80 ? 'bg-green-100 text-green-800' : ($ujian['nilai'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $ujian['nilai'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                                                {{ $ujian['benar'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                                {{ $ujian['salah'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $grade =
                                                        $ujian['nilai'] >= 90
                                                            ? 'A'
                                                            : ($ujian['nilai'] >= 80
                                                                ? 'B'
                                                                : ($ujian['nilai'] >= 70
                                                                    ? 'C'
                                                                    : ($ujian['nilai'] >= 60
                                                                        ? 'D'
                                                                        : 'E')));
                                                    $gradeColor =
                                                        $ujian['nilai'] >= 80
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($ujian['nilai'] >= 70
                                                                ? 'bg-yellow-100 text-yellow-800'
                                                                : 'bg-red-100 text-red-800');
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gradeColor }}">
                                                    {{ $grade }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-clipboard-list text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Hasil Ujian</h3>
                        <p class="text-gray-500">Anda belum mengerjakan ujian apapun.</p>
                    </div>
                @endif
            @break

            @default
                @if (count($data) > 0)
                    @foreach ($data as $namaKelas => $ujianList)
                        <div class="mb-8">
                            <div class="flex bg-white p-2 rounded-lg items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-800">Kelas {{ $namaKelas }}</h2>
                                <span class="text-sm text-blue-500 font-bold">{{ count($ujianList) }} ujian</span>
                            </div>

                            <x-reusable-table :searchBar="true" :truncate="true" :headers="['No', 'Judul Ujian', 'Batch', 'Total Hasil', 'Rata-rata']" :data="$ujianList"
                                :columns="[
                                    fn($row, $i) => $i + 1,
                                    fn($row) => $row['judul'],
                                    fn($row) => $row['batch_badge'],
                                    fn($row) => $row['total_hasil'] . ' siswa',
                                    fn($row) => $row['rata_rata'],
                                ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                                    'viewData' => [
                                        'id' => $row['id'],
                                        'fetchEndpoint' => '/nilai/' . $row['id'],
                                        'drawerTarget' => 'drawer-detail-nilai',
                                        'type' => 'bottomSheet',
                                        'title' => 'Detail Hasil Ujian: ' . $row['judul'],
                                        'description' =>
                                            'Kelas ' .
                                            $namaKelas .
                                            ' - ' .
                                            $row['total_hasil'] .
                                            ' siswa dengan rata-rata ' .
                                            $row['rata_rata'],
                                    ],
                                    'hideEdit' => true,
                                    'hideDelete' => true,
                                ])" />
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-clipboard-list text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Ujian</h3>
                        <p class="text-gray-500">Belum ada ujian yang tersedia di sistem.</p>
                    </div>
                @endif

                {{-- Universal Drawer dengan Fetch API --}}
                <x-drawer-layout type="bottomSheet" id="drawer-detail-nilai" title="Detail Hasil Ujian"
                    description="Informasi hasil ujian siswa">
                    <div x-data="{
                        ujianData: null,
                        siswaList: [],
                    }"
                        x-on:drawerDataLoaded.window="
                            if ($event.detail.drawerId === 'drawer-detail-nilai') {
                                ujianData = $event.detail.data
                                siswaList = ujianData.siswa || []
                                console.log('Ujian data diterima:', ujianData)
                            }
                        "
                        class="space-y-6">

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Informasi Ujian</h3>
                            <template x-if="ujianData">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Judul Ujian:</p>
                                        <p class="font-medium" x-text="ujianData.judul"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Kelas:</p>
                                        <p class="font-medium" x-text="ujianData.kelas"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Batch:</p>
                                        <p class="font-medium" x-text="ujianData.batch_nama"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Siswa:</p>
                                        <p class="font-medium" x-text="ujianData.total_hasil + ' siswa'"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Rata-rata Nilai:</p>
                                        <p class="font-medium" x-text="ujianData.rata_rata"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Hasil Siswa</h3>
                            <template x-if="siswaList.length > 0">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-white">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    No</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Siswa</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Batch</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Nilai</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Benar</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Salah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="(siswa, index) in siswaList" :key="index">
                                                <tr>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"
                                                        x-text="index + 1"></td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="h-8 w-8">
                                                                <img class="h-8 w-8 rounded-full object-cover"
                                                                    :src="siswa.avatar" :alt="siswa.nama_lengkap">
                                                            </div>
                                                            <div class="ml-2">
                                                                <div class="text-sm font-medium text-gray-900"
                                                                    x-text="siswa.nama_lengkap"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <span
                                                            :class="siswa.batch_status === 'active' ?
                                                                'bg-green-100 text-green-800' :
                                                                'bg-gray-100 text-gray-800'"
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                            x-text="siswa.batch_nama">
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <span
                                                            :class="siswa.nilai >= 80 ? 'bg-green-100 text-green-800' : (siswa
                                                                .nilai >= 70 ? 'bg-yellow-100 text-yellow-800' :
                                                                'bg-red-100 text-red-800')"
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                            x-text="siswa.nilai">
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-green-600 font-medium"
                                                        x-text="siswa.benar"></td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-red-600 font-medium"
                                                        x-text="siswa.salah"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <template x-if="siswaList.length === 0">
                                <div class="text-center py-8">
                                    <div class="text-gray-400 mb-4">
                                        <i class="fas fa-user-slash text-4xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">Belum ada siswa yang mengerjakan ujian ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </x-drawer-layout>
            @break
        @endswitch

        @if (in_array($user->role, ['admin', 'pengajar']))
            <x-modal-layout id="download-modal" title="Pilih Opsi Download" size="md" closable="true">
                <form action="{{ route('nilai.download') }}" method="GET">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Download Berdasarkan Batch</label>
                        <select name="batch_option"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all">Semua Batch</option>
                            <option value="active">Batch Aktif Saat Ini</option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Download
                        </button>
                    </div>
                </form>
            </x-modal-layout>
        @endif
    </div>
@endsection
