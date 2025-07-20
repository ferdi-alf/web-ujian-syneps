<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Website Ujian - Syneps Academy</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
</head>

<body class="bg-[#FDFDFC] ">
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Welcome Back</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <div class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 flex justify-center items-center p-4">
            <div class="w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[600px]">
                    <div class="p-8 lg:p-12 flex flex-col justify-center">

                        <div class="w-full max-w-md mx-auto">
                            <div class="max-w-md w-full flex justify-start items-center space-x-1.5  ">
                                <img src="/images/logo.png" alt="syneps-logo" class="w-14 h-14">
                                <h1
                                    class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-br from-teal-300 via-emerald-300 to-emerald-400">
                                    Syneps Academy</h1>
                            </div>
                            <p class="text-gray-600 mb-8">
                                Login ke Akunmu
                            </p>

                            <form class="space-y-6" method="POST" action="{{ route('store.login') }}">
                                @csrf

                                <x-fragments.text-field id="nameOrEmail" name="nameOrEmail" type="text"
                                    label="Name atau Email" placeholder="Masukan Name atau Email" :value="old('nameOrEmail')"
                                    :error="$errors->first('nameOrEmail')" required />

                                <x-fragments.text-field id="password" name="password" type="password" label="Password"
                                    placeholder="Masukan Password" :value="old('password')" :error="$errors->first('password')" required />


                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input id="remember-me" name="remember" type="checkbox"
                                            class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                            {{ old('remember') ? 'checked' : '' }} />
                                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                            Remember me
                                        </label>
                                    </div>
                                </div>

                                <x-fragments.gradient-button type="submit">
                                    Sign In
                                </x-fragments.gradient-button>
                            </form>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br md:flex hidden from-emerald-200 via-teal-300  to-emerald-400 p-8 lg:p-12 items-center justify-center">
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('images/login-removebg-preview.png') }}" alt="Login Illustration"
                                class="h-96 w-96 object-contain" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            @if (session('alert'))
                @php $alert = session('alert'); @endphp

                Swal.fire({
                    icon: '{{ $alert['type'] }}',
                    title: '{{ $alert['title'] }}',
                    text: '{{ $alert['message'] }}',
                    confirmButtonColor: '#991b1b',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if ($errors->any())
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '• {{ $error }}\n';
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessages,
                    confirmButtonColor: '#991b1b'
                });
            @endif
        </script>
    </body>

    </html>
