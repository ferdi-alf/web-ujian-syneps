@props([
    'id' => 'default-drawer',
    'title' => 'Detail Data',
    'description' => 'Informasi detail',
])

<div id="{{ $id }}-overlay" onclick="handleOverlayClick(event, '{{ $id }}')"
    class="fixed inset-0 bg-black/50 z-50 opacity-0 pointer-events-none transition-opacity duration-300">
    <div id="{{ $id }}-drawer"
        class="fixed bottom-0 left-0 right-0 h-[80vh] bg-white rounded-t-2xl shadow-2xl transform translate-y-full transition-transform duration-300 ease-out flex flex-col"
        onclick="event.stopPropagation()">

        <div class="flex justify-center pt-3 pb-2" id="{{ $id }}-drag-handle">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900" id="{{ $id }}-title">{{ $title }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1" id="{{ $id }}-description">{{ $description }}</p>
                </div>
                <button onclick="closeDrawer('{{ $id }}')"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="{{ $id }}-content" class="flex-1 min-h-0 overflow-y-auto">
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeDrawer('{{ $id }}');
    });

    function initializeDrawer(drawerId) {
        let startY = 0;
        let currentY = 0;
        const swipeThreshold = 100;

        const drawer = document.getElementById(drawerId + '-drawer');
        if (drawer) {
            drawer.addEventListener('touchstart', (e) => {
                startY = e.touches[0].clientY;
            });

            drawer.addEventListener('touchmove', (e) => {
                currentY = e.touches[0].clientY;
                const deltaY = currentY - startY;
                if (deltaY > 0) {
                    drawer.style.transform = `translateY(${deltaY}px)`;
                }
            });

            drawer.addEventListener('touchend', (e) => {
                const deltaY = currentY - startY;
                if (deltaY > swipeThreshold) {
                    closeDrawer(drawerId);
                    drawer.style.transform = '';
                } else {
                    drawer.style.transform = '';
                }
            });
        }
    }

    function openDrawer(drawerId, options = {}) {
        console.log("Opening drawer:", drawerId);

        const overlay = document.getElementById(drawerId + '-overlay');
        const drawer = document.getElementById(drawerId + '-drawer');
        const title = document.getElementById(drawerId + '-title');
        const description = document.getElementById(drawerId + '-description');
        const content = document.getElementById(drawerId + '-content');

        if (!overlay || !drawer) {
            console.error('Drawer elements not found');
            return;
        }

        if (options.title && title) {
            title.textContent = options.title;
        }

        if (options.description && description) {
            description.textContent = options.description;
        }

        if (options.content && content) {
            content.querySelector('.p-6').innerHTML = options.content;
        }

        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100');

        setTimeout(() => {
            drawer.classList.remove('translate-y-full');
            drawer.classList.add('translate-y-0');
        }, 10);

        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer(drawerId) {
        console.log("Closing drawer:", drawerId);

        const overlay = document.getElementById(drawerId + '-overlay');
        const drawer = document.getElementById(drawerId + '-drawer');

        if (!overlay || !drawer) return;

        drawer.classList.remove('translate-y-0');
        drawer.classList.add('translate-y-full');

        setTimeout(() => {
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
        }, 300);

        document.body.classList.remove('overflow-hidden');
    }

    function handleOverlayClick(e, drawerId) {
        const drawer = document.getElementById(drawerId + '-drawer');
        if (!drawer.contains(e.target)) {
            closeDrawer(drawerId);
        }
    }

    window.openDrawer = openDrawer;
    window.closeDrawer = closeDrawer;
</script>
