<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#132a6b">
    <title>@yield('title', 'KasirKu') · KasirKu</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/kasir/app.css') }}?v={{ filemtime(public_path('assets/kasir/app.css')) }}">
    <script src="{{ asset('assets/kasir/app.js') }}?v={{ filemtime(public_path('assets/kasir/app.js')) }}" defer></script>
</head>
<body class="@yield('body')">
@yield('page')
</body>
</html>
