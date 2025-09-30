{{-- resources/views/components/pdf-viewer.blade.php --}}
@props([
    'drawerId' => 'drawer-control-materi',
])

<div x-data="{
    materiData: null,
    loading: true,
    error: null,
    pdfUrl: null,
    pdfPages: [],
    loadingPages: false,
    currentZoom: 1,

    init() {
        console.log('PDF Viewer initialized for drawer:', '{{ $drawerId }}');
    },

    async loadPDFPages() {
        if (!this.pdfUrl) return;

        this.loadingPages = true;
        this.pdfPages = [];

        try {
            // Load PDF.js
            if (typeof pdfjsLib === 'undefined') {
                await this.loadPDFJS();
            }

            const loadingTask = pdfjsLib.getDocument(this.pdfUrl);
            const pdf = await loadingTask.promise;

            console.log('PDF loaded, pages:', pdf.numPages);

            // Generate previews for each page
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                // Set scale for good quality
                const scale = 2;
                const viewport = page.getViewport({ scale: scale });

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                await page.render(renderContext).promise;

                this.pdfPages.push({
                    pageNumber: pageNum,
                    canvas: canvas,
                    dataUrl: canvas.toDataURL('image/png'),
                    width: viewport.width,
                    height: viewport.height
                });
            }

            this.loadingPages = false;
            console.log('All pages rendered:', this.pdfPages.length);

        } catch (error) {
            console.error('Error loading PDF:', error);
            this.error = 'Gagal memuat halaman PDF: ' + error.message;
            this.loadingPages = false;
        }
    },

    async loadPDFJS() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
            script.onload = () => {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    },

    zoomIn() {
        this.currentZoom = Math.min(this.currentZoom + 0.25, 3);
    },

    zoomOut() {
        this.currentZoom = Math.max(this.currentZoom - 0.25, 0.5);
    },

    resetZoom() {
        this.currentZoom = 1;
    }
}"
    x-on:drawerDataLoaded.window="
        console.log('PDF Viewer received event:', $event.detail);
        if ($event.detail.drawerId === '{{ $drawerId }}') {
            console.log('Event matches drawer ID, processing data...');
            materiData = $event.detail.data;
            loading = false;
            
            console.log('Materi data:', materiData);
            
            if (materiData && materiData.file_pdf_url) {
                pdfUrl = materiData.file_pdf_url;
                console.log('PDF URL set:', pdfUrl);
                
                // Load PDF pages
                setTimeout(() => {
                    loadPDFPages();
                }, 100);
            } else {
                console.log('No PDF URL found in data');
            }
        } else {
            console.log('Event drawer ID mismatch:', $event.detail.drawerId, 'vs', '{{ $drawerId }}');
        }
    ">

    <template x-if="materiData && !loading">
        <div>

            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                <div class="flex sm:flex-row flex-col items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="materiData.judul"></h3>
                        <div class="flex items-center text-sm text-gray-600 mb-2">
                            <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>
                            <span x-text="materiData.file_pdf_name || 'File PDF'"></span>
                        </div>
                        <div class="text-xs text-blue-600" x-show="pdfPages.length > 0">
                            <i class="fa-solid fa-pages mr-1"></i>
                            <span x-text="pdfPages.length"></span> halaman
                        </div>
                    </div>
                    <div class="ml-4 mt-3 sm:mt-0">
                        <a :href="`/materi/download/${materiData.id}`"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                            <i class="fa-solid fa-download mr-2"></i>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- PDF Controls -->
            <div class="mb-4 flex sm:flex-row flex-col sm:items-center items-start  justify-between bg-white p-3 rounded-lg border shadow-sm"
                x-show="pdfPages.length > 0">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <i class="fa-solid fa-eye mr-1"></i>
                    Preview Halaman
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 mr-2">Zoom:</span>
                    <button @click="zoomOut()" class="p-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded">
                        <i class="fa-solid fa-minus text-xs"></i>
                    </button>
                    <span class="text-xs text-gray-600 px-2 min-w-12 text-center"
                        x-text="Math.round(currentZoom * 100) + '%'"></span>
                    <button @click="zoomIn()" class="p-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                    <button @click="resetZoom()" class="ml-2 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">
                        Reset
                    </button>
                </div>
            </div>

            <!-- PDF Pages Grid -->
            <div class="space-y-6" x-show="pdfPages.length > 0 && !loadingPages">
                <template x-for="(page, index) in pdfPages" :key="page.pageNumber">
                    <div
                        class="bg-white rounded-lg shadow-lg border overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Page Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div
                                    class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold">
                                    <span x-text="page.pageNumber"></span>
                                </div>
                                <span class="text-sm font-medium text-gray-700">
                                    Halaman <span x-text="page.pageNumber"></span>
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <span x-text="Math.round(page.width / 2)"></span> × <span
                                    x-text="Math.round(page.height / 2)"></span>px
                            </div>
                        </div>

                        <!-- Page Content -->
                        <div class="p-4 bg-gray-50">
                            <div class="bg-white rounded shadow-md overflow-hidden mx-auto"
                                :style="`width: fit-content; max-width: 100%;`">
                                <img :src="page.dataUrl" :alt="'Halaman ' + page.pageNumber"
                                    class="block mx-auto transition-transform duration-200 hover:scale-105 cursor-pointer"
                                    :style="`transform: scale(${currentZoom}); transform-origin: top center; max-width: 100%; height: auto;`"
                                    @click="window.open(page.dataUrl, '_blank')" loading="lazy">
                            </div>
                        </div>

                        <!-- Page Footer -->
                        <div class="px-4 py-2 bg-gray-50 border-t">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Klik gambar untuk memperbesar</span>
                                <span x-text="'Halaman ' + page.pageNumber + ' dari ' + pdfPages.length"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Loading Pages State -->
            <div x-show="loadingPages" class="space-y-4">
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Memuat Halaman PDF</h4>
                    <p class="text-sm text-gray-600">Sedang mengkonversi PDF menjadi preview halaman...</p>
                </div>

                <!-- Loading Skeleton -->
                <div class="space-y-6">
                    <template x-for="i in 3">
                        <div class="bg-white rounded-lg shadow border overflow-hidden animate-pulse">
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50">
                                <div class="bg-gray-200 rounded mx-auto h-96 max-w-md"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- No PDF State -->
            <template x-if="!pdfUrl && materiData">
                <div class="text-center py-12 text-gray-500">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-file-exclamation text-2xl text-red-500"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">File PDF Tidak Tersedia</h4>
                    <p class="text-sm text-gray-600">File PDF tidak ditemukan atau tidak dapat diakses</p>
                </div>
            </template>


        </div>
    </template>

    <!-- Loading state -->
    <template x-if="loading">
        <div class="text-center py-12 text-gray-500">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <div class="animate-pulse">
                    <i class="fa-solid fa-file-pdf text-2xl text-gray-400"></i>
                </div>
            </div>
            <h4 class="text-lg font-medium text-gray-700 mb-2">Memuat Data</h4>
            <p class="text-sm">Menunggu data dari drawer...</p>
        </div>
    </template>

    <!-- Error state -->
    <template x-if="error">
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-exclamation-triangle text-2xl text-red-500"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-900 mb-2">Terjadi Kesalahan</h4>
            <p class="text-sm text-gray-600 mb-4" x-text="error"></p>
            <button @click="loadPDFPages()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fa-solid fa-refresh mr-2"></i>
                Coba Lagi
            </button>
        </div>
    </template>
</div>

<style>
    /* Custom styles for PDF cards */
    .pdf-page-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .pdf-page-card:hover {
        transform: translateY(-2px);
    }

    /* Zoom transitions */
    img[src*="data:image"] {
        transition: transform 0.2s ease-in-out;
    }

    /* Loading animation */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .pdf-page-card img {
            max-width: 100% !important;
            height: auto !important;
        }
    }

    /* Print styles */
    @media print {
        .pdf-page-card {
            break-inside: avoid;
            margin-bottom: 20px;
        }
    }
</style>
