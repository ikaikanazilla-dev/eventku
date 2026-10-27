@extends('layouts.admin')

@section('admin-content')
    <section class="section">
        <div class="container"><div class="section-heading"><div><span class="eyebrow">AKUN PLATFORM</span><h1>Pengguna</h1><p class="muted">Kelola peran dan status akun sesuai kebutuhan operasional.</p></div></div>
            <div class="table-wrap"><table class="table"><thead><tr><th>Pengguna</th><th>Email</th><th>Peran</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                @forelse($users as $user)
                    <tr><td><strong>{{ $user->name }}</strong><br><small>{{ $user->phone ?: 'Nomor telepon belum diisi' }}</small></td><td>{{ $user->email }}</td><td>@if(!$user->is(auth()->user()))<form method="POST" action="{{ route('admin.user.role', $user) }}" data-submit-once>@csrf @method('PATCH')<label class="sr-only" for="role-{{ $user->id }}">Peran {{ $user->name }}</label><select id="role-{{ $user->id }}" class="select" name="role" style="min-width:130px;padding:8px" onchange="this.form.submit()"><option value="admin" @selected($user->role === 'admin')>Admin</option><option value="organizer" @selected($user->role === 'organizer')>Organizer</option><option value="participant" @selected($user->role === 'participant')>Peserta</option></select></form>@else<span class="badge">{{ ucfirst($user->role) }}</span>@endif</td><td><span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td><td>@if(!$user->is(auth()->user()))<form method="POST" action="{{ route('admin.user.toggle', $user) }}" data-submit-once>@csrf @method('PATCH')<button class="btn btn-soft btn-sm" type="submit">{{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>@else<small class="muted">Akun kamu</small>@endif</td></tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada pengguna.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="pagination">{{ $users->links() }}</div>
        </div>
    </section>
@endsection
