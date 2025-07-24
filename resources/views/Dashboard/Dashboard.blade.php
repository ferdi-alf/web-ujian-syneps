@extends('layouts.dashboard-layouts')
@section('content')
    @switch(Auth::user()->role)
        @case('siswa')
            <div class="grid grid-cols-10 gap-3">
                <div class="md:col-span-3 col-span-10 flex flex-col justify-center items-center bg-white rounded-lg shadow-md p-3">
                    <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="profile"
                        class="w-56 h-56 rounded-full object-cover">
                    <div class="w-full flex flex-col md:items-center items-start ">
                        <div class="w-fit space-y-2">
                            <div class="flex justify-start items-start space-x-3 mt-3">
                                <i class="fa-solid fa-user text-xl text-blue-500"></i>
                                <p class="text-sm font-semibold ">{{ Auth::user()->nama_lengkap }}</p>
                            </div>
                            <div class="flex justify-start items-center space-x-3 mt-3">
                                <i class="fa-solid fa-at text-xl text-red-500"></i>
                                <p class="text-sm font-semibold ">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="flex justify-start items-center space-x-3 mt-3">
                                <i class="fa-solid fa-briefcase text-xl text-teal-500"></i>
                                <p class="text-sm font-semibold ">{{ Auth::user()->siswaDetail->kelas->nama }}</p>
                            </div>
                            <div class="flex justify-start items-center space-x-3 mt-3">
                                <i class="fa-solid fa-circle-check text-lg text-green-500"></i>
                                <p class="text-sm font-semibold">
                                    {{ Auth::user()->hasilUjian()->count() }} Ujian Telah diselesaikan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-7 col-span-10 bg-white rounded-lg shadow-md p-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Grafik Perkembangan Nilai</h3>
                        <p class="text-sm text-gray-600">Menampilkan nilai ujian Anda dari waktu ke waktu</p>
                    </div>

                    @if (count($chartData['labels']) > 0)
                        <div class="h-80">
                            <canvas id="siswaChart"></canvas>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-80 bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <i class="fa-solid fa-chart-bar text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">Belum ada data ujian untuk ditampilkan</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 bg-white rounded-lg shadow-md p-4">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Leaderboard Siswa -
                        {{ Auth::user()->siswaDetail->kelas->nama }}</h3>
                    <p class="text-sm text-gray-600">Peringkat siswa berdasarkan rata-rata nilai ujian</p>
                </div>

                @if (count($leaderboardData) > 0)
                    @php
                        $currentUserRank =
                            collect($leaderboardData)->where('is_current_user', true)->first()['rank'] ?? null;
                    @endphp

                    @if ($currentUserRank && $currentUserRank <= 3)
                        <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 rounded">
                            <div class="flex items-center">
                                <i class="fa-solid fa-trophy text-yellow-500 mr-2"></i>
                                <p class="text-green-800 font-semibold">
                                    Selamat! Anda berada di peringkat {{ $currentUserRank }} dari {{ count($leaderboardData) }}
                                    siswa!
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peringkat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rata-rata Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                                        Ujian</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($leaderboardData as $siswa)
                                    <tr
                                        class="{{ $siswa['is_current_user'] ? 'bg-blue-50 border-l-4 border-blue-500' : '' }} 
                                       {{ $siswa['is_top_3'] ? 'bg-gradient-to-r from-yellow-50 to-yellow-100' : '' }}">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if ($siswa['rank'] == 1)
                                                    <i class="fa-solid fa-crown text-yellow-500 text-lg mr-2"></i>
                                                @elseif($siswa['rank'] == 2)
                                                    <i class="fa-solid fa-medal text-gray-400 text-lg mr-2"></i>
                                                @elseif($siswa['rank'] == 3)
                                                    <i class="fa-solid fa-medal text-orange-500 text-lg mr-2"></i>
                                                @else
                                                    <span class="w-6 text-center mr-2"></span>
                                                @endif
                                                <span class="text-sm font-bold text-gray-900">{{ $siswa['rank'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img src="{{ $siswa['avatar'] }}" alt="avatar"
                                                    class="w-10 h-10 rounded-full object-cover mr-3">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $siswa['nama'] }}
                                                        @if ($siswa['is_current_user'])
                                                            <span
                                                                class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Anda</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm font-bold text-gray-900">{{ $siswa['rata_rata'] }}</span>
                                                <div class="ml-2 w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full"
                                                        style="width: {{ $siswa['rata_rata'] }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ $siswa['total_ujian'] }} ujian</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-users text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">Belum ada data leaderboard</p>
                    </div>
                @endif
            </div>

            @if (count($chartData['labels']) > 0)
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('siswaChart').getContext('2d');
                        const chartData = @json($chartData);

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: chartData.labels,
                                datasets: chartData.datasets
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Perkembangan Nilai Ujian'
                                    },
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        title: {
                                            display: true,
                                            text: 'Nilai'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Bulan'
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                }
                            }
                        });
                    });
                </script>
            @endif
        @break

        @default
            <div @class([
                'grid',
                'grid-cols-1',
                'gap-3',
                'sm:grid-cols-3',
                'md:grid-cols-4' => Auth::user()->role === 'admin',
            ])>
                <x-fragments.card-dashboard title="Total Peserta" bgColor="red-100" icon="users" color="red"
                    count="{{ $cardData['total_peserta'] }}" description="Total Seluruh Peserta" />

                <x-fragments.card-dashboard title="Total Ujian" bgColor="blue-100" icon="book" color="blue"
                    count="{{ $cardData['total_ujian'] }}" description="Total Data Ujian" />

                <x-fragments.card-dashboard title="Total Ujian Active" bgColor="orange-100"
                    icon="fa-solid fa-square-poll-horizontal" color="orange" count="{{ $cardData['total_ujian_active'] }}"
                    description="Total Ujian yang sudah active" />

                @if ($cardData['show_kelas_card'])
                    <x-fragments.card-dashboard title="Total kelas" bgColor="teal-100" icon="users" color="teal"
                        count="{{ $cardData['total_kelas'] }}" description="Total Seluruh Kelas Syneps" />
                @endif
            </div>

            <div class="grid grid-cols-12 gap-3 mt-10">
                <div class="col-span-8">
                    <div class="bg-white p-4 rounded-lg shadow-md" style="height: 520px;">
                        <h3 class="text-lg font-semibold mb-4">Rata-rata Nilai Per Kelas</h3>
                        <canvas id="stackedBarChart" style="max-height: 450px;"></canvas>
                    </div>
                </div>

                <div class="col-span-4">
                    <div class="md:col-span-4 shadow-md bg-white p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Peserta yang baru selesai ujian</h3>
                        <p>diambil berdasarkan ujian active saat ini:
                            {{ $recentSubmissions['active_exam_titles'] ?: 'Tidak ada ujian aktif' }}</p>

                        @forelse ($recentSubmissions['submissions'] as $submission)
                            @if ($loop->first)
                                <div class="h-96 overflow-auto bg-gray-100 rounded-lg p-2 mt-3">
                            @endif
                            <div
                                class="p-3 w-full flex justify-start gap-3 items-start bg-white rounded-lg shadow-md mb-3 last:mb-0">
                                <img src="{{ $submission->siswa ? $submission->siswa->getAvatarUrl() : asset('images/avatar/default.jpg') }}"
                                    alt="avatar" class="w-10 h-10 rounded-lg flex-shrink-0">
                                <div class="flex flex-col flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900">
                                        @if ($submission->siswa->nama_lengkap)
                                            {{ $submission->siswa->nama_lengkap }}
                                        @else
                                            Unknown User
                                        @endif
                                    </h4>
                                    <p class="font-semibold text-gray-600 text-sm">
                                        {{ $submission->ujian->judul }} - Nilai: {{ $submission->nilai }}
                                    </p>
                                    <div class="text-xs text-end text-gray-500 mt-1">
                                        {{ $submission->created_at->diffForHumans() }}
                                        <span class="text-gray-400">•</span>
                                        {{ $submission->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                            @if ($loop->last)
                    </div>
                    @endif
                @empty
                    <div class="h-96 flex items-center justify-center bg-gray-100 rounded-lg p-2 mt-3">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500">Tidak ada pengumpulan ujian dalam 12 jam terakhir.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6 mt-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ujian Aktif</h3>
                    <span class="text-sm text-blue-600 font-medium">{{ count($activeExamData) }} ujian</span>
                </div>

                @if (count($activeExamData) > 0)
                    <x-reusable-table :searchBar="true" :truncate="true" :headers="['No', 'Judul Ujian', 'Waktu Pengerjaan', 'Status', 'Kelas', 'Total Hasil']" :data="$activeExamData" :columns="[
                        fn($row) => $row['no'],
                        fn($row) => $row['judul'],
                        fn($row) => $row['waktu_pengerjaan'],
                        fn(
                            $row,
                        ) => '<span class=\'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800\'>' .
                            $row['status'] .
                            '</span>',
                        fn($row) => $row['kelas'],
                        fn($row) => $row['total_hasil'],
                    ]"
                        :showActions="true"
                        :actionButtons="fn($row) => view('components.action-buttons', [
                            'drawerId' => 'drawer-detail-active-exam-'.$row['id'],
                            'hideEdit' => true,
                            'hideDelete' => true,
                        ])" />
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-clipboard-list text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Ujian Aktif</h3>
                        <p class="text-gray-500">
                            @if ($user->role == 'admin')
                                Belum ada ujian yang aktif di sistem.
                            @else
                                Belum ada ujian aktif untuk kelas Anda.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            @foreach ($activeExamData as $exam)
                <x-drawer-layout id="drawer-detail-active-exam-{{ $exam['id'] }}" title="Detail Ujian: {{ $exam['judul'] }}"
                    description="Ujian aktif untuk kelas {{ $exam['kelas'] }} dengan {{ $exam['total_hasil'] }}">

                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Informasi Ujian</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Judul Ujian:</p>
                                    <p class="font-medium">{{ $exam['judul'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kelas:</p>
                                    <p class="font-medium">{{ $exam['kelas'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Waktu Pengerjaan:</p>
                                    <p class="font-medium">{{ $exam['waktu_pengerjaan'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status:</p>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $exam['status'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                                <div>
                                    <p class="text-sm text-gray-600">Total Soal:</p>
                                    <p class="font-medium">{{ $exam['ujian_detail']->soals->count() ?? 0 }} soal</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Peserta:</p>
                                    <p class="font-medium">{{ count($exam['siswa_results']) }} siswa</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Rata-rata Nilai:</p>
                                    <p class="font-medium">
                                        @if (count($exam['siswa_results']) > 0)
                                            {{ number_format(collect($exam['siswa_results'])->avg('nilai'), 1) }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Hasil Siswa</h3>
                            @if (count($exam['siswa_results']) > 0)
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
                                                    Nilai</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Benar</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Salah</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Waktu Pengerjaan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($exam['siswa_results'] as $index => $siswa)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="h-8 w-8">
                                                                <img class="h-8 w-8 rounded-full object-cover"
                                                                    src="{{ $siswa['avatar'] }}"
                                                                    alt="{{ $siswa['nama_lengkap'] }}">
                                                            </div>
                                                            <div class="ml-2">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $siswa['nama_lengkap'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $siswa['nilai'] >= 80
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($siswa['nilai'] >= 70
                                                                ? 'bg-yellow-100 text-yellow-800'
                                                                : 'bg-red-100 text-red-800') }}">
                                                            {{ $siswa['nilai'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                                                        {{ $siswa['benar'] }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                                        {{ $siswa['salah'] }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $siswa['waktu_pengerjaan_siswa'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-gray-400 mb-4">
                                        <i class="fas fa-user-slash text-4xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">Belum ada siswa yang mengerjakan ujian ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-drawer-layout>
            @endforeach

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('stackedBarChart').getContext('2d');
                    const chartData = @json($chartData);

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartData.labels,
                            datasets: chartData.datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Rata-rata Nilai Per Kelas'
                                },
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                x: {
                                    stacked: chartData.datasets.some(ds => ds.type === 'bar'),
                                    title: {
                                        display: true,
                                        text: '{{ Auth::user()->role === 'pengajar' ? 'Permateri' : 'Bulan' }}'
                                    }
                                },
                                y: {
                                    stacked: chartData.datasets.some(ds => ds.type === 'bar'),
                                    beginAtZero: true,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'Rata-rata Nilai'
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                });
            </script>
        @endswitch
    @endsection
