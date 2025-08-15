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

    /* Semua slide default blur dan kecil - KURANGI INTENSITAS BLUR DI SINI */
    .swiper-slide {
        transition: all 0.3s ease;
        filter: blur(1px);
        /* Ubah dari 4px ke 2px atau 1px */
        transform: scale(0.85);
    }

    /* Hanya slide yang aktif yang tidak blur dan normal size */
    .swiper-slide-active {
        filter: blur(0) !important;
        transform: scale(1) !important;
    }

    /* Animasi hover untuk card */
    .kelas-card {
        transition: all 0.3s ease;
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .kelas-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }

    /* Admin Actions Styling */
    .admin-actions {
        transition: all 0.3s ease;
    }

    .admin-actions.hidden {
        display: none;
    }

    .admin-actions button {
        transition: all 0.2s ease;
    }

    .admin-actions button:hover {
        transform: translateY(-1px);
    }

    /* Loading state untuk CRUD operations */
    .kelas-card.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .kelas-card.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<section class="bg-white py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Heading -->
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-xs sm:text-sm font-semibold tracking-wide text-emerald-500 uppercase">
                Program Unggulan
            </h2>
            <h3 class="mt-2 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 px-4">
                Pilih Kelas Terbaik Untukmu
            </h3>
        </div>

        <!-- Swiper Carousel -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @php
                    // GUNAKAN DATA REAL DARI DATABASE DENGAN FALLBACK KE DUMMY
                    $kelasData = isset($kelas) && $kelas->count() > 0 
                        ? $kelas->map(function($item) {
                            return [
                                'id' => $item->id,
                                'nama' => $item->nama,
                                'harga' => $item->harga,
                                'dp' => $item->dp_persen ?? 0,
                                'tipe' => ucfirst($item->type ?? 'Regular'),
                                'durasiBelajar' => $item->durasi_belajar ?? 0,
                                'durasiMagang' => $item->waktu_magang,
                                'gambar' => asset('images/default-kelas.jpg'), // Default image
                            ];
                        })->toArray()
                        : [
                            // Fallback dummy data jika database kosong
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
                        <div class="kelas-card group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 h-full border border-gray-100">
                            
                            {{-- Background Gradient Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-emerald-50 opacity-60"></div>
                            
                            {{-- Popular Badge (if applicable) --}}
                            @if($kelasItem['tipe'] === 'Intensif')
                                <div class="absolute top-4 right-4 z-10">
                                    <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                        🔥 POPULAR
                                    </span>
                                </div>
                            @endif

                            {{-- Card Content --}}
                            <div class="relative z-10 h-full flex flex-col">
                                
                                {{-- Header with Icon --}}
                                <div class="p-4 sm:p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-code text-white text-sm"></i>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                    {{ $kelasItem['tipe'] ?? 'Regular' }}
                                                </span>
                                            </div>
                                            <h4 class="text-base sm:text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-emerald-600 transition-colors">
                                                {{ $kelasItem['nama'] }}
                                            </h4>
                                        </div>
                                    </div>

                                    {{-- Pricing Section --}}
                                    <div class="bg-gradient-to-r from-gray-50 to-emerald-50 rounded-xl p-4 mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            @if (is_numeric($kelasItem['harga']))
                                                <span class="text-sm text-gray-500 line-through">
                                                    Rp {{ number_format($kelasItem['harga'] + 500000, 0, ',', '.') }}
                                                </span>
                                            @endif
                                            @if($kelasItem['dp'] > 0)
                                                <span class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                                                    DP {{ $kelasItem['dp'] }}%
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                                {{ is_numeric($kelasItem['harga']) ? 'Rp ' . number_format($kelasItem['harga'], 0, ',', '.') : $kelasItem['harga'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Features List --}}
                                <div class="px-4 sm:px-6 flex-grow">
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3 text-sm text-gray-700">
                                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-clock text-emerald-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">{{ $kelasItem['durasiBelajar'] ?? '0' }} bulan pembelajaran</span>
                                        </div>
                                        
                                        @if (isset($kelasItem['durasiMagang']) && $kelasItem['durasiMagang'])
                                            <div class="flex items-center gap-3 text-sm text-gray-700">
                                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-briefcase text-blue-600 text-xs"></i>
                                                </div>
                                                <span class="font-medium">{{ $kelasItem['durasiMagang'] }} bulan magang</span>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-3 text-sm text-gray-700">
                                            <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-certificate text-purple-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">Sertifikat resmi</span>
                                        </div>

                                        <div class="flex items-center gap-3 text-sm text-gray-700">
                                            <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-users text-orange-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">Mentoring 1-on-1</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <div class="p-4 sm:p-6 mt-auto">
                                    <a href="{{ route('kelas.detail', $kelasItem['id']) }}"
                                        class="block w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-center py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg group">
                                        <span class="flex items-center justify-center gap-2">
                                            Lihat Detail
                                            <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                                        </span>
                                    </a>

                                    {{-- Admin CRUD Buttons (functionality tetap sama) --}}
                                    <div class="admin-actions hidden mt-3" data-admin-actions>
                                        <div class="flex gap-2">
                                            <button onclick="editKelas('{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}')"
                                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <button onclick="deleteKelas('{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}')"
                                                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors">
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

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        centeredSlides: true,
        grabCursor: true,
        mousewheel: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        breakpoints: {
            // Fokus 1 card di tengah dengan blur di samping
            320: {
                slidesPerView: 1.4,
                spaceBetween: 20,
                centeredSlides: true,
            },
            480: {
                slidesPerView: 1.6,
                spaceBetween: 24,
                centeredSlides: true,
            },
            640: {
                slidesPerView: 2.2,
                spaceBetween: 28,
                centeredSlides: true,
            },
            768: {
                slidesPerView: 2.6,
                spaceBetween: 32,
                centeredSlides: true,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 36,
                centeredSlides: true,
            },
            1280: {
                slidesPerView: 3.4,
                spaceBetween: 40,
                centeredSlides: true,
            }
        },
        // Navigation arrows (optional)
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        // Pagination (optional)
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        }
    });

    // Update active slide classes
    swiper.on('slideChange', function() {
        // Hapus class active dari semua slide
        swiper.slides.forEach(function(slide) {
            slide.classList.remove('swiper-slide-active');
        });

        // Tambah class active ke slide yang sedang aktif
        if (swiper.slides[swiper.activeIndex]) {
            swiper.slides[swiper.activeIndex].classList.add('swiper-slide-active');
        }
    });

    // Pastikan slide pertama sudah aktif saat halaman dimuat
    swiper.on('init', function() {
        if (swiper.slides[swiper.activeIndex]) {
            swiper.slides[swiper.activeIndex].classList.add('swiper-slide-active');
        }
    });

    // Hapus fungsi handleResize yang lama
    // function handleResize() {
    //     if (window.innerWidth <= 640) {
    //         // Disable blur effect on mobile for better performance
    //         document.querySelectorAll('.swiper-slide').forEach(slide => {
    //             slide.style.filter = 'none';
    //             slide.style.transform = 'scale(1)';
    //         });
    //     } else {
    //         // Re-enable effects on larger screens
    //         document.querySelectorAll('.swiper-slide').forEach(slide => {
    //             slide.style.filter = '';
    //             slide.style.transform = '';
    //         });
    //     }
    // }

    // Hapus event listener resize yang lama
    // window.addEventListener('resize', handleResize);

    // ===== CRUD FUNCTIONS - READY FOR BACKEND INTEGRATION =====

    // Check if user is admin (akan di-set oleh backend)
    function checkAdminRole() {
        // Backend akan set ini: window.isAdmin = true/false
        return window.isAdmin || false;
    }

    // Show/hide admin actions
    function toggleAdminActions() {
        const adminActions = document.querySelectorAll('[data-admin-actions]');
        if (checkAdminRole()) {
            adminActions.forEach(action => action.classList.remove('hidden'));
        }
    }

    // CREATE - Add new kelas
    function addKelas() {
        // Backend akan handle modal form
        if (typeof window.openAddKelasModal === 'function') {
            window.openAddKelasModal();
        } else {
            console.log('Backend belum implement modal add');
        }
    }

    // READ - View kelas detail
    function viewKelasDetail(kelasId) {
        // Backend akan handle redirect ke detail page
        if (typeof window.viewKelasDetail === 'function') {
            window.viewKelasDetail(kelasId);
        } else {
            console.log('View detail kelas:', kelasId);
        }
    }

    // UPDATE - Edit kelas
    function editKelas(kelasId) {
        // Backend akan handle modal edit
        if (typeof window.openEditKelasModal === 'function') {
            window.openEditKelasModal(kelasId);
        } else {
            console.log('Edit kelas:', kelasId);
        }
    }

    // DELETE - Delete kelas
    function deleteKelas(kelasId) {
        if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            // Backend akan handle delete request
            if (typeof window.deleteKelasRequest === 'function') {
                window.deleteKelasRequest(kelasId);
            } else {
                console.log('Delete kelas:', kelasId);
            }
        }
    }

    // Refresh carousel after CRUD operations
    function refreshCarousel() {
        if (swiper) {
            swiper.update();
            swiper.slideTo(0);
        }
    }

    // Listen for CRUD events from backend
    document.addEventListener('kelasAdded', function(e) {
        console.log('Kelas baru ditambahkan:', e.detail);
        // Backend akan refresh data
        if (typeof window.refreshKelasData === 'function') {
            window.refreshKelasData();
        }
    });

    document.addEventListener('kelasUpdated', function(e) {
        console.log('Kelas diupdate:', e.detail);
        // Backend akan refresh data
        if (typeof window.refreshKelasData === 'function') {
            window.refreshKelasData();
        }
    });

    document.addEventListener('kelasDeleted', function(e) {
        console.log('Kelas dihapus:', e.detail);
        // Backend akan refresh data
        if (typeof window.refreshKelasData === 'function') {
            window.refreshKelasData();
        }
    });

    // Initialize admin actions
    document.addEventListener('DOMContentLoaded', function() {
        toggleAdminActions();
    });

    // ===== END CRUD FUNCTIONS =====
</script>
