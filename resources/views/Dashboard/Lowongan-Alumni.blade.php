@extends('layouts.dashboard-layouts')

@section('content')
    <div class="flex flex-col gap-y-10">
        <h1 class="text-3xl font-bold text-dark-blue">Lowongan Pekerjaan</h1>

        <div class="flex flex-col p-8 bg-white rounded-xl gap-y-8">
            <h2 class="text-2xl font-bold text-dark-blue">Temukan peluang karir terbaik untuk Anda.</h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($lowongan as $item)
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{ $item->tipe }}</span>
                            <span class="text-sm text-gray-500">Deadline: {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-dark-blue">{{ $item->posisi }}</h3>
                        <p class="mb-1 text-gray-600">{{ $item->perusahaan }}</p>
                        <p class="mb-3 text-sm text-gray-500">{{ $item->lokasi }}</p>
                        <p class="mb-4 font-semibold text-green-600">Rp {{ number_format($item->gaji, 0, ',', '.') }}</p>
                        <a href="{{ route('lowongan.show', $item->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            Lihat Detail
                            <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full">
                        <p class="p-4 text-center text-gray-500">Saat ini tidak ada lowongan pekerjaan yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection