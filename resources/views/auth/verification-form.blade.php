@extends('layouts.auth-layouts')

@section('title', 'Verifikasi Email')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Verifikasi Email
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Kami telah mengirim kode verifikasi 6 digit ke email <strong>{{ $user->email }}</strong>
                </p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('verification.process', $user->id) }}" method="POST">
                @csrf

                <div>
                    <label for="verification_code" class="block text-sm font-medium text-gray-700">Kode Verifikasi</label>
                    <input id="verification_code" name="verification_code" type="text" maxlength="6" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-center text-2xl tracking-widest focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('verification_code') border-red-500 @enderror"
                        placeholder="000000">
                    @error('verification_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Verifikasi Email
                    </button>
                </div>

                <div class="text-center">
                    <form action="{{ route('verification.resend', $user->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-indigo-600 hover:text-indigo-500 text-sm">
                            Kirim ulang kode verifikasi
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
@endsection
