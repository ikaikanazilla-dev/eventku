@extends('layouts.admin')

@section('admin-content')
    <div class="dash-top">
        <div><span class="eyebrow">RUANG ADMIN</span><h1>Dashboard admin</h1><p class="muted">Pantau platform dan review event yang menunggu persetujuan.</p></div>
        <a class="btn btn-soft" href="{{ route('admin.events', ['status' => 'pending']) }}">Review tertunda ({{ $stats['pending'] }})</a>
    </div>
    <div class="stat-grid">
        <div class="stat-card"><label>Total pengguna</label><strong>{{ $stats['users'] }}</strong><span class="muted">{{ $stats['organizers'] }} penyelenggara</span></div>
        <div class="stat-card"><label>Total peserta</label><strong>{{ $stats['participants'] }}</strong><span class="muted">Akun aktif</span></div>
        <div class="stat-card"><label>Total event</label><strong>{{ $stats['events'] }}</strong><span class="muted">Semua status</span></div>
        <div class="stat-card"><label>Check-in sukses</label><strong>{{ $stats['checkins'] }}</strong><span class="muted">Data kehadiran</span></div>
    </div>
    <div class="card">
        <div class="section-heading"><div><h2>Antrean review</h2><p class="muted">Event yang membutuhkan pemeriksaan admin.</p></div><a href="{{ route('admin.events') }}" class="btn btn-soft">Semua event</a></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>Event</th><th>Penyelenggara</th><th>Jadwal</th><th>Aksi</th></tr></thead><tbody>
            @forelse($events as $event)
                <tr><td><strong>{{ $event->name }}</strong><br><small>{{ $event->location }}</small></td><td>{{ $event->organizer->name }}</td><td>{{ $event->start_at->format('d M Y H:i') }} WIB</td><td><div class="table-actions"><a class="btn btn-soft btn-sm" href="{{ route('admin.event.edit', $event) }}">Edit</a><a class="btn btn-primary btn-sm" href="{{ route('admin.event.show', $event) }}">Review</a></div></td></tr>
            @empty
                <tr><td colspan="4" class="empty">Antrean kosong. Tidak ada event yang menunggu review.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
@endsection
