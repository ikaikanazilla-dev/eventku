@extends('layouts.admin')

@section('admin-content')
    <section class="section">
        <div class="container">
            <div class="section-heading"><div><span class="eyebrow">KATALOG EVENT</span><h1>Kategori event</h1><p class="muted">Kelola kategori yang dipakai penyelenggara saat membuat event.</p></div></div>
            <div class="split">
                <div class="table-wrap"><table class="table"><thead><tr><th>Nama</th><th>Slug</th><th>Status</th></tr></thead><tbody>@forelse($categories as $category)<tr><td><strong>{{ $category->name }}</strong></td><td>{{ $category->slug }}</td><td><span class="badge badge-success">Aktif</span></td></tr>@empty<tr><td colspan="3" class="empty">Belum ada kategori.</td></tr>@endforelse</tbody></table></div>
                <div class="card"><h2>Tambah kategori</h2><p class="muted">Gunakan nama yang singkat dan mudah dipahami peserta.</p><form method="POST" action="{{ route('admin.category.store') }}" data-submit-once style="display:flex;flex-direction:column;gap:15px;margin-top:20px">@csrf<div class="form-group"><label for="category-name">Nama kategori</label><input id="category-name" class="input" name="name" required></div><div class="form-group"><label for="category-description">Deskripsi <span class="muted">(opsional)</span></label><textarea id="category-description" name="description" rows="4"></textarea></div><button class="btn btn-primary" type="submit">Simpan kategori</button></form></div>
            </div>
            <div class="pagination">{{ $categories->links() }}</div>
        </div>
    </section>
@endsection
