@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow">EVENTKU / TEMUKAN PENGALAMAN BARU</span>
                <h1>Temukan event seru <span>di sekitarmu.</span></h1>
                <p>Jelajahi seminar, workshop, dan acara komunitas. Daftar dengan mudah dan simpan tiketmu secara digital.</p>
                <form class="searchbar" method="GET" action="{{ route('events.index') }}">
                    <label class="sr-only" for="hero-search">Cari event</label>
                    <input id="hero-search" name="q" placeholder="Cari nama event atau lokasi...">
                    <button class="btn btn-primary" type="submit">Jelajahi Event</button>
                </form>
                <div class="hero-note" aria-label="Fitur Eventku">
                    <span>Daftar online</span>
                    <span>Tiket digital</span>
                    <span>QR untuk check-in</span>
                </div>
            </div>
            <img class="hero-img" src="{{ asset('uploads/events/hero-conference.jpg') }}" alt="Peserta konferensi mengikuti sesi presentasi" width="800" height="450">
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">PILIHAN UNTUKMU</span>
                    <h2>Event yang akan datang</h2>
                    <p class="muted">Temukan agenda berikutnya dan pilih pengalaman yang paling sesuai.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('events.index') }}">Lihat semua event <span aria-hidden="true">&rarr;</span></a>
            </div>

            @if($events->count())
                <div class="event-grid">
                    @foreach($events as $event)
                        <x-event-card :event="$event" :lazy="$loop->index > 0" />
                    @endforeach
                </div>
            @else
                <div class="card empty">
                    <h3>Belum ada event publik</h3>
                    <p class="muted">Nantikan event terbaru dari penyelenggara Eventku.</p>
                    <a class="btn btn-primary" href="{{ route('events.index') }}">Buka katalog event</a>
                </div>
            @endif
        </div>
    </section>

    @if($events->pluck('category')->filter()->count())
        <section class="section" style="padding-top:0">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">JELAJAH BERDASARKAN MINAT</span>
                        <h2>Kategori event</h2>
                    </div>
                </div>
                <div class="category-grid">
                    @foreach($events->pluck('category')->filter()->unique('id') as $category)
                        <a class="category-card" href="{{ route('events.index', ['category' => $category->id]) }}">
                            <span class="category-icon" aria-hidden="true">✦</span>
                            <span><strong>{{ $category->name }}</strong><small>Lihat event</small></span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="cara-kerja" class="section how">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">TIGA LANGKAH SEDERHANA</span>
                    <h2>Cara mengikuti event</h2>
                    <p class="muted">Semua kebutuhan peserta ada di alur yang singkat.</p>
                </div>
            </div>
            <div class="how-grid">
                <div class="how-card"><span class="how-num">01</span><h3>Pilih event</h3><p class="muted">Cari event berdasarkan topik, tanggal, atau lokasi yang kamu inginkan.</p></div>
                <div class="how-card"><span class="how-num">02</span><h3>Daftar dan simpan tiket</h3><p class="muted">Masuk sebagai peserta, pilih tiket gratis, lalu buka tiket digitalmu.</p></div>
                <div class="how-card"><span class="how-num">03</span><h3>Tunjukkan QR saat datang</h3><p class="muted">Gunakan QR pada tiket digital untuk proses check-in di lokasi event.</p></div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container cta">
            <div>
                <span class="eyebrow">UNTUK PENYELENGGARA</span>
                <h2>Siapkan eventmu di Eventku.</h2>
                <p>Buat event, kelola kuota, lihat pendaftar, dan lakukan check-in dari ruang kerja penyelenggara.</p>
                <a class="btn btn-primary" href="{{ auth()->check() && auth()->user()->role === 'organizer' ? route('organizer.dashboard') : route('login') }}">{{ auth()->check() && auth()->user()->role === 'organizer' ? 'Buka dashboard' : 'Masuk untuk mulai' }}</a>
            </div>
        </div>
    </section>
@endsection
