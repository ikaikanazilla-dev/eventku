<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Eventku &mdash; Event dan Tiket Digital' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="ek-nav">
        <div class="nav-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="Eventku, kembali ke beranda">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 7.5 6 5h12l2 2.5v10A1.5 1.5 0 0 1 18.5 19h-13A1.5 1.5 0 0 1 4 17.5v-10Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 5v4m8-4v4M7 13h10M7 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <span>EVENTKU</span>
            </a>

            <nav class="nav-main" aria-label="Navigasi utama">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">Jelajahi Event</a>
                <a class="nav-link" href="{{ route('home') }}#cara-kerja">Cara Mengikuti</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <a class="nav-action-link" href="{{ route(auth()->user()->role.'.dashboard') }}">Dashboard</a>
                    @if(auth()->user()->role === 'participant')
                        <a class="nav-action-link" href="{{ route('participant.tickets') }}">Tiket Saya</a>
                    @elseif(auth()->user()->role === 'organizer')
                        <a class="nav-action-link" href="{{ route('organizer.events') }}">Event Saya</a>
                    @elseif(auth()->user()->role === 'admin')
                        <a class="nav-action-link" href="{{ route('admin.events') }}">Moderasi</a>
                    @endif
                    <div class="profile-menu" data-profile-menu>
                        <button class="profile-trigger" type="button" data-profile-toggle aria-expanded="false" aria-haspopup="true">
                            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="profile-name">{{ Str::limit(auth()->user()->name, 16) }}</span>
                            <span class="chevron" aria-hidden="true">⌄</span>
                        </button>
                        <div class="profile-dropdown" data-profile-dropdown hidden>
                            <div class="profile-dropdown-head"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div>
                            <a href="{{ route('profile.edit') }}">Profil Saya</a>
                            <a href="{{ route(auth()->user()->role.'.dashboard') }}">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
                        </div>
                    </div>
                @else
                    <a class="nav-action-link" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-primary btn-sm nav-register" href="{{ route('register') }}">Daftar Gratis</a>
                @endauth
            </div>

            <button class="mobile-toggle" type="button" data-mobile-toggle aria-expanded="false" aria-controls="mobile-nav" aria-label="Buka menu navigasi">
                <span></span><span></span><span></span>
            </button>
        </div>

        <div class="mobile-panel" id="mobile-nav" data-mobile-panel hidden>
            <nav aria-label="Navigasi mobile">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">Jelajahi Event</a>
                <a class="nav-link" href="{{ route('home') }}#cara-kerja">Cara Mengikuti</a>
                @auth
                    <a class="mobile-cta" href="{{ route(auth()->user()->role.'.dashboard') }}">Dashboard</a>
                    @if(auth()->user()->role === 'participant')
                        <a class="mobile-cta" href="{{ route('participant.tickets') }}">Tiket Saya</a>
                    @elseif(auth()->user()->role === 'organizer')
                        <a class="mobile-cta" href="{{ route('organizer.events') }}">Event Saya</a>
                    @else
                        <a class="mobile-cta" href="{{ route('admin.events') }}">Moderasi</a>
                    @endif
                    <a class="nav-link" href="{{ route('profile.edit') }}">Profil Saya</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link" type="submit">Keluar</button></form>
                @else
                    <a class="mobile-cta" href="{{ route('login') }}">Masuk atau Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    @if(!session()->has('checkin_result') && session('success'))
        <div class="toast toast-success" role="status" aria-live="polite">{{ session('success') }}</div>
    @endif
    @if(!session()->has('checkin_result') && session('error'))
        <div class="toast toast-error" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="toast toast-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <main>@yield('content')</main>

    <footer class="footer">
        <div>
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true">✦</span> EVENTKU</a>
            <p>Temukan event, daftar dengan mudah, dan simpan tiket digitalmu dalam satu tempat.</p>
        </div>
        <div>
            <strong>Jelajahi</strong>
            <a href="{{ route('events.index') }}">Semua Event</a>
            <a href="{{ route('events.index') }}">Cari berdasarkan tanggal</a>
            <a href="{{ route('home') }}#cara-kerja">Cara Mengikuti</a>
        </div>
        <div>
            <strong>Ruang Kerja</strong>
            <a href="{{ route('register') }}">Daftar sebagai peserta</a>
            <a href="{{ route('login') }}">Kelola event</a>
            <a href="{{ route('login') }}">Akses check-in</a>
        </div>
        <small>&copy; {{ date('Y') }} Eventku. Platform event dan tiket digital.</small>
    </footer>
</body>
</html>
