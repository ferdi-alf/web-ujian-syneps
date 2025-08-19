<section class="bg-white pt-20 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('index') }}"
                class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Kolom Detail Kelas -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $kelasDetail->nama }}</h1>
                            <div class="mt-2 flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    {{ ucfirst($kelasDetail->type ?? 'Regular') }}
                                </span>
                                @if ($kelasDetail->durasi_belajar)
                                    <span class="text-sm text-gray-600">
                                        {{ $kelasDetail->durasi_belajar }} bulan belajar
                                        @if ($kelasDetail->waktu_magang && $kelasDetail->waktu_magang > 0)
                                            + {{ $kelasDetail->waktu_magang }} bulan magang
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid sm:grid-cols-2 gap-4">
                        <div
                            class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-emerald-50 border border-emerald-100">
                            <div class="text-sm text-gray-500">Harga</div>
                            <div
                                class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                Rp {{ number_format($kelasDetail->harga, 0, ',', '.') }}
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-emerald-50 border border-emerald-100">
                            <div class="text-sm text-gray-500">DP</div>
                            <div class="text-xl font-bold text-gray-900">
                                {{ (int) ($kelasDetail->dp_persen ?? 0) }}%
                                <span class="text-sm font-semibold text-gray-600">
                                    (Rp
                                    {{ number_format(($kelasDetail->harga * ($kelasDetail->dp_persen ?? 0)) / 100, 0, ',', '.') }})
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Apa yang kamu dapat</h2>
                        <div class="grid sm:grid-cols-2 gap-3">
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-code text-emerald-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-700">Project Based Learning</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-700">Mentoring 1-on-1</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-certificate text-purple-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-700">Sertifikat Resmi</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-briefcase text-orange-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-700">Penyaluran Karir</span>
                            </div>
                        </div>
                    </div>

                    @if (isset($activeBatch))
                        <div class="mt-8 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-bullhorn text-emerald-600"></i>
                                <div>
                                    <div class="font-semibold text-emerald-800">Pendaftaran Dibuka</div>
                                    <div class="text-emerald-700 text-sm">{{ $activeBatch->nama ?? 'Batch Terbuka' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Form Pendaftaran -->
            <div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Daftar Kelas Ini</h2>
                    <form method="POST" action="{{ route('daftar.store') }}" id="form-daftar">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelasDetail->id }}">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                    required>
                                @error('nama_lengkap')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                    required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">No HP</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                    required>
                                @error('no_hp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea name="alamat" rows="3"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                        required>
                                        <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>
                                            Pilih...</option>
                                        <option value="Laki-laki"
                                            {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                                        </option>
                                        <option value="Perempuan"
                                            {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan
                                        </option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pendidikan Terakhir
                                        (opsional)</label>
                                    <input type="text" name="pendidikan_terakhir"
                                        value="{{ old('pendidikan_terakhir') }}"
                                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('pendidikan_terakhir')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mengetahui Program Dari</label>
                                <select name="mengetahui_program_dari"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                    required>
                                    <option value="" disabled
                                        {{ old('mengetahui_program_dari') ? '' : 'selected' }}>Pilih sumber</option>
                                    @php
                                        $sumber = [
                                            'Instagram',
                                            'Tiktok',
                                            'Facebook',
                                            'Website',
                                            'Teman/Keluarga',
                                            'Google',
                                            'Lain-lain',
                                        ];
                                    @endphp
                                    @foreach ($sumber as $s)
                                        <option value="{{ $s }}"
                                            {{ old('mengetahui_program_dari') === $s ? 'selected' : '' }}>
                                            {{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('mengetahui_program_dari')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Tagihan</label>
                                    <input type="number" name="total_tagihan" id="total_tagihan"
                                        value="{{ old('total_tagihan', $kelasDetail->harga) }}" step="0.01"
                                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                        required>
                                    @error('total_tagihan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jumlah Cicilan</label>
                                    <select name="jumlah_cicilan" id="jumlah_cicilan"
                                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                        required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ (int) old('jumlah_cicilan', 1) === $i ? 'selected' : '' }}>
                                                {{ $i }}x</option>
                                        @endfor
                                    </select>
                                    @error('jumlah_cicilan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tagihan per Bulan
                                    (otomatis)</label>
                                <input type="number" name="tagihan_per_bulan" id="tagihan_per_bulan"
                                    value="{{ old('tagihan_per_bulan') }}" step="0.01"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                                    readonly>
                                @error('tagihan_per_bulan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full mt-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white py-3 rounded-xl font-semibold transition-all duration-300">Daftar
                                Sekarang</button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="https://wa.me/6283178569163?text=Halo%20saya%20ingin%20mendaftar%20kelas%20{{ urlencode($kelasDetail->nama) }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
                            <i class="fab fa-whatsapp"></i>
                            Tanya via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        function updateTagihanPerBulan() {
            const total = parseFloat(document.getElementById('total_tagihan').value || 0);
            const cicilan = parseInt(document.getElementById('jumlah_cicilan').value || 1);
            if (total > 0 && cicilan > 0) {
                const perBulan = total / cicilan;
                document.getElementById('tagihan_per_bulan').value = perBulan.toFixed(2);
            } else {
                document.getElementById('tagihan_per_bulan').value = '';
            }
        }

        document.getElementById('total_tagihan').addEventListener('input', updateTagihanPerBulan);
        document.getElementById('jumlah_cicilan').addEventListener('change', updateTagihanPerBulan);

        // Inisialisasi saat halaman dimuat
        updateTagihanPerBulan();
    </script>
@endpush
