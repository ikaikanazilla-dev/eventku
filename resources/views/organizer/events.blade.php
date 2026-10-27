@extends('layouts.organizer')

@section('organizer-content')
    <section class="section">
        <div class="container">
            <div class="section-heading"><div><span class="eyebrow">RUANG PENYELENGGARA</span><h1>Event Saya</h1><p class="muted">Kelola jadwal, tiket, dan status publikasi event milikmu.</p></div><a class="btn btn-primary" href="{{ route('organizer.event.create') }}">Buat event</a></div>
            <form class="filters organizer-filters" method="GET" action="{{ route('organizer.events') }}">
                <input class="input filter-search" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau lokasi event" aria-label="Cari event">
                <select class="select" name="status" aria-label="Filter status">
                    <option value="">Semua status</option>
                    @foreach(['draft' => 'Draft', 'pending' => 'Menunggu review', 'published' => 'Published', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan', 'completed' => 'Selesai'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit">Terapkan</button>
                <a class="btn btn-soft" href="{{ route('organizer.events') }}">Reset</a>
            </form>
            <div class="filter-summary"><span><strong>{{ $events->total() }}</strong> event ditemukan</span></div>
            <div class="table-wrap"><table class="table"><thead><tr><th>Event</th><th>Jadwal</th><th>Tiket</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                @forelse($events as $event)
                    <tr>
                        <td><strong>{{ $event->name }}</strong><br><small>{{ $event->location }}</small></td>
                        <td>{{ $event->start_at->format('d M Y H:i') }} WIB</td>
                        <td>{{ $event->ticketTypes->sum('quota') }}</td>
                        <td><span class="badge {{ $event->status === 'published' ? 'badge-success' : ($event->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($event->status) }}</span></td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('organizer.event.show', $event) }}" class="btn btn-soft btn-sm">Detail</a>
                                @if(in_array($event->status, ['draft', 'rejected'], true))
                                    <a href="{{ route('organizer.event.edit', $event) }}" class="btn btn-soft btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('organizer.event.destroy', $event) }}" onsubmit="return confirm('Hapus event ini? Data event yang sudah memiliki pendaftar tidak dapat dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endif
                                @if($event->status === 'published')
                                    <a href="{{ route('organizer.checkin', $event) }}" class="btn btn-soft btn-sm">Check-in</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada event. Buat event pertamamu untuk memulai.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="pagination">{{ $events->links() }}</div>
        </div>
    </section>
@endsection
