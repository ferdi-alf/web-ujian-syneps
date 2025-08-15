<!-- resources/views/components/kelas-detail.blade.php -->

{{-- Pastikan data $kelas tersedia --}}
@if(isset($kelas) && $kelas)
    {{-- Hero Section --}}
    <section class="detail-hero py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $kelas['nama'] }}</h1>
                <div class="flex items-center justify-center gap-4 text-gray-600">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $kelas['durasiBelajar'] }} Bulan
                    </span>
                    @if($kelas['durasiMagang'])
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                            {{ $kelas['durasiMagang'] }} Bulan Magang
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">
                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    {{-- Form Pendaftaran Multi-Step --}}
                    <div class="form-section rounded-2xl p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Form Pendaftaran</h2>
                        
                        {{-- Step Indicator --}}
                        <div class="flex items-center justify-between mb-8">
                            <div class="step-item active" data-step="1">
                                <div class="step-number">1</div>
                                <span class="step-text">Data Pribadi</span>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-item" data-step="2">
                                <div class="step-number">2</div>
                                <span class="step-text">Pembayaran</span>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-item" data-step="3">
                                <div class="step-number">3</div>
                                <span class="step-text">Konfirmasi</span>
                            </div>
                        </div>

                        {{-- Step 1: Data Pribadi --}}
                        <div class="step-content active" id="step-1">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" class="form-input" placeholder="Masukkan nama lengkap">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" class="form-input" placeholder="Masukkan email">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                    <input type="tel" class="form-input" placeholder="Masukkan no. telepon">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                    <input type="date" class="form-input">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                    <textarea class="form-textarea" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Pembayaran --}}
                        <div class="step-content" id="step-2">
                            <div class="space-y-6">
                                <div class="bg-gray-50 p-6 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span>Harga Kelas:</span>
                                            <span class="font-semibold">Rp {{ number_format(str_replace('.', '', $kelas['harga']), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>DP ({{ $kelas['dp'] }}%):</span>
                                            <span class="font-semibold text-emerald-600">Rp {{ number_format((str_replace('.', '', $kelas['harga']) * $kelas['dp'] / 100), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="border-t pt-3">
                                            <div class="flex justify-between">
                                                <span class="font-semibold">Sisa Bayar:</span>
                                                <span class="font-semibold text-orange-600">Rp {{ number_format((str_replace('.', '', $kelas['harga']) * (100 - $kelas['dp']) / 100), 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                                    <select class="form-select">
                                        <option>Pilih metode pembayaran</option>
                                        <option>Transfer Bank BCA</option>
                                        <option>Transfer Bank Mandiri</option>
                                        <option>DANA</option>
                                        <option>OVO</option>
                                    </select>
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
                                            <span class="font-medium">{{ $kelas['nama'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Total Bayar:</span>
                                            <span class="font-medium">Rp {{ number_format(str_replace('.', '', $kelas['harga']), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>DP:</span>
                                            <span class="font-medium">Rp {{ number_format((str_replace('.', '', $kelas['harga']) * $kelas['dp'] / 100), 0, ',', '.') }}</span>
                                        </div>
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
                                <span class="font-bold">Rp {{ number_format(str_replace('.', '', $kelas['harga']), 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>DP:</span>
                                <span class="font-bold">{{ $kelas['dp'] }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tipe:</span>
                                <span class="font-bold">{{ $kelas['tipe'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Durasi:</span>
                                <span class="font-bold">{{ $kelas['durasiBelajar'] }} Bulan</span>
                            </div>
                            @if($kelas['durasiMagang'])
                                <div class="flex justify-between">
                                    <span>Magang:</span>
                                    <span class="font-bold">{{ $kelas['durasiMagang'] }} Bulan</span>
                                </div>
                            @endif
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
        nextBtn.textContent = 'Selanjutnya';
    } else if (step === totalSteps) {
        prevBtn.style.display = 'inline-flex';
        nextBtn.textContent = 'Daftar Sekarang';
    } else {
        prevBtn.style.display = 'inline-flex';
        nextBtn.textContent = 'Selanjutnya';
    }
}

function nextStep() {
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
    } else {
        // Submit form
        alert('Form pendaftaran berhasil dikirim! (Ini masih dummy)');
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

// Event listeners
document.getElementById('nextBtn').addEventListener('click', nextStep);
document.getElementById('prevBtn').addEventListener('click', prevStep);

// Initialize first step
showStep(1);
</script>