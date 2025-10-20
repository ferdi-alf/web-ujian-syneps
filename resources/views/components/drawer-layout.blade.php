{{-- components/drawer-layout.blade.php --}}

@props([
    'id' => 'default-drawer',
    'title' => 'Detail Data',
    'description' => 'Informasi detail',
    'type' => 'bottomSheet',
])

<div x-data="drawerManager('{{ $id }}', '{{ $type }}')" x-init="init()" x-on:open-drawer.window="handleOpen($event)"
    x-on:close-drawer.window="handleClose($event)" x-cloak>


    <div x-show="open && drawerType === 'bottomSheet'">

        <div x-show="open" x-transition.opacity @click="closeDrawer()" class="fixed inset-0 bg-black/50 z-45">
        </div>

        <div x-show="open" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transform transition ease-in duration-300" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full" @transitionend="handleTransitionEnd()" @click.stop
            class="fixed bottom-0 left-0 right-0 h-[80vh] bg-white rounded-t-2xl shadow-2xl z-50 flex flex-col">

            <div class="flex justify-center pt-3 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 x-text="currentTitle" class="text-xl font-semibold text-gray-900">{{ $title }}
                        </h2>
                        <p x-text="currentDescription" class="text-sm text-gray-600 mt-1">{{ $description }}</p>
                    </div>
                    <button @click="closeDrawer()"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-h-0 overflow-y-auto">
                <div x-show="loading" class="flex items-center justify-center h-64">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                        <p class="text-gray-600">Memuat data...</p>
                    </div>
                </div>

                <div x-show="error" class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <i class="fa-solid fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                        <p class="text-red-600" x-text="errorMessage"></p>
                        <button @click="closeDrawer()"
                            class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Tutup
                        </button>
                    </div>
                </div>

                <div x-show="!loading && !error" class="md:p-6 p-2">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    <div x-show="open && drawerType === 'slideOver'">

        <div x-show="open" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" @transitionend="handleTransitionEnd()" @click.stop
            class="absolute top-0 mt-16 right-0 w-full h-full bg-white shadow-xl z-44 flex flex-col"
            style="padding-top: 0px !important;">

            <nav class="w-full p-3 shadow-lg flex justify-start">
                <div class="md:w-1/2 w-fit flex justify-between items-center">
                    <button @click="closeDrawer()"
                        class="p-3 rounded-lg border-2 border-gray-100 hover:shadow-lg hover:bg-gray-100 flex justify-center items-center cursor-pointer hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <h2 x-text="currentTitle" class="font-semibold">{{ $title }}</h2>
                </div>
            </nav>

            <div class="p-4 overflow-y-auto flex-1">
                <div x-show="loading" class="flex items-center justify-center h-64">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                        <p class="text-gray-600">Memuat data...</p>
                    </div>
                </div>

                <div x-show="error" class="flex items-center justify-center ">
                    <div class="text-center">
                        <i class="fa-solid fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                        <p class="text-red-600" x-text="errorMessage"></p>
                        <button @click="closeDrawer()"
                            class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Tutup
                        </button>
                    </div>
                </div>

                <div x-show="!loading && !error" class="md:p-6 p-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function drawerManager(drawerId, type) {
        return {
            open: false,
            loading: false,
            error: false,
            errorMessage: '',
            data: null,
            currentTitle: '',
            currentDescription: '',
            drawerType: type,
            drawerId: drawerId,
            isTransitioning: false,

            init() {
                this.currentTitle = '{{ $title }}';
                this.currentDescription = '{{ $description }}';

                console.log('Drawer initialized:', this.drawerId, this.drawerType);
            },

            handleOpen(event) {
                console.log('handleOpen called:', event.detail);

                if (event.detail.id !== this.drawerId) {
                    return;
                }

                this.currentTitle = event.detail.title || this.currentTitle;
                this.currentDescription = event.detail.description || this.currentDescription;

                this.open = true;
                this.isTransitioning = true;
                document.body.classList.add('overflow-hidden');

                console.log('Drawer opening:', this.drawerId);

                if (event.detail.fetchEndpoint) {
                    this.fetchData(event.detail.fetchEndpoint);
                } else {
                    this.dispatchDataEvent({});
                }
            },

            // 🔥 KUNCI: Dispatch event saat transition selesai
            handleTransitionEnd() {
                if (this.open && this.isTransitioning) {
                    this.isTransitioning = false;
                    console.log('✅ Drawer transition complete, dispatching drawer-opened event');

                    // Dispatch custom event
                    window.dispatchEvent(new CustomEvent('drawer-opened', {
                        detail: {
                            drawerId: this.drawerId
                        }
                    }));
                }
            },

            handleClose(event) {
                if (event.detail && event.detail.id !== this.drawerId) return;
                this.closeDrawer();
            },

            closeDrawer() {
                this.open = false;
                this.data = null;
                this.error = false;
                this.errorMessage = '';
                this.loading = false;
                this.isTransitioning = false;
                document.body.classList.remove('overflow-hidden');

                console.log('Drawer closed:', this.drawerId);
            },

            fetchData(endpoint) {
                this.loading = true;
                this.error = false;

                console.log('Fetching data from:', endpoint);

                fetch(endpoint, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        this.loading = false;
                        console.log('Raw response:', result);

                        if (result.success !== undefined) {
                            if (result.success) {
                                this.data = result.data;
                                this.dispatchDataEvent(result.data);
                            } else {
                                this.error = true;
                                this.errorMessage = result.message || 'Gagal memuat data';
                            }
                        } else {
                            this.data = result;
                            this.dispatchDataEvent(result);
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        this.error = true;
                        this.errorMessage = 'Terjadi kesalahan saat memuat data';
                        console.error('Fetch error:', err);
                    });
            },

            dispatchDataEvent(data) {
                console.log('Dispatching data event for drawer:', this.drawerId, 'with data:', data);

                window.dispatchEvent(new CustomEvent('drawerdataloaded', {
                    detail: {
                        drawerId: this.drawerId,
                        data: data
                    }
                }));
            }
        }
    }

    window.openDrawerWithData = function(drawerId, viewData) {
        console.log('openDrawerWithData called:', drawerId, viewData);

        window.dispatchEvent(new CustomEvent('open-drawer', {
            detail: {
                id: drawerId,
                drawerId: drawerId,
                dataId: viewData.id,
                fetchEndpoint: viewData.fetchEndpoint,
                title: viewData.title,
                description: viewData.description,
                type: viewData.type,
                drawerTarget: viewData.drawerTarget
            }
        }));

        console.log('Event dispatched: open-drawer with drawer ID:', drawerId);
    };

    window.closeDrawer = function(drawerId) {
        console.log('closeDrawer called:', drawerId);

        window.dispatchEvent(new CustomEvent('close-drawer', {
            detail: {
                id: drawerId
            }
        }));
    };
</script>
