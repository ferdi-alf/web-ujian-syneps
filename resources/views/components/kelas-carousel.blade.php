@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .swiper {
        overflow: visible !important;
        width: 100%;
        height: 100%;
        padding: 24px 0;
    }
    .swiper-wrapper {
        overflow: visible !important;
        display: flex;
        align-items: center;
    }
    .swiper-slide {
        transition: all 0.45s cubic-bezier(.7,.3,.25,1.2);
        filter: blur(2px);
        transform: scale(0.82);
        opacity: 0.65;
        display: flex;
        justify-content: center;
    }
    .swiper-slide-active {
        filter: blur(0) !important;
        transform: scale(1.10) !important;
        opacity: 1 !important;
        z-index: 3;
    }
    .swiper-slide-next,
    .swiper-slide-prev {
        filter: blur(1px);
        transform: scale(0.90);
        opacity: 0.85;
        z-index: 2;
    }
    .kelas-card {
        transition: all 0.45s;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.14);
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 1.2rem;
        width: 340px;
        max-width: 90%;
        margin: 0 auto;
        background: #fff;
    }
    .kelas-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 16px 32px rgba(32, 185, 129, 0.18);
    }
    @media (max-width: 1024px) {
        .kelas-card { width: 94vw; }
    }
    .kelas-card.loading {
        opacity: 0.6;
        pointer-events: none;
    }
    .kelas-card.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 24px;
        height: 24px;
        margin: -12px 0 0 -12px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

<section class="bg-white py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 sm:mb-8">
            <div class="text-center sm:text-left mb-4 sm:mb-0">
                <h2 class="text-xs sm:text-sm font-semibold tracking-wide text-emerald-500 uppercase">Program Unggulan</h2>
                <h3 class="mt-2 text-xl sm:text-2xl lg:text-3xl font-extrabold text-gray-900">Pilih Kelas Terbaik Untukmu</h3>
            </div>
        </div>

        <div class="swiper mySwiper swiper-horizontal">
            <div class="swiper-wrapper">
                @php
                    $kelasData = isset($kelas) && $kelas->count() > 0
                        ? $kelas->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'nama' => $item->nama,
                                'harga' => $item->harga,
                                'dp' => $item->dp_persen ?? 0,
                                'tipe' => ucfirst($item->type ?? 'Regular'),
                                'durasiBelajar' => $item->durasi_belajar ?? 0,
                                'durasiMagang' => $item->waktu_magang,
                                'gambar' => asset('images/default-kelas.jpg'),
                            ];
                        })->toArray()
                        : [
                            [
                                'id' => 'temp-1',
                                'nama' => 'Fullstack Android Development (Part-time)',
                                'harga' => 5000000,
                                'dp' => 30,
                                'tipe' => 'Intensif',
                                'durasiBelajar' => 6,
                                'durasiMagang' => 2,
                                'gambar' => asset('images/FSD.jpg'),
                            ],
                            [
                                'id' => 'temp-2',
                                'nama' => 'Fullstack Website Development (Part-time)',
                                'harga' => 3500000,
                                'dp' => 25,
                                'tipe' => 'Regular',
                                'durasiBelajar' => 4,
                                'durasiMagang' => null,
                                'gambar' => asset('images/FL.jpg'),
                            ],
                            [
                                'id' => 'temp-3',
                                'nama' => 'Fullstack Programming (Intensif)',
                                'harga' => 4500000,
                                'dp' => 20,
                                'tipe' => 'Intensif',
                                'durasiBelajar' => 5,
                                'durasiMagang' => 1,
                                'gambar' => asset('images/PBL.jpg'),
                            ],
                        ];
                @endphp

                @foreach ($kelasData as $kelasItem)
                    <div class="swiper-slide" data-kelas-id="{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}">
                        <div class="kelas-card group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-emerald-50 opacity-60"></div>
                            @if ($kelasItem['tipe'] === 'Intensif')
                                <div class="absolute top-4 right-4 z-10">
                                    <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                        🔥 POPULAR
                                    </span>
                                </div>
                            @endif
                            <div class="relative z-10 h-full flex flex-col">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-code text-white text-xs sm:text-sm"></i>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ $kelasItem['tipe'] ?? 'Regular' }}</span>
                                            </div>
                                            <h4 class="text-base sm:text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-emerald-600 transition-colors">{{ $kelasItem['nama'] }}</h4>
                                        </div>
                                    </div>
                                    <div class="bg-gradient-to-r from-gray-50 to-emerald-50 rounded-xl p-3 sm:p-4 mb-3 sm:mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            @if (is_numeric($kelasItem['harga']))
                                                <span class="text-xs sm:text-sm text-gray-500 line-through">Rp {{ number_format($kelasItem['harga'] + 500000, 0, ',', '.') }}</span>
                                            @endif
                                            @if ($kelasItem['dp'] > 0)
                                                <span class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold px-2 sm:px-3 py-1 rounded-full shadow-md">DP {{ $kelasItem['dp'] }}%</span>
                                            @endif
                                        </div>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">{{ is_numeric($kelasItem['harga']) ? 'Rp ' . number_format($kelasItem['harga'], 0, ',', '.') : $kelasItem['harga'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 sm:px-5 flex-grow">
                                    <div class="space-y-2 sm:space-y-3">
                                        <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-700">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-clock text-emerald-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">{{ $kelasItem['durasiBelajar'] ?? '0' }} bulan pembelajaran</span>
                                        </div>
                                        @if (isset($kelasItem['durasiMagang']) && $kelasItem['durasiMagang'])
                                            <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-700">
                                                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-briefcase text-blue-600 text-xs"></i>
                                                </div>
                                                <span class="font-medium">{{ $kelasItem['durasiMagang'] }} bulan magang</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-700">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-certificate text-purple-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">Sertifikat resmi</span>
                                        </div>
                                        <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-700">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-users text-orange-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">Mentoring 1-on-1</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 sm:p-5 mt-auto">
                                    <a href="{{ route('kelas.detail', $kelasItem['id']) }}" class="block w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-center py-2 sm:py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg group">
                                        <span class="flex items-center justify-center gap-2">
                                            Lihat Detail
                                            <i class="fas fa-arrow-right text-xs sm:text-sm group-hover:translate-x-1 transition-transform"></i>
                                        </span>
                                    </a>
                                    <div class="admin-actions hidden mt-2 sm:mt-3" data-admin-actions>
                                        <div class="flex gap-2">
                                            <button onclick="editKelas('{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}')" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-2 sm:px-3 py-1 sm:py-2 rounded-lg text-xs font-medium transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <button onclick="deleteKelas('{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}')" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-2 sm:px-3 py-1 sm:py-2 rounded-lg text-xs font-medium transition-colors">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    let swiperInstance = null;
    function initializeSwiper() {
        if (swiperInstance) swiperInstance.destroy(true, true);
        const slides = document.querySelectorAll('.swiper-slide');
        const totalSlides = slides.length;
        const middleSlide = Math.floor(totalSlides / 2);
        swiperInstance = new Swiper('.mySwiper', {
            loop: true,
            centeredSlides: true,
            grabCursor: true,
            mousewheel: true,
            autoplay: {
                delay: 3200,
                disableOnInteraction: true,
                pauseOnMouseEnter: true,
            },
            slidesPerView: 3,
            spaceBetween: 24,
            initialSlide: middleSlide,
            speed: 700,
            breakpoints: {
                320: { slidesPerView: 1.2, spaceBetween: 10 },
                480: { slidesPerView: 1.5, spaceBetween: 18 },
                640: { slidesPerView: 2.2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
            },
        });
        swiperInstance.on('init', function() {
            swiperInstance.slideToLoop(middleSlide, 0);
        });
        swiperInstance.init();
    }
    document.addEventListener('DOMContentLoaded', function() {
        initializeSwiper();
    });
</script>
@endpush

<script>
    function editKelas(kelasId) {
        // Implement edit functionality here
        console.log('Edit kelas with ID:', kelasId);
    }

    function deleteKelas(kelasId) {
        // Implement delete functionality here
        console.log('Delete kelas with ID:', kelasId);
    }
</script>