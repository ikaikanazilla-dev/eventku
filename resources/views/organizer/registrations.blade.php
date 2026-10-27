@extends('layouts.organizer')

@section('organizer-content')
    <section class="section">
        <div class="container">
            <div class="section-heading"><div><span class="eyebrow">REGISTRATION MONITOR</span><h1>Daftar Pendaftar</h1><p class="muted">Pantau peserta dan status tiket dari seluruh event milikmu.</p></div></div>
            <div class="table-wrap"><table class="table"><thead><tr><th>Peserta</th><th>Event</th><th>Tiket</th><th>Terdaftar</th><th>Status</th></tr></thead><tbody>
                @forelse($registrations as $registration)
                    <tr><td><strong>{{ $registration->user->name }}</strong><br><small>{{ $registration->user->email }}</small></td><td>{{ $registration->event->name }}</td><td>{{ $registration->ticketType->name }}</td><td>{{ $registration->registered_at->format('d M Y H:i') }}</td><td><span class="badge {{ $registration->digitalTicket?->status === 'used' ? 'badge-neutral' : 'badge-success' }}">{{ ucfirst($registration->digitalTicket?->status ?? $registration->status) }}</span></td></tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada pendaftar.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="pagination">{{ $registrations->links() }}</div>
        </div>
    </section>
@endsection
