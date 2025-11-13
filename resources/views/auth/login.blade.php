<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daily Report LPK BPI</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-sidebar {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            padding: 40px 30px;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .login-sidebar::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .bpi-logo {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-form {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--bpi-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.25);
        }
        
        .btn-bpi {
            background: linear-gradient(135deg, var(--bpi-blue), var(--bpi-dark-blue));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-bpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.4);
            color: white;
        }
        
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }
        
        .register-link {
            color: var(--bpi-blue);
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link:hover {
            color: var(--bpi-dark-blue);
            text-decoration: underline;
        }
        
        .form-check-input:checked {
            background-color: var(--bpi-blue);
            border-color: var(--bpi-blue);
        }
        
        @media (max-width: 768px) {
            .login-sidebar {
                padding: 30px 20px;
            }
            
            .login-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container">
                    <div class="row g-0">
                        <!-- Sidebar -->
                        <div class="col-lg-5">
                            <div class="login-sidebar position-relative">
                                <div class="position-relative z-1">
                                    <div class="text-center mb-5">
                                        <img src="{{ asset('images/logo-bpi.png') }}" 
                                             alt="LPK BPI Yogyakarta" 
                                             class="mb-3"
                                             style="max-width: 120px; height: auto;">
                                    </div>
                                    
                                    <h3 class="fw-bold mb-3">Selamat Datang Kembali</h3>
                                    <p class="mb-4">
                                        Login ke akun Anda untuk mengakses sistem daily report dan melanjutkan pekerjaan.
                                    </p>
                                    
                                    <div class="feature-list">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-rocket me-3 text-warning"></i>
                                            <span>Akses dashboard personal</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-chart-line me-3 text-warning"></i>
                                            <span>Lihat statistik real-time</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-file-upload me-3 text-warning"></i>
                                            <span>Input laporan harian</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-users me-3 text-warning"></i>
                                            <span>Kelola tim Anda</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form -->
                        <div class="col-lg-7">
                            <div class="login-form">
                                <h2 class="fw-bold mb-1">Login ke Akun</h2>
                                <p class="text-muted mb-4">Masukkan kredensial Anda untuk melanjutkan</p>
                                
                                <!-- Session Status -->
                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('status') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               required 
                                               autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3 position-relative">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        <span class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Ingat saya
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-bpi">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login
                                        </button>
                                    </div>
                                    
                                    <div class="text-center">
                                        <p class="mb-0">
                                            Belum punya akun? 
                                            <a href="{{ route('register') }}" class="register-link">Daftar di sini</a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.parentNode.querySelector('.password-toggle i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>