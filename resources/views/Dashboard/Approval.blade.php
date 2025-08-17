@extends('layouts.dashboard-layouts')

@section('content')
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
    </style>
    <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50  role="alert">
        <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span class="sr-only">Info</span>
        <div>
            <span class="font-medium">Info</span> Data pendafatar akan otomatis terhapus ketika pendafatar sudah membuat akun
        </div>
    </div>
    <div class="relative mt-10 overflow-auto shadow-md sm:rounded-lg">
        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-sm text-center rtl:text-right text-gray-500 ">
                <thead class="text-xs text-center text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th>
                        </th>
                        <th scope="col" class="px-4 py-3 ">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3 ">
                            Bukti Pembayaran
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Nama Lengkap
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Email
                        </th>

                        <th scope="col" class="px-4 py-3 ">
                            Kelas
                        </th>
                        <th scope="col" class="px-4 py-3 ">
                            Batch
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Action
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($peserta as $i => $p)
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200  hover:bg-gray-100 ">
                            <th scope="row" class="p-2">
                                <button type="button" onclick="toggleRowDropdown(this)"
                                    class="rounded-full p-2 w-10 h-10 flex justify-center items-center  hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                                    <i class="fas fa-chevron-down text-gray-500"></i>
                                </button>
                            </th>
                            <td class="px-4 py-4 font-medium text-gray-900  text-center">
                                {{ $i + 1 }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <img src="{{ asset('uploads/images/dp/' . $p->bukti_pembayaran_dp) }}"
                                    alt="Bukti Pembayaran"
                                    class="w-16 h-16 object-cover rounded-md mx-auto cursor-pointer hover:scale-110 transition-transform"
                                    onclick="showImageModal('{{ asset('uploads/images/dp/' . $p->bukti_pembayaran_dp) }}')">
                            </td>
                            <td class="px-4 py-4 font-medium text-gray-900 ">
                                <div class="break-words">
                                    {{ $p->nama_lengkap }}
                                </div>
                            </td>
                            <td class="px-4 py-4 truncate">
                                <div class="break-all text-blue-600 hover:text-blue-800">
                                    <a href="mailto:{{ $p->email }}" class="hover:underline">
                                        {{ $p->email }}
                                    </a>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm font-medium truncate">
                                {!! $p->kelas->formatted_kelas ?? '-' !!}
                            </td>
                            <td class="px-4 py-4  text-red-900 ">
                                {{ $p->batches->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="break-words flex flex-col items-center">
                                    @if ($p->status === 'pending')
                                        <span
                                            class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-yellow-400">
                                            {{ $p->status }}
                                        </span>
                                    @elseif ($p->status === 'confirmed')
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400">
                                            {{ $p->status }}
                                        </span>
                                        <small>Menunggu peserta membuat akun</small>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 flex flex-nowrap gap-3">
                                @switch ($p->status)
                                    @case('pending')
                                        <form action="{{ route('approval.update', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" onclick="confirmButton(this)"
                                                class="text-blue-500 bg-blue-100 hover:bg-blue-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-3 text-center inline-flex items-center">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('approval.delete', $p->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)"
                                                class="text-red-500 bg-red-100 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg p-3 text-center inline-flex items-center">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @break

                                    @case('confirmed')
                                        <form action="{{ route('approval.resend', $p->id) }}" method="POST">
                                            @csrf

                                            <button type="button" data-tooltip-target="tooltip-default"
                                                class="text-blue-500 bg-blue-100 hover:bg-blue-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-3 text-center inline-flex items-center">
                                                <i class="fa-solid fa-repeat"></i>
                                            </button>
                                            <div id="tooltip-default" role="tooltip"
                                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip ">
                                                Kirim ulang Email
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </form>
                                    @break

                                    @default
                                        <span class="text-gray-500">Tidak ada aksi</span>
                                @endswitch

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="imageModal" class="fixed inset-0 bg-gray-600/80 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div
                class="relative top-10 mx-auto h-5/6 p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white flex flex-col">
                <div class="mt-3 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Bukti Pembayaran</h3>
                        <button onclick="hideImageModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
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

            function confirmButton(button) {
                const form = button.closest('form');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengonfirmasi peserta ini? data peserta akan otomatis terhapus ketika peserta sudah membuat akun.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, konfirmasi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log("form submit dijalankan");
                        form.submit();
                    }
                })
            }

            function confirmDelete(button) {
                const form = button.closest('form');
                Swal.fire({
                    title: 'Hapus Peserta',
                    text: 'Yakin ingin menghapus peserta ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

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
                dropdownRow.className = 'dropdown-row bg-gray-300';

                const pesertaData = getPesertaDataFromRow(currentRow);

                dropdownRow.innerHTML = `
                    <td colspan="9" class="px-0 py-0 " style="padding: 0px; ">
                        <div class="dropdown-content bg-white  mx-4 mb-4 p-3 rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                            style="opacity: 0; transform: translateY(-10px); transition: all 0.3s ease;">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                                            Detail Peserta
                                </h4>
                                <div class="grid gap-5  grid-cols-12 shadow-lg rounded-md">
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium text-gray-600 mb-3">
                                            <i class="fas fa-receipt text-gray-400 mr-1"></i>
                                            Bukti Pembayaran
                                        </label>
                                        <div class="bg-gray-50 p-4 rounded-md border-l-4 border-indigo-400">
                                            <div class="relative group inline-block">
                                                <img src="uploads/images/dp/${pesertaData.buktiPembayaran}" 
                                                    alt="Bukti Pembayaran" 
                                                    class=" object-cover rounded-lg cursor-pointer shadow-md transform group-hover:scale-110 transition-all duration-300 hover:shadow-xl"
                                                    onclick="showImageModal('uploads/images/dp/${pesertaData.buktiPembayaran}')">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-9 p-6 shadow-lg rounded-md">
                                        <div class="grid grid-cols-3 gap-4 items-center-safe ">
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fas fa-user text-blue-400 mr-1"></i>
                                                    Nama Lengkap
                                                </label>
                                                <p class="text-gray-900 bg-gray-50 p-3 rounded-md border-l-4 border-blue-400">${pesertaData.nama}</p>
                                            </div>
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fas fa-envelope text-green-400 mr-1"></i>
                                                    Email
                                                </label>
                                                <p class="text-gray-900 bg-gray-50 p-3 rounded-md border-l-4 border-green-400 break-all">${pesertaData.email}</p>
                                            </div>
                                           <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fas fa-phone text-yellow-400 mr-1"></i>
                                                    No HP
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-yellow-400">
                                                    ${pesertaData.noHp}
                                                </div>
                                           </div>
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-graduation-cap text-orange-400 mr-1"></i>
                                                    Pendidikan Terakhir
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-orange-400">
                                                    ${pesertaData.pendidikanTerakhir}
                                                </div>
                                            </div>
                                           
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-location-dot text-red-400 mr-1"></i>
                                                    Alamat
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-red-400">
                                                    ${pesertaData.alamat}
                                                    lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum.
                                                </div>
                                            </div>
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    ${
                                                        pesertaData.jenisKelamin === 'Laki-laki'
                                                            ? '<i class="fas fa-mars text-blue-400 mr-1"></i>'
                                                            : '<i class="fas fa-venus text-pink-400 mr-1"></i>'
                                                    }
                                                    Jenis Kelamin
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 ${
                                                    pesertaData.jenisKelamin === 'Laki-laki'
                                                        ? 'border-cyan-400'
                                                        : 'border-pink-400'
                                                }">
                                                    ${pesertaData.jenisKelamin}
                                                </div>
                                            </div>
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-chalkboard-user text-fuchsia-400 mr-1"></i>
                                                    Kelas
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-fuchsia-400">
                                                    ${pesertaData.kelas}
                                                </div>
                                            </div>
                                            <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-layer-group text-rose-400 mr-1"></i>
                                                    Batch
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-rose-400">
                                                    ${pesertaData.batch}
                                                </div>
                                            </div>
                                           
                                             <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-info text-orange-400 mr-1"></i>
                                                    Mengetahui Program Dari
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-orange-400">
                                                    ${pesertaData.mengetahuiProgramDari}
                                                </div>
                                            </div>
                                             <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-money-bill-1 text-emerald-400 mr-1"></i>
                                                    Total Tagihan
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-emerald-400">
                                                    ${pesertaData.totalTagihan}
                                                </div>
                                            </div>
                                             <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-money-bill-transfer text-teal-400 mr-1"></i>
                                                   Jumlah Cicilan/Bulan
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-teal-400">
                                                    ${pesertaData.jumlahCicilan}x
                                                </div>
                                            </div>
                                             <div class="transform hover:scale-105 transition-transform duration-200">
                                                <label class="block text-sm font-medium text-gray-600 mb-2">
                                                    <i class="fa-solid fa-flag text-sky-400 mr-1"></i>
                                                   Status
                                                </label>
                                                <div class="bg-gray-50 text-gray-900 p-3 rounded-md border-l-4 border-sky-400">
                                                    ${pesertaData.status}
                                                    <br/>
                                                  ${pesertaData.status === 'confirmed' ? 
                                                  '<small>Menunggu peserta membuat akun</small> '
                                                   : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

            function getPesertaDataFromRow(row) {
                const cells = @json($peserta);

                let formattedKelas = cells[row.rowIndex - 1].kelas.nama ?? '-';
                const formatTotalTagihan = cells[row.rowIndex - 1].kelas.total_tagihan ?
                    ' - Rp. ' + cells[row.rowIndex - 1].kelas.total_tagihan.toLocaleString('id-ID') :
                    '';


                if (formattedKelas && cells[row.rowIndex - 1].kelas.durasi_belajar && cells[row.rowIndex - 1].kelas
                    .waktu_magang && cells[row.rowIndex - 1].kelas.type) {
                    formattedKelas += ' - ' + cells[row.rowIndex - 1].kelas.type + '    (' + cells[row.rowIndex - 1]
                        .kelas.durasi_belajar + ' bulan' +
                        ' + ' +
                        cells[row.rowIndex - 1].kelas.waktu_magang + ' bulan)';
                } else if (formattedKelas && cells[row.rowIndex - 1].kelas.durasi_belajar && cells[row.rowIndex - 1].kelas
                    .waktu_magang === 0 && cells[row.rowIndex - 1].kelas.type) {
                    formattedKelas += ' - ' + cells[row.rowIndex - 1].kelas.type + '    (' + cells[row.rowIndex - 1]
                        .kelas.durasi_belajar + ' bulan'
                    ' )';
                } else if (formattedKelas && cells[row.rowIndex - 1].kelas.durasi_belajar && cells[row.rowIndex - 1].kelas
                    .waktu_magang === 0 && !cells[row.rowIndex - 1].kelas.type) {
                    formattedKelas += ' - ' +
                        cells[row.rowIndex - 1]
                        .kelas.durasi_belajar + ' bulan';
                }

                return {
                    nama: cells[row.rowIndex - 1].nama_lengkap,
                    email: cells[row.rowIndex - 1].email,
                    status: cells[row.rowIndex - 1].status,
                    buktiPembayaran: cells[row.rowIndex - 1].bukti_pembayaran_dp,
                    kelas: formattedKelas,
                    noHp: cells[row.rowIndex - 1].no_hp,
                    alamat: cells[row.rowIndex - 1].alamat,
                    jenisKelamin: cells[row.rowIndex - 1].jenis_kelamin,
                    mengetahuiProgramDari: cells[row.rowIndex - 1].mengetahui_program_dari,
                    pendidikanTerakhir: cells[row.rowIndex - 1].pendidikan_terakhir,
                    status: cells[row.rowIndex - 1].status,
                    totalTagihan: cells[row.rowIndex - 1].total_tagihan ?
                        'Rp ' + Number(cells[row.rowIndex - 1].total_tagihan).toLocaleString('id-ID') : '-',
                    batch: cells[row.rowIndex - 1].batches.nama ?? '-',
                    jumlahCicilan: cells[row.rowIndex - 1].jumlah_cicilan ?? '-',
                };
            }
        </script>
    </div>

    <script>
        console.log('data anjay', @json($peserta));
    </script>
@endsection
