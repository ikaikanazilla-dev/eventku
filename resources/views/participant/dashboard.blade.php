@extends('layouts.app')

@section('content')
    <div class="dashboard-shell">
        <aside class="sidebar">
            <a class="active" href="{{ route('participant.dashboard') }}">Ringkasan</a>
            <a href="{{ route('events.index') }}">Jelajahi Event</a>
            <a href="{{ route('participant.tickets') }}">Tiket Saya</a>
            <a href="{{ route('profile.edit') }}">Profil</a>
        </aside>
        <div class="dash-main">
            <div class="dash-top">
                <div><span class="eyebrow">RUANG PESERTA</span><h1>Halo, {{ auth()->user()->name }}</h1><p class="muted">Kelola event yang kamu ikuti dan akses tiket digitalmu.</p></div>
                <a class="btn btn-primary" href="{{ route('events.index') }}">Jelajahi event</a>
            </div>
            <div class="stat-grid" style="grid-template-columns:repeat(2,minmax(0,220px))">
                <div class="stat-card"><label>Tiket valid</label><strong>{{ $stats['valid'] }}</strong><span class="badge badge-success">Siap digunakan</span></div>
                <div class="stat-card"><label>Sudah digunakan</label><strong>{{ $stats['used'] }}</strong><span class="badge badge-neutral">Riwayat check-in</span></div>
            </div>
            <div class="section-heading"><div><h2>Event mendatang</h2><p class="muted">Tiket digital terbaru yang kamu miliki.</p></div><a href="{{ route('participant.tickets') }}" class="btn btn-soft">Lihat semua</a></div>
            @if($registrations->count())
                <div class="event-grid">
                    @foreach($registrations as $registration)
                        <article class="event-card">
                            <a class="event-card-media" href="{{ route('participant.ticket.show', $registration->digitalTicket) }}"><img src="{{ $registration->event->poster_url }}" alt="Poster {{ $registration->event->name }}" loading="lazy" width="640" height="360"></a>
                            <div class="event-card-body">
                                <span class="badge badge-success">Tiket valid</span>
                                <h3>{{ $registration->event->name }}</h3>
                                <div class="meta"><span>{{ $registration->event->start_at->format('d M Y, H:i') }} WIB</span><span title="{{ $registration->event->location }}">{{ Str::limit($registration->event->location, 22) }}</span></div>
                                <a class="btn btn-primary btn-sm" href="{{ route('participant.ticket.show', $registration->digitalTicket) }}" style="margin-top:auto">Buka tiket digital</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="card empty"><h3>Belum ada tiket</h3><p class="muted">Jelajahi event untuk menemukan agenda yang ingin kamu ikuti.</p><a class="btn btn-primary" href="{{ route('events.index') }}">Jelajahi event</a></div>
            @endif
        </div>
    </div>
@endsection
