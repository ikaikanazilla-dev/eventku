@extends('layouts.organizer')

@section('organizer-content')
    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="badge {{ $event->status === 'published' ? 'badge-success' : '' }}">{{ ucfirst($event->status) }}</span>
                    <h1>{{ $event->name }}</h1>
                    <p class="muted">{{ $event->start_at->format('d M Y H:i') }} · {{ $event->location }}</p>
                </div>
                <div class="table-actions">
                    @if(in_array($event->status, ['draft', 'rejected'], true))
                        <form method="POST" action="{{ route('organizer.event.submit', $event) }}">
                            @csrf
                            <button class="btn btn-primary">Ajukan Review</button>
                        </form>
                        <a class="btn btn-soft" href="{{ route('organizer.event.edit', $event) }}">Edit</a>
                        <form method="POST" action="{{ route('organizer.event.destroy', $event) }}" onsubmit="return confirm('Hapus event ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Hapus</button>
                        </form>
                    @endif
                    @if($event->status === 'published')
                        <a class="btn btn-primary" href="{{ route('organizer.checkin', $event) }}">QR Check-in</a>
                    @endif
                </div>
            </div>

            @if($event->rejection_reason)
                <div class="card" style="border-color:#fecdca;color:#b42318;margin-bottom:18px"><strong>Alasan penolakan</strong><p>{{ $event->rejection_reason }}</p></div>
            @endif

            <div class="split">
                <div>
                    <div class="card">
                        <img class="detail-cover" src="{{ $event->poster_url }}" alt="Poster {{ $event->name }}">
                        <h2>Tentang Event</h2>
                        <p style="white-space:pre-line;line-height:1.7;color:var(--muted)">{{ $event->description }}</p>
                    </div>
                </div>
                <div>
                    <div class="stat-grid" style="grid-template-columns:1fr 1fr">
                        <div class="stat-card"><label>Kuota</label><strong>{{ $event->ticketTypes->sum('quota') }}</strong></div>
                        <div class="stat-card"><label>Pendaftar</label><strong>{{ $event->registrations->where('status', 'registered')->count() }}</strong></div>
                    </div>
                    <div class="card">
                        <h3>Jenis Tiket</h3>
                        @foreach($event->ticketTypes as $ticket)
                            <p><strong>{{ $ticket->name }}</strong><br><span class="muted">{{ $ticket->remainingQuota() }} tersisa dari {{ $ticket->quota }}</span></p>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:18px">
                <h2>Peserta terbaru</h2>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Peserta</th><th>Kode</th><th>Status Tiket</th></tr></thead>
                        <tbody>
                            @forelse($event->registrations->take(10) as $registration)
                                <tr><td>{{ $registration->user->name }}<br><small>{{ $registration->user->email }}</small></td><td>{{ $registration->registration_code }}</td><td><span class="badge">{{ ucfirst($registration->digitalTicket?->status ?? 'pending') }}</span></td></tr>
                            @empty
                                <tr><td colspan="3" class="empty">Belum ada pendaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
