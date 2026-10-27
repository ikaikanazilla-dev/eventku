@extends('layouts.admin')

@section('admin-content')
    <section class="section">
        <div class="container">
            <div class="section-heading"><div><span class="eyebrow">MODERASI EVENT</span><h1>Review event</h1><p class="muted">Periksa event sebelum ditampilkan kepada peserta.</p></div><form method="GET"><label class="sr-only" for="status">Filter status</label><select id="status" class="select" name="status" onchange="this.form.submit()"><option value="">Semua status</option>@foreach(['pending','published','rejected','draft'] as $status)<option @selected(request('status') === $status) value="{{ $status }}">{{ ucfirst($status) }}</option>@endforeach</select></form></div>
            <div class="table-wrap"><table class="table"><thead><tr><th>Event</th><th>Penyelenggara</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                @forelse($events as $event)
                    <tr><td><a href="{{ route('admin.event.show', $event) }}"><strong>{{ $event->name }}</strong></a><br><small>{{ $event->start_at->format('d M Y H:i') }} WIB &middot; {{ $event->location }}</small></td><td>{{ $event->organizer->name }}<br><small>{{ $event->organizer->email }}</small></td><td><span class="badge {{ $event->status === 'published' ? 'badge-success' : ($event->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($event->status) }}</span></td><td><div class="table-actions"><a class="btn btn-soft btn-sm" href="{{ route('admin.event.edit', $event) }}">Edit</a>@if($event->status === 'pending')<form method="POST" action="{{ route('admin.event.approve', $event) }}" data-submit-once>@csrf<button class="btn btn-primary btn-sm" type="submit">Setujui</button></form><form method="POST" action="{{ route('admin.event.reject', $event) }}" data-submit-once>@csrf<input type="hidden" name="rejection_reason" value="Perlu revisi sebelum dipublikasikan."><button class="btn btn-danger btn-sm" type="submit">Tolak</button></form>@endif</div></td></tr>
                @empty
                    <tr><td colspan="4" class="empty">Tidak ada event dengan status ini.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="pagination">{{ $events->links() }}</div>
        </div>
    </section>
@endsection
