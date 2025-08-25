@extends('layouts.auth-layouts')

@section('title', 'Verifikasi Email')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-8 bg-white rounded-lg shadow-md p-6">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Verifikasi Email
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Kami telah mengirim kode verifikasi 6 digit ke email <strong>{{ $user->email }}</strong>
                </p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('verification.process', $token) }}" method="POST"
                id="verificationForm">
                @csrf
                <div>
                    <input type="hidden" name="verification_code" id="fullCode">
                    <div class="flex justify-center space-x-3">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text"
                                class="otp-input md:w-12 md:h-12 w-10 h-10 text-center text-xl font-bold border-2 border-teal-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                maxlength="1" data-index="{{ $i }}">
                        @endfor
                    </div>

                    @error('verification_code')
                        <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-center">
                </div>
            </form>
            <form action="{{ route('verification.resend', $token) }}" method="POST" class="inline">
                @csrf
                <x-fragments.gradient-button type="submit" id="resend-btn" :disabled="$remainingCooldown > 0">
                    <span id="resend-text">
                        @if ($remainingCooldown > 0)
                            Kirim ulang dalam <span id="countdown">{{ $remainingCooldown }}</span> detik
                        @else
                            Kirim ulang kode verifikasi
                        @endif
                    </span>
                </x-fragments.gradient-button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const fullCodeInput = document.getElementById('fullCode');
            const form = document.getElementById('verificationForm');
            const resendBtn = document.getElementById('resend-btn');
            const resendText = document.getElementById('resend-text');
            const countdownEl = document.getElementById('countdown');
            let remainingTime = {{ $remainingCooldown }};

            if (remainingTime > 0) {
                const timer = setInterval(() => {
                    remainingTime--;
                    countdownEl.textContent = remainingTime;

                    if (remainingTime <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        resendText.innerHTML = 'Kirim ulang kode verifikasi';
                    }
                }, 1000);
            }

            otpInputs[0].focus();

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;

                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    updateFullCode();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowLeft' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text');
                    const digits = pastedData.replace(/\D/g, '');

                    if (digits.length >= 6) {
                        for (let i = 0; i < 6; i++) {
                            otpInputs[i].value = digits[i] || '';
                        }
                        updateFullCode();
                        otpInputs[5].focus();
                    }
                });
            });

            function updateFullCode() {
                let code = '';
                otpInputs.forEach(input => {
                    code += input.value;
                });

                fullCodeInput.value = code;

                if (code.length === 6) {
                    // Add loading state
                    const submitBtn = document.querySelector('[type="submit"]:not(#resend-btn)');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Memverifikasi...';
                    }

                    setTimeout(() => {
                        form.submit();
                    }, 500);
                }
            }

            @if ($errors->has('verification_code'))
                otpInputs.forEach(input => {
                    input.classList.add('border-red-500');
                });

                setTimeout(() => {
                    otpInputs.forEach(input => {
                        input.classList.remove('border-red-500');
                        input.value = '';
                    });
                    fullCodeInput.value = '';
                    otpInputs[0].focus();
                }, 2000);
            @endif
        });
    </script>
@endsection
