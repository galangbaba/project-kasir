<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Aplikasi Kasir')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        nav {
            background-color: #198754;
            padding: 18px 25px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }

        nav a:hover {
            color: #d1e7dd;
        }

        main {
            padding: 25px;
        }

        h1 {
            color: #198754;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #198754;
            color: white;
        }
    </style>
</head>

<body>

    <nav>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('kasir.kasir') }}">Kasir</a>
        <a href="{{ route('menu.menu') }}">Menu Minuman</a>
        <a href="{{ route('transaksi.transaksi') }}">Transaksi</a>
    </nav>

    <main>
        @yield('content')
    </main>

</body>
</html>
