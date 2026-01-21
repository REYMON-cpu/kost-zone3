<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kost Zone - @yield('title', 'Cari Kost Terbaik')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }
        
        .logo-img {
            height: 40px;
            margin-right: 10px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #357abd;
        }
        
        .kost-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .kost-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .kost-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .badge-type {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        
        .price-tag {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }
        
        .filter-bar {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .banner-container {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            margin-bottom: 30px;
            border-radius: 12px;
        }
        
        .banner-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .banner-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .carousel-item img {
            height: 400px;
            object-fit: cover;
        }
        
        .facility-item {
            display: inline-block;
            background-color: #e9ecef;
            padding: 5px 12px;
            border-radius: 20px;
            margin: 3px;
            font-size: 0.9rem;
        }
        
        .contact-btn {
            margin: 5px;
        }
        
        .owner-actions {
            margin-bottom: 20px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img" onerror="this.style.display='none'">
                Kost Zone
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#ketentuanModal">
                            <i class="bi bi-info-circle"></i> Ketentuan
                        </a>
                    </li>
                    @auth('owner')
                        <li class="nav-item">
                            <span class="nav-link">Hai, {{ auth()->guard('owner')->user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm ms-2">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-person-circle"></i> Login Owner
                            </button>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container mt-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="bi bi-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @auth('owner')
                @if(!auth()->guard('owner')->user()->hasVerifiedEmail())
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-envelope-exclamation"></i>
                    <strong>Email belum diverifikasi!</strong> 
                    Silakan cek inbox email Anda untuk verifikasi.
                    <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ auth()->guard('owner')->user()->email }}">
                        <button type="submit" class="btn btn-sm btn-warning ms-2">
                            <i class="bi bi-send"></i> Kirim Ulang Email
                        </button>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
            @endauth
        </div>
        
        @yield('content')
    </main>

    <!-- Modal Ketentuan -->
    <div class="modal fade" id="ketentuanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ketentuan Penggunaan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Untuk Pencari Kost:</h6>
                    <ul>
                        <li>Pastikan mengunjungi lokasi kost sebelum memutuskan untuk menyewa</li>
                        <li>Verifikasi informasi yang tertera dengan pemilik kost</li>
                        <li>Baca dan pahami aturan kost dengan baik</li>
                        <li>Hubungi pemilik kost melalui kontak yang tertera</li>
                    </ul>
                    <h6>Untuk Pemilik Kost:</h6>
                    <ul>
                        <li>Pastikan informasi yang diberikan akurat dan terkini</li>
                        <li>Foto yang diunggah harus sesuai dengan kondisi sebenarnya</li>
                        <li>Responsif terhadap calon penyewa yang menghubungi</li>
                        <li>Update ketersediaan kamar secara berkala</li>
                    </ul>
                    <p class="text-muted mt-3">
                        <small>Kost Zone hanya sebagai platform pencarian. Segala transaksi dan perjanjian dilakukan langsung antara pencari kost dan pemilik kost.</small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Login/Register -->
    @guest('owner')
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login / Daftar Pemilik Kost</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="authTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">Daftar</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="authTabContent">
                        <!-- Login Form -->
                        <div class="tab-pane fade show active" id="login">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                        <!-- Register Form -->
                        <div class="tab-pane fade" id="register">
                            <form action="{{ route('register') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Daftar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endguest

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    
    @if($errors->any())
    <script>
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
        @if($errors->has('name') || $errors->has('password_confirmation'))
            document.getElementById('register-tab').click();
        @endif
    </script>
    @endif
</body>
</html>