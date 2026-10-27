@extends('layouts.app')

@section('content')
    @php
        $organizerNavigation = [
            ['route' => 'organizer.dashboard', 'pattern' => 'organizer.dashboard', 'label' => 'Dashboard', 'icon' => '⌂'],
            ['route' => 'organizer.events', 'pattern' => 'organizer.events', 'label' => 'Event Saya', 'icon' => '◈'],
            ['route' => 'organizer.event.create', 'pattern' => 'organizer.event.create', 'label' => 'Buat Event', 'icon' => '+'],
            ['route' => 'organizer.registrations', 'pattern' => 'organizer.registrations', 'label' => 'Pendaftar', 'icon' => '◎'],
            ['route' => 'organizer.reports', 'pattern' => 'organizer.reports', 'label' => 'Laporan', 'icon' => '▤'],
        ];
        $currentOrganizerPage = collect($organizerNavigation)->first(fn ($item) => request()->routeIs($item['pattern']));
        $currentOrganizerPage = $currentOrganizerPage['label'] ?? 'Eventku';
        if (request()->routeIs('organizer.event.*') || request()->routeIs('organizer.checkin')) {
            $currentOrganizerPage = 'Event Saya';
        }
    @endphp
    <div class="dashboard-shell organizer-dashboard-shell">
        <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-expanded="false" aria-controls="organizer-sidebar">☰ <span>Menu organizer</span></button>
        <div class="sidebar-backdrop" data-sidebar-backdrop hidden></div>
        <aside class="sidebar organizer-sidebar" id="organizer-sidebar" aria-label="Navigasi organizer">
            @foreach($organizerNavigation as $item)
                @php($isActive = request()->routeIs($item['pattern']) || ($item['route'] === 'organizer.events' && (request()->routeIs('organizer.event.*') || request()->routeIs('organizer.checkin'))))
                <a class="{{ $isActive ? 'active' : '' }}" href="{{ route($item['route']) }}"><span class="sidebar-icon" aria-hidden="true">{{ $item['icon'] }}</span>{{ $item['label'] }}</a>
            @endforeach
        </aside>
        <div class="dash-main">
            <nav class="breadcrumb workspace-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('organizer.dashboard') }}">Dashboard</a>
                @if(! request()->routeIs('organizer.dashboard'))
                    <span aria-hidden="true">/</span><span>{{ $currentOrganizerPage }}</span>
                @endif
            </nav>
            @yield('organizer-content')
        </div>
    </div>
@endsection
