@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="container" style="max-width:720px">
            <div class="section-heading">
                <div><span class="eyebrow">PENGATURAN AKUN</span><h1>Profil Saya</h1><p class="muted">Perbarui informasi yang digunakan di Eventku.</p></div>
            </div>
            <div class="card">
                <form method="POST" action="{{ route('profile.update') }}" class="form-grid" data-submit-once>
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input id="name" class="input" name="name" value="{{ auth()->user()->name }}" autocomplete="name" required>
                        @error('name')<small class="field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" class="input" value="{{ auth()->user()->email }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor telepon</label>
                        <input id="phone" class="input" name="phone" value="{{ auth()->user()->phone }}" autocomplete="tel">
                        @error('phone')<small class="field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="role">Peran</label>
                        <input id="role" class="input" value="{{ ucfirst(auth()->user()->role) }}" disabled>
                    </div>
                    <div class="form-group full"><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
                </form>
            </div>
        </div>
    </section>
@endsection
