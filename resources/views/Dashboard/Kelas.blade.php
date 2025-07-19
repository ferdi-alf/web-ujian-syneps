@extends('layouts.dashboard-layouts')

@section('content')
    <div>
        <div class="flex justify-end">
            <x-fragments.modal-button target="add-jurusan-modal" variant="emerald">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kelas
            </x-fragments.modal-button>
        </div>
        <x-modal-layout id="add-jurusan-modal" title="Tambah Kelas Baru" size="lg" :show="$errors->any()">
            <form action="{{ route('kelas.store') }}" class="space-y-4 p-2" method="POST">
                @csrf
                <x-fragments.text-field label="Nama Kelas" name="name" placeholder="Masukan Kelas..." required />
                <div class="flex justify-end">

                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                        <i class="fa-solid fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </x-modal-layout>
    </div>
@endsection
