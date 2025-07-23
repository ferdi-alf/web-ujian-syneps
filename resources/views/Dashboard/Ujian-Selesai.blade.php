@extends('layouts.dashboard-layouts')


@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header Result -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="text-center">
                <div class="mb-4">
                    @if ($hasil->nilai >= 80)
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-green-600 mb-2">Selamat! 🎉</h1>
                        <p class="text-gray-600">Anda telah menyelesaikan ujian dengan baik</p>
                    @elseif($hasil->nilai >= 60)
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-yellow-600 mb-2">Cukup Baik 👍</h1>
                        <p class="text-gray-600">Anda lulus ujian ini</p>
                    @else
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-red-600 mb-2">Perlu Belajar Lagi 📚</h1>
                        <p class="text-gray-600">Jangan menyerah, terus semangat belajar!</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Hasil -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Info Ujian -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Detail Ujian</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Nama Ujian:</span>
                        <span class="font-semibold">{{ $ujian->judul }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Kelas:</span>
                        <span class="font-semibold">{{ $ujian->kelas->nama ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Soal:</span>
                        <span class="font-semibold">{{ $ujian->soals->count() }} soal</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Waktu Pengerjaan:</span>
                        <span class="font-semibold">{{ gmdate('H:i:s', $hasil->waktu_pengerjaan) }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistik Hasil -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Hasil Anda</h2>
                <div class="space-y-4">
                    <!-- Nilai -->
                    <div
                        class="text-center p-4 rounded-lg 
                    @if ($hasil->nilai >= 80) bg-green-50 border border-green-200
                    @elseif($hasil->nilai >= 60) bg-yellow-50 border border-yellow-200
                    @else bg-red-50 border border-red-200 @endif">
                        <div
                            class="text-4xl font-bold 
                        @if ($hasil->nilai >= 80) text-green-600
                        @elseif($hasil->nilai >= 60) text-yellow-600
                        @else text-red-600 @endif">
                            {{ $hasil->nilai }}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">Nilai Akhir</div>
                    </div>

                    <!-- Detail Jawaban -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $hasil->jumlah_benar }}</div>
                            <div class="text-sm text-gray-600">Benar</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $hasil->jumlah_salah }}</div>
                            <div class="text-sm text-gray-600">Salah</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300
                        @if ($hasil->nilai >= 80) bg-green-500
                        @elseif($hasil->nilai >= 60) bg-yellow-500
                        @else bg-red-500 @endif"
                            style="width: {{ $hasil->nilai }}%">
                        </div>
                    </div>
                    <div class="text-center text-sm text-gray-600">
                        {{ $hasil->nilai }}% dari 100%
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Jawaban (Opsional) -->
        @if ($jawabanDetails && $jawabanDetails->count() > 0)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Review Jawaban</h2>
                    <button id="toggleReview"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        Tampilkan Detail
                    </button>
                </div>

                <div id="reviewContent" class="hidden">
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach ($jawabanDetails as $index => $jawaban)
                            <div
                                class="border rounded-lg p-4 
                    @if ($jawaban->benar) bg-green-50 border-green-200 
                    @else bg-red-50 border-red-200 @endif">

                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="font-semibold text-gray-800">Soal {{ $index + 1 }}</h3>
                                    <span
                                        class="px-2 py-1 rounded text-xs font-semibold
                            @if ($jawaban->benar) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                                        {{ $jawaban->benar ? 'BENAR' : 'SALAH' }}
                                    </span>
                                </div>

                                <p class="text-gray-700 mb-3">{{ $jawaban->soal->soal }}</p>

                                <div class="space-y-2">
                                    @foreach ($jawaban->soal->jawabans as $option)
                                        <div
                                            class="flex items-center space-x-2 p-2 rounded
                            @if ($option->benar) bg-green-100 border border-green-300
                            @elseif($jawaban->jawaban_pilihan === $option->pilihan && !$option->benar) bg-red-100 border border-red-300
                            @else bg-gray-50 @endif">

                                            <span class="font-semibold">{{ $option->pilihan }}.</span>
                                            <span>{{ $option->teks }}</span>

                                            @if ($option->benar)
                                                <span class="ml-auto text-green-600 text-sm font-semibold">✓ Jawaban
                                                    Benar</span>
                                            @elseif($jawaban->jawaban_pilihan === $option->pilihan && !$option->benar)
                                                <span class="ml-auto text-red-600 text-sm font-semibold">✗ Jawaban
                                                    Anda</span>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if (!$jawaban->jawaban_pilihan)
                                        <div class="text-red-600 text-sm italic">Tidak dijawab</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('ujian.index') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                Kembali ke Daftar Ujian
            </a>


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleReview');
            const reviewContent = document.getElementById('reviewContent');

            if (toggleBtn && reviewContent) {
                toggleBtn.addEventListener('click', function() {
                    if (reviewContent.classList.contains('hidden')) {
                        reviewContent.classList.remove('hidden');
                        toggleBtn.textContent = 'Sembunyikan Detail';
                    } else {
                        reviewContent.classList.add('hidden');
                        toggleBtn.textContent = 'Tampilkan Detail';
                    }
                });
            }

            // Auto scroll to top
            window.scrollTo(0, 0);
        });
    </script>
@endsection
