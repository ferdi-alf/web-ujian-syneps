@extends('layouts.dashboard-layouts')

@section('content')
    <div class="flex flex-col gap-y-10">
        <div class="flex flex-col p-8 bg-white rounded-xl gap-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-dark-blue">{{ $lowongan->posisi }}</h1>
                    <p class="text-gray-600">{{ $lowongan->perusahaan }} - {{ $lowongan->lokasi }}</p>
                </div>
                <a href="#" class="px-6 py-3 font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    Lamar Sekarang
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-bold text-gray-500">Tipe Pekerjaan</h4>
                    <p class="text-lg font-semibold">{{ $lowongan->tipe }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-bold text-gray-500">Gaji</h4>
                    <p class="text-lg font-semibold">Rp {{ number_format($lowongan->gaji, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-bold text-gray-500">Batas Waktu</h4>
                    <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($lowongan->deadline)->format('d F Y') }}</p>
                </div>
            </div>

            <div>
                <h3 class="mb-4 text-2xl font-bold text-dark-blue">Deskripsi Pekerjaan</h3>
                <div class="prose max-w-none">
                    {!! nl2br(e($lowongan->deskripsi)) !!}
                </div>
            </div>

            <div>
                <h3 class="mb-4 text-2xl font-bold text-dark-blue">Persyaratan</h3>
                <div class="prose max-w-none">
                    {!! nl2br(e($lowongan->persyaratan)) !!}
                </div>
            </div>

            <div class="pt-8 mt-8 border-t">
                <a href="{{ route('lowongan.index') }}" class="text-blue-500 hover:underline"> &larr; Kembali ke Daftar Lowongan</a>
            </div>
        </div>
    </div>
@endsection
