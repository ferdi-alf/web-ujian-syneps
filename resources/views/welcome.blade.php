@extends('layouts.landing-layout')

@section('title', 'Home')

@section('content')
    {{-- Enhanced Hero Section - MEMPERTAHANKAN SEMUA ORIGINAL --}}
    <section class="relative bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 lg:pt-28 lg:pb-24">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    {{-- ORIGINAL Badge + Enhancement --}}
                    <div class="inline-flex items-center px-4 py-1 bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 
                        rounded-full mb-5 border border-white shadow gap-2 hover:shadow-lg transition-shadow duration-300">
                        <img src="{{ asset('images/logo.png') }}" alt="Sydemy Logo" class="w-5 h-5">
                        <span class="text-xs font-semibold text-gray-900">Sydemy</span>
                        {{-- Enhancement: Pulse indicator --}}
                        <span class="w-2 h-2 bg-emerald-600 rounded-full animate-pulse"></span>
                    </div>

                    {{-- ORIGINAL Heading + Minor Enhancement --}}
                    <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight text-gray-900">
                        Belajar Terarah, <br>
                        <span class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">
                            Raih Karier Impianmu
                        </span>
                    </h1>

                    {{-- ORIGINAL Description --}}
                    <p class="mt-5 text-lg text-gray-600">
                        Syneps Academy adalah platform pembelajaran interaktif dengan manajemen batch, ujian online,
                        leaderboard, dan komunitas alumni untuk membantumu berkembang.
                    </p>

                    {{-- ORIGINAL CTA Button + Minor Enhancement --}}
                    <div class="mt-6">
                        <a href="{{ route('kelas.index') }}"
                            class="group relative px-6 py-3 rounded-lg bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 
                                text-gray-900 font-semibold shadow border border-white
                                transition-all duration-300 ease-out
                                hover:scale-105 hover:shadow-xl hover:shadow-emerald-200">
                            <span class="relative z-10">Lihat Kelas</span>
                            <span class="absolute inset-0 rounded-lg bg-white opacity-0 group-hover:opacity-10 transition"></span>
                        </a>
                    </div>
                </div>

                {{-- ORIGINAL Hero Image --}}
                <div data-aos="fade-left" class="flex justify-center">
                    <img src="{{ asset('images/hero.png') }}" alt="Syneps Academy Logo"
                        class="w-full max-w-sm drop-shadow-lg border border-white rounded-xl">
                </div>
            </div>
        </div>
    </section>

    {{-- ORIGINAL Program Unggulan Section - TETAP SAMA --}}
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-sm font-semibold tracking-wide text-emerald-500 uppercase">
                    Program Unggulan
                </h2>
                <h3 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">
                    Kami Membantu Menemukan <br>
                    <span class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">
                        Passionmu di Bidang Digital
                    </span>
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                {{-- ORIGINAL Card 1 - TETAP SAMA --}}
                <div class="bg-white rounded-xl overflow-hidden shadow-md border border-white 
                    hover:shadow-xl hover:scale-105 transition transform duration-300 ease-out"
                    data-aos="fade-up" data-aos-delay="100">
                    <img src="{{ asset('images/PBL.jpg') }}" alt="Project Based Learning" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Project Based Learning</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Konsultasikan skill yang ingin kamu pelajari. Manfaatkan bimbingan untuk skripsi, penelitian,
                            dan proyek teknologi lainnya.
                        </p>
                    </div>
                </div>

                {{-- ORIGINAL Card 2 - TETAP SAMA --}}
                <div class="bg-white rounded-xl overflow-hidden shadow-md border border-white 
                    hover:shadow-xl hover:scale-105 transition transform duration-300 ease-out"
                    data-aos="fade-up" data-aos-delay="200">
                    <img src="{{ asset('images/FSD.jpg') }}" alt="Fullstack Development" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Fullstack Development</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Pelajari skill programming dari Front-End hingga Back-End untuk web dan mobile development.
                        </p>
                    </div>
                </div>

                {{-- ORIGINAL Card 3 - TETAP SAMA --}}
                <div class="bg-white rounded-xl overflow-hidden shadow-md border border-white 
                    hover:shadow-xl hover:scale-105 transition transform duration-300 ease-out"
                    data-aos="fade-up" data-aos-delay="300">
                    <img src="{{ asset('images/FL.jpg') }}" alt="Fundamental Learning" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Fundamental Learning</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Kuasai dasar UI/UX dan Web Design sebelum melangkah ke tingkat advance dengan biaya terjangkau.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ORIGINAL Kelas Section --}}
    @include('components.kelas-carousel')

    {{-- ORIGINAL Partner Section - MEMPERTAHANKAN SEMUA MARQUEE --}}
    <section class="bg-gray-50 py-16 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ORIGINAL Kampus Partner --}}
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold">
                    <span class="text-gray-900">Kampus</span> 
                    <span class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">Partner</span>
                </h2>
            </div>
            
            {{-- ORIGINAL Marquee --}}
            <div class="relative mb-16">
                <div class="marquee-container">
                    <div class="marquee-content">
                        {{-- Duplikasi gambar untuk efek seamless loop --}}
                        <div class="marquee-item">
                            <img src="images/partner/kampus1.png" alt="Partner 1" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus2.png" alt="Partner 2" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus3.png" alt="Partner 3" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus4.png" alt="Partner 4" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus5.png" alt="Partner 5" class="h-16 w-auto object-contain">
                        </div>
                        {{-- Duplikasi untuk seamless loop --}}
                        <div class="marquee-item">
                            <img src="images/partner/kampus1.png" alt="Partner 1" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus2.png" alt="Partner 2" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus3.png" alt="Partner 3" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus4.png" alt="Partner 4" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/kampus5.png" alt="Partner 5" class="h-16 w-auto object-contain">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ORIGINAL Partner Industry --}}
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold">
                    <span class="text-gray-900">Partner</span> 
                    <span class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">Industry</span>
                </h2>
            </div>
            <div class="relative">
                <div class="marquee-container">
                    <div class="marquee-content reverse">
                        {{-- Duplikasi gambar untuk efek seamless loop --}}
                        <div class="marquee-item">
                            <img src="images/partner/industri1.png" alt="Industry 1" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri2.png" alt="Industry 2" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri3.png" alt="Industry 3" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri4.png" alt="Industry 4" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri5.png" alt="Industry 5" class="h-16 w-auto object-contain">
                        </div>
                        {{-- Duplikasi untuk seamless loop --}}
                        <div class="marquee-item">
                            <img src="images/partner/industri1.png" alt="Industry 1" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri2.png" alt="Industry 2" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri3.png" alt="Industry 3" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri4.png" alt="Industry 4" class="h-16 w-auto object-contain">
                        </div>
                        <div class="marquee-item">
                            <img src="images/partner/industri5.png" alt="Industry 5" class="h-16 w-auto object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ORIGINAL FAQ Section - MEMPERTAHANKAN SEMUA --}}
    <section class="bg-white py-20" x-data="{ open: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">
                {{-- ORIGINAL Left Column --}}
                <div class="lg:col-span-1">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                        Frequently Asked
                        <span class="bg-gradient-to-r from-teal-400 to-emerald-500 bg-clip-text text-transparent">Questions</span>
                    </h2>
                    <p class="mt-4 text-gray-600">
                        Tidak menemukan jawaban yang Anda cari? Hubungi tim kami untuk informasi lebih lanjut.
                    </p>
                    <a href="https://wa.me/6283178569163?text=Halo%20saya%20ingin%20bertanya%20tentang%20kelas"
                        target="_blank"
                        class="mt-6 inline-block bg-gradient-to-r from-teal-400 to-emerald-500 text-white font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition-transform transform hover:scale-105">
                        Hubungi Kami
                    </a>
                </div>

                {{-- ORIGINAL Right Column: Accordion --}}
                <div class="lg:col-span-2 space-y-2">
                    <template
                        x-for="(item, index) in [
                        {q: 'Bagaimana Sistem Belajarnya?', a: 'Sistem belajar kami berbasis proyek (project-based learning) dengan sesi mentoring rutin. Anda akan mengerjakan proyek nyata untuk membangun portofolio yang kuat.'},
                        {q: 'Apakah Ini Berlangganan?', a: 'Tidak, program kami adalah pembayaran sekali untuk satu batch kelas. Tidak ada biaya berlangganan bulanan. Anda juga bisa membayar dengan sistem DP.'},
                        {q: 'Apakah Saya Boleh Mendownload Videonya?', a: 'Untuk melindungi hak cipta, video materi tidak dapat di-download. Namun, Anda akan memiliki akses selamanya ke materi tersebut melalui platform kami.'},
                        {q: 'Apakah Ada Jaminan Kerja?', a: 'Kami tidak memberikan jaminan kerja, namun kami memiliki program penyaluran karir yang akan menghubungkan lulusan terbaik kami dengan perusahaan rekanan.'}
                    ]"
                        :key="index">
                        <div class="rounded-lg transition-all duration-300"
                            :class="{
                                'bg-emerald-50 border border-emerald-200': open === index,
                                'border-b border-gray-200': open !== index
                            }">
                            <button @click="open = (open === index ? null : index)"
                                class="flex items-center justify-between w-full text-left p-4">
                                <span class="font-semibold text-lg text-gray-800" x-text="item.q"></span>
                                <i class="fas fa-chevron-down text-emerald-500 transition-transform duration-300"
                                    :class="{ 'rotate-180': open === index }"></i>
                            </button>
                            <div x-show="open === index" x-collapse class="px-4 pb-4 text-gray-600" x-text="item.a">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>

    {{-- ORIGINAL AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>

    {{-- ORIGINAL Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    {{-- ORIGINAL CSS untuk Marquee Animation + Small Enhancements --}}
    <style>
        /* ORIGINAL Marquee CSS - TETAP SAMA */
        .marquee-container {
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .marquee-content {
            display: flex;
            align-items: center;
            gap: 3rem;
            animation: marquee 30s linear infinite;
            width: max-content;
        }

        .marquee-content.reverse {
            animation: marquee-reverse 25s linear infinite;
        }

        .marquee-item {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            transition: transform 0.3s ease;
        }

        .marquee-item:hover {
            transform: scale(1.1);
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes marquee-reverse {
            0% {
                transform: translateX(-50%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .marquee-container:hover .marquee-content {
            animation-play-state: paused;
        }

        /* ORIGINAL Mobile Responsive - TETAP SAMA */
        @media (max-width: 768px) {
            .marquee-content {
                gap: 2rem;
                animation-duration: 20s;
            }

            .marquee-content.reverse {
                animation-duration: 18s;
            }

            .marquee-item img {
                height: 3rem;
            }
        }

        /* SMALL ENHANCEMENT: Subtle improvements only */
        .bg-gradient-to-r {
            position: relative;
        }
        
        /* Add subtle glow to buttons on hover - ENHANCEMENT KECIL */
        .hover\:shadow-emerald-200:hover {
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        /* Smooth scrolling - ENHANCEMENT KECIL */
        html {
            scroll-behavior: smooth;
        }

        /* Better focus states - ENHANCEMENT KECIL */
        button:focus, a:focus {
            outline: 2px solid #10b981;
            outline-offset: 2px;
        }
    </style>
@endsection