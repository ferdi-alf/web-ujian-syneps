@extends('layouts.dashboard-layouts')

@section('content')
    <div class="flex justify-end">
        <input type="file" class="hidden" id="excelFile">
        <label for="excelFile" type="button"
            class="bg-green-600 rounded-lg shadow-md p-3 font-medium cursor-pointer hover:bg-green-900 transition-all items-center gap-3 flex text-white">
            <i class="fa-solid fa-file-excel"></i>
            <p>Import Excel</p>
        </label>
    </div>
    <form id="examForm" class="relative pb-32">

        <div class="flex mb-4 items-center {{ Auth::user()->role === 'admin' ? 'justify-between' : 'justify-end' }} gap-2">
            @if (Auth::user()->role === 'admin')
                <x-fragments.select-field label="Pilih Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" />
            @endif
            <button type="button" id="tambahSoalBtn"
                class="text-white h-12 sm:mt-0 mt-5 bg-gradient-to-br from-teal-300 cursor-pointer to-emerald-400 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 font-medium rounded-lg text-sm p-2 text-center transition-all duration-200 hover:scale-105">
                Tambah Soal +
            </button>
        </div>

        <x-fragments.text-field label="Masukan Judul Materi Ujian" name="judul" required />

        <div id="soalContainer" class="mt-5 space-y-4">
            <div class="soal-item bg-white w-full flex rounded-lg shadow-md flex-col space-y-2 items-end p-3 transform transition-all duration-300 ease-in-out"
                data-soal-index="1">
                <div class="flex w-full justify-between items-center">
                    <div class="flex w-full justify-start gap-2">
                        <span class="text-xl font-bold soal-number">1.</span>
                        <div class="w-full">
                            <x-fragments.text-field name="soal[0][soal]" placeholder="Masukkan pertanyaan soal" required />
                        </div>
                    </div>
                </div>

                <div class="w-[95%] flex flex-col mt-3 space-y-3">
                    <div class="flex gap-3 items-center justify-center">
                        <div class="flex items-center justify-center">
                            <input type="radio" name="soal[0][jawaban_benar]" value="A"
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                            <label class="ms-2 text-lg font-medium text-gray-900">A.</label>
                        </div>
                        <div class="w-full">
                            <x-fragments.text-field name="soal[0][jawaban][A]" placeholder="Pilihan A" required />
                        </div>
                    </div>

                    <div class="flex gap-3 items-center justify-center">
                        <div class="flex items-center justify-center">
                            <input type="radio" name="soal[0][jawaban_benar]" value="B"
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                            <label class="ms-2 text-lg font-medium text-gray-900">B.</label>
                        </div>
                        <div class="w-full">
                            <x-fragments.text-field name="soal[0][jawaban][B]" placeholder="Pilihan B" required />
                        </div>
                    </div>

                    <div class="flex gap-3 items-center justify-center">
                        <div class="flex items-center justify-center">
                            <input type="radio" name="soal[0][jawaban_benar]" value="C"
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                            <label class="ms-2 text-lg font-medium text-gray-900">C.</label>
                        </div>
                        <div class="w-full">
                            <x-fragments.text-field name="soal[0][jawaban][C]" placeholder="Pilihan C" required />
                        </div>
                    </div>

                    <div class="flex gap-3 items-center justify-center">
                        <div class="flex items-center justify-center">
                            <input type="radio" name="soal[0][jawaban_benar]" value="D"
                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                            <label class="ms-2 text-lg font-medium text-gray-900">D.</label>
                        </div>
                        <div class="w-full">
                            <x-fragments.text-field name="soal[0][jawaban][D]" placeholder="Pilihan D" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex fixed bottom-0 md:w-5/6 w-11/12 z-30 bg-white justify-end p-3">
                <button type="submit"
                    class="px-6 py-2 font-semibold bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Simpan Ujian
                </button>
            </div>
        </div>

    </form>
    <div id="loadingModal" class="fixed inset-0 bg-black/80 bg-opacity-50  items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-2xl">
            <div class="text-center">
                <!-- Spinner -->
                <div class="flex justify-center mb-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                </div>

                <!-- Progress Text -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Menyimpan Ujian</h3>
                <p id="progressText" class="text-gray-600 mb-4">Memulai...</p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                    <div id="progressBar" class="bg-blue-500 h-full rounded-full transition-all duration-300 ease-out"
                        style="width: 0%"></div>
                </div>

                <!-- Progress Percentage -->
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>Progress</span>
                    <span id="progressPercent">0%</span>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let soalCount = 1;
            const tambahSoalBtn = document.getElementById('tambahSoalBtn');
            const soalContainer = document.getElementById('soalContainer');
            const examForm = document.getElementById('examForm');
            const loadingModal = document.getElementById('loadingModal');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const progressText = document.getElementById('progressText');
            let progressInterval = null;
            const excelFileInput = document.getElementById('excelFile');

            function normalizeColumnName(name) {
                if (!name) return '';
                return name.toString().toLowerCase().trim().replace(/\s+/g, '_');
            }

            function identifyColumns(headers) {
                console.log('📋 Headers Excel:', headers);
                const normalizedHeaders = headers.map(h => normalizeColumnName(h));
                console.log('📋 Normalized headers:', normalizedHeaders);

                const columnMap = {};

                const soalPatterns = ['soal', 'pertanyaan', 'question', 'soal_pertanyaan'];
                columnMap.soal = normalizedHeaders.findIndex(h =>
                    soalPatterns.some(pattern => h.includes(pattern))
                );

                const pilihanPatterns = [
                    ['pilihan_1', 'pilihan1', 'jawaban_1', 'jawaban1', 'jawaban 1', 'pilihan_a', 'pilihana',
                        'pilihan a', 'jawaban_a', 'jawabana', 'jawaban a', 'a'
                    ],
                    ['pilihan_2', 'pilihan2', 'jawaban_2', 'jawaban2', 'jawaban 2', 'pilihan_b', 'pilihanb',
                        'pilihan b', 'jawaban_b', 'jawabanb', 'jawaban b', 'b'
                    ],
                    ['pilihan_3', 'pilihan3', 'jawaban_3', 'jawaban3', 'jawaban 3', 'pilihan_c', 'pilihanc',
                        'pilihan c', 'jawaban_c', 'jawabanc', 'jawaban c', 'c'
                    ],
                    ['pilihan_4', 'pilihan4', 'jawaban_4', 'jawaban4', 'jawaban 4', 'pilihan_d', 'pilihand',
                        'pilihan d', 'jawaban_d', 'jawaband', 'jawaban d', 'd'
                    ]
                ];

                columnMap.pilihan = {};
                ['A', 'B', 'C', 'D'].forEach((letter, index) => {
                    columnMap.pilihan[letter] = normalizedHeaders.findIndex(h =>
                        pilihanPatterns[index].some(pattern => h.includes(pattern))
                    );
                });

                const jawabanBenarPatterns = ['jawaban_benar', 'pilihan_benar', 'correct', 'answer',
                    'kunci_jawaban', 'kunci'
                ];
                const exactJawabanIndex = normalizedHeaders.findIndex(h => h === 'jawaban');


                if (exactJawabanIndex !== -1) {
                    columnMap.jawabanBenar = exactJawabanIndex;
                } else {
                    columnMap.jawabanBenar = normalizedHeaders.findIndex(h =>
                        jawabanBenarPatterns.some(pattern => h.includes(pattern))
                    );
                }

                console.log('🎯 Column map result:', columnMap);
                return columnMap;
            }

            function parseJawabanBenar(value) {
                if (!value) return '';

                const str = value.toString().trim().toLowerCase();
                console.log(`Parsing jawaban: "${value}" -> "${str}"`);

                const clean = str.replace(/[^a-d0-9]/gi, '');
                if (/^[1-4]$/.test(clean)) {
                    const result = ['A', 'B', 'C', 'D'][parseInt(clean) - 1];
                    console.log(`Angka ${clean} -> ${result}`);
                    return result;
                }
                if (/^[a-d]$/i.test(clean)) {
                    const result = clean.toUpperCase();
                    console.log(`Huruf ${clean} -> ${result}`);
                    return result;
                }

                console.warn(`Tidak bisa parse: "${value}"`);
                return '';
            }

            function readExcelFile(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        try {
                            const data = new Uint8Array(e.target.result);
                            const workbook = XLSX.read(data, {
                                type: 'array'
                            });
                            const firstSheetName = workbook.SheetNames[0];
                            const worksheet = workbook.Sheets[firstSheetName];
                            const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                                header: 1
                            });
                            resolve(jsonData);
                        } catch (error) {
                            reject(error);
                        }
                    };
                    reader.onerror = reject;
                    reader.readAsArrayBuffer(file);
                });
            }

            function importDataToForm(data) {
                console.log('📊 Raw Excel data:', data);
                if (!data || data.length < 2) {
                    throw new Error('Data Excel tidak valid atau kosong');
                }

                const headers = data[0];
                const rows = data.slice(1).filter(row => row.some(cell => cell !== null && cell !== undefined &&
                    cell !== ''));

                console.log('📋 Headers:', headers);
                console.log('📝 Sample row:', rows[0]);

                if (rows.length === 0) {
                    throw new Error('Tidak ada data soal yang valid dalam file Excel');
                }

                const columnMap = identifyColumns(headers);
                console.log('🗺️ Final column map:', columnMap);

                if (columnMap.soal === -1) {
                    throw new Error('Kolom soal/pertanyaan tidak ditemukan');
                }

                const missingPilihan = ['A', 'B', 'C', 'D'].filter(letter => columnMap.pilihan[letter] === -1);
                if (missingPilihan.length > 0) {
                    throw new Error(`Kolom pilihan tidak lengkap. Missing: ${missingPilihan.join(', ')}`);
                }

                const existingSoal = document.querySelectorAll('.soal-item');
                existingSoal.forEach((item, index) => {
                    if (index > 0) {
                        item.remove();
                    }
                });

                soalCount = rows.length;

                rows.forEach((row, index) => {
                    let soalItem;

                    if (index === 0) {
                        soalItem = document.querySelector('.soal-item');
                    } else {
                        soalItem = createSoalElement(index);
                        soalContainer.appendChild(soalItem);
                        setTimeout(() => {
                            soalItem.classList.remove('opacity-0', 'scale-95');
                            soalItem.classList.add('opacity-100', 'scale-100');
                        }, 10);
                    }

                    const soalInput = soalItem.querySelector(`input[name="soal[${index}][soal]"]`);
                    if (soalInput && row[columnMap.soal]) {
                        soalInput.value = row[columnMap.soal].toString();
                    }

                    ['A', 'B', 'C', 'D'].forEach(letter => {
                        const pilihanInput = soalItem.querySelector(
                            `input[name="soal[${index}][jawaban][${letter}]"]`);
                        const columnIndex = columnMap.pilihan[letter];
                        if (pilihanInput && columnIndex !== -1 && row[columnIndex]) {
                            pilihanInput.value = row[columnIndex].toString();
                        }
                    });

                    if (columnMap.jawabanBenar !== -1 && row[columnMap.jawabanBenar]) {
                        console.log(`✅ Found jawaban benar for row ${index}:`, row[columnMap.jawabanBenar]);
                        const correctAnswer = parseJawabanBenar(row[columnMap.jawabanBenar]);
                        console.log(`🎯 Parsed answer:`, correctAnswer);
                        if (correctAnswer) {
                            setTimeout(() => {
                                const radioButton = soalItem.querySelector(
                                    `input[name="soal[${index}][jawaban_benar]"][value="${correctAnswer}"]`
                                );
                                if (radioButton) {
                                    radioButton.checked = true;
                                    console.log(
                                        `✅ Radio button set untuk soal ${index}: ${correctAnswer}`
                                    );
                                } else {
                                    console.warn(
                                        `❌ Radio button tidak ditemukan untuk soal ${index}: ${correctAnswer}`
                                    );
                                }
                            }, 50);
                        }
                    } else {
                        console.log(
                            `❌ No jawaban benar found for row ${index}. Column index: ${columnMap.jawabanBenar}, Value:`,
                            row[columnMap.jawabanBenar]);
                    }
                });

                updateSoalNumbers();
                updateFormNames();
            }

            excelFileInput.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const allowedTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                    'text/csv'
                ];

                if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Tidak Valid!',
                        text: 'Silakan pilih file Excel (.xlsx, .xls) atau CSV (.csv)',
                        confirmButtonColor: '#d33'
                    });
                    e.target.value = '';
                    return;
                }

                try {
                    Swal.fire({
                        title: 'Membaca File...',
                        text: 'Sedang memproses file Excel',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const data = await readExcelFile(file);
                    await importDataToForm(data);

                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        text: `Berhasil mengimpor ${soalCount} soal dari file Excel`,
                        confirmButtonColor: '#28a745',
                        timer: 3000,
                        timerProgressBar: true
                    });

                } catch (error) {
                    console.error('Error importing Excel:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Gagal!',
                        text: error.message || 'Terjadi kesalahan saat mengimpor file Excel',
                        confirmButtonColor: '#d33'
                    });
                }

                e.target.value = '';
            });

            function createSoalElement(index) {
                const soalHTML = `
                        <div class="soal-item bg-white w-full flex rounded-lg shadow-md flex-col space-y-2 items-end p-3 transform transition-all duration-300 ease-in-out opacity-0 scale-95" 
                            data-soal-index="${index}">
                            <div class="flex w-full justify-between items-center">
                                <div class="flex w-full justify-start gap-2">
                                    <span class="text-xl font-bold soal-number">${index + 1}.</span>
                                    <div class="w-full">
                                        <input type="text" name="soal[${index}][soal]" placeholder="Masukkan pertanyaan soal" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                <button type="button" class="hapus-soal-btn ml-3 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-all duration-200 transform hover:scale-110"
                                        title="Hapus Soal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="w-[95%] flex flex-col mt-3 space-y-3">
                                <!-- Pilihan A -->
                                <div class="flex gap-3 items-center justify-center">
                                    <div class="flex items-center justify-center">
                                        <input type="radio" name="soal[${index}][jawaban_benar]" value="A"
                                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label class="ms-2 text-lg font-medium text-gray-900">A.</label>
                                    </div>
                                    <div class="w-full">
                                        <input type="text" name="soal[${index}][jawaban][A]" placeholder="Pilihan A" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="flex gap-3 items-center justify-center">
                                    <div class="flex items-center justify-center">
                                        <input type="radio" name="soal[${index}][jawaban_benar]" value="B"
                                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label class="ms-2 text-lg font-medium text-gray-900">B.</label>
                                    </div>
                                    <div class="w-full">
                                        <input type="text" name="soal[${index}][jawaban][B]" placeholder="Pilihan B" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                
                                <div class="flex gap-3 items-center justify-center">
                                    <div class="flex items-center justify-center">
                                        <input type="radio" name="soal[${index}][jawaban_benar]" value="C"
                                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label class="ms-2 text-lg font-medium text-gray-900">C.</label>
                                    </div>
                                    <div class="w-full">
                                        <input type="text" name="soal[${index}][jawaban][C]" placeholder="Pilihan C" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                
                                <div class="flex gap-3 items-center justify-center">
                                    <div class="flex items-center justify-center">
                                        <input type="radio" name="soal[${index}][jawaban_benar]" value="D"
                                            class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label class="ms-2 text-lg font-medium text-gray-900">D.</label>
                                    </div>
                                    <div class="w-full">
                                        <input type="text" name="soal[${index}][jawaban][D]" placeholder="Pilihan D" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
             `;

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = soalHTML;
                return tempDiv.firstElementChild;
            }

            function getCsrfToken() {
                const metaTag = document.querySelector('meta[name="csrf-token"]')
                if (metaTag) {
                    return metaTag.getAttribute('content')
                }

                const csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) {
                    return csrfInput.value;
                }

                console.error('CSRF token tidak ditemukan!');
                return null;
            }

            tambahSoalBtn.addEventListener('click', function() {
                const newSoal = createSoalElement(soalCount);
                soalContainer.appendChild(newSoal);

                setTimeout(() => {
                    newSoal.classList.remove('opacity-0', 'scale-95');
                    newSoal.classList.add('opacity-100', 'scale-100');
                }, 10);

                soalCount++;
            });

            soalContainer.addEventListener('click', function(e) {
                if (e.target.closest('.hapus-soal-btn')) {
                    const soalItem = e.target.closest('.soal-item');
                    const currentIndex = parseInt(soalItem.getAttribute('data-soal-index'));

                    const isFirstSoal = currentIndex === 0;
                    if (isFirstSoal) {
                        alert('Soal pertama tidak bisa dihapus!');
                        return;
                    }

                    const targetSoalData = extractSoalData(soalItem);

                    soalItem.style.maxHeight = soalItem.scrollHeight + 'px';
                    soalItem.style.overflow = 'hidden';

                    setTimeout(() => {
                        soalItem.style.transition = 'all 0.3s ease-out';
                        soalItem.style.maxHeight = '0';
                        soalItem.style.marginTop = '0';
                        soalItem.style.marginBottom = '0';
                        soalItem.style.paddingTop = '0';
                        soalItem.style.paddingBottom = '0';
                        soalItem.style.opacity = '0';
                        soalItem.style.transform = 'translateX(-100%)';
                    }, 10);

                    setTimeout(() => {
                        soalItem.remove();
                        soalCount--;

                        updateSoalNumbers();
                        updateFormNames();

                        reorderSoalItems();
                    }, 320);
                }
            });

            function extractSoalData(soalItem) {
                const soalInput = soalItem.querySelector('input[name*="[soal]"]');
                const radioChecked = soalItem.querySelector('input[type="radio"]:checked');
                const jawabanInputs = soalItem.querySelectorAll('input[name*="[jawaban]"]');

                const data = {
                    soal: soalInput ? soalInput.value : '',
                    jawaban_benar: radioChecked ? radioChecked.value : '',
                    jawaban: {}
                };

                jawabanInputs.forEach(input => {
                    const match = input.name.match(/\[([A-D])\]$/);
                    if (match) {
                        data.jawaban[match[1]] = input.value;
                    }
                });

                return data;
            }

            function reorderSoalItems() {
                const soalItems = document.querySelectorAll('.soal-item');
                soalItems.forEach((item, index) => {
                    item.style.transform = 'translateY(-5px)';
                    item.style.transition = 'transform 0.2s ease-out';

                    setTimeout(() => {
                        item.style.transform = 'translateY(0)';
                    }, 100 + (index * 50));
                });
            }

            function updateSoalNumbers() {
                const soalItems = document.querySelectorAll('.soal-item');
                soalItems.forEach((item, index) => {
                    const numberSpan = item.querySelector('.soal-number');
                    numberSpan.textContent = `${index + 1}.`;
                    item.setAttribute('data-soal-index', index);
                });
            }

            function updateFormNames() {
                const soalItems = document.querySelectorAll('.soal-item');
                soalItems.forEach((item, index) => {
                    const soalInput = item.querySelector('input[name*="[soal]"]');
                    if (soalInput) {
                        soalInput.name = `soal[${index}][soal]`;
                    }

                    const radioButtons = item.querySelectorAll('input[type="radio"]');
                    radioButtons.forEach(radio => {
                        radio.name = `soal[${index}][jawaban_benar]`;
                    });

                    const jawabanInputs = item.querySelectorAll('input[name*="[jawaban]"]');
                    jawabanInputs.forEach(input => {
                        const pilihan = input.name.match(/\[([A-D])\]$/)[1];
                        input.name = `soal[${index}][jawaban][${pilihan}]`;
                    });
                });
            }

            function collectFormData() {
                const formData = new FormData(examForm);
                const data = {
                    judul: formData.get('judul') || '',
                    kelas_id: formData.get('kelas_id') || '',
                    soal: []
                };

                const soalItems = document.querySelectorAll('.soal-item');
                soalItems.forEach((item, index) => {
                    const soalText = item.querySelector('input[name*="[soal]"]').value || '';
                    const jawabanBenarRadio = item.querySelector('input[name*="[jawaban_benar]"]:checked');
                    const jawabanBenar = jawabanBenarRadio ? jawabanBenarRadio.value : '';

                    const jawaban = [];
                    ['A', 'B', 'C', 'D'].forEach(pilihan => {
                        const jawabanInput = item.querySelector(
                            `input[name*="[jawaban][${pilihan}]"]`);
                        const teks = jawabanInput ? jawabanInput.value || '' : '';
                        jawaban.push({
                            pilihan: pilihan,
                            teks: teks,
                            benar: pilihan === jawabanBenar
                        });
                    });

                    if (soalText.trim()) {
                        data.soal.push({
                            soal: soalText,
                            jawaban: jawaban
                        });
                    }
                });

                return data;
            }

            function showLoadingModal() {
                loadingModal.classList.add('flex');
                loadingModal.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressPercent.textContent = '0%';
                progressText.textContent = 'Memulai...';
            }

            function hideLoadingModal() {
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
                if (progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;
                }
            }

            function resetProgress() {
                progressBar.style.width = '0%';
                progressPercent.textContent = '0%';
                progressText.textContent = 'Memulai...';
            }

            function showLoadingModal() {
                loadingModal.classList.add('flex');
                loadingModal.classList.remove('hidden');
                resetProgress();
            }

            function hideLoadingModal() {
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
                if (progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;
                }
            }

            async function clearServerProgress() {
                const csrfToken = getCsrfToken();
                try {
                    await fetch('/tambah-ujian/clear-progress', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                } catch (error) {
                    console.log('Clear progress error (ignorable):', error);
                }
            }

            function startProgressTracking() {
                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    console.error('Cannot start progress tracking: CSRF token not found');
                    return;
                }

                let consecutiveErrorCount = 0;
                let lastPercentage = 0;

                progressInterval = setInterval(async () => {
                    try {
                        const response = await fetch('/tambah-ujian/progress', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            console.log('📊 Progress Update:', data);

                            consecutiveErrorCount = 0;

                            if (data.percentage > lastPercentage || data.message !== progressText
                                .textContent) {
                                lastPercentage = data.percentage;
                                progressBar.style.transition = 'width 0.8s ease-in-out'; // Lebih smooth
                                progressBar.style.width = data.percentage + '%';
                                progressPercent.textContent = data.percentage + '%';
                                progressText.textContent = data.message || 'Memproses...';
                            }

                            if (data.percentage >= 100) {
                                console.log('✅ Progress completed');
                                clearInterval(progressInterval);
                                progressInterval = null;

                                // Tunggu sebentar sebelum hide modal
                                setTimeout(() => {
                                    hideLoadingModal();
                                }, 1500);
                            }
                        } else {
                            consecutiveErrorCount++;
                            console.error('Progress endpoint error:', response.status);
                            if (consecutiveErrorCount > 3) { // Kurangi threshold
                                clearInterval(progressInterval);
                                progressInterval = null;
                            }
                        }
                    } catch (error) {
                        consecutiveErrorCount++;
                        console.error('Error fetching progress:', error);
                        if (consecutiveErrorCount > 5) { // Kurangi threshold
                            clearInterval(progressInterval);
                            progressInterval = null;
                        }
                    }
                }, 800);
            }

            examForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Token keamanan tidak ditemukan. Silakan refresh halaman.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                const data = collectFormData();
                console.log('📤 Mengirim data:', data);

                const invalidSoal = data.soal.find(soal => {
                    const jawabanBenar = soal.jawaban.filter(j => j.benar).length;
                    return jawabanBenar !== 1;
                });

                if (invalidSoal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal!',
                        text: 'Setiap soal harus memiliki tepat satu jawaban yang benar!',
                        confirmButtonColor: '#f39c12'
                    });
                    return;
                }

                if (data.soal.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal!',
                        text: 'Minimal harus ada satu soal!',
                        confirmButtonColor: '#f39c12'
                    });
                    return;
                }

                clearServerProgress().then(() => {
                    showLoadingModal();

                    fetch('/tambah-ujian', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(responseData => {
                            console.log('📥 Server Response:', responseData);

                            if (responseData.success) {
                                console.log('✅ Ujian berhasil disimpan!');

                                setTimeout(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: `Ujian "${responseData.data?.judul || data.judul}" berhasil disimpan dengan ${responseData.data?.total_soal || data.soal.length} soal!`,
                                        confirmButtonColor: '#28a745',
                                        timer: 4000,
                                        timerProgressBar: true
                                    }).then(() => {
                                        examForm.reset();

                                        const soalItems = document
                                            .querySelectorAll('.soal-item');

                                        soalItems.forEach((item, index) => {
                                            if (index > 0) {
                                                item.remove();
                                            }
                                        });

                                        if (soalItems.length > 0) {
                                            const firstSoal = soalItems[0];

                                            firstSoal.querySelectorAll(
                                                    'input[type="text"]')
                                                .forEach(input => {
                                                    input.value = '';
                                                });

                                            firstSoal.querySelectorAll(
                                                    'input[type="radio"]')
                                                .forEach(radio => {
                                                    radio.checked = false;
                                                });

                                            firstSoal.setAttribute(
                                                'data-soal-index', '0');

                                            const numberSpan = firstSoal
                                                .querySelector('.soal-number');
                                            if (numberSpan) numberSpan
                                                .textContent = '1.';
                                        }

                                        soalCount = 1;
                                    });
                                }, 2500);

                            } else {
                                if (progressInterval) {
                                    clearInterval(progressInterval);
                                    progressInterval = null;
                                }
                                hideLoadingModal();

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan!',
                                    text: responseData.message ||
                                        'Terjadi kesalahan saat menyimpan ujian',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('❌ Network/Server Error:', error);

                            if (progressInterval) {
                                clearInterval(progressInterval);
                                progressInterval = null;
                            }
                            hideLoadingModal();

                            Swal.fire({
                                icon: 'error',
                                title: 'Error Koneksi!',
                                text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                                confirmButtonColor: '#d33'
                            });
                        });

                    setTimeout(() => {
                        startProgressTracking();
                    }, 300);
                });
            });
        });
    </script>
@endsection
