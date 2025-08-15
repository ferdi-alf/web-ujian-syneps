<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Detail Kelas' }} - Syneps Academy</title>

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
    
    <style>
        /* Clean layout tanpa navbar/footer */
        body {
            margin: 0;
            padding: 0;
            background: #FDFDFC !important;
            font-family: 'Instrument Sans', sans-serif;
        }
        
        /* Pastikan tidak ada sidebar yang muncul */
        #logo-sidebar, .sm\:ml-64 {
            display: none !important;
        }
        
        /* Full width content */
        main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-height: 100vh;
        }

        /* Back button styling */
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-[#FDFDFC] text-gray-800 antialiased">
    {{-- Back Button --}}
    <a href="{{ route('index') }}" class="back-button">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali ke Beranda
    </a>

    <main>
        @if(isset($content))
            {!! $content !!}
        @else
            @yield('content')
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('alert'))
            @php $alert = session('alert'); @endphp
            Swal.fire({
                icon: '{{ $alert['type'] }}',
                title: '{{ $alert['title'] }}',
                text: '{{ $alert['message'] }}',
                confirmButtonColor: '#10b981',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#10b981',
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
                confirmButtonColor: '#ef4444'
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>