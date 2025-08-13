<nav class="bg-white shadow-lg sticky top-0 z-50 border-b-2 border-teal-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/nav-logo.png') }}" alt="Syneps Academy" class="h-10">
                </a>
            </div>

            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="{{ url('/') }}"
                        class="text-gray-700 hover:text-teal-500 px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:bg-teal-50">
                        Home
                    </a>

                    <div class="relative group">
                        <button
                            class="text-gray-700 hover:text-teal-500 px-3 py-2 rounded-md text-sm font-medium flex items-center transition-all duration-300 hover:bg-teal-50">
                            Coding Bootcamp
                            <svg class="ml-1 h-4 w-4 transform group-hover:rotate-180 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-teal-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 ease-out z-50">
                            <div class="py-3">
                                <div class="relative group/sub">
                                    <button
                                        class="flex items-center justify-between w-full px-4 py-3 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-all duration-200">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Part-time 4 Bulan
                                        </span>
                                        <svg class="h-4 w-4 transform group-hover/sub:rotate-90 transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <div
                                        class="absolute left-full top-0 ml-2 w-60 bg-white rounded-lg shadow-xl border border-teal-100 opacity-0 invisible group-hover/sub:opacity-100 group-hover/sub:visible transform translate-x-2 group-hover/sub:translate-x-0 transition-all duration-300 ease-out">
                                        <div class="py-3">
                                            <a href="#"
                                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-all duration-200">
                                                <svg class="w-4 h-4 mr-2 text-teal-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                                </svg>
                                                Fullstack Web Dev
                                            </a>
                                            <a href="#"
                                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-all duration-200">
                                                <svg class="w-4 h-4 mr-2 text-teal-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                Fullstack Android Dev
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <a href="#"
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Intensif 2 Bulan
                                </a>

                                <a href="#"
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-600 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Data Analytics
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="#"
                        class="text-gray-700 hover:text-teal-500 px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:bg-teal-50">
                        Online Class
                    </a>

                    <a href="#"
                        class="text-gray-700 hover:text-teal-500 px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:bg-teal-50">
                        Project Based
                    </a>
                </div>
            </div>

            <div class="hidden md:block">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 px-6 py-2 rounded-lg text-sm font-medium hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        Dashboard
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login.index') }}"
                        class="bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 px-6 py-2 rounded-lg text-sm font-medium hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        Login
                    </a>
                @endguest
            </div>

            <div class="md:hidden">
                <button type="button" id="mobile-menu-button"
                    class="text-gray-700 hover:text-teal-500 focus:outline-none p-2 rounded-md hover:bg-teal-50 transition-all duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-teal-100 shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1 max-h-96 overflow-y-auto">
            <a href="{{ url('/') }}"
                class="block px-4 py-3 text-gray-700 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                Home
            </a>

            <div class="space-y-1">
                <button id="mobile-bootcamp-toggle"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        Coding Bootcamp
                    </span>
                    <svg class="h-5 w-5 transform transition-transform duration-300 ease-in-out" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="mobile-bootcamp-menu"
                    class="hidden pl-4 space-y-1 transform transition-all duration-300 ease-in-out">
                    <div>
                        <button id="mobile-parttime-toggle"
                            class="flex items-center justify-between w-full px-4 py-3 text-sm text-gray-600 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Part-time 4 Bulan
                            </span>
                            <svg class="h-4 w-4 transform transition-transform duration-300 ease-in-out"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="mobile-parttime-menu"
                            class="hidden pl-6 space-y-1 transform transition-all duration-300 ease-in-out">
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                                <svg class="w-3 h-3 mr-2 text-teal-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                Fullstack Web Dev
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                                <svg class="w-3 h-3 mr-2 text-teal-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Fullstack Android Dev
                            </a>
                        </div>
                    </div>

                    <a href="#"
                        class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Intensif 2 Bulan
                    </a>
                    <a href="#"
                        class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Data Analytics
                    </a>
                </div>
            </div>

            <a href="#"
                class="flex items-center px-4 py-3 text-gray-700 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Online Class
            </a>
            <a href="#"
                class="flex items-center px-4 py-3 text-gray-700 hover:text-teal-500 hover:bg-teal-50 rounded-lg transition-all duration-200">
                <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Project Based
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-3 bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 rounded-lg hover:shadow-lg transition-all duration-200 text-center font-medium">
                    Dashboard
                </a>
            @endauth
            @guest
                <a href="{{ route('login.index') }}"
                    class="block px-4 py-3 bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 rounded-lg hover:shadow-lg transition-all duration-200 text-center font-medium">
                    Login
                </a>
            @endguest
        </div>
    </div>
</nav>

<style>
    nav {
        border-bottom: 2px solid #e6fffa !important;
    }


    * {
        border-color: transparent !important;
    }

    nav,
    nav * {
        border-color: #e6fffa !important;
    }


    .border-blue-500,
    .border-blue-600,
    .border-blue-700 {
        border-color: transparent !important;
    }
</style>

<script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    mobileMenuButton.addEventListener('click', () => {
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
            mobileMenu.style.maxHeight = '0px';
            mobileMenu.style.opacity = '0';

            mobileMenu.offsetHeight;

            mobileMenu.style.transition = 'max-height 0.3s ease-out, opacity 0.3s ease-out';
            mobileMenu.style.maxHeight = '500px';
            mobileMenu.style.opacity = '1';

            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        } else {
            mobileMenu.style.transition = 'max-height 0.3s ease-in, opacity 0.3s ease-in';
            mobileMenu.style.maxHeight = '0px';
            mobileMenu.style.opacity = '0';

            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);

            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });

    const bootcampToggle = document.getElementById('mobile-bootcamp-toggle');
    const bootcampMenu = document.getElementById('mobile-bootcamp-menu');
    const bootcampArrow = bootcampToggle.querySelector('svg:last-child');

    bootcampToggle.addEventListener('click', () => {
        if (bootcampMenu.classList.contains('hidden')) {
            bootcampMenu.classList.remove('hidden');
            bootcampMenu.style.maxHeight = '0px';
            bootcampMenu.style.opacity = '0';

            bootcampMenu.offsetHeight;

            bootcampMenu.style.transition =
                'max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            bootcampMenu.style.maxHeight = '300px';
            bootcampMenu.style.opacity = '1';

            bootcampArrow.style.transform = 'rotate(180deg)';
        } else {
            bootcampMenu.style.transition =
                'max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            bootcampMenu.style.maxHeight = '0px';
            bootcampMenu.style.opacity = '0';

            setTimeout(() => {
                bootcampMenu.classList.add('hidden');
            }, 300);

            bootcampArrow.style.transform = 'rotate(0deg)';
        }
    });

    const parttimeToggle = document.getElementById('mobile-parttime-toggle');
    const parttimeMenu = document.getElementById('mobile-parttime-menu');
    const parttimeArrow = parttimeToggle.querySelector('svg:last-child');

    parttimeToggle.addEventListener('click', () => {
        if (parttimeMenu.classList.contains('hidden')) {
            parttimeMenu.classList.remove('hidden');
            parttimeMenu.style.maxHeight = '0px';
            parttimeMenu.style.opacity = '0';

            parttimeMenu.offsetHeight;

            parttimeMenu.style.transition =
                'max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            parttimeMenu.style.maxHeight = '200px';
            parttimeMenu.style.opacity = '1';

            parttimeArrow.style.transform = 'rotate(180deg)';
        } else {
            parttimeMenu.style.transition =
                'max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            parttimeMenu.style.maxHeight = '0px';
            parttimeMenu.style.opacity = '0';

            setTimeout(() => {
                parttimeMenu.classList.add('hidden');
            }, 300);

            parttimeArrow.style.transform = 'rotate(0deg)';
        }
    });

    document.addEventListener('click', (event) => {
        const isClickInsideMenu = mobileMenu.contains(event.target);
        const isClickOnButton = mobileMenuButton.contains(event.target);

        if (!isClickInsideMenu && !isClickOnButton && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.style.transition = 'max-height 0.3s ease-in, opacity 0.3s ease-in';
            mobileMenu.style.maxHeight = '0px';
            mobileMenu.style.opacity = '0';

            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);

            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });
</script>
