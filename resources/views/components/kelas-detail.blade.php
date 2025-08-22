@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endpush

<section class="min-h-screen bg-white py-6 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <a href="{{ route('index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 font-semibold shadow hover:shadow-lg transition-all text-sm sm:text-base">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
            </a>
            @if(isset($activeBatch) && (($activeBatch->status ?? '') === 'registration'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 shadow">Pendaftaran Dibuka</span>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 sm:px-6 py-4 rounded-lg shadow-sm" id="success-alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">
                        <p class="font-medium text-sm sm:text-base">{{ session('success') }}</p>
                    </div>
                    <button onclick="document.getElementById('success-alert').remove()" class="ml-4 text-emerald-600 hover:text-emerald-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 sm:px-6 py-4 rounded-lg shadow-sm" id="error-alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">
                        <p class="font-medium mb-2 text-sm sm:text-base">Terdapat beberapa kesalahan:</p>
                        <ul class="list-disc list-inside text-xs sm:text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="document.getElementById('error-alert').remove()" class="ml-4 text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Left: Register Stepper Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-2xl rounded-2xl overflow-hidden ring-1 ring-gray-100">
                    <div class="px-4 sm:px-6 py-6 sm:py-8 text-center bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400">
                        <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900">Registrasi Kelas: {{ $kelasDetail->nama }}</h1>
                        <p class="text-gray-800/80 mt-2 text-sm sm:text-base">Lengkapi data Anda dengan mengikuti langkah-langkah berikut</p>
                    </div>

                    <div class="bg-white px-4 sm:px-6 py-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Step 1 -->
                            <div class="flex items-center flex-1">
                                <div id="step-1-circle" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold transition-all duration-300">1</div>
                                <div class="ml-3"><p id="step-1-title" class="text-xs sm:text-sm font-medium text-emerald-600">Data Pribadi</p></div>
                            </div>
                            <!-- Connector 1 -->
                            <div id="connector-1" class="flex-1 h-0.5 bg-gray-300 mx-4 transition-all duration-300 hidden sm:block"></div>
                            <!-- Step 2 -->
                            <div class="flex items-center flex-1">
                                <div id="step-2-circle" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 font-semibold transition-all duration-300">2</div>
                                <div class="ml-3"><p id="step-2-title" class="text-xs sm:text-sm font-medium text-gray-500">Kontak & Alamat</p></div>
                            </div>
                            <!-- Connector 2 -->
                            <div id="connector-2" class="flex-1 h-0.5 bg-gray-300 mx-4 transition-all duration-300 hidden sm:block"></div>
                            <!-- Step 3 -->
                            <div class="flex items-center flex-1">
                                <div id="step-3-circle" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 font-semibold transition-all duration-300">3</div>
                                <div class="ml-3"><p id="step-3-title" class="text-xs sm:text-sm font-medium text-gray-500">Pembayaran</p></div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('daftar.store') }}" enctype="multipart/form-data" class="px-4 sm:px-6 pb-8" id="form-daftar" data-dp-persen="{{ (int)($kelasDetail->dp_persen ?? 0) }}">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelasDetail->id }}">

                        <div id="step-1" class="transition duration-500 transform step-content animate__animated">
                            <div class="space-y-6">
                                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 border-b border-gray-200 pb-3">Data Pribadi</h2>
                                <div>
                                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" placeholder="Masukkan nama lengkap Anda" required>
                                    @error('nama_lengkap')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                                        <label class="flex items-center cursor-pointer"><input type="radio" name="jenis_kelamin" value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500"><span class="ml-2 text-gray-700 text-sm sm:text-base">Laki-laki</span></label>
                                        <label class="flex items-center cursor-pointer"><input type="radio" name="jenis_kelamin" value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500"><span class="ml-2 text-gray-700 text-sm sm:text-base">Perempuan</span></label>
                                    </div>
                                    @error('jenis_kelamin')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir (opsional)</label>
                                    <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base">
                                        <option value="" {{ old('pendidikan_terakhir') ? '' : 'selected' }}>- Pilih Pendidikan Terakhir -</option>
                                        @foreach(['SD','SMP','SMA/SMK','Diploma','Sarjana','Magister','Doktor'] as $opt)
                                            <option value="{{ $opt }}" {{ old('pendidikan_terakhir') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    @error('pendidikan_terakhir')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div id="step-2" class="transition duration-500 transform step-content hidden animate__animated">
                            <div class="space-y-6">
                                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 border-b border-gray-200 pb-3">Kontak & Alamat</h2>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" placeholder="contoh@email.com" required>
                                    @error('email')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">No. HP <span class="text-red-500">*</span></label>
                                    <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" placeholder="08xx xxxx xxxx" required>
                                    @error('no_hp')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="4" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 resize-none text-sm sm:text-base" placeholder="Masukkan alamat lengkap Anda" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="mengetahui_program_dari" class="block text-sm font-medium text-gray-700 mb-2">Info kelas ini dari mana? <span class="text-red-500">*</span></label>
                                    <select id="mengetahui_program_dari" name="mengetahui_program_dari" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" required>
                                        <option value="" disabled {{ old('mengetahui_program_dari') ? '' : 'selected' }}>- Pilih Sumber -</option>
                                        @foreach(['Instagram','Tiktok','Facebook','Website','Teman/Keluarga','Google','Lain-lain'] as $src)
                                            <option value="{{ $src }}" {{ old('mengetahui_program_dari') == $src ? 'selected' : '' }}>{{ $src }}</option>
                                        @endforeach
                                    </select>
                                    @error('mengetahui_program_dari')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div id="step-3" class="transition duration-500 transform step-content hidden animate__animated">
                            <div class="space-y-6">
                                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 border-b border-gray-200 pb-3">Pembayaran</h2>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Tagihan <span class="text-red-500">*</span></label>
                                        <input type="number" name="total_tagihan" id="total_tagihan" value="{{ old('total_tagihan', $kelasDetail->harga) }}" step="1" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" required>
                                        @error('total_tagihan')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Cicilan <span class="text-red-500">*</span></label>
                                        <select name="jumlah_cicilan" id="jumlah_cicilan" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" required>
                                            @for($i=1;$i<=12;$i++)
                                                <option value="{{ $i }}" {{ (int)old('jumlah_cicilan', 1) === $i ? 'selected' : '' }}>{{ $i }}x</option>
                                            @endfor
                                        </select>
                                        @error('jumlah_cicilan')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tagihan per Bulan (otomatis)</label>
                                            <input type="number" name="tagihan_per_bulan" id="tagihan_per_bulan" value="{{ old('tagihan_per_bulan') }}" step="1" class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 text-sm sm:text-base" readonly>
                                            @error('tagihan_per_bulan')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                                            <div class="text-sm text-gray-700">Rincian</div>
                                            <div class="mt-2 space-y-1 text-xs sm:text-sm">
                                                <div class="flex justify-between"><span>DP (<span id="dp_persen_text">{{ (int)($kelasDetail->dp_persen ?? 0) }}</span>%)</span><span id="dp_nominal_text">Rp 0</span></div>
                                                <div class="flex justify-between"><span>Sisa Tagihan</span><span id="sisa_tagihan_text">Rp 0</span></div>
                                                <div class="flex justify-between font-semibold text-emerald-700"><span>Per Bulan</span><span id="per_bulan_text">Rp 0 / bulan</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="bukti_pembayaran_dp" class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran DP <span class="text-red-500">*</span></label>
                                    <div id="drop-area" class="w-full h-32 sm:h-48 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-500 cursor-pointer hover:border-emerald-500 transition-colors duration-200">
                                        <div class="text-center">
                                            <p class="text-sm sm:text-base">Seret dan lepas gambar di sini atau klik untuk memilih</p>
                                            <p class="text-xs text-gray-400">Hanya file JPG, PNG (maks. 2MB)</p>
                                        </div>
                                    </div>
                                    <input type="file" id="bukti_pembayaran_dp" name="bukti_pembayaran_dp" accept="image/jpeg,image/png" class="hidden">
                                    <div id="image-preview" class="mt-4 hidden">
                                        <img id="preview-img" src="" alt="Preview" class="max-w-full h-auto rounded-lg shadow-sm">
                                        <button type="button" id="remove-image" class="mt-2 text-red-500 text-xs sm:text-sm hover:text-red-700">Hapus Gambar</button>
                                    </div>
                                    @error('bukti_pembayaran_dp')<p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>@enderror
                                    <p id="file-error" class="text-red-500 text-xs sm:text-sm mt-1 hidden"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between mt-6 sm:mt-8 pt-6 border-t border-gray-200 gap-4">
                            <button type="button" id="prevBtn" class="hidden px-4 sm:px-6 py-2 sm:py-3 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-4 focus:ring-gray-200 transition-all font-medium text-sm sm:text-base">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Sebelumnya
                            </button>
                            <button type="button" id="nextBtn" class="px-4 sm:px-6 py-2 sm:py-3 rounded-xl bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 hover:shadow-lg focus:ring-4 focus:ring-emerald-200 transition-all font-semibold text-sm sm:text-base sm:ml-auto">
                                Selanjutnya
                                <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <button type="submit" id="submitBtn" class="hidden px-6 sm:px-8 py-2 sm:py-3 rounded-xl bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 hover:shadow-lg focus:ring-4 focus:ring-emerald-200 transition-all font-semibold text-sm sm:text-base sm:ml-auto">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:order-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:sticky lg:top-24">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $kelasDetail->nama }}</h2>
                            <div class="mt-2 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ ucfirst($kelasDetail->type ?? 'Regular') }}</span>
                                @if ($kelasDetail->durasi_belajar)
                                    <span class="text-xs sm:text-sm text-gray-600">
                                        {{ $kelasDetail->durasi_belajar }} bulan belajar
                                        @if ($kelasDetail->waktu_magang && $kelasDetail->waktu_magang > 0)
                                            + {{ $kelasDetail->waktu_magang }} bulan magang
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if(isset($activeBatch) && (($activeBatch->status ?? '') === 'registration'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-teal-300 via-emerald-300 to-emerald-400 text-gray-900 shadow">Pendaftaran Dibuka</span>
                        @endif
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-emerald-50 border border-emerald-100">
                            <div class="text-xs sm:text-sm text-gray-500">Harga</div>
                            <div class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Rp {{ number_format($kelasDetail->harga, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-emerald-50 border border-emerald-100">
                            <div class="text-xs sm:text-sm text-gray-500">DP</div>
                            <div class="text-lg sm:text-xl font-bold text-gray-900">{{ (int) ($kelasDetail->dp_persen ?? 0) }}% <span class="text-xs sm:text-sm font-semibold text-gray-600">(Rp {{ number_format(($kelasDetail->harga * ($kelasDetail->dp_persen ?? 0)) / 100, 0, ',', '.') }})</span></div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-8">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3">Apa yang kamu dapat</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-code text-emerald-600 text-sm"></i></div>
                                <span class="font-medium text-gray-700 text-sm sm:text-base">Project Based Learning</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-graduate text-blue-600 text-sm"></i></div>
                                <span class="font-medium text-gray-700 text-sm sm:text-base">Mentoring 1-on-1</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-certificate text-purple-600 text-sm"></i></div>
                                <span class="font-medium text-gray-700 text-sm sm:text-base">Sertifikat Resmi</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-briefcase text-orange-600 text-sm"></i></div>
                                <span class="font-medium text-gray-700 text-sm sm:text-base">Penyaluran Karir</span>
                            </div>
                        </div>
                    </div>

                    @if (isset($activeBatch))
                        <div class="mt-6 sm:mt-8 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-bullhorn text-emerald-600"></i>
                                <div>
                                    <div class="font-semibold text-emerald-800 text-sm sm:text-base">Pendaftaran Dibuka</div>
                                    <div class="text-emerald-700 text-xs sm:text-sm">{{ $activeBatch->nama ?? 'Batch Terbuka' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 3;

        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('bukti_pembayaran_dp');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const removeImageBtn = document.getElementById('remove-image');
        const fileError = document.getElementById('file-error');

        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('animate__fadeInRight');
            });
            const currentStepElement = document.getElementById(`step-${step}`);
            currentStepElement.classList.remove('hidden');
            currentStepElement.classList.add('animate__fadeInRight');
            updateStepperUI(step);
            updateButtons(step);
        }

        function setCircle(circle, cls, html) {
            circle.className = cls;
            circle.innerHTML = html;
        }

        function updateStepperUI(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const circle = document.getElementById(`step-${i}-circle`);
                const title = document.getElementById(`step-${i}-title`);
                const connector = document.getElementById(`connector-${i}`);
                if (i < step) {
                    setCircle(circle, 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold transition-all duration-300', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M model_3 13l4 4L19 7"/></svg>');
                    title.className = 'text-xs sm:text-sm font-medium text-emerald-600';
                    if (connector) connector.className = 'flex-1 h-0.5 bg-emerald-500 mx-4 transition-all duration-300 hidden sm:block';
                } else if (i === step) {
                    setCircle(circle, 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold transition-all duration-300 ring-4 ring-emerald-200', String(i));
                    title.className = 'text-xs sm:text-sm font-medium text-emerald-600';
                    if (connector) connector.className = 'flex-1 h-0.5 bg-gray-300 mx-4 transition-all duration-300 hidden sm:block';
                } else {
                    setCircle(circle, 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 font-semibold transition-all duration-300', String(i));
                    title.className = 'text-xs sm:text-sm font-medium text-gray-500';
                    if (connector) connector.className = 'flex-1 h-0.5 bg-gray-300 mx-4 transition-all duration-300 hidden sm:block';
                }
            }
        }

        function updateButtons(step) {
            if (step === 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            } else if (step === totalSteps) {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function validateCurrentStep() {
            const currentStepElement = document.getElementById(`step-${currentStep}`);
            const requiredFields = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            requiredFields.forEach(field => {
                const errorElement = field.parentNode.querySelector('.validation-error');
                if (errorElement) errorElement.remove();
                if (!String(field.value || '').trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'text-red-500 text-xs sm:text-sm mt-1 validation-error';
                    errorMsg.textContent = 'Field ini wajib diisi';
                    field.parentNode.appendChild(errorMsg);
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            if (currentStep === 1) {
                const genderRadios = currentStepElement.querySelectorAll('input[name="jenis_kelamin"]');
                const isGenderSelected = Array.from(genderRadios).some(radio => radio.checked);
                const genderContainer = genderRadios[0]?.closest('div').parentNode;
                if (!isGenderSelected) {
                    isValid = false;
                    if (genderContainer && !genderContainer.querySelector('.gender-error')) {
                        const errorMsg = document.createElement('p');
                        errorMsg.className = 'text-red-500 text-xs sm:text-sm mt-1 gender-error';
                        errorMsg.textContent = 'Pilih jenis kelamin';
                        genderContainer.appendChild(errorMsg);
                    }
                } else {
                    const errorElement = genderContainer?.querySelector('.gender-error');
                    if (errorElement) errorElement.remove();
                }
            }
            if (currentStep === 3) {
                fileError.classList.add('hidden');
                if (!fileInput.files.length) {
                    isValid = false;
                    dropArea.classList.add('border-red-500');
                    fileError.textContent = 'Bukti pembayaran wajib diupload';
                    fileError.classList.remove('hidden');
                } else {
                    dropArea.classList.remove('border-red-500');
                }
            }
            return isValid;
        }

        // Drag & Drop
        ['dragenter','dragover','dragleave','drop'].forEach(eventName => dropArea.addEventListener(eventName, preventDefaults, false));
        function preventDefaults(e){ e.preventDefault(); e.stopPropagation(); }
        ['dragenter','dragover'].forEach(eventName => dropArea.addEventListener(eventName, () => dropArea.classList.add('animate__animated','animate__pulse','border-emerald-500'), false));
        ['dragleave','drop'].forEach(eventName => dropArea.addEventListener(eventName, () => dropArea.classList.remove('animate__animated','animate__pulse','border-emerald-500'), false));
        dropArea.addEventListener('drop', (e) => { handleFile(e.dataTransfer.files[0]); });
        dropArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => { if (fileInput.files.length) handleFile(fileInput.files[0]); });

        function handleFile(file){
            fileError.classList.add('hidden');
            dropArea.classList.remove('border-red-500');
            if (!file) return;
            const validTypes = ['image/jpeg','image/png'];
            if (!validTypes.includes(file.type)){
                fileError.textContent = 'File harus berupa JPG atau PNG';
                fileError.classList.remove('hidden');
                dropArea.classList.add('border-red-500');
                fileInput.value = ''; imagePreview.classList.add('hidden'); dropArea.classList.remove('hidden'); return;
            }
            if (file.size > 2 * 1024 * 1024){
                fileError.textContent = 'Ukuran file maksimal 2MB';
                fileError.classList.remove('hidden');
                dropArea.classList.add('border-red-500');
                fileInput.value = ''; imagePreview.classList.add('hidden'); dropArea.classList.remove('hidden'); return;
            }
            dropArea.classList.add('hidden');
            imagePreview.classList.remove('hidden');
            const reader = new FileReader(); reader.onload = (e) => { previewImg.src = e.target.result; }; reader.readAsDataURL(file);
        }
        document.getElementById('remove-image').addEventListener('click', () => {
            fileInput.value=''; imagePreview.classList.add('hidden'); dropArea.classList.remove('hidden'); dropArea.classList.remove('border-red-500'); fileError.classList.add('hidden');
        });

        // Navigation
        nextBtn.addEventListener('click', function(){ if (validateCurrentStep() && currentStep < totalSteps){ currentStep++; showStep(currentStep);} });
        prevBtn.addEventListener('click', function(){ if (currentStep > 1){ currentStep--; showStep(currentStep);} });

        // Init
        showStep(currentStep);

        // Remove validation errors on input
        document.addEventListener('input', function(e){ if (e.target.matches('input, select, textarea')){ e.target.classList.remove('border-red-500'); const err = e.target.parentNode.querySelector('.validation-error'); if (err) err.remove(); }});
        document.addEventListener('change', function(e){ if (e.target.matches('input[name="jenis_kelamin"]')){ const err = document.querySelector('.gender-error'); if (err) err.remove(); }});

        // Auto-calc tagihan per bulan (memperhitungkan DP % dari total)
        const form = document.getElementById('form-daftar');
        const dpPersen = parseInt(form?.dataset?.dpPersen || '0', 10);
        const totalEl = document.getElementById('total_tagihan');
        const cicilanEl = document.getElementById('jumlah_cicilan');
        const perBulanEl = document.getElementById('tagihan_per_bulan');
        const dpPersenText = document.getElementById('dp_persen_text');
        const dpNominalText = document.getElementById('dp_nominal_text');
        const sisaTagihanText = document.getElementById('sisa_tagihan_text');
        const perBulanText = document.getElementById('per_bulan_text');

        function formatRupiah(n){
            try { n = Math.round(Number(n) || 0); } catch(e){ n = 0; }
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function updateTagihanPerBulan(){
            const total = Math.max(parseInt(totalEl?.value || '0', 10), 0);
            const cicilan = Math.max(parseInt(cicilanEl?.value || '1', 10), 1);
            const dpNominal = Math.round(total * (dpPersen / 100));
            const sisa = Math.max(total - dpNominal, 0);
            const perBulan = Math.round(sisa / cicilan);
            if (perBulanEl) perBulanEl.value = perBulan;
            if (dpPersenText) dpPersenText.textContent = dpPersen;
            if (dpNominalText) dpNominalText.textContent = formatRupiah(dpNominal);
            if (sisaTagihanText) sisaTagihanText.textContent = formatRupiah(sisa);
            if (perBulanText) perBulanText.textContent = formatRupiah(perBulan) + ' / bulan';
        }
        totalEl?.addEventListener('input', updateTagihanPerBulan);
        cicilanEl?.addEventListener('change', updateTagihanPerBulan);
        updateTagihanPerBulan();
    });
</script>
@endpush