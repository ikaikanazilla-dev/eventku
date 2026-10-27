@php($adminMode = $adminMode ?? false)
@extends($adminMode ? 'layouts.admin' : 'layouts.organizer')

@section($adminMode ? 'admin-content' : 'organizer-content')
    <section class="section">
        <div class="container" style="max-width:920px">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">{{ $adminMode ? 'RUANG ADMIN' : 'RUANG PENYELENGGARA' }}</span>
                    <h1>{{ $adminMode ? 'Edit Event' : ($event ? 'Edit Event' : 'Buat Event Baru') }}</h1>
                    <p class="muted">{{ $adminMode ? 'Perbarui jadwal atau informasi event tanpa membuat ulang tiket dan registrasi.' : ($event ? 'Perbarui informasi event lalu simpan perubahan.' : 'Lengkapi informasi event. Event baru akan tersimpan sebagai draft dan perlu diajukan untuk review admin.') }}</p>
                </div>
            </div>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route($adminMode ? 'admin.dashboard' : 'organizer.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><a href="{{ route($adminMode ? 'admin.events' : 'organizer.events') }}">{{ $adminMode ? 'Moderasi Event' : 'Event Saya' }}</a><span aria-hidden="true">/</span><span>{{ $event ? 'Edit Event' : 'Buat Event' }}</span>
            </nav>

            <div class="card">
                <p class="form-help" style="margin-bottom:22px"><span class="required-mark" aria-hidden="true">*</span> Wajib diisi. Pastikan jadwal pendaftaran berakhir sebelum event dimulai.</p>
                <form method="POST" enctype="multipart/form-data" action="{{ $adminMode ? route('admin.event.update', $event) : ($event ? route('organizer.event.update', $event) : route('organizer.event.store')) }}" class="form-grid" data-submit-once>
                    @csrf
                    @if($event)
                        @method('PUT')
                    @endif

                    <div class="form-group full">
                        <label for="name">Nama event <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="name" class="input" name="name" value="{{ old('name', $event?->name) }}" required>
                        @error('name')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategori <span class="required-mark" aria-hidden="true">*</span></label>
                        <select id="category_id" class="select" name="category_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $event?->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label for="location">Lokasi atau platform <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="location" class="input" name="location" value="{{ old('location', $event?->location) }}" placeholder="Contoh: Auditorium, Jakarta atau Online" required>
                        @error('location')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="description">Deskripsi event <span class="required-mark" aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="7" placeholder="Jelaskan topik, agenda singkat, dan siapa yang cocok mengikuti event ini." required>{{ old('description', $event?->description) }}</textarea>
                        @error('description')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label for="start_at">Mulai event <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="start_at" class="input" type="datetime-local" name="start_at" value="{{ old('start_at', $event?->start_at?->format('Y-m-d\TH:i')) }}" required>
                        @error('start_at')<small class="field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="end_at">Selesai event <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="end_at" class="input" type="datetime-local" name="end_at" value="{{ old('end_at', $event?->end_at?->format('Y-m-d\TH:i')) }}" required>
                        @error('end_at')<small class="field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="registration_start_at">Pendaftaran mulai <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="registration_start_at" class="input" type="datetime-local" name="registration_start_at" value="{{ old('registration_start_at', $event?->registration_start_at?->format('Y-m-d\TH:i')) }}" required>
                        @error('registration_start_at')<small class="field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="registration_end_at">Pendaftaran berakhir <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="registration_end_at" class="input" type="datetime-local" name="registration_end_at" value="{{ old('registration_end_at', $event?->registration_end_at?->format('Y-m-d\TH:i')) }}" required>
                        @error('registration_end_at')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label for="poster">Poster event <span class="muted">(opsional)</span></label>
                        <input id="poster" class="input" type="file" name="poster" accept="image/jpeg,image/png,image/webp">
                        <small class="form-help">JPG, PNG, atau WebP maksimal 4 MB.</small>
                        @if($event?->poster_path)<small class="form-help">Poster saat ini tersimpan. Unggah file baru bila ingin menggantinya.</small>@endif
                        @error('poster')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    @php($ticket = $event?->ticketTypes?->first())
                    <div class="form-group">
                        <label for="ticket_name">Nama tiket gratis <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="ticket_name" class="input" name="ticket_name" value="{{ old('ticket_name', $ticket?->name ?? 'General Pass') }}" required>
                        @error('ticket_name')<small class="field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="ticket_quota">Kuota tiket <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="ticket_quota" class="input" type="number" min="1" name="ticket_quota" value="{{ old('ticket_quota', $ticket?->quota ?? 100) }}" required>
                        @error('ticket_quota')<small class="field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group full"><div class="table-actions"><button class="btn btn-primary" type="submit">{{ $adminMode ? 'Simpan perubahan' : 'Simpan draft event' }} <span aria-hidden="true">&rarr;</span></button><a class="btn btn-soft" href="{{ route($adminMode ? 'admin.event.show' : 'organizer.events', $adminMode ? $event : []) }}">Batal</a></div></div>
                </form>
            </div>
        </div>
    </section>
@endsection
