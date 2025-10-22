<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite('resources/css/app.css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <title>Dashboard</title>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .dropdown-submenu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
        }

        .dropdown-submenu.show {
            max-height: 300px;

            opacity: 1;
        }

        .chevron {
            transition: transform 0.2s ease;
        }

        .chevron.rotate {
            transform: rotate(180deg);
        }

        .preload * {
            transition: none !important;
            animation: none !important;
        }

        .dropdown-toggle {
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .dropdown-icon {
            transition: color 0.2s ease;
        }

        .dropdown-submenu a {
            transition: background-color 0.2s ease, color 0.2s ease;
            border-radius: 0.5rem;
            display: block;
        }

        .dropdown-submenu a:hover {
            background-color: rgba(16, 185, 129, 0.1);
            color: rgba(16, 185, 129, 1);
        }

        .dropdown-submenu a.bg-emerald-100 {
            background-color: rgba(16, 185, 129, 0.2) !important;
            color: rgba(5, 150, 105, 1) !important;
            font-weight: 500;
        }
    </style>
</head>

<body>

    @include('components.navigation')
    @include('components.sidebar')


    <div class="md:p-4 relative p-2 sm:ml-64 bg-gray-50 min-h-screen overflow-y-auto">
        <div class="p-4 mt-14 relative">
            @yield('content')
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        class MultipleDropdownManager {
            constructor() {
                this.currentPath = window.location.pathname;
                this.isInitialized = false;
                this.init();
            }

            init() {
                if (this.isInitialized) {
                    this.checkActiveRoutes();
                    return;
                }

                document.body.classList.add('preload');
                setTimeout(() => document.body.classList.remove('preload'), 100);

                this.setupDropdowns();
                this.checkActiveRoutes();
                this.isInitialized = true;
            }

            setupDropdowns() {
                this.removeExistingListeners();

                const toggles = document.querySelectorAll('.dropdown-toggle');

                toggles.forEach(toggle => {
                    const newToggle = toggle.cloneNode(true);
                    toggle.parentNode.replaceChild(newToggle, toggle);

                    newToggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleDropdown(newToggle);
                    });
                });
            }

            removeExistingListeners() {
                const processed = document.querySelectorAll('.dropdown-toggle[data-processed]');
                processed.forEach(el => el.removeAttribute('data-processed'));
            }

            toggleDropdown(toggle) {
                const dropdownId = toggle.getAttribute('data-dropdown');
                const submenu = document.querySelector(`[data-submenu="${dropdownId}"]`);
                const chevron = toggle.querySelector('.dropdown-chevron');

                if (submenu && chevron) {
                    const isOpen = submenu.classList.contains('show');

                    if (isOpen) {
                        submenu.classList.remove('show');
                        chevron.classList.remove('rotate');
                    } else {
                        // Allow multiple dropdowns to be open
                        submenu.classList.add('show');
                        chevron.classList.add('rotate');
                    }

                    toggle.setAttribute('data-processed', 'true');
                }
            }

            checkActiveRoutes() {
                const toggles = document.querySelectorAll('.dropdown-toggle');

                toggles.forEach(toggle => {
                    const routesAttr = toggle.getAttribute('data-routes');
                    if (!routesAttr) return;

                    const routes = routesAttr.split(',').map(route => route.trim());
                    const dropdownId = toggle.getAttribute('data-dropdown');
                    const submenu = document.querySelector(`[data-submenu="${dropdownId}"]`);
                    const icon = toggle.querySelector('.dropdown-icon');
                    const chevron = toggle.querySelector('.dropdown-chevron');

                    this.resetDropdownStyles(toggle, icon);

                    const isActive = routes.some(route => {
                        return this.currentPath.includes(route) ||
                            this.currentPath.startsWith('/' + route) ||
                            this.currentPath === '/' + route;
                    });

                    if (isActive) {
                        this.setActiveDropdown(toggle, icon, submenu, chevron);
                        this.highlightActiveSubmenuItem(submenu);
                    }
                });
            }

            resetDropdownStyles(toggle, icon) {
                toggle.classList.remove('text-emerald-600');
                toggle.classList.add('text-gray-500');

                if (icon) {
                    icon.classList.remove('text-emerald-400');
                    icon.classList.add('text-gray-500');
                }
            }

            setActiveDropdown(toggle, icon, submenu, chevron) {
                if (icon) {
                    icon.classList.remove('text-gray-500');
                    icon.classList.add('text-emerald-400');
                }

                toggle.classList.remove('text-gray-500');
                toggle.classList.add('text-emerald-600');

                if (submenu && chevron) {
                    submenu.style.transition = 'none';
                    submenu.classList.add('show');
                    chevron.classList.add('rotate');

                    setTimeout(() => {
                        submenu.style.transition = '';
                    }, 50);
                }
            }

            highlightActiveSubmenuItem(submenu) {
                if (!submenu) return;

                const links = submenu.querySelectorAll('a');
                links.forEach(link => {
                    link.classList.remove('bg-emerald-100', 'text-emerald-700', 'font-medium');

                    const href = link.getAttribute('href');
                    if (href) {
                        const linkPath = href.replace(/^\/+/, '');
                        const currentPathClean = this.currentPath.replace(/^\/+/, '');

                        if (currentPathClean === linkPath ||
                            currentPathClean.startsWith(linkPath + '/') ||
                            (linkPath && currentPathClean.includes(linkPath))) {
                            link.classList.add('bg-emerald-100', 'text-emerald-700', 'font-medium');
                        }
                    }
                });
            }

            updatePath(newPath) {
                this.currentPath = newPath;
                this.checkActiveRoutes();
            }

            refresh() {
                this.isInitialized = false;
                this.init();
            }
        }

        let dropdownManager = null;

        function initDropdownManager() {
            if (!dropdownManager) {
                dropdownManager = new MultipleDropdownManager();
            } else {
                dropdownManager.updatePath(window.location.pathname);
            }
        }

        document.addEventListener('DOMContentLoaded', initDropdownManager);
        document.addEventListener('turbo:load', initDropdownManager);
        document.addEventListener('livewire:navigated', initDropdownManager);

        window.addEventListener('popstate', () => {
            if (dropdownManager) {
                dropdownManager.updatePath(window.location.pathname);
            }
        });

        window.refreshSidebarDropdowns = function() {
            if (dropdownManager) {
                dropdownManager.refresh();
            }
        };

        @if (session('alert'))
            @php $alert = session('alert'); @endphp

            Swal.fire({
                icon: '{{ $alert['type'] }}',
                title: '{{ $alert['title'] }}',
                text: '{{ $alert['message'] }}',
                confirmButtonColor: '#991b1b',
                timer: 6000,
                timerProgressBar: true
            });
        @endif

        @if ($errors->any())
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessages,
                confirmButtonColor: '#991b1b'
            });
        @endif
    </script>

    @stack('scripts')
    @stack('modals')
</body>

</html>
