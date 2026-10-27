@props(['event', 'lazy' => true])

<article class="event-card">
    <a class="event-card-media" href="{{ route('events.show', $event->slug) }}" aria-label="Lihat detail {{ $event->name }}">
        <img src="{{ $event->poster_url }}" alt="Poster {{ $event->name }}" width="640" height="360" @if($lazy) loading="lazy" @endif decoding="async">
        <span class="event-card-media-label">{{ $event->category?->name ?? 'Event' }}</span>
    </a>
    <div class="event-card-body">
        <div class="event-card-badges">
            <span class="badge">{{ $event->category?->name ?? 'Event' }}</span>
            <span class="badge badge-success">Gratis</span>
        </div>
        <h3><a href="{{ route('events.show', $event->slug) }}">{{ $event->name }}</a></h3>
        <div class="meta event-card-meta">
            <span title="{{ $event->start_at->format('d M Y, H:i') }} WIB">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg>
                {{ $event->start_at->format('d M Y, H:i') }} WIB
            </span>
            <span title="{{ $event->location }}">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.2"/></svg>
                {{ Str::limit($event->location, 24) }}
            </span>
        </div>
        <p>{{ Str::limit($event->description, 105) }}</p>
        <div class="event-card-footer">
            <small>{{ $event->remainingQuota() }} tiket tersisa</small>
            <a class="btn btn-primary btn-sm" href="{{ route('events.show', $event->slug) }}">Lihat detail <span aria-hidden="true">&rarr;</span></a>
        </div>
    </div>
</article>
