@extends('layouts.dashboard-layouts')

@section('content')
    <div class="bg-white rounded-lg px-5 py-5">
        <h3 class="text-lg text-end font-bold">Senin, 22 Juli 2025</h3>
        <div class="grid grid-cols-12 gap-3 mt-8">
            <div class="col-span-3">
                <span
                    class="bg-indigo-100 text-indigo-800 text-sm font-medium p-2 rounded-lg  border border-indigo-400">Fullstack
                    Web Developer</span>
                <h1 class="text-3xl truncate mt-8 font-bold">
                    Pengenalan Front End
                </h1>
                <h1 class="text-2xl truncate mt-2 font-bold">
                    Leo kannedy
                </h1>
                <div class="flex gap-3 mt-10 items-center">
                    <i class="fa-regular fa-user text-purple-500 text-2xl leading-none"></i>
                    <p class="font-medium text-lg">Melisa Tamara</p>
                </div>
                <div class="grid  pl-9 grid-cols-[auto_1fr] items-center mt-3">
                    <div></div>
                    <div class="border-t border-2 border-gray-500  w-24"></div>
                </div>
                <div class="grid grid-cols-[auto_1fr] gap-2 mt-3 items-center">
                    <i class="fa-regular fa-clipboard text-purple-400 text-2xl leading-none h-6 w-6 text-center"></i>
                    <p class="font-medium text-lg">25 Soal | 40 menit</p>
                </div>
                <button type="button"
                    class="text-white bg-gradient-to-r from-blue-500 to-blue-700 mt-8 cursor-pointer focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg w-36 text-sm py-3 px-5 shadow-md hover:!bg-gradient-to-r hover:!from-blue-600 hover:!to-blue-800 transition-all">
                    Mulai Ujian
                </button>
            </div>
            <div class="col-span-9 flex justify-center">
                <img class="w-96 h-96" src="{{ asset('images/background/bg-2.png') }}" alt="">
            </div>
        </div>
        <div class=" mt-10">
            <p class="font-bold">Ujian Lainnya</p>
            <div class="grid grid-cols-3 gap-3 mt-3">
                <div
                    class="rounded-lg text-white cursor-pointer bg-gradient-to-bl from-emerald-300 to-emerald-500 shadow-lg px-3 py-7 flex gap-3">
                    <div
                        class="w-16 h-16 font-bold rounded-lg bg-white/30 backdrop-blur-md  shadow-inner flex justify-center items-center">
                        PF
                    </div>
                    <div class="flex flex-col justify-around">
                        <h2 class="font-semibold text-xl">Pengenalan Front End</h2>
                        <p class="font-light">Senin, 22 Juli 2025</p>
                    </div>
                </div>
                <div
                    class="rounded-lg text-black cursor-pointer bg-gradient-to-bl border border-red-500 shadow-lg px-3 py-7 flex gap-3">
                    <div
                        class="w-16 h-16 font-bold rounded-lg bg-white/30 backdrop-blur-md  shadow-inner flex justify-center items-center">
                        HD
                    </div>
                    <div class="flex flex-col justify-around">
                        <h2 class="font-semibold text-xl">HTML Dasar</h2>
                        <p class="font-light">Senin, 29 Juli 2025</p>
                    </div>
                </div>
                <div
                    class="rounded-lg text-black cursor-pointer bg-gradient-to-bl border border-amber-500 shadow-lg px-3 py-7 flex gap-3">
                    <div
                        class="w-16 h-16 font-bold rounded-lg bg-white/30 backdrop-blur-md  shadow-inner flex justify-center items-center">
                        TH
                    </div>
                    <div class="flex flex-col justify-around">
                        <h2 class="font-semibold text-xl">Tag HTML</h2>
                        <p class="font-light">Miggu, 4 Agustus 2025</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
