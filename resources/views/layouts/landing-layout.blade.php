<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Syneps Academy - {{ $title ?? 'Home' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @stack('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <style>
        /* Reset untuk memastikan tidak ada konflik dengan layout dashboard */
        body {
            margin: 0;
            padding: 0;
            background: #FDFDFC !important;
        }
        
        /* Pastikan tidak ada sidebar yang muncul */
        #logo-sidebar, .sm\:ml-64 {
            display: none !important;
        }
        
        /* Reset main content */
        main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
    </style>
</head>

<body class="bg-[#FDFDFC] text-gray-800 antialiased">
    @if(empty($hideNavFooter))
        <x-landing.navbar />
    @endif

    <main>
        @if(isset($content))
            {!! $content !!}
        @else
            @yield('content')
        @endif
    </main>

    @if(empty($hideNavFooter))
        <x-landing.footer />
    @endif

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

    @stack('scripts')
</body>

</html>