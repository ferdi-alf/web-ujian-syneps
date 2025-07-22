<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-44 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <div class="p-2 group border-b">
            <a href="#" class="flex items-center">
                <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="profile"
                    class="h-10 w-10 rounded-full border-teal-900 border" />
                <div class="flex flex-col">
                    <span class="ms-3 font-bold text-teal-400">{{ Auth::user()->name }}</span>
                    <span class="ms-3 font-light capitalize text-teal-600">{{ Auth::user()->role }}</span>
                </div>
            </a>
        </div>

        <ul class="space-y-2 font-medium mt-10">
            <x-fragments.sidebar-item route="dashboard" icon="gauge"
                colors="emerald">Dashboard</x-fragments.sidebar-item>
            <x-fragments.sidebar-item route="users.index" icon="user"
                colors="emerald">Users</x-fragments.sidebar-item>
            <x-fragments.sidebar-item route="peserta.index" icon="users"
                colors="emerald">Peserta</x-fragments.sidebar-item>
            <x-fragments.sidebar-item route="kelas.index" icon="code-branch"
                colors="emerald">Kelas</x-fragments.sidebar-item>
            <x-fragments.sidebar-item route="manajemen-ujian.index" icon="book" colors="emerald">Manajemen
                Ujian</x-fragments.sidebar-item>
            <x-fragments.sidebar-item route="tambah-ujian.index" icon="plus" colors="emerald">Tambah
                ujian</x-fragments.sidebar-item>
            <x-fragments.sidebar-item route="leaderboard.siswa" icon="chart-simple" colors="emerald">Leaderboard Member
            </x-fragments.sidebar-item>
        </ul>
    </div>
</aside>
