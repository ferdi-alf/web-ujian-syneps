@extends('layouts.landing-layout')

@section('title', 'Home')

@section('content')

    <section class="relative bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 lg:pt-28 lg:pb-24">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                
                <div data-aos="fade-right">
                    
                    <div
                        class="inline-flex items-center px-4 py-1 bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 
                            rounded-full mb-5 border border-white shadow gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Sydemy Logo" class="w-5 h-5">
                        <span class="text-xs font-semibold text-gray-900">Sydemy</span>
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
                        <a href="{{ route('kelas.index') }}"
                            class="group relative px-6 py-3 rounded-lg bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 
                              text-gray-900 font-semibold shadow border border-white
                              transition-all duration-300 ease-out
                              hover:scale-105 hover:shadow-xl hover:shadow-emerald-200">
                            <span class="relative z-10">Lihat Kelas</span>
                            <span
                                class="absolute inset-0 rounded-lg bg-white opacity-0 group-hover:opacity-10 transition"></span>
                        </a>
                    </div>

                  
                    <div class="mt-8 flex items-center gap-8">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">500+</p>
                            <p class="text-sm text-gray-500">Peserta Aktif</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">10+</p>
                            <p class="text-sm text-gray-500">Mentor Berpengalaman</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">98%</p>
                            <p class="text-sm text-gray-500">Kepuasan Peserta</p>
                        </div>
                    </div>
                </div>

            
                <div data-aos="fade-left" class="flex justify-center">
                    <img src="{{ asset('images/syn-logo-ori.png') }}" alt="Syneps Academy Logo"
                        class="w-full max-w-sm drop-shadow-lg border border-white rounded-xl">
                </div>
            </div>
        </div>
    </section>

    {{-- PROGRAM UNGGULAN --}}

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
            
            {{-- Card 1 --}}
            <div class="bg-white rounded-xl overflow-hidden shadow-md border border-white 
                        hover:shadow-xl hover:scale-105 transition transform duration-300 ease-out"
                        data-aos="fade-up" data-aos-delay="100">
                <img src="{{ asset('images/Project-Based-Learning.jpg') }}" alt="Project Based Learning"
                    class="w-full h-52 object-cover">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        Project Based Learning
                    </h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Konsultasikan skill yang ingin kamu pelajari. Manfaatkan bimbingan untuk skripsi, penelitian,
                        dan proyek teknologi lainnya.
                    </p>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-white rounded-xl overflow-hidden shadow-md border border-white 
                        hover:shadow-xl hover:scale-105 transition transform duration-300 ease-out"
                        data-aos="fade-up" data-aos-delay="200">
                <img src="{{ asset('images/Full-Stack-Developer.jpg') }}" alt="Fullstack Development"
                    class="w-full h-52 object-cover">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        Fullstack Development
                    </h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Pelajari skill programming dari Front-End hingga Back-End untuk web dan mobile development.
                    </p>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-white rounded-xl overflow-hidden shadow-md border border-white 
                        hover:shadow-xl hover:scale-105 transition transform duration-300 ease-out"
                 data-aos="fade-up" data-aos-delay="300">
                <img src="{{ asset('images/elearning.jpg') }}" alt="Fundamental Learning"
                    class="w-full h-52 object-cover">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        Fundamental Learning
                    </h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Kuasai dasar UI/UX dan Web Design sebelum melangkah ke tingkat advance dengan biaya terjangkau.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- AOS Animation --}}
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>

    <!-- FAQ Section -->
<section class="bg-white py-20" x-data="{ open: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Heading -->
        <div class="text-center mb-12">
            <h2 class="text-sm font-semibold tracking-wide text-emerald-500 uppercase">
                Frequently Asked Questions
            </h2>
            <h3 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">
                Pertanyaan yang Sering Diajukan
            </h3>
        </div>

        <!-- FAQ Grid -->
        <div class="grid md:grid-cols-2 gap-6">
            
            <!-- FAQ Item -->
            <div class="bg-gray-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                <button 
                    class="flex justify-between w-full text-left text-lg font-semibold text-gray-800"
                    @click="open === 1 ? open = null : open = 1">
                    Program apa saja yang ada di <span class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 bg-clip-text text-transparent">SYDEMY</span>?
                    <span class="text-emerald-500 transition-transform" :class="open === 1 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="open === 1" x-transition.duration.300ms class="mt-3 text-gray-600 text-sm leading-relaxed">
                    Tersedia Program Kelas Intensif, Part-Time, Private dan Project (Studi Kasus).
                </div>
            </div>

            <!-- FAQ Item -->
            <div class="bg-gray-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                <button 
                    class="flex justify-between w-full text-left text-lg font-semibold text-gray-800"
                    @click="open === 2 ? open = null : open = 2">
                    Tidak punya background IT, apakah bisa gabung kelas?
                    <span class="text-emerald-500 transition-transform" :class="open === 2 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="open === 2" x-transition.duration.300ms class="mt-3 text-gray-600 text-sm leading-relaxed">
                    Tentu saja! Materi akan disesuaikan untuk pemula hingga tingkat lanjut.
                </div>
            </div>

            <!-- FAQ Item -->
            <div class="bg-gray-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                <button 
                    class="flex justify-between w-full text-left text-lg font-semibold text-gray-800"
                    @click="open === 3 ? open = null : open = 3">
                    Apakah perlu membawa laptop sendiri?
                    <span class="text-emerald-500 transition-transform" :class="open === 3 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="open === 3" x-transition.duration.300ms class="mt-3 text-gray-600 text-sm leading-relaxed">
                    Disarankan membawa laptop sendiri untuk memaksimalkan proses belajar.
                </div>
            </div>

            <!-- FAQ Item -->
            <div class="bg-gray-50 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                <button 
                    class="flex justify-between w-full text-left text-lg font-semibold text-gray-800"
                    @click="open === 4 ? open = null : open = 4">
                    Apakah bisa mempelajari skill digital lainnya selain programming?
                    <span class="text-emerald-500 transition-transform" :class="open === 4 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="open === 4" x-transition.duration.300ms class="mt-3 text-gray-600 text-sm leading-relaxed">
                    Tentu! Konsultasikan kebutuhan skill sesuai minat kamu, seperti UI/UX, desain grafis, dan lainnya.
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .swiper-slide {
        transition: all 0.3s ease;
        filter: blur(4px);
        transform: scale(0.85);
    }
    .swiper-slide-active {
        filter: blur(0);
        transform: scale(1);
    }
</style>

<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Heading -->
        <div class="text-center mb-12">
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

        <!-- Swiper Carousel -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">

                <!-- Card 1 -->
                <div class="swiper-slide bg-white rounded-xl overflow-hidden shadow-md border border-white">
                    <img src="{{ asset('images/Project-Based-Learning.jpg') }}" alt="Project Based Learning" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Project Based Learning</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Konsultasikan skill yang ingin kamu pelajari. Manfaatkan bimbingan untuk skripsi, penelitian, dan proyek teknologi lainnya.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="swiper-slide bg-white rounded-xl overflow-hidden shadow-md border border-white">
                    <img src="{{ asset('images/Full-Stack-Developer.jpg') }}" alt="Fullstack Development" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Fullstack Development</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Pelajari skill programming dari Front-End hingga Back-End untuk web dan mobile development.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="swiper-slide bg-white rounded-xl overflow-hidden shadow-md border border-white">
                    <img src="{{ asset('images/elearning.jpg') }}" alt="Fundamental Learning" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Fundamental Learning</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Kuasai dasar UI/UX dan Web Design sebelum melangkah ke tingkat advance dengan biaya terjangkau.
                        </p>
                    </div>
                </div>

                <!-- Card 4 (duplikat sementara) -->
                <div class="swiper-slide bg-white rounded-xl overflow-hidden shadow-md border border-white">
                    <img src="{{ asset('images/Full-Stack-Developer.jpg') }}" alt="Mobile Development" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Mobile Development</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Belajar membuat aplikasi Android dan iOS dengan teknologi modern.
                        </p>
                    </div>
                </div>

                <!-- Card 5 (duplikat sementara) -->
                <div class="swiper-slide bg-white rounded-xl overflow-hidden shadow-md border border-white">
                    <img src="{{ asset('images/Project-Based-Learning.jpg') }}" alt="UI/UX Masterclass" class="w-full h-52 object-cover">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">UI/UX Masterclass</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Tingkatkan kemampuan desain agar produk digitalmu memikat pengguna.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        centeredSlides: true,
        slidesPerView: "auto",
        spaceBetween: 20,
        grabCursor: true,
        mousewheel: true,
        breakpoints: {
            640: { slidesPerView: 1.5 },
            1024: { slidesPerView: 3 }
        }
    });
</script>


<!-- Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>



@endsection
