@extends('layouts.landing-layout')

@section('title', $kelas->nama ?? 'Detail Kelas')

@section('content')
    {{-- Pastikan data $kelas tersedia --}}
    @if(isset($kelas) && $kelas)
        {{-- Hero Section --}}
        <section class="detail-hero py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $kelas->nama }}</h1>
                    <div class="flex items-center justify-center gap-4 text-gray-600">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $kelas->durasi_belajar ?? 0 }} Bulan
                        </span>
                        @if($kelas->waktu_magang)
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                </svg>
                                {{ $kelas->waktu_magang }} Bulan Magang
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <ul class="mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Content Section --}}
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-12">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2">
                        {{-- Form Pendaftaran Multi-Step --}}
                        <div class="form-section rounded-2xl p-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Form Pendaftaran</h2>
                            
                            {{-- Check if batch is available --}}
                            @if(!isset($activeBatch) || !$activeBatch)
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                                    <strong class="font-bold">Informasi:</strong>
                                    <span class="block sm:inline">Pendaftaran untuk kelas ini sedang tidak dibuka. Silakan hubungi admin untuk informasi lebih lanjut.</span>
                                </div>
                            @else
                                {{-- Step Indicator --}}
                                <div class="flex items-center justify-between mb-8">
                                    <div class="step-item active" data-step="1">
                                        <div class="step-number">1</div>
                                        <span class="step-text">Data Pribadi</span>
                                    </div>
                                    <div class="step-line"></div>
                                    <div class="step-item" data-step="2">
                                        <div class="step-number">2</div>
                                        <span class="step-text">Informasi Tambahan</span>
                                    </div>
                                    <div class="step-line"></div>
                                    <div class="step-item" data-step="3">
                                        <div class="step-number">3</div>
                                        <span class="step-text">Konfirmasi</span>
                                    </div>
                                </div>

                                <form action="{{ route('daftar.store') }}" method="POST" id="registrationForm">
                                    @csrf
                                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

                                    {{-- Step 1: Data Pribadi --}}
                                    <div class="step-content active" id="step-1">
                                        <div class="grid md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                                <input type="text" name="nama_lengkap" class="form-input" placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap') }}" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                                <input type="email" name="email" class="form-input" placeholder="Masukkan email" value="{{ old('email') }}" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon *</label>
                                                <input type="tel" name="no_hp" class="form-input" placeholder="Masukkan no. telepon" value="{{ old('no_hp') }}" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                                                <select name="jenis_kelamin" class="form-select" required>
                                                    <option value="">Pilih jenis kelamin</option>
                                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat *</label>
                                                <textarea name="alamat" class="form-textarea" rows="3" placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Step 2: Informasi Tambahan --}}
                                    <div class="step-content" id="step-2">
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir</label>
                                                <input type="text" name="pendidikan_terakhir" class="form-input" placeholder="Contoh: SMA, D3, S1, dll" value="{{ old('pendidikan_terakhir') }}">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Dari mana Anda mengetahui program ini? *</label>
                                                <select name="mengetahui_program_dari" class="form-select" required>
                                                    <option value="">Pilih sumber informasi</option>
                                                    <option value="Instagram" {{ old('mengetahui_program_dari') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                                    <option value="Tiktok" {{ old('mengetahui_program_dari') == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                                                    <option value="Facebook" {{ old('mengetahui_program_dari') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                                    <option value="Website" {{ old('mengetahui_program_dari') == 'Website' ? 'selected' : '' }}>Website</option>
                                                    <option value="Teman/Keluarga" {{ old('mengetahui_program_dari') == 'Teman/Keluarga' ? 'selected' : '' }}>Teman/Keluarga</option>
                                                    <option value="Google" {{ old('mengetahui_program_dari') == 'Google' ? 'selected' : '' }}>Google</option>
                                                    <option value="Lain-lain" {{ old('mengetahui_program_dari') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                                                </select>
                                            </div>

                                            <div class="bg-gray-50 p-6 rounded-lg">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h3>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between">
                                                        <span>Harga Kelas:</span>
                                                        <span class="font-semibold">Rp {{ number_format($kelas->harga, 0, ',', '.') }}</span>
                                                    </div>
                                                    @if($kelas->dp_persen > 0)
                                                        <div class="flex justify-between">
                                                            <span>DP ({{ $kelas->dp_persen }}%):</span>
                                                            <span class="font-semibold text-emerald-600">Rp {{ number_format(($kelas->harga * $kelas->dp_persen / 100), 0, ',', '.') }}</span>
                                                        </div>
                                                        <div class="border-t pt-3">
                                                            <div class="flex justify-between">
                                                                <span class="font-semibold">Sisa Bayar:</span>
                                                                <span class="font-semibold text-orange-600">Rp {{ number_format(($kelas->harga * (100 - $kelas->dp_persen) / 100), 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Step 3: Konfirmasi --}}
                                    <div class="step-content" id="step-3">
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Konfirmasi Pendaftaran</h3>
                                            <p class="text-gray-600 mb-6">Pastikan semua data yang Anda masukkan sudah benar sebelum melanjutkan.</p>
                                            
                                            <div class="bg-gray-50 p-6 rounded-lg text-left max-w-md mx-auto">
                                                <h4 class="font-semibold text-gray-900 mb-3">Ringkasan Pendaftaran:</h4>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span>Kelas:</span>
                                                        <span class="font-medium">{{ $kelas->nama }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Batch:</span>
                                                        <span class="font-medium">{{ $activeBatch->nama ?? 'Belum ditentukan' }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Total Harga:</span>
                                                        <span class="font-medium">Rp {{ number_format($kelas->harga, 0, ',', '.') }}</span>
                                                    </div>
                                                    @if($kelas->dp_persen > 0)
                                                        <div class="flex justify-between">
                                                            <span>DP:</span>
                                                            <span class="font-medium">Rp {{ number_format(($kelas->harga * $kelas->dp_persen / 100), 0, ',', '.') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Navigation Buttons --}}
                                    <div class="flex justify-between mt-8">
                                        <button type="button" class="btn-secondary" id="prevBtn" style="display: none;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                            Sebelumnya
                                        </button>
                                        <button type="button" class="btn-primary" id="nextBtn">
                                            Selanjutnya
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="lg:col-span-1">
                        {{-- Price Card --}}
                        <div class="price-card rounded-2xl p-6 text-white">
                            <h3 class="text-xl font-bold mb-4">Informasi Kelas</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span>Harga:</span>
                                    <span class="font-bold">Rp {{ number_format($kelas->harga, 0, ',', '.') }}</span>
                                </div>
                                @if($kelas->dp_persen > 0)
                                    <div class="flex justify-between">
                                        <span>DP:</span>
                                        <span class="font-bold">{{ $kelas->dp_persen }}%</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span>Tipe:</span>
                                    <span class="font-bold">{{ ucfirst($kelas->type ?? 'Regular') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Durasi:</span>
                                    <span class="font-bold">{{ $kelas->durasi_belajar ?? 0 }} Bulan</span>
                                </div>
                                @if($kelas->waktu_magang)
                                    <div class="flex justify-between">
                                        <span>Magang:</span>
                                        <span class="font-bold">{{ $kelas->waktu_magang }} Bulan</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Contact Info --}}
                            <div class="mt-6 pt-6 border-t border-emerald-400">
                                <h4 class="font-semibold mb-3">Butuh Bantuan?</h4>
                                <a href="https://wa.me/6283178569163?text=Halo%20saya%20ingin%20bertanya%20tentang%20kelas%20{{ urlencode($kelas->nama) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 bg-white text-emerald-600 px-4 py-2 rounded-lg font-semibold hover:bg-emerald-50 transition w-full justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    Hubungi WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @else
        {{-- Fallback jika data tidak ada --}}
        <div class="min-h-screen flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Data Kelas Tidak Ditemukan</h1>
                <p class="text-gray-600 mb-6">Maaf, data kelas yang Anda cari tidak tersedia.</p>
                <a href="{{ route('index') }}" class="btn-primary">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif

    <style>
        .detail-hero {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        }
        
        .price-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            position: sticky;
            top: 2rem;
        }
        
        .form-section {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border: 1px solid #e5e7eb;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: #ffffff;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .step-number {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .step-item.active .step-number {
            background: #10b981;
            color: white;
        }
        
        .step-text {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        .step-item.active .step-text {
            color: #10b981;
        }
        
        .step-line {
            width: 3rem;
            height: 2px;
            background: #e5e7eb;
            margin: 0 1rem;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
        }
    </style>

    <script>
        let currentStep = 1;
        const totalSteps = 3;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show current step
            document.getElementById(`step-${step}`).classList.add('active');
            
            // Update step indicators
            document.querySelectorAll('.step-item').forEach((item, index) => {
                item.classList.remove('active');
                if (index + 1 <= step) {
                    item.classList.add('active');
                }
            });
            
            // Update navigation buttons
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            if (step === 1) {
                prevBtn.style.display = 'none';
                nextBtn.innerHTML = 'Selanjutnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
            } else if (step === totalSteps) {
                prevBtn.style.display = 'inline-flex';
                nextBtn.innerHTML = 'Daftar Sekarang';
            } else {
                prevBtn.style.display = 'inline-flex';
                nextBtn.innerHTML = 'Selanjutnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
            }
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                // Validate current step before proceeding
                if (validateCurrentStep()) {
                    currentStep++;
                    showStep(currentStep);
                }
            } else {
                // Submit form
                document.getElementById('registrationForm').submit();
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

        // Event listeners
        document.getElementById('nextBtn').addEventListener('click', nextStep);
        document.getElementById('prevBtn').addEventListener('click', prevStep);

        // Initialize first step
        showStep(1);
    </script>
@endsection