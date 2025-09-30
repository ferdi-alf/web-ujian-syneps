@extends('layouts.auth-layouts')

@section('title', 'Buat Akun')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 flex justify-center items-center p-4">
        <div class="max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="">
                <div class="p-8 flex flex-col justify-center">
                    <div class="w-full mx-auto">
                        <div class="w-full flex justify-start items-center space-x-1.5">
                            <img src="/images/logo.png" alt="syneps-logo" class="w-14 h-14">
                            <h1
                                class="font-extrabold sm:text-3xl text-2xl text-transparent bg-clip-text bg-gradient-to-br from-teal-300 via-emerald-300 to-emerald-400">
                                Syneps Academy</h1>
                        </div>
                        <p class="text-gray-600 mb-8">
                            Buat Akun Anda untuk melanjutkan ke kelas
                            <strong>{{ $peserta->kelas->nama ?? 'Program' }}</strong>
                        </p>

                        <form class="space-y-4" method="POST" action="{{ route('registration.process', $token) }}">
                            @csrf

                            <div>
                                <x-fragments.text-field id="name" name="name" type="text" label="Name"
                                    placeholder="Masukan Name" :value="old('name')" :error="$errors->first('nameOrEmail')" required />
                                <small>name digunakan untuk login selain email</small>
                            </div>

                            <div>
                                <x-fragments.text-field id="password" name="password" type="password" label="Password"
                                    placeholder="Masukan Password" :value="old('password')" :error="$errors->first('password')" required />

                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center space-x-2" id="length-rule">
                                        <svg class="w-4 h-4 text-gray-400 transition-colors duration-200" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Minimal 6 karakter</span>
                                    </div>
                                    <div class="flex items-center space-x-2" id="uppercase-rule">
                                        <svg class="w-4 h-4 text-gray-400 transition-colors duration-200" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Mengandung huruf kapital</span>
                                    </div>
                                    <div class="flex items-center space-x-2" id="number-rule">
                                        <svg class="w-4 h-4 text-gray-400 transition-colors duration-200" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Mengandung angka</span>
                                    </div>
                                </div>
                            </div>

                            <x-fragments.text-field id="confirmPassword" name="confirmPassword" type="password"
                                label="Konfirmasi Password" placeholder="Masukan Konfirmasi Password" :error="$errors->first('confirmPassword')"
                                required />

                            <div class="mt-2" id="password-match" style="display: none;">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400 transition-colors duration-200" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">Password cocok</span>
                                </div>
                            </div>

                            <x-fragments.gradient-button type="submit" id="submit-btn" disabled>
                                Sign Up
                            </x-fragments.gradient-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const submitBtn = document.getElementById('submit-btn');

            const lengthRule = document.getElementById('length-rule');
            const uppercaseRule = document.getElementById('uppercase-rule');
            const numberRule = document.getElementById('number-rule');
            const passwordMatch = document.getElementById('password-match');

            const checkIcon = `<svg class="w-4 h-4 text-green-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>`;

            const crossIcon = `<svg class="w-4 h-4 text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>`;

            const grayIcon = `<svg class="w-4 h-4 text-gray-400 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>`;

            function updateRule(element, isValid) {
                const icon = element.querySelector('svg');
                const text = element.querySelector('span');

                if (isValid) {
                    element.innerHTML = checkIcon + text.outerHTML;
                    text.classList.remove('text-gray-600', 'text-red-600');
                    text.classList.add('text-green-600');
                } else if (passwordInput.value.length > 0) {
                    element.innerHTML = crossIcon + text.outerHTML;
                    text.classList.remove('text-gray-600', 'text-green-600');
                    text.classList.add('text-red-600');
                } else {
                    element.innerHTML = grayIcon + text.outerHTML;
                    text.classList.remove('text-green-600', 'text-red-600');
                    text.classList.add('text-gray-600');
                }
            }

            function validatePassword() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                const hasMinLength = password.length >= 6;
                const hasUppercase = /[A-Z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const passwordsMatch = password === confirmPassword && confirmPassword.length > 0;

                updateRule(lengthRule, hasMinLength);
                updateRule(uppercaseRule, hasUppercase);
                updateRule(numberRule, hasNumber);

                if (confirmPassword.length > 0) {
                    passwordMatch.style.display = 'block';
                    updateRule(passwordMatch, passwordsMatch);
                } else {
                    passwordMatch.style.display = 'none';
                }

                const allValid = hasMinLength && hasUppercase && hasNumber && passwordsMatch;
                submitBtn.disabled = !allValid;

                if (allValid) {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePassword);

            validatePassword();
        });
    </script>

    <style>
        #submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endsection
