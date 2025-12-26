<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') GigaGears</title>
    {{-- Memanggil Font dari Figma --}}
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* ======================================= */
        /* LAYOUT UTAMA (FIXED STABILITAS & LEBAR) */
        /* ======================================= */
        body {
            font-family: 'Chakra Petch', sans-serif;
            background: #FFFFFF;
            margin: 0;
            padding: 0;
            width: 100%; 
        }
        /* Container untuk centering: Lebar Maksimum 1280px */
        .page-container {
            width: 1280px; 
            max-width: 90%; /* Konten di tengah dan responsif */
            margin: 0 auto; 
        }
    </style>
</head>
<body>
    
    {{-- SLOT HEADER (Navbar & Hero Section) --}}
    <header>
        @yield('header')
    </header>

    {{-- KONTEN UTAMA MASUK DI SINI --}}
    <main class="page-container">

        @yield('content') 
    </main>

    {{-- SLOT FOOTER --}}
    <footer>
        @yield('footer')
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('footer-script')
</body>
</html>