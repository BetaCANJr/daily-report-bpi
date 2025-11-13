<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Daily Report LPK BPI')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .admin-sidebar {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 280px;
            transition: all 0.3s;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .admin-sidebar-brand {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar-nav {
            padding: 20px 0;
        }
        
        .admin-nav-item {
            margin-bottom: 8px;
        }
        
        .admin-nav-link {
            color: rgba(255,255,255,0.8);
            padding: 14px 25px;
            border-radius: 0;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .admin-nav-link:hover, .admin-nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--bpi-gold);
        }
        
        .admin-nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .admin-main-content {
            margin-left: 280px;
            padding: 0;
            transition: all 0.3s;
        }
        
        .admin-topbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 18px 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .admin-content {
            padding: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
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
        
        .bg-info-light {
            background: rgba(6, 182, 212, 0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--bpi-blue);
            line-height: 1;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: 100%;
        }
        
        .activity-item {
            border-left: 3px solid var(--bpi-blue);
            padding: 15px 20px;
            margin-bottom: 15px;
            background: white;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.4);
            color: white;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                margin-left: -100%;
                z-index: 1000;
            }
            
            .admin-sidebar.active {
                margin-left: 0;
            }
            
            .admin-main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo-bpi.png') }}" 
                     alt="LPK BPI" 
                     style="max-width: 45px; height: auto;" 
                     class="me-3">
                <div>
                    <h5 class="mb-0 fw-bold">LPK BPI</h5>
                    <small class="text-white-50">Admin Panel</small>
                </div>
            </div>
        </div>
        
        <div class="admin-sidebar-nav">
            <div class="admin-nav-item">
                <a class="admin-nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </div>
            
            <div class="admin-nav-item">
                <a class="admin-nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" 
                   href="{{ route('admin.users') }}">
                    <i class="fas fa-users"></i>
                    User Management
                </a>
            </div>
            
            <div class="admin-nav-item">
                <a class="admin-nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" 
                   href="{{ route('admin.reports') }}">
                    <i class="fas fa-file-alt"></i>
                    Reports Management
                </a>
            </div>
            
            <div class="admin-nav-item mt-4">
                <a class="admin-nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-arrow-left"></i>
                    Back to User View
                </a>
            </div>
            
            <div class="admin-nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="admin-nav-link" href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-main-content">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-primary">@yield('page-title', 'Admin Dashboard')</h4>
                    <small class="text-muted">@yield('page-subtitle', 'System Overview & Analytics')</small>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="me-3 text-end">
                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                        <small class="text-muted">Administrator</small>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 45px; height: 45px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-user me-2"></i>User Dashboard</a></li>
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

        <!-- Page Content -->
        <div class="admin-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile menu toggle for admin
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('button');
            menuToggle.className = 'btn btn-primary menu-toggle d-md-none';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.position = 'fixed';
            menuToggle.style.top = '20px';
            menuToggle.style.left = '20px';
            menuToggle.style.zIndex = '1001';
            
            menuToggle.addEventListener('click', function() {
                document.querySelector('.admin-sidebar').classList.toggle('active');
            });
            
            document.body.appendChild(menuToggle);
        });
    </script>
    
    @stack('scripts')
</body>
</html>