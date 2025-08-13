<!-- resources/views/components/kelas-carousel.blade.php -->

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>

        .swiper {
        overflow: visible !important;
    }

    .swiper-wrapper {
        overflow: visible !important;
    }
    
    .swiper-slide {
        transition: all 0.3s ease;
        filter: blur(4px);
        transform: scale(0.85);
    }

    .swiper-slide-active {
        filter: blur(0);
        transform: scale(1);
    }

    /* Animasi hover untuk card */
    .kelas-card {
        transition: all 0.3s ease;
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .kelas-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
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
                Pilih Kelas Terbaik Untukmu
            </h3>
        </div>

        <!-- Swiper Carousel -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">

                @php
                    $dummyKelas = [
                        [
                            'nama' => 'Fullstack Android Development (Part-time)',
                            'harga' => '5.000.000',
                            'dp' => '30',
                            'tipe' => 'Intensif',
                            'durasiBelajar' => 6,
                            'durasiMagang' => 2,
                            'gambar' => asset('images/FSD.jpg'),
                        ],
                        [
                            'nama' => 'Fullstack Website Development (Part-time)',
                            'harga' => '3.500.000',
                            'dp' => '25',
                            'tipe' => 'Regular',
                            'durasiBelajar' => 4,
                            'durasiMagang' => null,
                            'gambar' => asset('FL.jpg'),
                        ],
                        [
                            'nama' => 'Fullstack Programming (Itensif)',
                            'harga' => '4.500.000',
                            'dp' => '20',
                            'tipe' => 'Intensif',
                            'durasiBelajar' => 5,
                            'durasiMagang' => 1,
                            'gambar' => asset('images/PBL.jpg'),
                        ],
                    ];
                @endphp

                @foreach ($dummyKelas as $kelas)
                    <div
                        class="swiper-slide kelas-card border border-orange-500 rounded-lg overflow-hidden bg-gray-50 shadow-md">
                        <div class="bg-orange-100 p-4">
                            <h4 class="text-lg font-semibold">{{ $kelas['nama'] }}</h4>
                            <p class="text-sm text-gray-700 mb-2">{{ $kelas['tipe'] }} Class</p>
                            <div class="flex items-center gap-2 mb-1">
                                @if (is_numeric($kelas['harga']))
                                    <span class="line-through text-gray-500 text-sm">Rp
                                        {{ number_format($kelas['harga'] + 500000, 0, ',', '.') }}</span>
                                @endif
                                <span
                                    class="bg-emerald-500 text-white text-xs font-semibold px-2 py-1 rounded">{{ $kelas['dp'] }}%
                                    DP</span>
                            </div>
                            <p class="text-xl font-bold text-gray-900">
                                {{ is_numeric($kelas['harga']) ? 'Rp ' . number_format($kelas['harga'], 0, ',', '.') : $kelas['harga'] }}
                            </p>
                        </div>
                        <div class="p-4 space-y-2">
                            <div class="flex items-center text-sm text-gray-700 gap-2">
                                <i class="fa-solid fa-check text-emerald-500"></i>
                                <span>Durasi Belajar: {{ $kelas['durasiBelajar'] }} bulan</span>
                            </div>
                            @if ($kelas['durasiMagang'])
                                <div class="flex items-center text-sm text-gray-700 gap-2">
                                    <i class="fa-solid fa-check text-emerald-500"></i>
                                    <span>Durasi Magang: {{ $kelas['durasiMagang'] }} bulan</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 text-center">
                            <a href="#"
                                class="inline-block bg-gradient-to-br from-red-600 to-orange-500 text-white px-4 py-2 rounded-md font-semibold hover:opacity-90 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach


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
    slidesPerView: 3.5, // atau 3.5 kalau mau separo card blur di kiri-kanan
    spaceBetween: 20,
    grabCursor: true,
    mousewheel: true,
    breakpoints: {
        640: {
            slidesPerView: 1.5
        },
        1024: {
            slidesPerView: 3
        }
    }
});
    swiper.on('slideChange', function() {
        swiper.slides.forEach(function(slide) {
            slide.classList.remove('swiper-slide-active');
        });
        swiper.slides[swiper.activeIndex].classList.add('swiper-slide-active');
    });     
</script>
