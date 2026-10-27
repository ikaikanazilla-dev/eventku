@extends('layouts.admin')

@section('admin-content')
    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="badge">{{ ucfirst($event->status) }}</span>
                    <h1>{{ $event->name }}</h1>
                    <p class="muted">{{ $event->start_at->format('d M Y H:i') }} · {{ $event->location }} · {{ $event->organizer->name }}</p>
                </div>
                <div class="table-actions">
                    <a class="btn btn-soft" href="{{ route('admin.event.edit', $event) }}">Edit event</a>
                    @if($event->status === 'pending')
                        <form method="POST" action="{{ route('admin.event.approve', $event) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Setujui &amp; Publikasikan</button>
                        </form>
                        <form method="POST" action="{{ route('admin.event.reject', $event) }}">
                            @csrf
                            <input type="hidden" name="rejection_reason" value="Perlu revisi sebelum dipublikasikan.">
                            <button class="btn btn-danger" type="submit">Tolak</button>
                        </form>
                    @endif
                </div>
            </div>

            @if($event->rejection_reason)
                <div class="card" style="border-color:#fecdca;color:#b42318;margin-bottom:18px">
                    <strong>Alasan penolakan</strong>
                    <p>{{ $event->rejection_reason }}</p>
                </div>
            @endif

            <div class="detail-grid">
                <div class="card">
                    <img class="detail-cover" src="{{ $event->poster_url }}" alt="Poster {{ $event->name }}">
                    <h2>Tentang Event</h2>
                    <p style="white-space:pre-line;line-height:1.8;color:var(--muted)">{{ $event->description }}</p>
                </div>
                <div>
                    <div class="stat-grid" style="grid-template-columns:1fr 1fr">
                        <div class="stat-card"><label>Kategori</label><strong>{{ $event->category->name }}</strong></div>
                        <div class="stat-card"><label>Kuota</label><strong>{{ $event->ticketTypes->sum('quota') }}</strong></div>
                        <div class="stat-card"><label>Pendaftar</label><strong>{{ $event->registrations->where('status','registered')->count() }}</strong></div>
                        <div class="stat-card"><label>Registrasi</label><strong>{{ $event->registration_start_at->format('d M') }}–{{ $event->registration_end_at->format('d M Y') }}</strong></div>
                    </div>
                    <div class="card">
                        <h3>Jenis Tiket</h3>
                        @foreach($event->ticketTypes as $ticket)
                            <p><strong>{{ $ticket->name }}</strong><br><span class="muted">{{ $ticket->remainingQuota() }} tersisa dari {{ $ticket->quota }}</span></p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
