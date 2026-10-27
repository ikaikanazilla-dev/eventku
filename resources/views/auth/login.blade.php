@extends('layouts.app')

@section('content')
    <div class="auth-shell">
        <div class="auth-card">
            <span class="eyebrow">AKSES EVENTKU</span>
            <h1>Selamat datang kembali.</h1>
            <p>Masuk untuk mengakses tiket digital atau mengelola eventmu.</p>
            <form method="POST" action="{{ route('login.store') }}" data-submit-once>
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                    @error('email')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" class="input" type="password" name="password" autocomplete="current-password" required>
                    @error('password')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <label class="muted" for="remember"><input id="remember" type="checkbox" name="remember"> Ingat saya di perangkat ini</label>
                <button class="btn btn-primary" type="submit">Masuk ke Eventku <span aria-hidden="true">&rarr;</span></button>
            </form>
            <p>Belum punya akun peserta? <a href="{{ route('register') }}">Daftar gratis</a></p>
        </div>
    </div>
@endsection
