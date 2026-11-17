
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KasirKu')</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f5f4;
            color: #17231d;
        }

        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background: #062719;
            padding: 25px 18px;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #a3e635;
            margin: 15px 10px 45px;
        }

        .logo span {
            color: white;
        }

        .menu-title {
            color: #80968b;
            font-size: 12px;
            margin: 20px 12px 12px;
            font-weight: bold;
        }

        .sidebar a {
            color: #b0c0b6;
            text-decoration: none;
            display: block;
            padding: 15px;
            margin-bottom: 7px;
            border-radius: 12px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #143629;
            color: #a3e635;
        }

        .profile {
            margin-top: auto;
            padding: 15px;
            background: #102e21;
            border-radius: 15px;
        }

        .profile small {
            color: #9caf9f;
        }

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            min-height: 85px;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid #e5ebe7;
        }

        .search {
            flex: 1;
            max-width: 550px;
        }

        .search input {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #e1e9e4;
            border-radius: 30px;
        }

        .content {
            padding: 30px;
        }

        .page-heading {
            margin-bottom: 28px;
        }

        .page-heading h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #829188;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 4px 18px #00000005;
        }

        .card.dark {
            background: #0b3525;
            color: white;
        }

        .card-label {
            color: #829188;
            font-size: 14px;
        }

        .dark .card-label {
            color: #c1d3c7;
        }

        .card h2 {
            margin: 17px 0 10px;
            font-size: 26px;
        }

        .accent {
            color: #a3e635;
        }

        .section-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 22px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .table-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #edf0ee;
        }

        th {
            color: #829188;
            font-size: 13px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            background: #0b3525;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn:hover {
            background: #145c3d;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce5df;
            border-radius: 9px;
        }

        @media (max-width: 1100px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 75px;
                padding: 15px 7px;
            }

            .logo {
                font-size: 20px;
                margin: 10px 5px 30px;
            }

            .logo span,
            .menu-title,
            .nav-text,
            .profile {
                display: none;
            }

            .sidebar a {
                padding: 15px 5px;
                text-align: center;
                font-size: 18px;
            }

            .main {
                margin-left: 75px;
            }

            .topbar {
                padding: 15px;
                flex-wrap: wrap;
            }

            .content {
                padding: 18px 12px;
            }

            .cards,
            .section-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo">✳ <span>KasirKu</span></div>

        <div class="menu-title">MENU UTAMA</div>

        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            ▦ <span class="nav-text">Dashboard</span>
        </a>

        <a href="{{ route('kasir.index') }}"
           class="{{ request()->routeIs('kasir.*') ? 'active' : '' }}">
            ♙ <span class="nav-text">Data Kasir</span>
        </a>

        <a href="{{ route('menu.index') }}"
           class="{{ request()->routeIs('menu.*') ? 'active' : '' }}">
            ☕ <span class="nav-text">Menu Minuman</span>
        </a>

        <div class="menu-title">TRANSAKSI</div>

        <a href="{{ route('transaksi.index') }}"
           class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
            ▤ <span class="nav-text">Penjualan</span>
        </a>

        <a href="{{ route('laporan.index') }}"
           class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
            ▥ <span class="nav-text">Laporan</span>
        </a>

        <div class="profile">
            <strong>Administrator</strong>
            <small>admin@email.com</small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <strong>Panel Admin</strong>

            <div class="search">
                <input type="search"
                       placeholder="Cari apa saja di KasirKu...">
            </div>

            <strong>🔔 Administrator</strong>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
