@extends('layouts.dashboard-layouts')

@section('content')

    <div class="">
        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center sm:hidden block">
            @switch($user->role)
                @case('admin')
                    Leaderboard Semua Kelas
                @break

                @case('pengajar')
                    Leaderboard Kelas
                @break

                @default
                    Leaderboard
            @endswitch
        </h1>

        <div class="flex md:flex-row flex-col sm:justify-between justify-end items-end sm:items-center mb-6">
            <h1 class="text-2xl sm:block hidden font-bold text-gray-800">
                @switch($user->role)
                    @case('admin')
                        Leaderboard Semua Kelas
                    @break

                    @case('pengajar')
                        Leaderboard Kelas
                    @break

                    @default
                        Leaderboard
                @endswitch
            </h1>
        </div>

        @switch($user->role)
            @case('pengajar')
                @if (count($data) > 0)
                    <x-reusable-table :searchBar="true" :truncate="true" :headers="['Peringkat', 'Siswa', 'Rata-rata Nilai', 'Status Perkembangan']" :data="$data" :columns="[
                        fn(
                            $row,
                            $i,
                        ) => '<span class=\'inline-flex items-center px-3 py-1 rounded-full text-sm font-bold ' .
                            ($i === 0
                                ? 'bg-yellow-100 text-yellow-800'
                                : ($i === 1
                                    ? 'bg-gray-100 text-gray-800'
                                    : ($i === 2
                                        ? 'bg-orange-100 text-orange-800'
                                        : 'bg-blue-100 text-blue-800'))) .
                            '\'>#' .
                            ($i + 1) .
                            '</span>',
                        fn(
                            $row,
                        ) => '<div class=\'flex items-center\'><div class=\'h-10 w-10\'><img class=\'h-10 w-10 rounded-full object-cover\' src=\'' .
                            $row['avatar'] .
                            '\' alt=\'' .
                            $row['nama'] .
                            '\'></div><div class=\'ml-3\'><div class=\'text-sm font-medium text-gray-900\'>' .
                            $row['nama'] .
                            '</div></div></div>',
                        fn(
                            $row,
                        ) => '<span class=\'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                            ((float) $row['rata_rata'] >= 80
                                ? 'bg-green-100 text-green-800'
                                : ((float) $row['rata_rata'] >= 70
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-red-100 text-red-800')) .
                            '\'>' .
                            $row['rata_rata'] .
                            '</span>',
                        fn($row) => '<div class=\'text-sm text-gray-600 max-w-xs truncate\'>' .
                            $row['status'] .
                            '</div>',
                    ]"
                        :showActions="true"
                        :actionButtons="fn($row) => view('components.action-buttons', [
                            'drawerId' => 'drawer-leaderboard-'.$row['id'],
                            'hideEdit' => true,
                            'hideDelete' => true,
                        ])" />
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-trophy text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Leaderboard</h3>
                        <p class="text-gray-500">Belum ada siswa yang mengerjakan ujian.</p>
                    </div>
                @endif

                @foreach ($data as $siswa)
                    <x-drawer-layout id="drawer-leaderboard-{{ $siswa['id'] }}" title="Detail Progress: {{ $siswa['nama'] }}"
                        description="Rata-rata nilai: {{ $siswa['rata_rata'] }} - {{ $siswa['status'] }}">
                        <div class="space-y-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Grafik Perkembangan Nilai</h3>
                                <div class="h-64">
                                    <canvas id="chart-{{ $siswa['id'] }}"></canvas>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Detail Hasil Ujian</h3>
                                @if (count($siswa['table_data']) > 0)
                                    <x-reusable-table :searchBar="false" :truncate="false" :headers="['No', 'Judul Ujian', 'Nilai', 'Benar', 'Salah']" :data="$siswa['table_data']"
                                        :columns="[
                                            fn($row, $i) => $i + 1,
                                            fn($row) => $row['judul'],
                                            fn(
                                                $row,
                                            ) => '<span class=\'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                                                ($row['nilai'] >= 80
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($row['nilai'] >= 70
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-red-100 text-red-800')) .
                                                '\'>' .
                                                $row['nilai'] .
                                                '</span>',
                                            fn($row) => $row['benar'],
                                            fn($row) => $row['salah'],
                                        ]" :showActions="false" />
                                @else
                                    <div class="text-center py-8">
                                        <div class="text-gray-400 mb-4">
                                            <i class="fas fa-clipboard-list text-4xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm">Belum ada data ujian.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-drawer-layout>
                @endforeach
            @break

            @default
                @if (count($data) > 0)
                    @foreach ($data as $namaKelas => $siswaList)
                        <div class="mb-8">
                            <div class="flex bg-white p-2 rounded-lg items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-800">Kelas {{ $namaKelas }}</h2>
                                <span class="text-sm text-blue-500 font-bold">{{ count($siswaList) }} siswa</span>
                            </div>
                            <x-reusable-table :searchBar="true" :headers="['Peringkat', 'Siswa', 'Rata-rata Nilai', 'Status Perkembangan']" :data="$siswaList" :columns="[
                                fn(
                                    $row,
                                    $i,
                                ) => '<span class=\'inline-flex items-center px-3 py-1 rounded-full text-sm font-bold ' .
                                    ($i === 0
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : ($i === 1
                                            ? 'bg-gray-100 text-gray-800'
                                            : ($i === 2
                                                ? 'bg-orange-100 text-orange-800'
                                                : 'bg-blue-100 text-blue-800'))) .
                                    '\'>#' .
                                    ($i + 1) .
                                    '</span>',
                                fn(
                                    $row,
                                ) => '<div class=\'flex items-center\'><div class=\'h-10 w-10\'><img class=\'h-10 w-10 rounded-full object-cover\' src=\'' .
                                    $row['avatar'] .
                                    '\' alt=\'' .
                                    $row['nama'] .
                                    '\'></div><div class=\'ml-3\'><div class=\'text-sm font-medium text-gray-900\'>' .
                                    $row['nama'] .
                                    '</div></div></div>',
                                fn(
                                    $row,
                                ) => '<span class=\'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                                    ((float) $row['rata_rata'] >= 80
                                        ? 'bg-green-100 text-green-800'
                                        : ((float) $row['rata_rata'] >= 70
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-red-100 text-red-800')) .
                                    '\'>' .
                                    $row['rata_rata'] .
                                    '</span>',
                                fn($row) => '<div class=\'text-sm text-gray-600 \'>' . $row['status'] . '</div>',
                            ]"
                                :showActions="true"
                                :actionButtons="fn($row) => view('components.action-buttons', [
                                    'drawerId' => 'drawer-admin-leaderboard-'.$row['id'],
                                    'hideEdit' => true,
                                    'hideDelete' => true,
                                ])" />
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-trophy text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Leaderboard</h3>
                        <p class="text-gray-500">Belum ada data ujian yang tersedia di sistem.</p>
                    </div>
                @endif

                @foreach ($data as $namaKelas => $siswaList)
                    @foreach ($siswaList as $siswa)
                        <x-drawer-layout id="drawer-admin-leaderboard-{{ $siswa['id'] }}"
                            title="Detail Progress: {{ $siswa['nama'] }}"
                            description="Kelas {{ $namaKelas }} - Rata-rata nilai: {{ $siswa['rata_rata'] }}">
                            <div class="space-y-6">
                                <div>

                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-semibold mb-4">Grafik Perkembangan Nilai</h3>
                                    <div class="h-64">
                                        <canvas id="chart-admin-{{ $siswa['id'] }}"></canvas>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-semibold mb-4">Detail Hasil Ujian</h3>
                                    @if (count($siswa['table_data']) > 0)
                                        <x-reusable-table :searchBar="false" :truncate="false" :headers="['No', 'Judul Ujian', 'Nilai', 'Benar', 'Salah']"
                                            :data="$siswa['table_data']" :columns="[
                                                fn($row, $i) => $i + 1,
                                                fn($row) => $row['judul'],
                                                fn(
                                                    $row,
                                                ) => '<span class=\'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                                                    ($row['nilai'] >= 80
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($row['nilai'] >= 70
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-red-100 text-red-800')) .
                                                    '\'>' .
                                                    $row['nilai'] .
                                                    '</span>',
                                                fn($row) => $row['benar'],
                                                fn($row) => $row['salah'],
                                            ]" :showActions="false" />
                                    @else
                                        <div class="text-center py-8">
                                            <div class="text-gray-400 mb-4">
                                                <i class="fas fa-clipboard-list text-4xl"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm">Belum ada data ujian.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-drawer-layout>
                    @endforeach
                @endforeach
            @break
        @endswitch
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($user->role === 'pengajar')
                @foreach ($data as $siswa)
                    initChart('chart-{{ $siswa['id'] }}', @json($siswa['chart_ujian']));
                @endforeach
            @else
                @foreach ($data as $namaKelas => $siswaList)
                    @foreach ($siswaList as $siswa)
                        initChart('chart-admin-{{ $siswa['id'] }}', @json($siswa['chart_ujian']));
                    @endforeach
                @endforeach
            @endif
        });

        function initChart(canvasId, chartData) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(item => item.judul),
                    datasets: [{
                        label: 'Nilai',
                        data: chartData.map(item => item.nilai),
                        backgroundColor: chartData.map(item => {
                            if (item.nilai >= 80) return 'rgba(34, 197, 94, 0.8)';
                            if (item.nilai >= 70) return 'rgba(234, 179, 8, 0.8)';
                            return 'rgba(239, 68, 68, 0.8)';
                        }),
                        borderColor: chartData.map(item => {
                            if (item.nilai >= 80) return 'rgba(34, 197, 94, 1)';
                            if (item.nilai >= 70) return 'rgba(234, 179, 8, 1)';
                            return 'rgba(239, 68, 68, 1)';
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 10
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
