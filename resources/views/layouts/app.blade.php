<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'i remember all the places')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@auth
<header class="site-navbar">
    <div class="navbar-inner">
        <a href="{{ route('dashboard') }}" class="site-logo">
            <span class="logo-main">i remember</span>
            <span class="logo-sub">all the places.</span>
        </a>
        <div class="navbar-right">
            <span class="navbar-user">{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout-button" type="submit">Logout</button>
            </form>
        </div>
    </div>
</header>
@endauth
<main class="site-main">
    @if(session('success'))
        <div class="alert success-alert"><span class="alert-icon">✓</span><span>{{ session('success') }}</span></div>
    @endif
    @if($errors->any())
        <div class="alert validation-alert">
            <div class="alert-title">Ada yang perlu diperbaiki.</div>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
