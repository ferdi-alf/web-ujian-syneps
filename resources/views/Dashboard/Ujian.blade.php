@extends('layouts.dashboard-layouts')

@section('content')
    <div class="bg-white rounded-lg px-5 py-5">
        <h3 class="text-lg text-end font-bold">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
        </h3>

        <div class="grid grid-cols-12 gap-3 mt-8">
            <div class="col-span-12 md:block hidden md:col-span-3">
                <div id="active-exam-content">
                    @if ($ujians->count() > 0)
                        @php
                            $activeUjian = $ujians->first();
                            $isCompleted = $activeUjian->hasilUjians->where('siswa_id', Auth::id())->isNotEmpty();
                            $totalSoal = $activeUjian->soals->count();
                        @endphp

                        <span
                            class="bg-teal-100 text-teal-800 text-sm font-medium p-2 rounded-lg border border-teal-400 exam-kelas">
                            {{ $activeUjian->kelas->nama ?? 'Fullstack Web Developer' }}
                        </span>
                        <h1 class="text-3xl mt-8 font-bold exam-title" id="exam-title">
                            {{ $activeUjian->judul }}
                        </h1>

                        <div class="flex gap-3 mt-10 items-center">
                            <i class="fa-regular fa-user text-teal-500 text-2xl leading-none"></i>
                            <p class="font-medium text-lg">{{ Auth::user()->nama_lengkap }}</p>
                        </div>
                        <div class="grid pl-9 grid-cols-[auto_1fr] items-center mt-3">
                            <div></div>
                            <div class="border-t border-2 border-gray-500 w-24"></div>
                        </div>
                        <div class="grid grid-cols-[auto_1fr] gap-2 mt-3 items-center">
                            <i class="fa-regular fa-clipboard text-teal-400 text-2xl leading-none h-6 w-6 text-center"></i>
                            <p class="font-medium text-lg exam-details" id="exam-details">{{ $totalSoal }} Soal |
                                {{ $activeUjian->waktu }} menit</p>
                        </div>
                        <div class="mt-8" id="exam-button">
                            @if ($isCompleted)
                                <div class="flex items-center gap-2 text-green-600 font-medium mb-3 justify-center">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    <span>Ujian Selesai</span>
                                </div>
                                <button type="button" data-ujian-id="{{ $activeUjian->id }}" data-ujian-completed="true"
                                    class="bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg w-full text-sm py-3 px-5 shadow-md transition-all focus:ring-4 focus:ring-green-300 exam-button">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Hasil
                                </button>
                            @else
                                <button type="button" data-ujian-id="{{ $activeUjian->id }}" data-ujian-completed="false"
                                    class="text-white exam-button w-full mt-8 cursor-pointer focus:ring-4 focus:outline-none font-medium rounded-lg text-sm py-3 px-5 shadow-md transition-all bg-gradient-to-r from-blue-500 to-blue-700 hover:!bg-gradient-to-r hover:!from-blue-600 hover:!to-blue-800 focus:ring-blue-300">
                                    <i class="fas fa-play mr-2"></i>
                                    Mulai Ujian
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-span-12 md:col-span-9 flex justify-center">
                @php
                    $activeUjian = $ujians->first();
                    $imageNumber = ($activeUjian->id % 10) + 1;
                @endphp
                <img class="exam-image" src="{{ asset('images/background/bg-' . $imageNumber . '.png') }}"
                    alt="Background Ujian {{ $activeUjian->judul }}">
            </div>

            <div class="col-span-12 md:hidden block md:col-span-3">
                <div id="active-exam-content">
                    @if ($ujians->count() > 0)
                        @php
                            $activeUjian = $ujians->first();
                            $isCompleted = $activeUjian->hasilUjians->where('siswa_id', Auth::id())->isNotEmpty();
                            $totalSoal = $activeUjian->soals->count();
                        @endphp

                        <span
                            class="bg-teal-100 text-teal-800 text-sm font-medium p-2 rounded-lg border border-teal-400 exam-kelas">
                            {{ $activeUjian->kelas->nama ?? 'Fullstack Web Developer' }}
                        </span>
                        <h1 class="text-2xl mt-8 font-bold exam-title" id="exam-title">
                            {{ $activeUjian->judul }}
                        </h1>

                        <div class="flex gap-3 mt-10 items-center">
                            <i class="fa-regular fa-user text-teal-500 text-2xl leading-none"></i>
                            <p class="font-medium text-lg">{{ Auth::user()->nama_lengkap }}</p>
                        </div>
                        <div class="grid pl-9 grid-cols-[auto_1fr] items-center mt-3">
                            <div></div>
                            <div class="border-t border-2 border-gray-500 w-24"></div>
                        </div>
                        <div class="grid grid-cols-[auto_1fr] gap-2 mt-3 items-center">
                            <i class="fa-regular fa-clipboard text-teal-400 text-2xl leading-none h-6 w-6 text-center"></i>
                            <p class="font-medium text-lg exam-details" id="exam-details">{{ $totalSoal }} Soal |
                                {{ $activeUjian->waktu }} menit</p>
                        </div>

                        <div class="mt-8" id="exam-button">
                            @if ($isCompleted)
                                <div class="flex items-center gap-2 text-green-600 font-medium mb-3 justify-center">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    <span>Ujian Selesai</span>
                                </div>
                                <button type="button" data-ujian-id="{{ $activeUjian->id }}" data-ujian-completed="true"
                                    class="bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg w-full text-sm py-3 px-5 shadow-md transition-all focus:ring-4 focus:ring-green-300 exam-button">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Hasil
                                </button>
                            @else
                                <button type="button" data-ujian-id="{{ $activeUjian->id }}" data-ujian-completed="false"
                                    class="text-white exam-button w-full mt-8 cursor-pointer focus:ring-4 focus:outline-none font-medium rounded-lg text-sm py-3 px-5 shadow-md transition-all bg-gradient-to-r from-blue-500 to-blue-700 hover:!bg-gradient-to-r hover:!from-blue-600 hover:!to-blue-800 focus:ring-blue-300">
                                    <i class="fas fa-play mr-2"></i>
                                    Mulai Ujian
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-3">
            <p class="font-bold md:px-5 px-3">Ujian Lainnya</p>
            <div class="relative mt-3">
                <div class="overflow-x-auto scrollbar-hide">
                    <div class="flex space-x-5 md:px-5 px-3 py-4" id="carousel-container" style="width: max-content;">
                        @foreach ($ujians as $index => $ujian)
                            @php
                                $isCompleted = $ujian->hasilUjians->where('siswa_id', Auth::id())->isNotEmpty();
                                $isActive = $index === 0;
                                $colors = [
                                    [
                                        'gradient' => 'from-emerald-300 to-emerald-500',
                                        'border' => 'border-emerald-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-red-300 to-red-500',
                                        'border' => 'border-red-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-amber-300 to-amber-500',
                                        'border' => 'border-amber-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-cyan-300 to-cyan-500',
                                        'border' => 'border-cyan-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-purple-300 to-purple-500',
                                        'border' => 'border-purple-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-pink-300 to-pink-500',
                                        'border' => 'border-pink-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-fuchsia-300 to-fuchsia-500',
                                        'border' => 'border-fuchsia-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-cyan-300 to-cyan-500',
                                        'border' => 'border-cyan-500',
                                        'text' => 'text-white',
                                    ],
                                    [
                                        'gradient' => 'from-yellow-300 to-yellow-500',
                                        'border' => 'border-yellow-500',
                                        'text' => 'text-white',
                                    ],
                                ];
                                $colorIndex = $index % count($colors);
                                $color = $colors[$colorIndex];
                                $words = explode(' ', $ujian->judul);
                                $initials = '';
                                foreach (array_slice($words, 0, 2) as $word) {
                                    $initials .= strtoupper(substr($word, 0, 1));
                                }
                                if (strlen($initials) < 2) {
                                    $initials = strtoupper(substr($ujian->judul, 0, 2));
                                }
                                $formattedDate = \Carbon\Carbon::parse($ujian->created_at)
                                    ->locale('id')
                                    ->isoFormat('dddd, DD MMMM YYYY');
                                $ujianImageNumber = ($ujian->id % 10) + 1;
                            @endphp

                            <div class="flex-shrink-0 w-96 sm:w-72 lg:w-80">
                                <div class="exam-card cursor-pointer rounded-lg shadow-lg px-3 py-7 flex gap-3 transform transition-all duration-300 hover:scale-105 hover:shadow-xl
                                    {{ $isActive ? 'bg-gradient-to-bl ' . $color['gradient'] . ' ' . $color['text'] : 'bg-white border ' . $color['border'] . ' text-gray-700 hover:shadow-lg' }}"
                                    data-ujian-id="{{ $ujian->id }}" data-ujian-title="{{ $ujian->judul }}"
                                    data-ujian-kelas="{{ $ujian->kelas->nama ?? 'Fullstack Web Developer' }}"
                                    data-ujian-soal="{{ $ujian->soals->count() }}" data-ujian-waktu="{{ $ujian->waktu }}"
                                    data-ujian-date="{{ $formattedDate }}"
                                    data-ujian-completed="{{ $isCompleted ? 'true' : 'false' }}"
                                    data-ujian-image="{{ $ujianImageNumber }}"
                                    data-color-gradient="{{ $color['gradient'] }}"
                                    data-color-border="{{ $color['border'] }}" data-color-text="{{ $color['text'] }}">
                                    <div
                                        class="w-16 h-16 font-bold rounded-lg bg-white/30 backdrop-blur-md shadow-inner flex justify-center items-center text-lg">
                                        {{ $initials }}
                                    </div>
                                    <div class="flex flex-col justify-around">
                                        <h2 class="font-semibold text-lg">{{ Str::limit($ujian->judul, 25) }}</h2>
                                        <p class="font-light text-sm">{{ $formattedDate }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .exam-card {
            min-height: 120px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            will-change: transform;
        }

        .exam-card:hover {
            transform: scale(1.02);
        }

        @media (max-width: 768px) {
            #carousel-container {
                scroll-behavior: smooth;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('carousel-container');
            let startX = 0;
            let scrollLeft = 0;
            let isDragging = false;
            carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].pageX - carousel.offsetLeft;
                scrollLeft = carousel.scrollLeft;
                isDragging = true;
            });

            carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.touches[0].pageX - carousel.offsetLeft;
                const walk = (x - startX) * 2;
                carousel.scrollLeft = scrollLeft - walk;
            });

            carousel.addEventListener('touchend', () => {
                isDragging = false;
            });

            carousel.addEventListener('mousedown', (e) => {
                isDragging = true;
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = carousel.scrollLeft;
                carousel.style.cursor = 'grabbing';
            });

            carousel.addEventListener('mouseleave', () => {
                isDragging = false;
                carousel.style.cursor = 'grab';
            });

            carousel.addEventListener('mouseup', () => {
                isDragging = false;
                carousel.style.cursor = 'grab';
            });

            carousel.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                const walk = (x - startX) * 2;
                carousel.scrollLeft = scrollLeft - walk;
            });
            document.querySelectorAll('.exam-card').forEach((card) => {
                card.addEventListener('click', function() {
                    if (isDragging) return;
                    document.querySelectorAll('.exam-card').forEach((c) => {
                        c.classList.remove('bg-gradient-to-bl', ...(c.dataset.colorGradient
                            .split(' ')), c.dataset.colorText);
                        c.classList.add('bg-white', 'text-gray-700', 'border', c.dataset
                            .colorBorder);
                    });
                    this.classList.remove('bg-white', 'text-gray-700', 'border', this.dataset
                        .colorBorder);
                    this.classList.add('bg-gradient-to-bl', ...(this.dataset.colorGradient.split(
                        ' ')), this.dataset.colorText);
                    updateMainContent({
                        id: this.dataset.ujianId,
                        title: this.dataset.ujianTitle,
                        kelas: this.dataset.ujianKelas,
                        soal: this.dataset.ujianSoal,
                        waktu: this.dataset.ujianWaktu,
                        completed: this.dataset.ujianCompleted === 'true',
                        image: this.dataset.ujianImage
                    });
                });
            });

            function updateMainContent(ujian) {
                document.querySelectorAll('.exam-title').forEach(el => el.textContent = ujian.title);
                document.querySelectorAll('.exam-details').forEach(el => el.textContent =
                    `${ujian.soal} Soal | ${ujian.waktu} menit`);
                document.querySelectorAll('.exam-kelas').forEach(el => el.textContent = ujian.kelas);
                document.querySelectorAll('.exam-image').forEach(el => el.src =
                    `/images/background/bg-${ujian.image}.png`);
                document.querySelectorAll('#exam-button').forEach(container => {
                    if (ujian.completed) {
                        container.innerHTML = `
                           <div class="flex items-center gap-2 text-green-600 font-medium mb-3 justify-center">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    <span>Ujian Selesai</span>
                                </div>
                                <button type="button" 
                                    class="bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg w-full text-sm py-3 px-5 shadow-md transition-all focus:ring-4 focus:ring-green-300 exam-button">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Hasil
                               </button>
                        `;
                    } else {
                        container.innerHTML = `
                             <button type="button"data-ujian-id="${ujian.id}" data-ujian-completed="false"
                                    class="text-white exam-button w-full mt-8 cursor-pointer focus:ring-4 focus:outline-none font-medium rounded-lg text-sm py-3 px-5 shadow-md transition-all bg-gradient-to-r from-blue-500 to-blue-700 hover:!bg-gradient-to-r hover:!from-blue-600 hover:!to-blue-800 focus:ring-blue-300">
                                    <i class="fas fa-play mr-2"></i>
                                    Mulai Ujian
                            </button>
                        `;
                    }
                });
            }

            // Handle button click for redirect
            document.querySelectorAll('#exam-button').forEach(container => {
                container.addEventListener('click', function(e) {
                    const button = e.target.closest('.exam-button');
                    if (!button) return;

                    const ujianId = button.dataset.ujianId;
                    const isCompleted = button.dataset.ujianCompleted === 'true';
                    const ujianTitle = document.querySelector('.exam-title')?.textContent ||
                        'ujian';
                    const ujianSlug = ujianTitle.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');

                    const path = isCompleted ? 'selesai' : 'mulai';
                    window.location.href = `/ujian/${ujianSlug}/${path}`;
                });
            });
        });
    </script>
@endsection
