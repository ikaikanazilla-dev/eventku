@extends('layouts.app')

@section('content')
    @php
        $registrationOpen = $event->isRegistrationOpen();
        $totalQuota = $event->ticketTypes->sum('quota');
        $remainingQuota = $event->remainingQuota();
    @endphp

    <section class="detail-hero">
        <div class="container detail-grid">
            <div>
                <img class="detail-cover" src="{{ $event->poster_url }}" alt="Poster {{ $event->name }}" width="800" height="450">
            </div>
            <div class="detail-main">
                <span class="badge">{{ $event->category?->name ?? 'Event' }} &middot; Gratis</span>
                <h1>{{ $event->name }}</h1>
                <p>Diselenggarakan oleh <strong>{{ $event->organizer->name }}</strong>. Temukan informasi jadwal, lokasi, dan cara mendaftar di halaman ini.</p>
                <div class="info-grid">
                    <div class="card"><small class="muted">Tanggal mulai</small><strong>{{ $event->start_at->format('d M Y') }}</strong><small>{{ $event->start_at->format('H:i') }} WIB</small></div>
                    <div class="card"><small class="muted">Lokasi</small><strong title="{{ $event->location }}">{{ Str::limit($event->location, 27) }}</strong></div>
                    <div class="card"><small class="muted">Tiket tersisa</small><strong>{{ $remainingQuota }}</strong><small>dari {{ $totalQuota }} kuota</small></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container detail-content">
            <div>
                <article class="card">
                    <span class="eyebrow">TENTANG EVENT</span>
                    <h2 style="margin-top:16px">{{ $event->name }}</h2>
                    <p class="detail-description">{{ $event->description }}</p>
                    <div class="detail-meta">
                        <span><strong>Waktu pendaftaran</strong> {{ $event->registration_start_at->format('d M Y, H:i') }} &ndash; {{ $event->registration_end_at->format('d M Y, H:i') }} WIB</span>
                        <span><strong>Lokasi atau platform</strong> {{ $event->location }}</span>
                        <span><strong>Penyelenggara</strong> {{ $event->organizer->name }}</span>
                    </div>
                </article>
            </div>

            <aside class="card booking-card">
                <span class="eyebrow">PILIH TIKET</span>
                <h2>Daftar untuk hadir</h2>
                <p class="muted">Tiket Eventku saat ini gratis. Kuota diperbarui berdasarkan pendaftaran aktif.</p>

                @if($registration)
                    <div class="booking-message success">
                        Kamu sudah terdaftar di event ini. Tiket digitalmu siap digunakan.
                    </div>
                    @if($registration->digitalTicket)
                        <a class="btn btn-primary" style="width:100%;margin-top:12px" href="{{ route('participant.ticket.show', $registration->digitalTicket) }}">Buka tiket digital</a>
                    @endif
                @else
                    @foreach($event->ticketTypes as $ticket)
                        <div class="ticket-option">
                            <div class="ticket-option-head"><strong>{{ $ticket->name }}</strong><span class="badge badge-success">Gratis</span></div>
                            <p>{{ $ticket->description ?: 'Akses masuk ke event.' }}</p>
                            <div class="ticket-option-foot"><small class="muted">{{ $ticket->remainingQuota() }} tersisa</small>
                                @if(auth()->check() && auth()->user()->role === 'participant' && $registrationOpen && $ticket->remainingQuota() > 0)
                                    <form method="POST" action="{{ route('participant.event.register', $event) }}">
                                        @csrf
                                        <input type="hidden" name="ticket_type_id" value="{{ $ticket->id }}">
                                        <button class="btn btn-primary btn-sm" type="submit">Daftar sekarang</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if(!auth()->check())
                        <a class="btn btn-primary" style="width:100%;margin-top:14px" href="{{ route('login') }}">Masuk untuk mendaftar</a>
                    @elseif(auth()->user()->role !== 'participant')
                        <div class="booking-message">Akun {{ auth()->user()->role }} dapat mengelola event, tetapi pendaftaran peserta hanya tersedia untuk akun peserta.</div>
                    @elseif(!$registrationOpen)
                        <div class="booking-message warning">Pendaftaran sedang ditutup. Periksa kembali jadwal pendaftaran event ini.</div>
                    @elseif($remainingQuota < 1)
                        <div class="booking-message warning">Kuota event sudah penuh.</div>
                    @endif
                @endif
            </aside>
        </div>
    </section>
@endsection
