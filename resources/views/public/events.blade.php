@extends('layouts.app')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">KATALOG EVENTKU</span>
            <h1>Jelajahi event pilihan.</h1>
            <p class="muted">Cari agenda yang sesuai dengan minat, waktu, dan lokasimu.</p>

            <form class="filters" method="GET" action="{{ route('events.index') }}" aria-label="Filter event">
                <div class="filter-search">
                    <label class="sr-only" for="event-search">Cari event atau lokasi</label>
                    <input id="event-search" class="input" name="q" value="{{ request('q') }}" placeholder="Cari nama event atau lokasi...">
                </div>
                <div>
                    <label class="sr-only" for="event-category">Kategori</label>
                    <select id="event-category" class="select" name="category">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="sr-only" for="event-date">Tanggal event</label>
                    <input id="event-date" class="input" type="date" name="date" value="{{ request('date') }}" aria-label="Tanggal event">
                </div>
                <button class="btn btn-primary" type="submit">Terapkan</button>
                @if(request()->hasAny(['q', 'category', 'date']))
                    <a class="btn btn-ghost" href="{{ route('events.index') }}">Reset</a>
                @endif
            </form>
        </div>
    </section>

    <section class="section">
        <div class="container">
            @if($events->count())
                <div class="filter-summary">
                    <span>Menampilkan <strong>{{ $events->total() }}</strong> event yang tersedia.</span>
                    @if(request()->hasAny(['q', 'category', 'date']))<span>Filter sedang aktif</span>@endif
                </div>
                <div class="event-grid">
                    @foreach($events as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>
                <div class="pagination">{{ $events->links() }}</div>
            @else
                <div class="card empty">
                    <span class="eyebrow">BELUM ADA HASIL</span>
                    <h2>Event tidak ditemukan</h2>
                    <p class="muted">Coba ubah kata kunci, kategori, atau tanggal pencarianmu.</p>
                    <a class="btn btn-primary" href="{{ route('events.index') }}">Lihat semua event</a>
                </div>
            @endif
        </div>
    </section>
@endsection
