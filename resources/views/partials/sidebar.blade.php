@php
    // Dynamic: pulled fresh from DB every request, so sidebar auto-updates
    // whenever a farmer is added in a new state — no hardcoding.
    $__sidebarStates = \App\Models\Farmer::query()->distinct()->orderBy('state')->pluck('state');
    $__currentState = request()->route('state');
@endphp

<div class="sidebar-inner p-3">
    <div class="sidebar-eyebrow mb-2 px-1">Menu</div>
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item">
            <a href="{{ route('dashboard.index') }}"
               class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('farmers.index') }}"
               class="nav-link {{ request()->routeIs('farmers.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Farmers
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('generation.summary') }}"
               class="nav-link {{ request()->routeIs('generation.summary') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                Generation Summary
            </a>
        </li>
    </ul>

    <div class="sidebar-eyebrow mb-2 px-1">States ({{ $__sidebarStates->count() }})</div>
    <ul class="nav nav-pills flex-column">
        @forelse($__sidebarStates as $stateName)
            <li class="nav-item">
                <a href="{{ route('dashboard.state', $stateName) }}"
                   class="nav-link {{ $__currentState === $stateName ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $stateName }}
                </a>
            </li>
        @empty
            <li class="px-1 text-muted small">No states yet.</li>
        @endforelse
    </ul>
    <ul class="nav nav-logout flex-column">
        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m0 0v16m0-8h8m-8-8v8"/></svg>
                Logout
            </a>
        </li>
    </ul>
</div>
