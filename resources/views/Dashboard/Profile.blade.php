@extends('layouts.dashboard-layouts')

@section('content')
    <div class=" px-4 py-6">
        <div class="">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Profile Settings</h2>
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ $errors->first('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-fragments.text-field label="Name" name="name" placeholder="Masukkan name untuk login"
                            value="{{ old('name', auth()->user()->name) }}" required />
                    </div>

                    @if (in_array(auth()->user()->role, ['siswa', 'pengajar']))
                        <div class="mb-4">
                            <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap"
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('nama_lengkap', auth()->user()->nama_lengkap) }}" required />
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                            {{ ucfirst(auth()->user()->role) }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru (Kosongkan jika tidak ingin mengubah)
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Masukkan password baru">
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Konfirmasi password baru">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
