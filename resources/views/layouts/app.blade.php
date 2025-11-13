<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Daily Report LPK BPI')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --bpi-blue: #1e3a8a;
            --bpi-dark-blue: #1e40af;
            --bpi-gold: #d97706;
        }
        
        .sidebar {
            background: var(--bpi-blue);
            color: white;
            min-height: 100vh;
        }
        
        .navbar-bpi {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
        }
        
        .btn-bpi {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            border: none;
        }
        
        .btn-bpi:hover {
            background: var(--bpi-dark-blue);
            color: white;
        }
    </style>
</head>
<body>
    @if(request()->is('admin/*'))
        @include('layouts.admin-nav')
    @else
        @include('layouts.user-nav')
    @endif

    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>