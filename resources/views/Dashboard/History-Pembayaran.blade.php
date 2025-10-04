@extends('layouts.dashboard-layouts')

@section('content')
    <script>
        console.log("hello", @json($historyData))
    </script>
    <style>
        .dropdown-row.collapsed .dropdown-content {
            opacity: 0 !important;
            transform: translateY(-10px) !important;
            transition: all 0.3s ease;
            display: none !important;
        }

        .dropdown-row .dropdown-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dropdown-content>div>div:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .fa-chevron-down,
        .fa-chevron-up {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        button:hover i {
            animation: pulse 0.6s ease-in-out;
        }

        .detail-table {
            font-size: 0.875rem;
        }

        .detail-table th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
    </style>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">History Pembayaran</h1>
        <p class="text-gray-600 mt-2">Riwayat pembayaran siswa yang telah disetujui</p>
    </div>

    @if (empty($historyData))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Belum ada data pembayaran yang disetujui untuk batch yang aktif.
                    </p>
                </div>
            </div>
        </div>
    @else
        @foreach ($historyData as $history)
            <div class="mb-8">
                <div class="bg-gray-200 rounded-t-lg p-4 shadow-md">
                    <h2 class=" text-sm sm:text-lg text-left font-bold text-gray-800">
                        <i class="fas fa-history mr-2"></i>
                        {{ $history['title'] }}
                    </h2>
                </div>

                <div class="relative overflow-auto shadow-md rounded-b-lg">
                    <table class="w-full  text-sm text-center text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class=" py-3">#</th>
                                <th class="px-4 py-3">Bulan</th>
                                <th class="px-4 py-3 truncate">Total Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history['data'] as $index => $bulan)
                                <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200 hover:bg-gray-100"
                                    data-history-index="{{ $loop->parent->index }}" data-bulan-index="{{ $index }}">
                                    <td class=" py-4 flex justify-center items-center font-medium text-gray-900">
                                        <button type="button" onclick="toggleRowDropdown(this)"
                                            class="rounded-full p-2 w-10 h-10 flex justify-center items-center hover:bg-gray-200  focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                                            <i class="fas fa-chevron-down text-gray-500"></i>
                                        </button>
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4  font-semibold text-gray-900">
                                        <i class="far fa-calendar-alt text-blue-500 mr-2"></i>
                                        {{ $bulan['bulan'] }}
                                    </td>
                                    <td class="px-4 py-4 truncate">
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1.5 rounded-full border border-green-400">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $bulan['total'] }} Pembayaran
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif

    <div id="imageModal" class="fixed inset-0 bg-gray-600/80 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div
            class="relative top-10 mx-auto h-5/6 p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white flex flex-col">
            <div class="mt-3 flex flex-col h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Bukti Pembayaran</h3>
                    <button onclick="hideImageModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 flex items-center justify-center overflow-hidden">
                    <img id="modalImage" src="" alt="Bukti Pembayaran"
                        class="rounded-md max-h-full max-w-full object-contain" style="height: 100%;">
                </div>
            </div>
        </div>
    </div>

    <script>
        const historyData = @json($historyData);

        function showImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function hideImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideImageModal();
            }
        });

        function toggleRowDropdown(button) {
            const currentRow = button.closest('tr');
            const nextRow = currentRow.nextElementSibling;
            const chevronIcon = button.querySelector('i');

            chevronIcon.style.transition = 'transform 0.3s ease';

            if (nextRow && nextRow.classList.contains('dropdown-row')) {
                const dropdownContent = nextRow.querySelector('.dropdown-content');

                if (nextRow.classList.contains('collapsed')) {
                    nextRow.classList.remove('collapsed');
                    nextRow.classList.add('expanding');
                    chevronIcon.style.transform = 'rotate(180deg)';
                    chevronIcon.classList.remove('fa-chevron-down');
                    chevronIcon.classList.add('fa-chevron-up');

                    setTimeout(() => {
                        dropdownContent.style.opacity = '1';
                        dropdownContent.style.transform = 'translateY(0)';
                        nextRow.classList.remove('expanding');
                    }, 10);
                } else {
                    nextRow.classList.add('collapsed');
                    chevronIcon.style.transform = 'rotate(0deg)';
                    chevronIcon.classList.remove('fa-chevron-up');
                    chevronIcon.classList.add('fa-chevron-down');
                    dropdownContent.style.opacity = '0';
                    dropdownContent.style.transform = 'translateY(-10px)';
                }
            } else {
                createDropdownRow(currentRow);
                chevronIcon.style.transform = 'rotate(180deg)';
                chevronIcon.classList.remove('fa-chevron-down');
                chevronIcon.classList.add('fa-chevron-up');
            }
        }

        function createDropdownRow(currentRow) {
            const dropdownRow = document.createElement('tr');
            dropdownRow.className = 'dropdown-row bg-gray-50';

            const pembayaranData = getPembayaranDataFromRow(currentRow);

            dropdownRow.innerHTML = `
                <td colspan="4" class="px-0 py-0" style="padding: 0px;">
                    <div class="dropdown-content bg-white mx-4 mb-4 p-5 rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                        style="opacity: 0; transform: translateY(-10px); transition: all 0.3s ease;">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Detail Pembayaran Siswa
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="detail-table w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase">
                                    <tr>
                                        <th class="px-4 py-3">#</th>
                                        <th class="px-4 py-3">Bukti Pembayaran</th>
                                        <th class="px-4 py-3">Nama Siswa</th>
                                        <th class="px-4 py-3">Email</th>
                                        <th class="px-4 py-3">Cicilan Ke</th>
                                        <th class="px-4 py-3">Total Tagihan</th>
                                        <th class="px-4 py-3">Tanggal Jatuh Tempo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${pembayaranData.map((item, idx) => `
                                                                                                                                                                                <tr class="border-b hover:bg-gray-50">
                                                                                                                                                                                    <td class="px-4 py-3 font-medium">${idx + 1}</td>
                                                                                                                                                                                    <td class="px-4 py-3">
                                                                                                                                                                                        <img src="${item.bukti_pembayaran}" 
                                                                                                                                                                                            alt="Bukti Pembayaran" 
                                                                                                                                                                                            class="w-12 h-12 object-cover rounded-md cursor-pointer hover:scale-110 transition-transform"
                                                                                                                                                                                            onclick="showImageModal('${item.bukti_pembayaran}')">
                                                                                                                                                                                    </td>
                                                                                                                                                                                    <td class="px-4 py-3 font-medium text-gray-900">${item.nama}</td>
                                                                                                                                                                                    <td class="px-4 py-3 text-blue-600">
                                                                                                                                                                                        <a href="mailto:${item.email}" class="hover:underline">${item.email}</a>
                                                                                                                                                                                    </td>
                                                                                                                                                                                    <td class="px-4 py-3">
                                                                                                                                                                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">${item.cicilan_ke}</span>
                                                                                                                                                                                    </td>
                                                                                                                                                                                    <td class="px-4 py-3 font-semibold text-green-600">${item.total_tagihan}</td>
                                                                                                                                                                                    <td class="px-4 py-3">${item.tanggal_jatuh_tempo}</td>
                                                                                                                                                                                </tr>
                                                                                                                                                                            `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            `;

            currentRow.parentNode.insertBefore(dropdownRow, currentRow.nextSibling);

            const dropdownContent = dropdownRow.querySelector('.dropdown-content');
            setTimeout(() => {
                dropdownContent.style.opacity = '1';
                dropdownContent.style.transform = 'translateY(0)';
            }, 10);
        }

        function getPembayaranDataFromRow(row) {
            const historyIndex = parseInt(row.dataset.historyIndex);
            const bulanIndex = parseInt(row.dataset.bulanIndex);

            return historyData[historyIndex].data[bulanIndex].pembayaran;
        }
    </script>
@endsection
