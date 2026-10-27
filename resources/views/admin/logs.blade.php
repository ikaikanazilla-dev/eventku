@extends('layouts.admin')

@section('admin-content')
    <section class="section"><div class="container"><div class="section-heading"><div><span class="eyebrow">AUDIT TRAIL</span><h1>Log aktivitas</h1><p class="muted">Catatan tindakan penting yang terjadi di dalam platform.</p></div></div><div class="table-wrap"><table class="table"><thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th><th>IP</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at?->format('d M Y H:i') }}</td><td>{{ $log->user?->name ?? 'Sistem' }}</td><td><span class="badge">{{ $log->action }}</span></td><td>{{ $log->description }}</td><td>{{ $log->ip_address }}</td></tr>@empty<tr><td colspan="5" class="empty">Belum ada aktivitas.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $logs->links() }}</div></div></section>
@endsection
