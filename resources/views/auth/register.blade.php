@extends('layouts.app')

@section('content')
    <div class="auth-shell">
        <div class="auth-card">
            <span class="eyebrow">BUAT AKUN PESERTA</span>
            <h1>Mulai pengalaman event.</h1>
            <p>Buat akun untuk mendaftar event dan menyimpan tiket digitalmu.</p>
            <form method="POST" action="{{ route('register.store') }}" data-submit-once>
                @csrf
                <div class="form-group">
                    <label for="name">Nama lengkap</label>
                    <input id="name" class="input" name="name" value="{{ old('name') }}" autocomplete="name" required>
                    @error('name')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                    @error('email')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="phone">Nomor telepon <span class="muted">(opsional)</span></label>
                    <input id="phone" class="input" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                    @error('phone')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" class="input" type="password" name="password" autocomplete="new-password" minlength="8" required>
                    @error('password')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi password</label>
                    <input id="password_confirmation" class="input" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
                </div>
                <button class="btn btn-primary" type="submit">Buat akun peserta <span aria-hidden="true">&rarr;</span></button>
            </form>
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </div>
@endsection
