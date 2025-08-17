@if (isset($kelasDetail) && $kelasDetail)
    <section class="detail-hero py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $kelasDetail->nama ?? $kelasDetail['nama'] }}</h1>
                <div class="flex items-center justify-center gap-4 text-gray-600">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $kelasDetail->durasi_belajar ?? ($kelasDetail['durasiBelajar'] ?? 0) }} Bulan
                    </span>
                    @if ($kelasDetail->waktu_magang ?? ($kelasDetail['durasiMagang'] ?? null))
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6">
                                </path>
                            </svg>
                            {{ $kelasDetail->waktu_magang ?? $kelasDetail['durasiMagang'] }} Bulan Magang
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6">
                            <h2 class="text-3xl font-bold text-white">Form Pendaftaran</h2>
                            <p class="text-emerald-100 mt-2">Lengkapi data diri Anda untuk mendaftar</p>
                        </div>

                        @if (!isset($activeBatch) || !$activeBatch)
                            <div class="p-8">
                                <div
                                    class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-xl mb-6">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                        <div>
                                            <strong class="font-semibold">Informasi:</strong>
                                            <span class="block sm:inline">Pendaftaran untuk kelas ini sedang tidak
                                                dibuka. Silakan hubungi admin untuk informasi lebih lanjut.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-8">
                                <div class="flex items-center justify-center mb-8">
                                    <div class="flex items-center space-x-4">
                                        <div class="step-item active" data-step="1">
                                            <div class="step-number">1</div>
                                            <span class="step-text">Data Pribadi</span>
                                        </div>
                                        <div class="step-line"></div>
                                        <div class="step-item" data-step="2">
                                            <div class="step-number">2</div>
                                            <span class="step-text">Program & Pembayaran</span>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('daftar.store') }}" method="POST" id="registrationForm">
                                    @csrf
                                    <input type="hidden" name="kelas_id"
                                        value="{{ $kelasDetail->id ?? $kelasDetail['id'] }}">

                                    <div class="step-content active" id="step-1">
                                        <div class="space-y-6">
                                            <div class="grid md:grid-cols-2 gap-6">
                                                <div class="form-group">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Nama
                                                        Lengkap *</label>
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <input type="text" name="nama_lengkap"
                                                            class="form-input pl-10" placeholder="Masukkan nama lengkap"
                                                            value="{{ old('nama_lengkap') }}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Email
                                                        *</label>
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <input type="email" name="email" class="form-input pl-10"
                                                            placeholder="Masukkan email" value="{{ old('email') }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">No.
                                                        Telepon *</label>
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.554.89l1.555 8.954a1 1 0 01-.554 1.11l-1.555.954a1 1 0 01-1.554-.89L3.28 5.89A1 1 0 013 5z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <input type="tel" name="no_hp" class="form-input pl-10"
                                                            placeholder="Masukkan no. telepon"
                                                            value="{{ old('no_hp') }}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Jenis
                                                        Kelamin *</label>
                                                    <div class="relative">
                                                        <select name="jenis_kelamin" class="form-select" required>
                                                            <option value="">Pilih jenis kelamin</option>
                                                            <option value="Laki-laki"
                                                                {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                                                Laki-laki</option>
                                                            <option value="Perempuan"
                                                                {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                                                Perempuan</option>
                                                        </select>
                                                        <div
                                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Alamat
                                                    *</label>
                                                <textarea name="alamat" class="form-textarea" rows="3" placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-content" id="step-2">
                                        <div class="space-y-6">
                                            <div
                                                class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100">
                                                <h3
                                                    class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                                                    <span
                                                        class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                    Informasi Program
                                                </h3>
                                                <div class="grid md:grid-cols-2 gap-4">
                                                    <div class="form-group">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">Pendidikan
                                                            Terakhir</label>
                                                        <input type="text" name="pendidikan_terakhir"
                                                            class="form-input" placeholder="Contoh: SMA, D3, S1, dll"
                                                            value="{{ old('pendidikan_terakhir') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">Dari
                                                            mana Anda mengetahui program ini? *</label>
                                                        <select name="mengetahui_program_dari" class="form-select"
                                                            required>
                                                            <option value="">Pilih sumber informasi</option>
                                                            <option value="Instagram"
                                                                {{ old('mengetahui_program_dari') == 'Instagram' ? 'selected' : '' }}>
                                                                Instagram</option>
                                                            <option value="Tiktok"
                                                                {{ old('mengetahui_program_dari') == 'Tiktok' ? 'selected' : '' }}>
                                                                Tiktok</option>
                                                            <option value="Facebook"
                                                                {{ old('mengetahui_program_dari') == 'Facebook' ? 'selected' : '' }}>
                                                                Facebook</option>
                                                            <option value="Website"
                                                                {{ old('mengetahui_program_dari') == 'Website' ? 'selected' : '' }}>
                                                                Website</option>
                                                            <option value="Teman/Keluarga"
                                                                {{ old('mengetahui_program_dari') == 'Teman/Keluarga' ? 'selected' : '' }}>
                                                                Teman/Keluarga</option>
                                                            <option value="Google"
                                                                {{ old('mengetahui_program_dari') == 'Google' ? 'selected' : '' }}>
                                                                Google</option>
                                                            <option value="Lain-lain"
                                                                {{ old('mengetahui_program_dari') == 'Lain-lain' ? 'selected' : '' }}>
                                                                Lain-lain</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 rounded-2xl border border-emerald-100">
                                                <h3
                                                    class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                                                    <span
                                                        class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                    Informasi Pembayaran
                                                </h3>

                                                <div class="bg-white p-6 rounded-xl mb-6 border border-emerald-200">
                                                    <div class="space-y-4">
                                                        <div
                                                            class="flex justify-between items-center py-2 border-b border-gray-100">
                                                            <span class="text-gray-600">Harga Kelas:</span>
                                                            <span class="text-2xl font-bold text-gray-900">Rp
                                                                {{ number_format($kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga']), 0, ',', '.') }}</span>
                                                        </div>
                                                        @if (($kelasDetail->dp_persen ?? ($kelasDetail['dp'] ?? 0)) > 0)
                                                            <div
                                                                class="flex justify-between items-center py-2 border-b border-gray-100">
                                                                <span class="text-gray-600">DP
                                                                    ({{ $kelasDetail->dp_persen ?? $kelasDetail['dp'] }}%):</span>
                                                                <span class="text-xl font-bold text-emerald-600">Rp
                                                                    {{ number_format((($kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga'])) * ($kelasDetail->dp_persen ?? $kelasDetail['dp'])) / 100, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center py-2 bg-emerald-50 rounded-lg px-4">
                                                                <span class="text-lg font-semibold text-gray-800">Sisa
                                                                    Bayar:</span>
                                                                <span class="text-xl font-bold text-orange-600">Rp
                                                                    {{ number_format((($kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga'])) * (100 - ($kelasDetail->dp_persen ?? $kelasDetail['dp']))) / 100, 0, ',', '.') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="grid md:grid-cols-2 gap-4">
                                                    <div class="form-group">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">Jumlah
                                                            Cicilan *</label>
                                                        <select name="jumlah_cicilan" class="form-select" required
                                                            id="jumlah_cicilan">
                                                            <option value="">Pilih jumlah cicilan</option>
                                                            <option value="1"
                                                                {{ old('jumlah_cicilan') == '1' ? 'selected' : '' }}>1x
                                                                (Lunas)</option>
                                                            <option value="2"
                                                                {{ old('jumlah_cicilan') == '2' ? 'selected' : '' }}>2x
                                                                Cicilan</option>
                                                            <option value="3"
                                                                {{ old('jumlah_cicilan') == '3' ? 'selected' : '' }}>3x
                                                                Cicilan</option>
                                                            <option value="4"
                                                                {{ old('jumlah_cicilan') == '4' ? 'selected' : '' }}>4x
                                                                Cicilan</option>
                                                            <option value="6"
                                                                {{ old('jumlah_cicilan') == '6' ? 'selected' : '' }}>6x
                                                                Cicilan</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">Tagihan
                                                            per Bulan</label>
                                                        <input type="text" name="tagihan_per_bulan_display"
                                                            class="form-input bg-gray-50" readonly
                                                            placeholder="Akan dihitung otomatis" id="tagihan_display">
                                                        <input type="hidden" name="tagihan_per_bulan"
                                                            id="tagihan_per_bulan">
                                                    </div>
                                                </div>

                                                <input type="hidden" name="total_tagihan"
                                                    value="{{ $kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga']) }}">
                                            </div>

                                            <div
                                                class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-2xl border border-purple-100">
                                                <h3
                                                    class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                                                    <span
                                                        class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                    Konfirmasi Pendaftaran
                                                </h3>
                                                <div class="space-y-3 text-sm">
                                                    <div class="flex justify-between py-2 border-b border-purple-200">
                                                        <span class="text-gray-600">Kelas:</span>
                                                        <span
                                                            class="font-semibold text-gray-800">{{ $kelasDetail->nama ?? $kelasDetail['nama'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between py-2 border-b border-purple-200">
                                                        <span class="text-gray-600">Batch:</span>
                                                        <span
                                                            class="font-semibold text-gray-800">{{ $activeBatch->nama ?? 'Belum ditentukan' }}</span>
                                                    </div>
                                                    <div class="flex justify-between py-2 border-b border-purple-200">
                                                        <span class="text-gray-600">Total Harga:</span>
                                                        <span class="font-semibold text-gray-800">Rp
                                                            {{ number_format($kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga']), 0, ',', '.') }}</span>
                                                    </div>
                                                    @if (($kelasDetail->dp_persen ?? ($kelasDetail['dp'] ?? 0)) > 0)
                                                        <div
                                                            class="flex justify-between py-2 border-b border-purple-200">
                                                            <span class="text-gray-600">DP:</span>
                                                            <span class="font-semibold text-gray-800">Rp
                                                                {{ number_format((($kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga'])) * ($kelasDetail->dp_persen ?? $kelasDetail['dp'])) / 100, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mt-4 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                                                    <div class="flex items-start gap-3">
                                                        <svg class="w-6 h-6 text-yellow-600 mt-0.5 flex-shrink-0"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                            </path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm text-yellow-800 font-medium">
                                                                <strong>📝 Catatan:</strong> Setelah mendaftar, tim kami
                                                                akan menghubungi Anda dalam 1x24 jam untuk konfirmasi
                                                                pembayaran dan informasi lebih lanjut.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <div class="flex justify-between items-center p-8 bg-gray-50 border-t border-gray-100">
                            <button type="button" class="btn-secondary" id="prevBtn" style="display: none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Sebelumnya
                            </button>
                            <button type="button" class="btn-primary" id="nextBtn">
                                Selanjutnya
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div
                        class="bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600 rounded-3xl p-8 text-white shadow-2xl sticky top-8">
                        <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                            <span
                                class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            Informasi Kelas
                        </h3>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center py-3 border-b border-emerald-400 border-opacity-30">
                                <span class="text-emerald-100">Harga:</span>
                                <span class="text-xl font-bold">Rp
                                    {{ number_format($kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga'] ?? '0'), 0, ',', '.') }}</span>
                            </div>
                            @if (($kelasDetail->dp_persen ?? ($kelasDetail['dp'] ?? 0)) > 0)
                                <div
                                    class="flex justify-between items-center py-3 border-b border-emerald-400 border-opacity-30">
                                    <span class="text-emerald-100">DP:</span>
                                    <span
                                        class="text-xl font-bold">{{ $kelasDetail->dp_persen ?? $kelasDetail['dp'] }}%</span>
                                </div>
                            @endif
                            <div
                                class="flex justify-between items-center py-3 border-b border-emerald-400 border-opacity-30">
                                <span class="text-emerald-100">Tipe:</span>
                                <span
                                    class="text-xl font-bold">{{ ucfirst($kelasDetail->type ?? ($kelasDetail['tipe'] ?? 'Regular')) }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-3 border-b border-emerald-400 border-opacity-30">
                                <span class="text-emerald-100">Durasi:</span>
                                <span
                                    class="text-xl font-bold">{{ $kelasDetail->durasi_belajar ?? ($kelasDetail['durasiBelajar'] ?? 0) }}
                                    Bulan</span>
                            </div>
                            @if ($kelasDetail->waktu_magang ?? ($kelasDetail['durasiMagang'] ?? null))
                                <div
                                    class="flex justify-between items-center py-3 border-b border-emerald-400 border-opacity-30">
                                    <span class="text-emerald-100">Magang:</span>
                                    <span
                                        class="text-xl font-bold">{{ $kelasDetail->waktu_magang ?? $kelasDetail['durasiMagang'] }}
                                        Bulan</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8 pt-6 border-t border-emerald-400 border-opacity-30">
                            <h4 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                </svg>
                                Butuh Bantuan?
                            </h4>
                            <a href="https://wa.me/6283178569163?text=Halo%20saya%20ingin%20bertanya%20tentang%20kelas%20{{ urlencode($kelasDetail->nama ?? $kelasDetail['nama']) }}"
                                target="_blank"
                                class="inline-flex items-center gap-3 bg-white text-emerald-600 px-6 py-3 rounded-xl font-semibold hover:bg-emerald-50 transition-all duration-300 w-full justify-center group hover:scale-105">
                                <span>Hubungi WhatsApp</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Data Kelas Tidak Ditemukan</h1>
            <p class="text-gray-600 mb-6">Maaf, data kelas yang Anda cari tidak tersedia.</p>
            <a href="{{ url('/') }}" class="btn-primary">
                Kembali ke Beranda
            </a>
        </div>
    </div>
@endif

<style>
    .detail-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    }

    .form-group {
        position: relative;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        background-color: #ffffff;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        font-weight: 500;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        transform: translateY(-2px);
        background-color: #fafafa;
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .form-input.pl-10 {
        padding-left: 3rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1.25rem 2.5rem;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        padding: 1.25rem 2.5rem;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .step-number {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        transition: all 0.4s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 3px solid transparent;
    }

    .step-item.active .step-number {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transform: scale(1.15);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .step-text {
        font-size: 0.95rem;
        color: #6b7280;
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
    }

    .step-item.active .step-text {
        color: #10b981;
        font-weight: 800;
        transform: scale(1.05);
    }

    .step-line {
        width: 5rem;
        height: 4px;
        background: linear-gradient(90deg, #e5e7eb 0%, #d1d5db 100%);
        margin: 0 1.5rem;
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sticky {
        position: sticky;
        top: 2rem;
    }

    .form-input.bg-gray-50 {
        background-color: #f9fafb;
        color: #6b7280;
        font-weight: 600;
    }

    .form-input.bg-gray-50:focus {
        background-color: #f3f4f6;
        color: #374151;
    }

    .bg-gradient-to-r.from-blue-50.to-indigo-50,
    .bg-gradient-to-r.from-emerald-50.to-teal-50,
    .bg-gradient-to-r.from-purple-50.to-pink-50 {
        transition: all 0.3s ease;
    }

    .bg-gradient-to-r.from-blue-50.to-indigo-50:hover,
    .bg-gradient-to-r.from-emerald-50.to-teal-50:hover,
    .bg-gradient-to-r.from-purple-50.to-pink-50:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    let currentStep = 1;
    const totalSteps = 2;
    const kelasHarga = {{ $kelasDetail->harga ?? str_replace('.', '', $kelasDetail['harga'] ?? '0') }};
    const dpPersen = {{ $kelasDetail->dp_persen ?? ($kelasDetail['dp'] ?? 0) }};

    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });

        document.getElementById(`step-${step}`).classList.add('active');

        document.querySelectorAll('.step-item').forEach((item, index) => {
            item.classList.remove('active');
            if (index + 1 <= step) {
                item.classList.add('active');
            }
        });

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (step === 1) {
            prevBtn.style.display = 'none';
            nextBtn.innerHTML =
                'Selanjutnya <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
        } else if (step === totalSteps) {
            prevBtn.style.display = 'inline-flex';
            nextBtn.innerHTML = '🚀 Daftar Sekarang';
        }
    }

    function nextStep() {
        if (currentStep < totalSteps) {
            if (validateCurrentStep()) {
                currentStep++;
                showStep(currentStep);
            }
        } else {
            if (validateCurrentStep()) {
                document.getElementById('registrationForm').submit();
            }
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }

    function validateCurrentStep() {
        const currentStepElement = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');

        for (let field of requiredFields) {
            if (!field.value.trim()) {
                field.focus();
                alert('Mohon lengkapi semua field yang wajib diisi.');
                return false;
            }
        }
        return true;
    }

    function calculateInstallment() {
        const jumlahCicilan = document.getElementById('jumlah_cicilan').value;
        const tagihanDisplay = document.getElementById('tagihan_display');
        const tagihanHidden = document.getElementById('tagihan_per_bulan');

        if (jumlahCicilan) {
            let sisaBayar = kelasHarga;

            if (dpPersen > 0) {
                const dpAmount = kelasHarga * dpPersen / 100;
                sisaBayar = kelasHarga - dpAmount;
            }

            const tagihanPerBulan = Math.ceil(sisaBayar / parseInt(jumlahCicilan));

            tagihanDisplay.value = 'Rp ' + tagihanPerBulan.toLocaleString('id-ID');
            tagihanHidden.value = tagihanPerBulan;
        } else {
            tagihanDisplay.value = '';
            tagihanHidden.value = '';
        }
    }

    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('prevBtn').addEventListener('click', prevStep);

    const jumlahCicilanElement = document.getElementById('jumlah_cicilan');
    if (jumlahCicilanElement) {
        jumlahCicilanElement.addEventListener('change', calculateInstallment);
    }

    showStep(1);
</script>
