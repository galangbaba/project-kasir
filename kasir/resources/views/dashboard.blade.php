<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi Kasir</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- CSS Vendor / Libs -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">

    <!-- CSS Utama Template -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
</head>
<body>

    <!-- Wrapper Utama -->
    <div class="wrapper">

        <!-- Sidebar (Opsional / Bagian Navigasi Kiri) -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-content">
                <a class="sidebar-brand" href="{{ route('dashboard') }}">
                    <span class="align-middle">Kasir App</span>
                </a>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">Menu Utama</li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> <span class="align-middle">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#">
                            <i class="bi bi-box-seam"></i> <span class="align-middle">Produk / Barang</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#">
                            <i class="bi bi-cart-check"></i> <span class="align-middle">Transaksi Penjualan</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#">
                            <i class="bi bi-file-earmark-text"></i> <span class="align-middle">Laporan</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Konten Utama -->
        <div class="main">
            <!-- Navbar Atas -->
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </a>
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img src="{{ asset('assets/images/avatar.png') }}" class="avatar img-fluid rounded me-1" alt="Admin Kasir" />
                                <span class="text-dark">Administrator</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Profil Saya</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Keluar</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Bagian Isi Halaman (Content) -->
            <main class="content">
                <div class="container-fluid p-0">

                    <!-- Judul Halaman -->
                    <div class="row mb-2">
                        <div class="col-12 col-xl-6">
                            <h2>Dashboard Kasir</h2>
                            <p class="text-muted">Selamat datang kembali di panel administrasi kasir Anda.</p>
                        </div>
                    </div>

                    <!-- Baris Statistik / Kartu Ringkasan -->
                    <div class="row">
                        <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Pendapatan Hari Ini</h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="bi bi-currency-dollar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">Rp 1.500.000</h1>
                                    <div class="mb-0">
                                        <span class="text-success"><i class="bi bi-arrow-up-right"></i> +3.5%</span>
                                        <span class="text-muted">Sejak kemarin</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Total Transaksi</h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="bi bi-cart-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">45</h1>
                                    <div class="mb-0">
                                        <span class="text-success"><i class="bi bi-arrow-up-right"></i> +12%</span>
                                        <span class="text-muted">Minggu ini</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Produk Terjual</h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">128</h1>
                                    <div class="mb-0">
                                        <span class="text-danger"><i class="bi bi-arrow-down-right"></i> -2.4%</span>
                                        <span class="text-muted">Dari kemarin</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Stok Menipis</h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat text-danger">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">3</h1>
                                    <div class="mb-0">
                                        <span class="text-muted">Perlu restock segera</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a href="#" class="text-muted"><strong>Aplikasi Kasir Laravel</strong></a> &copy; 2026
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JavaScript Vendor -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- JS Utama Template -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
</body>
</html>
