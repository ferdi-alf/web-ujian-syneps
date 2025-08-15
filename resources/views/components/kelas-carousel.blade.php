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
                        <div
                            class="kelas-card border border-orange-500 rounded-lg overflow-hidden bg-gray-50 shadow-md h-full">
                            {{-- Header Card --}}
                            <div class="bg-orange-100 p-3 sm:p-4">
                                <h4 class="text-sm sm:text-base lg:text-lg font-semibold leading-tight">
                                    {{ $kelasItem['nama'] }}</h4>
                                <p class="text-xs sm:text-sm text-gray-700 mb-2 mt-1">{{ $kelasItem['tipe'] ?? 'Regular' }}
                                    Class</p>

                                {{-- Harga dan DP --}}
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    @if (is_numeric($kelasItem['harga']))
                                        <span class="line-through text-gray-500 text-xs sm:text-sm">Rp
                                            {{ number_format($kelasItem['harga'] + 500000, 0, ',', '.') }}</span>
                                    @endif
                                    <span
                                        class="bg-emerald-500 text-white text-xs font-semibold px-2 py-1 rounded whitespace-nowrap">
                                        {{ $kelasItem['dp'] ?? '0' }}% DP
                                    </span>
                                </div>
                                <p class="text-lg sm:text-xl font-bold text-gray-900">
                                    {{ is_numeric($kelasItem['harga']) ? 'Rp ' . number_format($kelasItem['harga'], 0, ',', '.') : $kelasItem['harga'] }}
                                </p>
                            </div>

                            {{-- Detail Info --}}
                            <div class="p-3 sm:p-4 space-y-2 flex-grow">
                                <div class="flex items-start text-xs sm:text-sm text-gray-700 gap-2">
                                    <i class="fa-solid fa-check text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>Durasi Belajar: {{ $kelasItem['durasiBelajar'] ?? '0' }} bulan</span>
                                </div>
                                @if (isset($kelasItem['durasiMagang']) && $kelasItem['durasiMagang'])
                                    <div class="flex items-start text-xs sm:text-sm text-gray-700 gap-2">
                                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                        <span>Durasi Magang: {{ $kelasItem['durasiMagang'] }} bulan</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="p-3 sm:p-4 text-center mt-auto space-y-2">
                                <a href="{{ route('kelas.detail', $kelasItem['id']) }}"
                                    class="inline-block bg-gradient-to-br from-red-600 to-orange-500 text-white px-3 sm:px-4 py-2 rounded-md font-semibold hover:opacity-90 transition text-sm sm:text-base w-full sm:w-auto">
                                    Lihat Detail
                                </a>

                                {{-- Admin CRUD Buttons (akan muncul jika user adalah admin) --}}
                                <div class="admin-actions hidden" data-admin-actions>
                                    <div class="flex gap-2 mt-2 justify-center">
                                        <button onclick="editKelas('{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}')"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <button onclick="deleteKelas('{{ $kelasItem['id'] ?? 'temp-' . $loop->index }}')"
                                            class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
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
            // Mobile first approach
            320: {
                slidesPerView: 1.2,
                spaceBetween: 16,
                centeredSlides: true,
            },
            480: {
                slidesPerView: 1.5,
                spaceBetween: 20,
                centeredSlides: true,
            },
            640: {
                slidesPerView: 2.2,
                spaceBetween: 20,
                centeredSlides: true,
            },
            768: {
                slidesPerView: 2.5,
                spaceBetween: 24,
                centeredSlides: true,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 24,
                centeredSlides: true,
            },
            1280: {
                slidesPerView: 3.5,
                spaceBetween: 24,
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
