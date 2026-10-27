@extends('layouts.app')

@section('content')
    @php
        $adminNavigation = [
            ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '⌂'],
            ['route' => 'admin.events', 'pattern' => 'admin.events', 'label' => 'Moderasi Event', 'icon' => '◈'],
            ['route' => 'admin.categories', 'pattern' => 'admin.categories', 'label' => 'Kategori', 'icon' => '▦'],
            ['route' => 'admin.users', 'pattern' => 'admin.users', 'label' => 'Pengguna', 'icon' => '◎'],
            ['route' => 'admin.logs', 'pattern' => 'admin.logs', 'label' => 'Log Aktivitas', 'icon' => '≡'],
        ];
        $currentAdminPage = collect($adminNavigation)->first(fn ($item) => request()->routeIs($item['pattern']));
        $currentAdminPage = $currentAdminPage['label'] ?? 'Admin';
        if (request()->routeIs('admin.event.*')) {
            $currentAdminPage = 'Moderasi Event';
        }
    @endphp
    <div class="dashboard-shell admin-dashboard-shell">
        <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-expanded="false" aria-controls="admin-sidebar">☰ <span>Menu admin</span></button>
        <div class="sidebar-backdrop" data-sidebar-backdrop hidden></div>
        <aside class="sidebar admin-sidebar" id="admin-sidebar" aria-label="Navigasi admin">
            @foreach($adminNavigation as $item)
                @php($isActive = request()->routeIs($item['pattern']) || ($item['route'] === 'admin.events' && request()->routeIs('admin.event.*')))
                <a class="{{ $isActive ? 'active' : '' }}" href="{{ route($item['route']) }}"><span class="sidebar-icon" aria-hidden="true">{{ $item['icon'] }}</span>{{ $item['label'] }}</a>
            @endforeach
        </aside>
        <div class="dash-main">
            <nav class="breadcrumb admin-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                @if(! request()->routeIs('admin.dashboard'))
                    <span aria-hidden="true">/</span><span>{{ $currentAdminPage }}</span>
                @endif
            </nav>
            @yield('admin-content')
        </div>
    </div>
@endsection
