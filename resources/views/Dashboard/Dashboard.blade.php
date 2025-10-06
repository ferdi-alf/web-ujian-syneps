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
                    <h3 class="text-lg font-semibold text-gray-800 ">Leaderboard Peserta -
                        {{ Auth::user()->siswaDetail->kelas->nama }}</h3>
                    <p class="text-sm text-gray-600">Peringkat peserta berdasarkan rata-rata nilai ujian</p>

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
                                    peserta!
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Siswa
                                    </th>
                                    dro
                                    <th
                                        class=" px-10 truncate py-3 text-left text-xs font-medium   text-gray-500 uppercase tracking-wider">
                                        Rata-rata Nilai</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase truncate tracking-wider">
                                        Total
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
                                            <div class="flex items-center md:px-0 px-5">
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
            <div class="bg-white rounded-lg shadow-md p-4 mt-5">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Data Tagihan Pembayaran</h3>
                    <p class="text-sm text-gray-600">tagihan pembayaran akan masuk disini</p>
                </div>

                <x-fragments.form-modal id="universal-pembayaran-modal" title="Upload Bukti Pembayaran" :updateOnly="true">
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:border-blue-400">
                        <input type="file" name="bukti_pembayaran" class="hidden" id="dropzone-file" accept="image/*"
                            onchange="document.getElementById('preview-upload').src = window.URL.createObjectURL(this.files[0]); document.getElementById('preview-upload').classList.remove('hidden');">

                        <label for="dropzone-file" class="cursor-pointer">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 16a4 4 0 01-.88-7.903A5.992 5.992 0 0112 4a5.992 5.992 0 015.88 4.097A4 4 0 1117 16H7z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Klik atau drag file bukti pembayaran</p>
                            </div>
                        </label>

                        <img id="preview-upload" class="hidden mt-3 h-40 rounded">
                    </div>

                </x-fragments.form-modal>
                {{-- drawer pembayaran --}}
                <x-drawer-layout id="drawer-control-pembayaran" title="Detail Pembayaran" description="Preview dan informasi materi"
                    type="bottomSheet">
                    <div x-data="{
                        pembayaranData: null,
                        hasBukti: false,
                    }"
                        x-on:drawerDataLoaded.window="
                            if ($event.detail.drawerId === 'drawer-control-pembayaran') {
                                pembayaranData = $event.detail.data
                                idPembayaran = pembayaranData.id
                                hasBukti = pembayaranData && pembayaranData.bukti_pembayaran
                                console.log('Pembayaran data diterima:', pembayaranData)
                            }
                        "
                        class="p-3">
                        <template x-if="hasBukti">
                            <div class="w-full h-full flex justify-center items-center flex-col ">
                                <img :src="pembayaranData.bukti_pembayaran" class="w-56 rounded-xl shadow-md"
                                    alt="Bukti Pembayaran">
                                <p class="text-xs text-gray-500 mt-1">Bukti sudah diupload</p>
                            </div>
                        </template>
                    </div>
                </x-drawer-layout>

                {{-- table pembayaran --}}
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bukti Pembayaran</th>

                            <th class="px-4 py-3 truncate  text-left text-xs font-medium   text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase truncate tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase truncate tracking-wider">
                                Cicilan ke</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase truncate tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pembayaran as $row)
                            <tr class="text-sm text-gray-800">
                                <td class="px-4 py-4 flex justify-center items-center whitespace-nowrap">
                                    @if (!empty($row['bukti_pembayaran']))
                                        <img src="{{ asset($row['bukti_pembayaran']) }}" alt="Bukti Pembayaran" class="h-16">
                                    @else
                                        <span class="text-gray-400 text-sm">Belum upload</span>
                                    @endif

                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    {{ $row['jumlah'] }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span @class([
                                        'text-xs font-medium px-2.5 py-0.5 rounded-sm border',
                                        'bg-yellow-100 text-yellow-800 border-yellow-400' =>
                                            $row['status'] === 'belum dibayar',
                                        'bg-blue-100 text-blue-800 border-blue-400' => $row['status'] === 'pending',
                                        'bg-green-100 text-green-800 border-green-400' =>
                                            $row['status'] === 'disetujui',
                                        'bg-red-100 text-red-800 border-red-400' => $row['status'] === 'ditolak',
                                    ])>
                                        {{ ucfirst($row['status']) }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    {{ $row['cicilan_ke'] }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">

                                    @if (!empty($row['bukti_pembayaran']))
                                        <x-action-buttons :viewData="[
                                            'id' => $row['id'],
                                            'fetchEndpoint' => '/pembayaran/' . $row['id'],
                                            'drawerTarget' => 'drawer-control-pembayaran',
                                            'type' => 'bottomSheet',
                                            'title' => 'Detail Pembayaran',
                                            'description' => 'Detail Bukti Pembayaran',
                                        ]" />
                                    @else
                                        <x-action-buttons modalTarget="universal-pembayaran-modal" :editData="[
                                            'id' => $row['id'],
                                            'fetchEndpoint' => '/pembayaran/' . $row['id'],
                                            'updateEndpoint' => '/pembayaran/' . $row['id'],
                                            'act' => 'update',
                                            'title' => 'Upload Pembayaran',
                                        ]" />
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <div class="text-center py-8">
                                <i class="fa-solid fa-users text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">Belum ada data tagihan pembayaran yang masuk</p>
                            </div>
                        @endforelse
                    </tbody>
                </table>

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
                <div class="md:col-span-8 col-span-12">
                    @if (Auth::user()->role === 'admin')
                        <div class="bg-white mb-3 p-4 rounded-lg shadow-md"
                            style="height: 540px; overflow-x: auto; overflow-y: hidden;">
                            <h3 class="text-lg font-semibold mb-4">Chart Perbandingan total peserta kelas</h3>
                            <div style="min-width: 700px;">
                                <canvas id="lineBar" style="height: 450px; width: 100%;"></canvas>
                            </div>
                        </div>
                    @endif

                    <div class="bg-whitep p-4 rounded-lg shadow-md" style="height: 520px;">
                        <h3 class="text-lg font-semibold mb-4">Rata-rata Nilai Per Kelas</h3>
                        <canvas id="stackedBarChart" style="max-height: 450px; width: 100%"></canvas>
                    </div>
                </div>

                <div class="md:col-span-4  col-span-12">
                    @if (Auth::user()->role === 'admin')
                        <div class="shadow-md mb-3 bg-white rounded-lg p-4 md:h-[540px] ">
                            <h3 class="text-lg font-semibold mb-4">Referensi program kelas </h3>
                            <canvas id="pieChart" style="max-height: 450px; width: 100%"></canvas>
                        </div>
                    @endif
                    <div class=" shadow-md bg-white p-4 rounded-lg">
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
                    <x-reusable-table tableId="active-exams" :searchBar="true" :truncate="true" :headers="['No', 'Judul Ujian', 'Waktu Pengerjaan', 'Status', 'Kelas', 'Total Hasil']"
                        :data="$activeExamData" :columns="[
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
                        ]" :showActions="true"
                        :actionButtons="fn($row) => view('components.action-buttons', [
                            'viewData' => [
                                'id' => $row['id'],
                                'fetchEndpoint' => '/dashboard/active-exam/'.$row['id'],
                                'drawerTarget' => 'drawer-detail-active-exam',
                                'type' => 'bottomSheet',
                                'title' => 'Detail Ujian Aktif',
                                'description' => 'Informasi lengkap ujian dan hasil peserta',
                            ],
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


            <x-drawer-layout type="bottomSheet" id="drawer-detail-active-exam" title="Detail Ujian Aktif"
                description="Informasi lengkap ujian dan hasil peserta">

                <div x-data="{
                    examData: null,
                    siswaResults: [],
                }"
                    x-on:drawerDataLoaded.window="
            if ($event.detail.drawerId === 'drawer-detail-active-exam') {
                examData = $event.detail.data
                siswaResults = examData.siswa_results || []
                console.log('Exam data diterima:', examData)
            }
        "
                    class="space-y-6">

                    <template x-if="examData">
                        <div>
                            {{-- Informasi Ujian --}}
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Informasi Ujian</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Judul Ujian:</p>
                                        <p class="font-medium" x-text="examData.judul"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Kelas:</p>
                                        <p class="font-medium" x-text="examData.kelas"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Waktu Pengerjaan:</p>
                                        <p class="font-medium" x-text="examData.waktu_pengerjaan"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status:</p>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                            x-text="examData.status"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Soal:</p>
                                        <p class="font-medium" x-text="(examData.total_soal || 0) + ' soal'"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Peserta:</p>
                                        <p class="font-medium" x-text="siswaResults.length + ' Peserta'"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Rata-rata Nilai:</p>
                                        <p class="font-medium" x-text="examData.rata_rata_nilai || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Hasil Peserta --}}
                            <div class="bg-gray-50 p-4 rounded-lg mt-6">
                                <h3 class="text-lg font-semibold mb-4">Hasil Peserta</h3>
                                <template x-if="siswaResults.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th
                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        No</th>
                                                    <th
                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Peserta</th>
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
                                                <template x-for="(siswa, index) in siswaResults" :key="siswa.id">
                                                    <tr class="hover:bg-gray-50">
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
                                                                :class="{
                                                                    'bg-green-100 text-green-800': siswa.nilai >= 80,
                                                                    'bg-yellow-100 text-yellow-800': siswa.nilai >=
                                                                        70 && siswa.nilai < 80,
                                                                    'bg-red-100 text-red-800': siswa.nilai < 70
                                                                }"
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                                x-text="siswa.nilai"></span>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-green-600 font-medium"
                                                            x-text="siswa.benar"></td>
                                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-red-600 font-medium"
                                                            x-text="siswa.salah"></td>
                                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"
                                                            x-text="siswa.waktu_pengerjaan_siswa"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="siswaResults.length === 0">
                                    <div class="text-center py-8">
                                        <div class="text-gray-400 mb-4">
                                            <i class="fas fa-user-slash text-4xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm">Belum ada peserta yang mengerjakan ujian ini.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </x-drawer-layout>

            @if (Auth::user()->role === 'admin')
                <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center sm:hidden block">Manajemen Batch</h2>

                    <div class="flex items-center justify-end gap-2">
                        <x-fragments.modal-button target="modal-control-batch" variant="emerald" act="create">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Tambah Batch
                        </x-fragments.modal-button>
                    </div>

                    <x-fragments.form-modal id="modal-control-batch" title="Tambah Batch Baru"
                        action="{{ route('batch.store') }}" createTitle="Tambah Batch" editTitle="Edit Batch">
                        <x-fragments.text-field label="Nama Batch" name="nama" placeholder="Masukkan nama batch" required />
                        <div id="select-kelas" class="block">
                            <x-fragments.select-field required label="Pilih Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" />
                        </div>
                        <div class="mt-4">
                            <x-fragments.select-field label="Status" name="status" :options="[
                                'inactive' => 'Inactive',
                                'registration' => 'Registration',
                                'active' => 'Active',
                                'finished' => 'Finished',
                            ]" required />
                        </div>
                        <div>
                            <div class="grid grid-cols-2 gap-3">
                                <x-fragments.text-field type="date" label="Tanggal Mulai" name="tanggal_mulai"
                                    placeholder="Masukkan tanggal mulai batch" />
                                <x-fragments.text-field type="date" label="Tanggal Selesai" name="tanggal_selesai"
                                    placeholder="Masukkan tanggal selesai batch" />
                            </div>
                            <small id="periode-info">Harap set periode batch</small>
                            <div id="kelas-duration-info" class="mt-2 hidden">
                                <small>
                                    Harap set periode batch
                                    <span class="text-blue-500" id="duration-text"></span>
                                </small>
                            </div>
                            <div id="duration-validation" class="mt-2 hidden">
                                <small id="validation-message" class="text-red-500"></small>
                            </div>
                        </div>
                        <div class="hidden" id="card-kelas">
                            <p id="kelas-display" class="text-emerald-400 font-medium"></p>
                            <p class="font-medium text-blue-500">Jika sudah menambahkan batch, kelas tidak dapat dirubah lagi dari
                                batch</p>
                        </div>

                    </x-fragments.form-modal>

                    <x-reusable-table tableId="batch-management" :searchBar="true" :headers="['No', 'Nama Batch', 'Status', 'kelas', 'Jumlah Peserta', 'Periode', 'Dibuat']" :data="$batchData"
                        :columns="[
                            fn($row, $i) => $i + 1,
                            fn($row) => $row['nama'],
                            fn($row) => $row['status_badge'],
                            fn($row) => $row['kelas'],
                            fn($row) => $row['jumlah_peserta'],
                            fn($row) => $row['periode'],
                            fn($row) => $row['created_at'],
                        ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                            'modalTarget' => 'modal-control-batch',
                            'deleteRoute' => route('batch.destroy', $row['id']),
                            'editData' => [
                                'id' => $row['id'],
                                'fetchEndpoint' => '/batch/' . $row['id'],
                                'updateEndpoint' => '/batch/' . $row['id'],
                                'act' => 'update',
                            ],
                            'viewData' => [
                                'id' => $row['id'],
                                'fetchEndpoint' => '/batch/' . $row['id'],
                                'drawerTarget' => 'drawer-detail-batch',
                                'type' => 'bottomSheet',
                                'title' => 'Detail Batch: ' . $row['nama'],
                                'description' => 'Informasi lengkap tentang batch ' . $row['nama'],
                            ],
                            'downloadPdfRoute' => route('batch.downloadPdf', $row['id']),
                            'deleteMessage' =>
                                'Menghapus batch ini juga akan menghapus seluruh data peserta dan ujian terkait. Yakin?',
                        ])" />
                    <x-drawer-layout type="bottomSheet" id="drawer-detail-batch" title="Detail Batch"
                        description="Informasi lengkap batch">
                        <div x-data="{
                            batchData: null,
                            siswaList: [],
                            materiList: [],
                            pembayaranList: [],
                            ujianList: [],
                            chartData: [],
                            chartInstance: null,
                        }"
                            x-on:drawerDataLoaded.window="
                            if ($event.detail.drawerId === 'drawer-detail-batch') {
                                batchData = $event.detail.data
                                siswaList = batchData.siswa || []
                                materiList = batchData.materi || []
                                pembayaranList = batchData.pembayaran || []
                                ujianList = batchData.ujian || []
                                chartData = batchData.chart_data || []
                                console.log('Batch data diterima:', batchData)
                                
                                $nextTick(() => {
                                    if (chartData.length > 0) {
                                        initBatchChart()
                                    }
                                })
                            }
                        "
                            class="space-y-6">

                            {{-- Info Batch --}}
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
                                <template x-if="batchData">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-4" x-text="batchData.nama"></h3>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                                <p class="text-2xl font-bold text-blue-600" x-text="batchData.total_siswa"></p>
                                                <p class="text-xs text-gray-600">Siswa</p>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                                <p class="text-2xl font-bold text-green-600" x-text="batchData.total_materi"></p>
                                                <p class="text-xs text-gray-600">Materi</p>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                                <p class="text-2xl font-bold text-purple-600" x-text="batchData.total_ujian"></p>
                                                <p class="text-xs text-gray-600">Ujian</p>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                                                <p class="text-2xl font-bold text-yellow-600" x-text="batchData.total_pembayaran">
                                                </p>
                                                <p class="text-xs text-gray-600">Pembayaran</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-600">Kelas:</span>
                                                <span class="font-medium ml-2" x-text="batchData.kelas"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Status:</span>
                                                <span class="font-medium ml-2 capitalize" x-text="batchData.status"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Periode:</span>
                                                <span class="font-medium ml-2" x-text="batchData.periode"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Chart Rata-rata Nilai per Ujian --}}
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Rata-rata Nilai per Ujian</h3>
                                <template x-if="chartData.length > 0">
                                    <div class="h-64">
                                        <canvas id="chart-batch-detail"></canvas>
                                    </div>
                                </template>
                                <template x-if="chartData.length === 0">
                                    <div class="text-center py-8">
                                        <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-sm">Belum ada data ujian</p>
                                    </div>
                                </template>
                            </div>

                            {{-- Daftar Peserta --}}
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Daftar Siswa (<span x-text="siswaList.length"></span>)</h3>
                                <template x-if="siswaList.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Nama</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Email</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Rata-rata</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Status</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Magang</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="(siswa, index) in siswaList" :key="siswa.id">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="siswa.nama"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="siswa.email"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="siswa.rata_rata"></td>
                                                        <td class="px-4 py-3 text-sm capitalize" x-text="siswa.status"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="siswa.ikut_magang"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="siswaList.length === 0">
                                    <p class="text-center text-gray-500 py-8">Belum ada siswa</p>
                                </template>
                            </div>

                            {{-- Daftar Materi --}}
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Daftar Materi (<span x-text="materiList.length"></span>)
                                </h3>
                                <template x-if="materiList.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Judul Materi</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Ditambahkan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="(materi, index) in materiList" :key="materi.id">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="materi.judul"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="materi.created_at"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="materiList.length === 0">
                                    <p class="text-center text-gray-500 py-8">Belum ada materi</p>
                                </template>
                            </div>

                            {{-- History Pembayaran --}}
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">History Pembayaran Disetujui (<span
                                        x-text="pembayaranList.length"></span>)</h3>
                                <template x-if="pembayaranList.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Siswa</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Jumlah</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Cicilan Ke</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="(pembayaran, index) in pembayaranList" :key="pembayaran.id">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="pembayaran.siswa"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="pembayaran.jumlah"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="pembayaran.cicilan_ke"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="pembayaran.tanggal"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="pembayaranList.length === 0">
                                    <p class="text-center text-gray-500 py-8">Belum ada pembayaran yang disetujui</p>
                                </template>
                            </div>

                            {{-- Daftar Ujian --}}
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Daftar Ujian (<span x-text="ujianList.length"></span>)</h3>
                                <template x-if="ujianList.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Judul Ujian</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Total Soal</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Total Siswa</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Rata-rata</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="(ujian, index) in ujianList" :key="ujian.id">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="ujian.judul"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="ujian.total_soal"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="ujian.total_siswa"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="ujian.rata_rata"></td>
                                                        <td class="px-4 py-3 text-sm capitalize" x-text="ujian.status"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="ujianList.length === 0">
                                    <p class="text-center text-gray-500 py-8">Belum ada ujian</p>
                                </template>
                            </div>
                        </div>
                    </x-drawer-layout>
                </div>

                <script>
                    const kelasData = @json($kelasData ?? []);
                    let currentMode = 'create';


                    function generateDurationText(kelasId) {
                        if (!kelasData[kelasId]) return '';

                        const kelas = kelasData[kelasId];
                        const namaKelas = kelas.nama;
                        const typeKelas = kelas.type;
                        const durasi = parseInt(kelas.durasi_belajar) + parseInt(kelas.waktu_magang)
                        const durasiBelajar = parseInt(kelas.durasi_belajar) || 0;
                        const waktuMagang = parseInt(kelas.waktu_magang) || 0;

                        if (durasiBelajar === 0) return '';

                        let durationText =
                            `- periode kelas ${namaKelas} ${typeKelas !== null ? `- ${typeKelas}` : ''} sekitar ${durasi} bulan`;

                        if (waktuMagang > 0) {
                            durationText += ` (${durasiBelajar} bulan + ${waktuMagang} bulan magang)`;
                        }

                        return durationText;
                    }

                    function validateDuration(startDate, endDate, expectedDuration, validationElementId) {
                        const validationElement = document.getElementById(validationElementId);
                        const validationMessage = validationElement?.querySelector('#validation-message');

                        if (!startDate || !endDate || !expectedDuration) {
                            validationElement?.classList.add('hidden');
                            return true;
                        }

                        const start = new Date(startDate);
                        const end = new Date(endDate);

                        let months = (end.getFullYear() - start.getFullYear()) * 12;
                        months -= start.getMonth();
                        months += end.getMonth();

                        if (end.getDate() < start.getDate()) {
                            months--;
                        }

                        if (months !== expectedDuration) {
                            validationMessage.textContent =
                                `Durasi tidak valid! Seharusnya ${expectedDuration} bulan, tapi yang diinput ${months} bulan`;
                            validationMessage.className = 'text-red-500';
                            validationElement?.classList.remove('hidden');
                            return false;
                        } else {
                            validationMessage.textContent = `✓ Durasi valid: ${months} bulan`;
                            validationMessage.className = 'text-green-500';
                            validationElement?.classList.remove('hidden');
                            return true;
                        }
                    }

                    function initBatchChart() {
                        const ctx = document.getElementById('chart-batch-detail');
                        if (!ctx) return;

                        const existingChart = Chart.getChart(ctx);
                        if (existingChart) {
                            existingChart.destroy();
                        }

                        const chartData = Alpine.$data(ctx.closest('[x-data]')).chartData;

                        // fungsi buat warna random transparan
                        const getRandomColor = () => {
                            const r = Math.floor(Math.random() * 255);
                            const g = Math.floor(Math.random() * 255);
                            const b = Math.floor(Math.random() * 255);
                            return `rgba(${r}, ${g}, ${b}, 0.5)`; // transparan
                        };

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: chartData.map(item => item.judul),
                                datasets: [{
                                    label: 'Rata-rata Nilai',
                                    data: chartData.map(item => item.rata_rata),
                                    backgroundColor: chartData.map(() => getRandomColor()),
                                    borderColor: 'rgba(0, 0, 0, 0.2)', // border bawaan lembut
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



                    document.addEventListener('DOMContentLoaded', function() {
                        const statusSelect = document.querySelector('#modal-control-batch select[name="status"]');
                        const kelasSelect = document.querySelector('#modal-control-batch select[name="kelas_id"]');
                        const periodeBatch = document.querySelector('#periode-batch');
                        const kelasDurationInfo = document.querySelector('#kelas-duration-info');
                        const durationTextSpan = document.querySelector('#duration-text');
                        const periodeInfo = document.querySelector('#periode-info');
                        const tanggalMulaiInput = document.querySelector('#modal-control-batch input[name="tanggal_mulai"]');
                        const tanggalSelesaiInput = document.querySelector(
                            '#modal-control-batch input[name="tanggal_selesai"]');
                        const kelasDisplayCard = document.querySelector('#card-kelas');
                        const kelasDisplayText = document.querySelector('#kelas-display');
                        const selectKelasDiv = document.querySelector('#select-kelas');
                        const formElement = document.querySelector('#modal-control-batch form');



                        function resetModalState() {

                            document.getElementById('duration-validation')?.classList.add('hidden');
                            tanggalMulaiInput?.removeAttribute('required');
                            tanggalSelesaiInput?.removeAttribute('required');
                            kelasDurationInfo?.classList.add('hidden');
                            periodeInfo?.classList.remove('hidden');
                        }

                        document.addEventListener('modalCreate', function(e) {
                            if (e.detail.modalId === 'modal-control-batch') {
                                currentMode = 'create';
                                resetModalState();

                                if (kelasSelect) {
                                    kelasSelect.disabled = false;
                                    kelasSelect.style.backgroundColor = '';
                                    kelasSelect.style.cursor = '';
                                }
                                if (selectKelasDiv) selectKelasDiv.classList.remove('hidden');
                                if (kelasDisplayCard) kelasDisplayCard.classList.add('hidden');

                                statusSelect?.removeAttribute('data-original-value');

                                updateDurationDisplay();
                                validateAddBatch();
                            }
                        });
                        const lineBar = document.getElementById('lineBar').getContext('2d');
                        const pieChart = document.getElementById('pieChart').getContext('2d');
                        const chartData = @json($chartData);
                        const kelasChartData = @json($kelasChartData ?? []);
                        const sumberChartData = @json($sumberChartData ?? []);

                        let gradientLine = null;
                        let gradientFill = null;
                        let width, height;

                        function getGradient(ctx, chartArea, type = 'line') {
                            const chartWidth = chartArea.right - chartArea.left;
                            const chartHeight = chartArea.bottom - chartArea.top;

                            if (!gradientLine || width !== chartWidth || height !== chartHeight) {
                                width = chartWidth;
                                height = chartHeight;

                                gradientLine = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                gradientLine.addColorStop(0, '#ef4444');
                                gradientLine.addColorStop(0.5, '#14b8a6');
                                gradientLine.addColorStop(1, '#10b981');

                                gradientFill = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                gradientFill.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
                                gradientFill.addColorStop(0.5, 'rgba(20, 184, 166, 0.20)');
                                gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0.5)');
                            }

                            return type === 'line' ? gradientLine : gradientFill;
                        }

                        const processedData = {
                            labels: chartData.labels,
                            datasets: chartData.datasets.map(dataset => {
                                const newDataset = {
                                    ...dataset
                                };

                                if (dataset.borderColor === 'gradient') {
                                    newDataset.borderColor = function(context) {
                                        const chart = context.chart;
                                        const {
                                            ctx,
                                            chartArea
                                        } = chart;
                                        if (!chartArea) return '#14b8a6';
                                        return getGradient(ctx, chartArea, 'line');
                                    };
                                }

                                if (dataset.backgroundColor === 'gradient-fill') {
                                    newDataset.backgroundColor = function(context) {
                                        const chart = context.chart;
                                        const {
                                            ctx,
                                            chartArea
                                        } = chart;
                                        if (!chartArea) return 'rgba(20, 184, 166, 0.1)';
                                        return getGradient(ctx, chartArea, 'fill');
                                    };
                                }

                                return newDataset;
                            })
                        };

                        new Chart(lineBar, {
                            type: 'bar',
                            data: {
                                labels: kelasChartData.labels || [],
                                datasets: [{
                                        label: 'Total Peserta',
                                        data: kelasChartData.counts || [],
                                        borderWidth: 2,
                                        backgroundColor: function(context) {
                                            const chart = context.chart;
                                            const {
                                                ctx,
                                                chartArea
                                            } = chart;
                                            if (!chartArea) return '#14b8a6';
                                            return getGradient(ctx, chartArea, 'fill');
                                        },
                                        borderColor: function(context) {
                                            const chart = context.chart;
                                            const {
                                                ctx,
                                                chartArea
                                            } = chart;
                                            if (!chartArea) return '#14b8a6';
                                            return getGradient(ctx, chartArea, 'line');
                                        },
                                        borderRadius: 6,
                                        borderSkipped: false
                                    },
                                    {
                                        label: 'Garis Total',
                                        data: kelasChartData.counts || [],
                                        type: 'line',
                                        borderColor: function(context) {
                                            const chart = context.chart;
                                            const {
                                                ctx,
                                                chartArea
                                            } = chart;
                                            if (!chartArea) return '#14b8a6';
                                            return getGradient(ctx, chartArea, 'line');
                                        },
                                        backgroundColor: 'transparent',
                                        tension: 0.3,
                                        pointRadius: 4,
                                        pointBackgroundColor: '#14b8a6'
                                    }
                                ]
                            },
                            options: {
                                responsive: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            usePointStyle: true
                                        }
                                    }
                                }
                            }
                        });


                        const pieColors = [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(201, 203, 207, 0.6)'
                        ];

                        new Chart(pieChart, {
                            type: 'pie',
                            data: {
                                labels: sumberChartData.labels || [],
                                datasets: [{
                                    data: sumberChartData.counts || [],
                                    backgroundColor: pieColors,
                                    borderColor: pieColors.map(c => c.replace('0.6', '1')),
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });


                        document.addEventListener('modalUpdate', function(e) {
                            if (e.detail.modalId === 'modal-control-batch') {
                                currentMode = 'update';
                                const batchData = e.detail.data;

                                resetModalState();

                                document.querySelector('input[name="nama"]').value = batchData.nama || '';
                                document.querySelector('select[name="status"]').value = batchData.status || '';
                                document.querySelector('input[name="tanggal_mulai"]').value = batchData.tanggal_mulai ||
                                    '';
                                document.querySelector('input[name="tanggal_selesai"]').value = batchData
                                    .tanggal_selesai || '';
                                document.querySelector('select[name="kelas_id"]').value = batchData.kelas_id || '';

                                if (kelasSelect) {
                                    if (kelasDisplayCard) kelasDisplayCard.classList.remove('hidden');
                                    if (selectKelasDiv) {
                                        selectKelasDiv.classList.add('hidden')
                                        kelasSelect.removeAttribute('required');
                                    };

                                    if (kelasDisplayText) kelasDisplayText.textContent = `Kelas: ${batchData.kelas}`;
                                }

                                statusSelect?.setAttribute('data-original-value', batchData.status);

                                if (batchData.status === 'active' || batchData.status === 'registration') {

                                    tanggalMulaiInput?.setAttribute('required', true);
                                    tanggalSelesaiInput?.setAttribute('required', true);
                                }

                                updateDurationDisplay();
                                validateAddBatch();
                            }
                        });

                        function updateDurationDisplay() {
                            const selectedStatus = statusSelect?.value;
                            const selectedKelas = kelasSelect?.value;

                            if (selectedStatus === 'active' && selectedKelas) {
                                const durationText = generateDurationText(selectedKelas);
                                if (durationText) {
                                    durationTextSpan.textContent = durationText;
                                    kelasDurationInfo.classList.remove('hidden');
                                    periodeInfo.classList.add('hidden');
                                } else {
                                    kelasDurationInfo.classList.add('hidden');
                                    periodeInfo.classList.remove('hidden');
                                }
                            } else if (selectedStatus === 'active') {
                                kelasDurationInfo.classList.add('hidden');
                                periodeInfo.classList.remove('hidden');
                            } else {
                                kelasDurationInfo.classList.add('hidden');
                                periodeInfo.classList.remove('hidden');
                                document.getElementById('duration-validation')?.classList.add('hidden');
                            }
                        }

                        function validateAddBatch() {
                            const selectedKelas = kelasSelect?.value;
                            const startDate = tanggalMulaiInput?.value;
                            const endDate = tanggalSelesaiInput?.value;

                            if (selectedKelas && kelasData[selectedKelas] && startDate && endDate) {
                                const expectedDuration = parseInt(kelasData[selectedKelas].durasi_belajar) +
                                    parseInt(kelasData[selectedKelas].waktu_magang);
                                validateDuration(startDate, endDate, expectedDuration, 'duration-validation');
                            } else {
                                document.getElementById('duration-validation')?.classList.add('hidden');
                            }
                        }


                        statusSelect?.addEventListener('change', function(e) {
                            const originalValue = this.getAttribute('data-original-value');


                            if (this.value === 'finished' && currentMode === 'update' && originalValue !== 'finished') {
                                Swal.fire({
                                    title: 'Apakah Anda yakin?',
                                    text: 'Jika batch diubah ke finished, semua peserta akan menjadi alumni.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, ubah ke Finished!',
                                    cancelButtonText: 'Batal',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (!result.isConfirmed) {
                                        statusSelect.value = originalValue || 'active';
                                        handleStatusChange();
                                    }
                                });
                            }

                            handleStatusChange();
                        });


                        function handleStatusChange() {
                            if (statusSelect.value === 'active') {

                                tanggalMulaiInput?.setAttribute('required', true);
                                tanggalSelesaiInput?.setAttribute('required', true);
                            } else {

                                tanggalMulaiInput?.removeAttribute('required');
                                tanggalSelesaiInput?.removeAttribute('required');
                                document.getElementById('duration-validation')?.classList.add('hidden');
                            }

                            updateDurationDisplay();
                            validateAddBatch();
                        }

                        kelasSelect?.addEventListener('change', function() {
                            updateDurationDisplay();
                            validateAddBatch();
                        });

                        tanggalMulaiInput?.addEventListener('change', validateAddBatch);
                        tanggalSelesaiInput?.addEventListener('change', validateAddBatch);

                    });
                </script>
            @endif



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('stackedBarChart').getContext('2d');
                    const chartData = @json($chartData);


                    let gradientLine = null;
                    let gradientFill = null;
                    let width, height;

                    function getGradient(ctx, chartArea, type = 'line') {
                        const chartWidth = chartArea.right - chartArea.left;
                        const chartHeight = chartArea.bottom - chartArea.top;

                        if (!gradientLine || width !== chartWidth || height !== chartHeight) {
                            width = chartWidth;
                            height = chartHeight;

                            gradientLine = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradientLine.addColorStop(0, '#ef4444');
                            gradientLine.addColorStop(0.5, '#14b8a6');
                            gradientLine.addColorStop(1, '#10b981');

                            gradientFill = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradientFill.addColorStop(0, 'rgba(239, 68, 68, 0.1)');
                            gradientFill.addColorStop(0.5, 'rgba(20, 184, 166, 0.15)');
                            gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0.2)');
                        }

                        return type === 'line' ? gradientLine : gradientFill;
                    }

                    const processedData = {
                        labels: chartData.labels,
                        datasets: chartData.datasets.map(dataset => {
                            const newDataset = {
                                ...dataset
                            };

                            if (dataset.borderColor === 'gradient') {
                                newDataset.borderColor = function(context) {
                                    const chart = context.chart;
                                    const {
                                        ctx,
                                        chartArea
                                    } = chart;
                                    if (!chartArea) return '#14b8a6';
                                    return getGradient(ctx, chartArea, 'line');
                                };
                            }

                            if (dataset.backgroundColor === 'gradient-fill') {
                                newDataset.backgroundColor = function(context) {
                                    const chart = context.chart;
                                    const {
                                        ctx,
                                        chartArea
                                    } = chart;
                                    if (!chartArea) return 'rgba(20, 184, 166, 0.1)'; // Fallback color
                                    return getGradient(ctx, chartArea, 'fill');
                                };
                            }

                            return newDataset;
                        })
                    };

                    new Chart(ctx, {
                        type: 'line',
                        data: processedData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Rata-rata Nilai Per Kelas',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: {
                                        top: 10,
                                        bottom: 20
                                    }
                                },
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        font: {
                                            size: 12,
                                            family: "'Inter', -apple-system, sans-serif"
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += context.parsed.y.toFixed(1);
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Kelas',
                                        font: {
                                            size: 13,
                                            weight: 'bold'
                                        }
                                    },
                                    ticks: {
                                        font: {
                                            size: 10
                                        },
                                        maxRotation: 90,
                                        minRotation: 45,
                                        autoSkip: false,
                                        callback: function(value, index, ticks) {
                                            const label = this.getLabelForValue(value);
                                            const maxLength = 20;

                                            if (label.length > maxLength) {
                                                const words = label.split(' ');
                                                const lines = [];
                                                let currentLine = '';

                                                words.forEach(word => {
                                                    if ((currentLine + ' ' + word).trim().length <=
                                                        maxLength) {
                                                        currentLine = (currentLine + ' ' + word).trim();
                                                    } else {
                                                        if (currentLine) lines.push(currentLine);
                                                        currentLine = word;
                                                    }
                                                });

                                                if (currentLine) lines.push(currentLine);
                                                return lines;
                                            }

                                            return label;
                                        }
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: true,
                                        borderColor: '#e5e7eb'
                                    },

                                },
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'Rata-rata Nilai',
                                        font: {
                                            size: 13,
                                            weight: 'bold'
                                        }
                                    },
                                    ticks: {
                                        stepSize: 10,
                                        callback: function(value) {
                                            return value.toFixed(0);
                                        },
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: true,
                                        borderColor: '#e5e7eb'
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
