@extends('layouts.app')

@section('content')
    <section class="page-head">
        <div class="container"><span class="eyebrow">KOLEKSI TIKET</span><h1>Tiket Saya</h1><p class="muted">Buka tiket digital dan QR check-in untuk event yang kamu ikuti.</p></div>
    </section>
    <section class="section">
        <div class="container">
            @if($registrations->count())
                <div class="event-grid">
                    @foreach($registrations as $registration)
                        <article class="event-card">
                            <a class="event-card-media" href="{{ route('participant.ticket.show', $registration->digitalTicket) }}"><img src="{{ $registration->event->poster_url }}" alt="Poster {{ $registration->event->name }}" loading="lazy" width="640" height="360"></a>
                            <div class="event-card-body">
                                <span class="badge {{ $registration->digitalTicket->status === 'valid' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($registration->digitalTicket->status) }}</span>
                                <h3>{{ $registration->event->name }}</h3>
                                <p>{{ $registration->ticketType->name }} &middot; {{ $registration->registration_code }}</p>
                                <div class="meta"><span>{{ $registration->event->start_at->format('d M Y, H:i') }} WIB</span><span title="{{ $registration->event->location }}">{{ Str::limit($registration->event->location, 22) }}</span></div>
                                <div style="display:flex;gap:8px;margin-top:auto"><a class="btn btn-primary btn-sm" href="{{ route('participant.ticket.show', $registration->digitalTicket) }}">Detail</a><a class="btn btn-soft btn-sm" href="{{ route('participant.ticket.pdf', $registration->digitalTicket) }}" target="_blank" rel="noopener">Unduh PDF</a></div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="pagination">{{ $registrations->links() }}</div>
            @else
                <div class="card empty"><h2>Belum ada tiket</h2><p class="muted">Tiket event yang kamu ikuti akan muncul di sini.</p><a class="btn btn-primary" href="{{ route('events.index') }}">Cari event</a></div>
            @endif
        </div>
    </section>
@endsection
