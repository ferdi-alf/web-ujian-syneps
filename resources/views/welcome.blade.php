@extends('layouts.landing-layout')

@section('title', 'Home')

@section('content')
    @if (empty($showKelasDetail))
        <section class="relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 lg:pt-28 lg:pb-24">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div data-aos="fade-right" class="md:order-1 order-2">
                        <div
                            class="inline-flex items-center px-4 py-1 bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 rounded-full mb-5 border border-white shadow gap-2 hover:shadow-lg transition-shadow duration-300">
                            <img src="{{ asset('images/logo.png') }}" alt="Sydemy Logo" class="w-5 h-5">
                            <span class="text-xs font-semibold text-white">Sydemy</span>
                            <span class="w-2 h-2 bg-emerald-600 rounded-full animate-pulse"></span>
                        </div>

                        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight text-gray-900">
                            Belajar Terarah, <br>
                            <span
                                class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">
                                Raih Karier Impianmu
                            </span>
                        </h1>

                        <p class="mt-5 text-lg text-gray-600">
                            Syneps Academy adalah platform pembelajaran interaktif dengan manajemen batch, ujian online,
                            leaderboard, dan komunitas alumni untuk membantumu berkembang.
                        </p>

                        <div class="mt-6">
                            <a href="#kelas"
                                class="group relative px-6 py-3 rounded-lg bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-white font-semibold shadow border border-white transition-all duration-300 ease-out hover:scale-105 hover:shadow-xl hover:shadow-emerald-200">
                                <span class="relative z-10">lihat kelas</span>
                                <span
                                    class="absolute inset-0 rounded-lg bg-white opacity-0 group-hover:opacity-10 transition"></span>
                            </a>
                        </div>
                    </div>

                    <div data-aos="fade-left" class="flex justify-center md:order-2 order-1">
                        <img src="{{ asset('images/hero.png') }}" alt="Syneps Academy Logo"
                            class="w-full max-w-sm drop-shadow-lg border border-white rounded-xl">
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-20">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">

                <div class="text-center mb-16" data-aos="fade-up">
                    <span
                        class="inline-block px-4 py-2 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-600 text-sm font-semibold rounded-full mb-4 uppercase tracking-wider">
                        Program Unggulan
                    </span>
                    <h3 class="mt-4 text-4xl sm:text-5xl font-bold text-gray-900 leading-tight">
                        Kami Membantu Menemukan
                        <br class="hidden sm:block">
                        <span
                            class="bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 bg-clip-text text-transparent">
                            Passion Digital Anda
                        </span>
                    </h3>
                    <p class="mt-6 text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Temukan dan kembangkan potensi digital Anda melalui program pembelajaran yang dirancang khusus untuk
                        era modern
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl border border-gray-100 hover:border-emerald-200 transition-all duration-500 ease-out hover:-translate-y-2"
                        data-aos="fade-up" data-aos-delay="100">
                        <div class="relative overflow-hidden">
                            <img src="{{ asset('images/PBL.jpg') }}" alt="Project Based Learning"
                                class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.78 0-2.678-2.153-1.415-3.414l5-5A2 2 0 009 9.172V5L8 4z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <h4
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors duration-300">
                                Project Based Learning
                            </h4>
                            <p class="text-gray-600 text-base leading-relaxed mb-4">
                                Konsultasikan skill yang ingin Anda pelajari. Manfaatkan bimbingan untuk skripsi,
                                penelitian, dan proyek teknologi lainnya.
                            </p>
                            <div
                                class="flex items-center text-emerald-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                Pelajari Lebih Lanjut
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </div>
                        </div>
                    </div>


                    <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl border border-gray-100 hover:border-emerald-200 transition-all duration-500 ease-out hover:-translate-y-2"
                        data-aos="fade-up" data-aos-delay="200">
                        <div class="relative overflow-hidden">
                            <img src="{{ asset('images/FSD.jpg') }}" alt="Fullstack Development"
                                class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                    </svg>
                                </div>
                            </div>
                            <h4
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors duration-300">
                                Fullstack Development
                            </h4>
                            <p class="text-gray-600 text-base leading-relaxed mb-4">
                                Pelajari skill programming dari Front-End hingga Back-End untuk web dan mobile development
                                dengan teknologi terkini.
                            </p>
                            <div
                                class="flex items-center text-emerald-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                Pelajari Lebih Lanjut
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl border border-gray-100 hover:border-emerald-200 transition-all duration-500 ease-out hover:-translate-y-2"
                        data-aos="fade-up" data-aos-delay="300">
                        <div class="relative overflow-hidden">
                            <img src="{{ asset('images/FL.jpg') }}" alt="Fundamental Learning"
                                class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <h4
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors duration-300">
                                Fundamental Learning
                            </h4>
                            <p class="text-gray-600 text-base leading-relaxed mb-4">
                                Kuasai dasar UI/UX dan Web Design sebelum melangkah ke tingkat advance dengan biaya
                                terjangkau.
                            </p>
                            <div
                                class="flex items-center text-emerald-600 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                Pelajari Lebih Lanjut
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (isset($showKelasDetail) && $showKelasDetail)
        @include('components.kelas-detail')
    @else
        @include('components.kelas-carousel')
    @endif

    @if (empty($showKelasDetail))
        <section class="bg-gray-50 py-16 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl font-bold">
                        <span class="text-gray-900">Kampus</span>
                        <span
                            class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">Partner</span>
                    </h2>
                </div>

                <div class="relative mb-16">
                    <div class="marquee-container">
                        <div class="marquee-content">
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

                <div class="text-center mb-12">
                    <h2 class="text-2xl font-bold">
                        <span class="text-gray-900">Partner</span>
                        <span
                            class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">Industry</span>
                    </h2>
                </div>
                <div class="relative">
                    <div class="marquee-container">
                        <div class="marquee-content reverse">
                            <div class="marquee-item">
                                <img src="images/partner/industri1.png" alt="Industry 1"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri2.png" alt="Industry 2"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri3.png" alt="Industry 3"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri4.png" alt="Industry 4"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri5.png" alt="Industry 5"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri1.png" alt="Industry 1"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri2.png" alt="Industry 2"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri3.png" alt="Industry 3"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri4.png" alt="Industry 4"
                                    class="h-16 w-auto object-contain">
                            </div>
                            <div class="marquee-item">
                                <img src="images/partner/industri5.png" alt="Industry 5"
                                    class="h-16 w-auto object-contain">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-20 faq-section" x-data="{ open: null }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-12">
                    <div class="lg:col-span-1">
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                            Frequently Asked
                            <span
                                class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">Questions</span>
                        </h2>
                        <p class="mt-4 text-gray-600">
                            Tidak menemukan jawaban yang Anda cari? Hubungi tim kami untuk informasi lebih lanjut.
                        </p>
                        <a href="https://wa.me/6283178569163?text=Halo%20saya%20ingin%20bertanya%20tentang%20kelas"
                            target="_blank"
                            class="mt-6 inline-flex items-center gap-2 bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-white font-semibold px-6 py-3 rounded-lg hover:shadow-lg transition-all">
                            <i class="fab fa-whatsapp"></i>
                            Hubungi Kami
                        </a>
                    </div>
                    <div class="lg:col-span-2 space-y-3">
                        <template
                            x-for="(item, index) in [
                            {q: 'Bagaimana Sistem Belajarnya?', a: 'Sistem belajar kami berbasis proyek (project-based learning) dengan sesi mentoring rutin. Anda akan mengerjakan proyek nyata untuk membangun portofolio yang kuat.'},
                            {q: 'Apakah Ini Berlangganan?', a: 'Tidak, program kami adalah pembayaran sekali untuk satu batch kelas. Tidak ada biaya berlangganan bulanan. Anda juga bisa membayar dengan sistem DP.'},
                            {q: 'Apakah Saya Boleh Mendownload Videonya?', a: 'Untuk melindungi hak cipta, video materi tidak dapat di-download. Namun, Anda akan memiliki akses selamanya ke materi tersebut melalui platform kami.'},
                            {q: 'Apakah Ada Jaminan Kerja?', a: 'Kami tidak memberikan jaminan kerja, namun kami memiliki program penyaluran karir yang akan menghubungkan lulusan terbaik kami dengan perusahaan rekanan.'}
                        ]"
                            :key="index">
                            <div
                                class="rounded-xl border border-gray-200 bg-white/90 hover:border-gray-300 shadow-sm hover:shadow transition-all overflow-hidden">
                                <button @click="open = (open === index ? null : index)" :aria-expanded="open === index"
                                    class="flex items-center justify-between w-full text-left p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center ring-1 ring-emerald-100">
                                            <i class="fas fa-question text-emerald-500 text-sm"></i>
                                        </div>
                                        <span class="font-semibold text-gray-900" x-text="item.q"></span>
                                    </div>
                                    <i class="fas fa-chevron-down text-emerald-500 transition-transform duration-300"
                                        :class="{ 'rotate-180': open === index }"></i>
                                </button>
                                <div x-show="open === index" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2" class="px-4 pb-5 text-gray-600">
                                    <p x-text="item.a"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"></script>

    <style>
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

        .bg-gradient-to-r {
            position: relative;
        }

        .hover\:shadow-emerald-200:hover {
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        html {
            scroll-behavior: smooth;
        }

        button:focus,
        a:focus {
            outline: 2px solid #10b981;
            outline-offset: 2px;
        }

        .faq-section button:focus,
        .faq-section a:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection
