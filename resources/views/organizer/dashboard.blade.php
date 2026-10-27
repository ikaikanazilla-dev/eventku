@extends('layouts.organizer')

@section('organizer-content')
            <div class="dash-top">
                <div><span class="eyebrow">RUANG PENYELENGGARA</span><h1>Dashboard event</h1><p class="muted">Kelola event, pantau pendaftar, dan lakukan check-in dari satu tempat.</p></div>
                <a class="btn btn-primary" href="{{ route('organizer.event.create') }}">Buat event baru</a>
            </div>
            <div class="stat-grid">
                <div class="stat-card"><label>Total event</label><strong>{{ $stats['events'] }}</strong><span class="muted">Semua event milikmu</span></div>
                <div class="stat-card"><label>Total pendaftar</label><strong>{{ $stats['registrations'] }}</strong><span class="muted">Pendaftaran aktif</span></div>
                <div class="stat-card"><label>Dipublikasikan</label><strong>{{ $stats['published'] }}</strong><span class="badge badge-success">Tampil di katalog</span></div>
                <div class="stat-card"><label>Check-in berhasil</label><strong>{{ $stats['checkins'] }}</strong><span class="muted">Data kehadiran</span></div>
            </div>
            <div class="section-heading"><div><h2>Event terbaru</h2><p class="muted">Pantau jadwal dan status event milikmu.</p></div><a href="{{ route('organizer.events') }}" class="btn btn-soft">Lihat semua</a></div>
            <div class="table-wrap">
                <table class="table"><thead><tr><th>Nama event</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                    @forelse($events as $event)
                        <tr><td><strong>{{ $event->name }}</strong><br><small>{{ $event->category->name }}</small></td><td>{{ $event->start_at->format('d M Y') }}<br><small>{{ $event->location }}</small></td><td><span class="badge {{ $event->status === 'published' ? 'badge-success' : ($event->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($event->status) }}</span></td><td><a class="btn btn-soft btn-sm" href="{{ route('organizer.event.show', $event) }}">Buka detail</a></td></tr>
                    @empty
                        <tr><td colspan="4" class="empty">Belum ada event. Buat event pertamamu sekarang.</td></tr>
                    @endforelse
                </tbody></table>
            </div>
@endsection
