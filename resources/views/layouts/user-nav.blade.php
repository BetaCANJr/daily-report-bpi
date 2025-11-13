<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Daily Report LPK BPI')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bpi-blue: #1e3a8a;
            --bpi-dark-blue: #1e40af;
            --bpi-gold: #d97706;
            --bpi-light: #f8fafc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-nav {
            padding: 15px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .navbar-bpi {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .bg-primary-light {
            background: rgba(30, 58, 138, 0.1);
        }
        
        .bg-success-light {
            background: rgba(21, 128, 61, 0.1);
        }
        
        .bg-warning-light {
            background: rgba(217, 119, 6, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--bpi-blue);
        }
        
        .btn-bpi {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-bpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.4);
            color: white;
        }
        
        .recent-reports {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .report-item {
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
            transition: background 0.3s;
        }
        
        .report-item:hover {
            background: #f8fafc;
        }
        
        .report-item:last-child {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-progress {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                margin-left: -100%;
                z-index: 1000;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo-bpi.png') }}" 
                     alt="LPK BPI" 
                     style="max-width: 40px; height: auto;" 
                     class="me-2">
                <div>
                    <h5 class="mb-0 fw-bold">LPK BPI</h5>
                    <small class="text-white-50">Daily Report</small>
                </div>
            </div>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.create') }}">
                        <i class="fas fa-plus-circle"></i>
                        Buat Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="fas fa-list"></i>
                        Semua Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user"></i>
                        Profil Saya
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                <li class="nav-item mt-3">
                    <small class="text-white-50 px-3">ADMIN</small>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-cog"></i>
                        Admin Dashboard
                    </a>
                </li>
                @endif
                <li class="nav-item mt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="nav-link" href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar-bpi rounded-3 mb-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">@yield('page-title', 'Dashboard')</h4>
                        <small class="text-muted">@yield('page-subtitle', 'Selamat datang di sistem daily report')</small>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-end">
                            <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->jabatan }}</small>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('logout') }}" 
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('button');
            menuToggle.className = 'btn btn-primary menu-toggle d-md-none';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.position = 'fixed';
            menuToggle.style.top = '15px';
            menuToggle.style.left = '15px';
            menuToggle.style.zIndex = '1001';
            
            menuToggle.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
            
            document.body.appendChild(menuToggle);
        });
    </script>
    
    @stack('scripts')
</body>
</html>