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
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <title>{{ $ujian->judul }} - Syneps Academy</title>
    <style>
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $ujian->judul }}</h1>
                    <p class="text-gray-600">{{ $ujian->kelas->nama ?? 'Fullstack Web Developer' }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg">
                        <i class="fa-regular fa-clock mr-2"></i>
                        <span id="timer" class="font-bold">{{ $ujian->waktu }}:00</span>
                    </div>
                </div>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    style="width: 0%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                Soal <span id="current-question">1</span> dari <span
                    id="total-questions">{{ $ujian->soals->count() }}</span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div id="question-container">
                @foreach ($ujian->soals as $index => $soal)
                    <div class="question-slide {{ $index === 0 ? 'active' : 'hidden' }}"
                        data-question-index="{{ $index }}" data-soal-id="{{ $soal->id }}">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                {{ $index + 1 }}. {{ $soal->soal }}
                            </h3>

                            <div class="space-y-3">
                                @foreach ($soal->jawabans as $jawaban)
                                    <label
                                        class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="radio" name="jawaban_{{ $soal->id }}"
                                            value="{{ $jawaban->pilihan }}" class="mr-3 text-blue-600"
                                            {{ isset($existingAnswers[$soal->id]) && $existingAnswers[$soal->id] === $jawaban->pilihan ? 'checked' : '' }}>
                                        <span class="font-medium text-gray-700 mr-3">{{ $jawaban->pilihan }}.</span>
                                        <span class="text-gray-800">{{ $jawaban->teks }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Navigation -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center">
                <button id="prev-btn"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Sebelumnya
                </button>



                <div class="flex gap-3">
                    <button id="next-btn"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        Selanjutnya
                        <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>

                    <button id="submit-btn"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors hidden">
                        <i class="fa-solid fa-check mr-2"></i>
                        Selesai
                    </button>
                </div>
            </div>
            <div class="flex justify-center">
                <div id="question-nav-container"
                    class="flex flex-wrap md:flex-nowrap gap-2 overflow-x-auto whitespace-nowrap p-3">
                    <!-- Buttons will be dynamically generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <script>
        class AntiCheatDetector {
            constructor(ujianApp) {
                this.ujianApp = ujianApp;
                this.violations = [];
                this.warningCount = 0;
                this.maxWarnings = 3;
                this.isWindowFocused = true;
                this.originalWindowHeight = window.innerHeight;
                this.originalWindowWidth = window.innerWidth;
                this.minimumScreenPercentage = 80;
                this.lastTabSwitchViolation = 0; // Track last tab switch violation time
                this.debounceDelay = 500; // Debounce delay in milliseconds
                this.isSubmitting = false;

                this.showInitialWarning();

                this.initDetection();
                this.logViolation('Ujian dimulai', 'info');
            }

            showInitialWarning() {
                Swal.fire({
                    title: 'Peringatan Penting!',
                    html: `
                <div class="text-left">
                    <p class="text-gray-700 mb-2">Untuk menjaga integritas ujian, harap patuhi peraturan berikut:</p>
                    <ul class="text-sm text-red-600 list-disc list-inside">
                        <li>Jangan berpindah tab atau aplikasi lain selama ujian.</li>
                        <li>Jangan keluar dari halaman ujian.</li>
                        <li>Jangan menggunakan split-screen atau membuka developer tools.</li>
                    </ul>
                    <p class="text-gray-700 mt-2">Sistem ini dapat mendeteksi aktivitas tersebut dan akan mencatat pelanggaran. Pelanggaran berulang dapat menyebabkan ujian otomatis disubmit.</p>
                </div>
            `,
                    icon: 'info',
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Saya Mengerti',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }

            initDetection() {
                this.detectTabSwitch();
                this.detectWindowResize();
                this.detectDevTools();
                this.detectKeyboardShortcuts();
                this.detectCopyPaste();
                this.detectMouseActivity();
            }

            detectTabSwitch() {
                window.addEventListener('blur', () => {
                    if (this.isSubmitting) return;
                    if (this.isWindowFocused) {
                        this.isWindowFocused = false;
                        const now = Date.now();
                        if (now - this.lastTabSwitchViolation > this.debounceDelay) {
                            this.handleViolation('Berpindah tab atau aplikasi lain', 'tab_switch');
                            this.lastTabSwitchViolation = now;
                        }
                    }
                });

                window.addEventListener('focus', () => {
                    if (!this.isWindowFocused) {
                        this.isWindowFocused = true;
                        this.logViolation('Kembali ke ujian', 'info');
                    }
                });

                document.addEventListener('visibilitychange', () => {
                    if (this.isSubmitting) return;
                    if (document.hidden) {
                        const now = Date.now();
                        if (now - this.lastTabSwitchViolation > this.debounceDelay) {
                            this.handleViolation('Tab ujian tidak aktif/tersembunyi', 'visibility_change');
                            this.lastTabSwitchViolation = now;
                        }
                    } else {
                        this.logViolation('Tab ujian kembali aktif', 'info');
                    }
                });
            }

            detectWindowResize() {
                window.addEventListener('resize', () => {
                    const currentHeight = window.innerHeight;
                    const currentWidth = window.innerWidth;

                    const heightPercentage = (currentHeight / this.originalWindowHeight) * 100;
                    const widthPercentage = (currentWidth / this.originalWindowWidth) * 100;

                    if (heightPercentage < this.minimumScreenPercentage || widthPercentage < this
                        .minimumScreenPercentage) {
                        this.handleViolation(
                            `Ukuran layar terlalu kecil (${Math.round(heightPercentage)}% x ${Math.round(widthPercentage)}%). Minimum ${this.minimumScreenPercentage}%`,
                            'window_resize'
                        );
                    }
                });
            }

            detectDevTools() {
                let devtools = {
                    open: false
                };
                const threshold = 160;

                setInterval(() => {
                    if (window.outerHeight - window.innerHeight > threshold ||
                        window.outerWidth - window.innerWidth > threshold) {
                        if (!devtools.open) {
                            devtools.open = true;
                            this.handleViolation('Developer Tools terdeteksi dibuka', 'devtools');
                        }
                    } else {
                        if (devtools.open) {
                            devtools.open = false;
                            this.logViolation('Developer Tools ditutup', 'info');
                        }
                    }
                }, 500);
            }

            detectKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    const dangerousShortcuts = [{
                            key: 'F12'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'I'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'J'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'C'
                        },
                        {
                            ctrl: true,
                            key: 'U'
                        },
                        {
                            ctrl: true,
                            key: 'R'
                        },
                        {
                            key: 'F5'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'R'
                        },
                        {
                            alt: true,
                            key: 'Tab'
                        },
                        {
                            ctrl: true,
                            key: 'Tab'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'Tab'
                        },
                        {
                            ctrl: true,
                            key: 'N'
                        },
                        {
                            ctrl: true,
                            key: 'T'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'N'
                        },
                        {
                            key: 'PrintScreen'
                        },
                        {
                            alt: true,
                            key: 'PrintScreen'
                        },
                        {
                            ctrl: true,
                            shift: true,
                            key: 'Escape'
                        },
                        {
                            ctrl: true,
                            alt: true,
                            key: 'Delete'
                        }
                    ];

                    const isMatch = dangerousShortcuts.some(shortcut => {
                        return Object.keys(shortcut).every(prop => {
                            if (prop === 'key') return e.key === shortcut[prop] || e.code ===
                                shortcut[prop];
                            if (prop === 'ctrl') return e.ctrlKey === shortcut[prop];
                            if (prop === 'alt') return e.altKey === shortcut[prop];
                            if (prop === 'shift') return e.shiftKey === shortcut[prop];
                            return true;
                        });
                    });

                    if (isMatch) {
                        e.preventDefault();
                        this.handleViolation(`Shortcut terlarang: ${e.key}`, 'keyboard_shortcut');
                        return false;
                    }
                });

                document.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    this.handleViolation('Klik kanan terdeteksi', 'right_click');
                    return false;
                });
            }

            detectCopyPaste() {
                document.addEventListener('copy', (e) => {
                    e.preventDefault();
                    this.handleViolation('Mencoba menyalin teks', 'copy');
                    return false;
                });

                document.addEventListener('paste', (e) => {
                    e.preventDefault();
                    this.handleViolation('Mencoba menempel teks', 'paste');
                    return false;
                });

                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 'a') {
                        e.preventDefault();
                        this.handleViolation('Mencoba memilih semua teks', 'select_all');
                        return false;
                    }
                });
            }

            detectMouseActivity() {
                let mouseLeaveCount = 0;

                document.addEventListener('mouseleave', () => {
                    mouseLeaveCount++;
                    if (mouseLeaveCount > 5) {
                        this.handleViolation('Mouse sering keluar dari area ujian', 'mouse_leave');
                        mouseLeaveCount = 0;
                    }
                });
            }

            handleViolation(message, type) {
                this.warningCount++;
                this.logViolation(message, 'warning');
                this.saveViolationToServer(message, type);

                if (this.warningCount >= this.maxWarnings) {
                    this.forceSubmitDueToViolation();
                } else {
                    this.showWarning(message);
                }
            }

            showWarning(message) {
                const remainingWarnings = this.maxWarnings - this.warningCount;

                Swal.fire({
                    title: '⚠️ Peringatan Kecurangan!',
                    html: `
                <div class="text-left">
                    <p class="text-red-600 font-semibold mb-2">${message}</p>
                    <p class="text-gray-700">Peringatan ke-${this.warningCount} dari ${this.maxWarnings}</p>
                    <p class="text-sm text-red-500 mt-2">
                        ${remainingWarnings > 0 ? 
                            `Sisa ${remainingWarnings} peringatan lagi sebelum ujian otomatis disubmit!` : 
                            'Ujian akan otomatis disubmit!'
                        }
                    </p>
                </div>
            `,
                    icon: 'warning',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Saya Mengerti',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            }

            forceSubmitDueToViolation() {
                clearInterval(this.ujianApp.timerInterval);
                if (!this.ujianApp || typeof this.ujianApp.processSubmit !== 'function') {
                    console.error('Error: ujianApp is not properly initialized');
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal mengakhiri ujian karena masalah teknis.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }
                Swal.fire({
                    title: '🚫 Ujian Dihentikan!',
                    html: `
                <div class="text-left">
                    <p class="text-red-600 font-semibold mb-2">Terlalu banyak pelanggaran terdeteksi!</p>
                    <p class="text-gray-700">Ujian akan otomatis disubmit karena:</p>
                    <ul class="text-sm text-red-600 mt-2 list-disc list-inside">
                        ${this.violations.slice(-3).map(v => `<li>${v.message}</li>`).join('')}
                    </ul>
                </div>
            `,
                    icon: 'error',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Submit Ujian',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    this.ujianApp.processSubmit();
                });
            }

            logViolation(message, level) {
                const violation = {
                    timestamp: new Date().toISOString(),
                    message: message,
                    level: level,
                    url: window.location.href,
                    userAgent: navigator.userAgent
                };

                this.violations.push(violation);
                console.warn(`[Anti-Cheat] ${level.toUpperCase()}: ${message}`);
            }

            saveViolationToServer(message, type) {
                fetch(`/ujian/${this.ujianApp.ujianSlug}/violation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: message,
                        type: type,
                        timestamp: new Date().toISOString(),
                        warning_count: this.warningCount,
                        user_agent: navigator.userAgent,
                        screen_resolution: `${screen.width}x${screen.height}`,
                        window_size: `${window.innerWidth}x${window.innerHeight}`
                    })
                }).catch(error => {
                    console.error('Failed to save violation:', error);
                });
            }

            getViolationSummary() {
                return {
                    total_violations: this.violations.length,
                    warning_count: this.warningCount,
                    violations: this.violations
                };
            }
        }

        class UjianApp {
            constructor() {
                this.currentQuestion = 0;
                this.totalQuestions = {{ $ujian->soals->count() }};
                this.timeLimit = {{ $ujian->waktu * 60 }};
                this.answers = JSON.parse(localStorage.getItem('ujian_answers_{{ $ujian->id }}') || '{}');
                this.startTime = Date.now();
                this.ujianSlug = '{{ \Illuminate\Support\Str::slug($ujian->judul) }}';
                this.isMobile = window.innerWidth < 768;
                this.isSubmitting = false;
                this.initializeTimer();
                this.loadSavedAnswers();
                this.bindEvents();
                this.updateUI();
                this.renderQuestionNavigation();
                localStorage.setItem('ujian_start_time_{{ $ujian->id }}', this.startTime);


                window.addEventListener('resize', () => {
                    const newIsMobile = window.innerWidth < 768;
                    if (newIsMobile !== this.isMobile) {
                        this.isMobile = newIsMobile;
                        this.renderQuestionNavigation();
                    }
                });
            }

            initializeTimer() {
                const savedTime = localStorage.getItem('ujian_remaining_time_{{ $ujian->id }}');
                if (savedTime) {
                    this.timeLimit = parseInt(savedTime);
                }

                this.timerInterval = setInterval(() => {
                    this.timeLimit--;
                    this.updateTimerDisplay();
                    localStorage.setItem('ujian_remaining_time_{{ $ujian->id }}', this.timeLimit);

                    if (this.timeLimit <= 0) {
                        this.autoSubmit();
                    }
                }, 1000);
            }

            updateTimerDisplay() {
                const minutes = Math.floor(this.timeLimit / 60);
                const seconds = this.timeLimit % 60;
                const timerElement = document.getElementById('timer');
                timerElement.textContent =
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                if (this.timeLimit <= 300) { // 5 menit aje
                    timerElement.parentElement.className =
                        'bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg animate-pulse';
                }
            }

            loadSavedAnswers() {
                const existingAnswers = @json($existingAnswers);
                Object.keys(existingAnswers).forEach(soalId => {
                    this.answers[soalId] = existingAnswers[soalId];
                });
            }

            bindEvents() {
                document.getElementById('prev-btn').addEventListener('click', () => this.prevQuestion());
                document.getElementById('next-btn').addEventListener('click', () => this.nextQuestion());
                document.getElementById('submit-btn').addEventListener('click', () => this.submitUjian());

                document.querySelectorAll('input[type="radio"]').forEach(radio => {
                    radio.addEventListener('change', (e) => {
                        const soalId = e.target.name.replace('jawaban_', '');
                        this.answers[soalId] = e.target.value;
                        this.saveProgress(soalId, e.target.value);
                        this.renderQuestionNavigation();
                        localStorage.setItem('ujian_answers_{{ $ujian->id }}', JSON.stringify(this
                            .answers));
                    });
                });


            }

            saveProgress(soalId, jawaban) {
                fetch(`/ujian/${this.ujianSlug}/save-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        soal_id: soalId,
                        jawaban: jawaban
                    })
                }).catch(console.error);
            }

            prevQuestion() {
                if (this.currentQuestion > 0) {
                    this.currentQuestion--;
                    this.updateUI();
                    this.renderQuestionNavigation();
                }
            }

            nextQuestion() {
                if (this.currentQuestion < this.totalQuestions - 1) {
                    this.currentQuestion++;
                    this.updateUI();
                    this.renderQuestionNavigation();
                }
            }

            goToQuestion(index) {
                this.currentQuestion = index;
                this.updateUI();
                this.renderQuestionNavigation();
            }

            updateUI() {
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.classList.add('hidden');
                    slide.classList.remove('active');
                });

                const currentSlide = document.querySelector(`[data-question-index="${this.currentQuestion}"]`);
                if (currentSlide) {
                    currentSlide.classList.remove('hidden');
                    currentSlide.classList.add('active');
                }

                document.getElementById('prev-btn').disabled = this.currentQuestion === 0;

                const nextBtn = document.getElementById('next-btn');
                const submitBtn = document.getElementById('submit-btn');

                if (this.currentQuestion === this.totalQuestions - 1) {
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                }

                const progress = ((this.currentQuestion + 1) / this.totalQuestions) * 100;
                document.getElementById('progress-bar').style.width = `${progress}%`;
                document.getElementById('current-question').textContent = this.currentQuestion + 1;
            }

            renderQuestionNavigation() {
                const container = document.getElementById('question-nav-container');
                container.innerHTML = '';

                const soals = @json($ujian->soals);
                const maxButtonsMobile = 5;

                if (this.isMobile && this.totalQuestions > maxButtonsMobile) {
                    let buttonsToShow = [];
                    const current = this.currentQuestion;
                    const total = this.totalQuestions;

                    if (current < 2) {
                        buttonsToShow = [0, 1, 2];
                        if (total > 4) buttonsToShow.push('...');
                        if (total > 3) buttonsToShow.push(total - 2);
                        if (total > 2) buttonsToShow.push(total - 1);
                    } else if (current >= total - 2) {
                        buttonsToShow = [0, 1];
                        if (total > 4) buttonsToShow.push('...');
                        buttonsToShow.push(total - 3, total - 2, total - 1);
                    } else {
                        buttonsToShow = [0, 1, '...', current, '...', total - 2, total - 1];
                    }

                    buttonsToShow.forEach(item => {
                        if (item === '...') {
                            const span = document.createElement('span');
                            span.className = 'text-gray-500 font-medium flex items-center';
                            span.textContent = '...';
                            container.appendChild(span);
                        } else {
                            const index = item;
                            const soalId = soals[index].id;
                            const btn = document.createElement('button');
                            btn.className = `question-nav w-10 h-10 rounded-full border-2 text-sm font-medium transition-colors hover:border-blue-500 flex-shrink-0 ${
                                index === this.currentQuestion
                                    ? 'border-blue-500 bg-blue-500 text-white'
                                    : this.answers[soalId]
                                    ? 'border-green-500 bg-green-500 text-white'
                                    : 'border-gray-300 text-gray-600'
                            }`;
                            btn.dataset.questionIndex = index;
                            btn.textContent = index + 1;
                            btn.addEventListener('click', () => this.goToQuestion(index));
                            container.appendChild(btn);
                        }
                    });
                } else {
                    // Desktop: Show all buttons
                    soals.forEach((soal, index) => {
                        const btn = document.createElement('button');
                        btn.className = `question-nav w-10 h-10 rounded-full border-2 text-sm font-medium transition-colors hover:border-blue-500 flex-shrink-0 ${
                            index === this.currentQuestion
                                ? 'border-blue-500 bg-blue-500 text-white'
                                : this.answers[soal.id]
                                ? 'border-green-500 bg-green-500 text-white'
                                : 'border-gray-300 text-gray-600'
                        }`;
                        btn.dataset.questionIndex = index;
                        btn.textContent = index + 1;
                        btn.addEventListener('click', () => this.goToQuestion(index));
                        container.appendChild(btn);
                    });
                }
            }

            autoSubmit() {
                clearInterval(this.timerInterval);
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Ujian akan otomatis disubmit.',
                    icon: 'warning',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    this.submitUjian(true);
                });
            }

            submitUjian(autoSubmit = false) {
                this.isSubmitting = true;
                if (this.antiCheat) {
                    this.antiCheat.isSubmitting = true;
                }

                const answeredQuestions = Object.keys(this.answers).length;

                if (!autoSubmit && answeredQuestions < this.totalQuestions) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: `Anda masih memiliki ${this.totalQuestions - answeredQuestions} soal yang belum dijawab. Yakin ingin submit?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, Submit!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.processSubmit();
                        } else {
                            this.isSubmitting = false;
                            if (this.antiCheat) {
                                this.antiCheat.isSubmitting = false;
                            }
                        }
                    });
                } else {
                    this.processSubmit();
                }
            }

            processSubmit() {
                clearInterval(this.timerInterval);

                const waktuPengerjaan = Math.floor((Date.now() - this.startTime) / 1000);
                const violationSummary = this.antiCheat ? this.antiCheat.getViolationSummary() : null;

                const formData = {
                    answers: this.answers,
                    waktu_pengerjaan: waktuPengerjaan,
                    violations: violationSummary
                };

                Swal.fire({
                    title: 'Menyimpan Jawaban...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/ujian/${this.ujianSlug}/store`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem('ujian_answers_{{ $ujian->id }}');
                            localStorage.removeItem('ujian_remaining_time_{{ $ujian->id }}');
                            localStorage.removeItem('ujian_start_time_{{ $ujian->id }}');

                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Ujian telah diselesaikan.',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            throw new Error(data.error || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {

                        this.isSubmitting = false;
                        if (this.antiCheat) {
                            this.antiCheat.isSubmitting = false;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Terjadi kesalahan saat menyimpan jawaban.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    });
            }

        }

        document.addEventListener('DOMContentLoaded', () => {
            const ujianApp = new UjianApp();
            ujianApp.antiCheat = new AntiCheatDetector(ujianApp);
        });
    </script>
</body>

</html>
