@extends('layouts.dashboard-layouts')
@section('content')
    <div class="bg-blue-100/80 font-semibold text-blue-500 border-blue-500 rounded-xl p-3 flex items-center gap-2">
        <i class="fa-solid fa-info"></i>
        <p>Data Pembayaran akan masuk disini</p>
    </div>
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
    <x-drawer-layout id="drawer-control-pembayaran" title="Detail Pembayaran" description="Preview dan informasi materi"
        type="bottomSheet">
        <div x-data="{
            pembayaranData: null,
            hasBukti: false,
        }"
            x-on:drawerDataLoaded.window="
                            if ($event.detail.drawerId === 'drawer-control-pembayaran') {
                                pembayaranData = $event.detail.data
                                idPembayaran = pembayaranData.id
                                hasBukti = pembayaranData && pembayaranData.bukti_pembayaran
                                console.log('Pembayaran data diterima:', pembayaranData)
                            }
                        "
            class="p-3">
            <template x-if="hasBukti">
                <div class="w-full h-full flex justify-center items-center flex-col ">
                    <img :src="pembayaranData.bukti_pembayaran" class="w-96 rounded-xl shadow-md" alt="Bukti Pembayaran">
                    <p class="text-xs text-gray-500 mt-1">Bukti sudah diupload</p>
                </div>
            </template>
        </div>
    </x-drawer-layout>
    <div class="relative mt-10 overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Bukti Pembayaran
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Nama
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Tagihan
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Pembayaran Ke
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr class="odd:bg-white even:bg-gray-50  border-b  border-gray-200">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            <img onclick="showImageModal('{{ asset($row->bukti_pembayaran) }}')"
                                class="w-24 hover:scale-105 hover:shadow- cursor-pointer" src="{{ $row->bukti_pembayaran }}"
                                alt="bukti pembayaran-{{ $row->butki_pembayaran }}">
                        </th>
                        <td class="px-6 py-4">
                            {{ $row->siswaDetail->nama_lengkap }}
                        </td>
                        <td class="px-6 py-4">
                            Rp {{ $row->jumlah_formatted }}
                        </td>
                        <td class="px-6 py-4 font-boldm t">
                            {{ $row->cicilan_ke }}
                        </td>
                        <td class="px-6 py-4">
                            <span @class([
                                'text-xs font-medium px-2.5 py-0.5 rounded-sm border',
                                'bg-yellow-100 text-yellow-800 border-yellow-400' =>
                                    $row['status'] === 'belum dibayar',
                                'bg-blue-100 text-blue-800 border-blue-400' => $row['status'] === 'pending',
                                'bg-green-100 text-green-800 border-green-400' =>
                                    $row['status'] === 'disetujui',
                                'bg-red-100 text-red-800 border-red-400' => $row['status'] === 'ditolak',
                            ])>
                                {{ ucfirst($row['status']) }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="pembayaran/{{ $row->id }}/approve" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="button" onclick="confirm(this)"
                                    class="inline-flex items-center p-3 text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <form method="POST" action="pembayaran/{{ $row->id }}/reject" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="button" onclick="reject(this)"
                                    class="inline-flex items-center p-3 text-xs font-medium text-red-600 bg-red-100 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors duration-200">
                                    <i class="fa-solid fa-x"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <script>
            console.log('dd', @json($data))

            function showImageModal(imageSrc) {
                document.getElementById('modalImage').src = imageSrc;
                document.getElementById('imageModal').classList.remove('hidden');
            }

            function hideImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
            }

            function confirm(button) {
                const form = button.closest('form');
                const title = button.getAttribute('data-title') || 'Hapus';
                const message = button.getAttribute('data-message') || 'Yakin ingin menerima data ini?';

                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, terimah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            function reject(button) {
                const form = button.closest('form');
                const title = button.getAttribute('data-title') || 'Hapus';
                const message = button.getAttribute('data-message') || 'Yakin ingin menolak data ini?';

                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    </div>
@endsection
