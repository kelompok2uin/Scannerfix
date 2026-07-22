<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logos">
            <img src="{{ asset('images/logo-pancacita.png') }}" alt="Logo Pancacita" class="brand-logo-img">
            <img src="{{ asset('images/logo-bpka.png') }}" alt="Logo BPKA" class="brand-logo-img">
        </div>
        <div class="brand-name">BPKA <span>Scanner</span></div>
        <div class="brand-subtitle">Badan Pengelolaan Keuangan Aceh</div>
    </div>

    <div class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('scanner') }}" class="menu-item {{ request()->routeIs('scanner') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
            Scanner
        </a>
        <a href="{{ route('documents.index') }}" class="menu-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            Dokumen
        </a>
        <a href="{{ route('settings') }}" class="menu-item {{ request()->routeIs('settings*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
            Pengaturan
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="profile-avatar"></div>
            <div class="profile-info">
                <span class="profile-name">BPKA Scanner</span>
                <span class="profile-tag">v1.0</span>
            </div>
        </div>
    </div>
</aside>
