<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Samity Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        #sidebar { transition: transform 0.28s cubic-bezier(.4,0,.2,1); }
        @media (max-width: 1023px) {
            #sidebar.sidebar-hidden { transform: translateX(-100%); }
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 0.855rem;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            width: 100%;
            border: none;
            background: none;
            text-align: left;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.07);
            color: #f1f5f9;
        }
        .nav-link.active {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(13,148,136,0.35);
        }
        .nav-link.active .nav-icon { color: #fff; }
        .nav-link:hover .nav-icon { color: #5eead4; }
        .nav-link.active:hover { background: linear-gradient(135deg, #0f766e, #115e59); }
        .nav-icon {
            width: 16px;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            flex-shrink: 0;
            transition: color 0.15s;
        }
        .nav-logout:hover { background-color: rgba(239,68,68,0.1); color: #f87171; }
        .nav-logout:hover .nav-icon { color: #f87171; }
        .section-label {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #475569;
            padding: 0 14px;
            margin-top: 20px;
            margin-bottom: 4px;
            display: block;
        }

        /* Custom scrollbar for sidebar */
        #sidebar-nav::-webkit-scrollbar { width: 3px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        /* Pagination styling */
        .pagination { display: flex; gap: 4px; align-items: center; }
        .pagination span, .pagination a {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            border: 1px solid #e5e7eb;
            color: #374151;
            text-decoration: none;
        }
        .pagination a:hover { background: #f0fdfa; border-color: #0d9488; color: #0d9488; }
        .pagination span[aria-current] { background: #0d9488; border-color: #0d9488; color: #fff; }
        .pagination span.disabled { color: #d1d5db; }
    </style>
</head>
<body style="background:#f1f5f9; min-height:100vh; display:flex;">

    {{-- ═══════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════ --}}
    <aside id="sidebar" style="width:248px; background:#0f172a; flex-shrink:0; position:fixed; top:0; left:0; height:100vh; z-index:50; display:flex; flex-direction:column; box-shadow: 4px 0 24px rgba(0,0,0,0.25);">

        {{-- Brand --}}
        <div style="padding: 20px 18px 16px; border-bottom: 1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:12px;">
            <div style="width:38px; height:38px; background:linear-gradient(135deg,#0d9488,#0f766e); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 12px rgba(13,148,136,0.4);">
                <i class="fas fa-landmark" style="color:#fff; font-size:16px;"></i>
            </div>
            <div style="line-height:1.2; overflow:hidden;">
                <div style="color:#f8fafc; font-weight:700; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Samity Management</div>
                <div style="color:#64748b; font-size:0.72rem; margin-top:1px;">{{ __('Cooperative Platform') }}</div>
            </div>
            <button onclick="toggleSidebar()" style="margin-left:auto; background:none; border:none; color:#475569; cursor:pointer; font-size:14px; padding:4px; display:none;" id="close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav id="sidebar-nav" style="flex:1; overflow-y:auto; padding:10px 10px 0;">

            <span class="section-label">{{ __('Main') }}</span>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-gauge-high nav-icon"></i> {{ __('Dashboard') }}
            </a>

            <span class="section-label">{{ __('Financial') }}</span>
            <a href="{{ route('samities.index') }}" class="nav-link {{ request()->routeIs('samities.*') ? 'active' : '' }}">
                <i class="fas fa-people-group nav-icon"></i> {{ __('Samities') }}
            </a>
            <a href="{{ route('deposits.index') }}" class="nav-link {{ request()->routeIs('deposits.*') ? 'active' : '' }}">
                <i class="fas fa-piggy-bank nav-icon"></i> {{ __('Deposits') }}
            </a>
            <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-dollar nav-icon"></i> {{ __('Loans') }}
            </a>
            <a href="{{ route('repayments.index') }}" class="nav-link {{ request()->routeIs('repayments.*') ? 'active' : '' }}">
                <i class="fas fa-rotate-left nav-icon"></i> {{ __('Repayments') }}
            </a>
            <a href="{{ route('fines.index') }}" class="nav-link {{ request()->routeIs('fines.*') ? 'active' : '' }}">
                <i class="fas fa-triangle-exclamation nav-icon"></i> {{ __('Fines') }}
            </a>

            <span class="section-label">{{ __('Management') }}</span>
            <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                <i class="fas fa-users nav-icon"></i> {{ __('Members') }}
            </a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar nav-icon"></i> {{ __('Reports') }}
            </a>

        </nav>

        {{-- Sidebar Footer — user info only --}}
        <div style="padding:10px; border-top:1px solid rgba(255,255,255,0.06);">
            <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:rgba(255,255,255,0.04); border-radius:10px;">
                <div style="width:34px; height:34px; background:linear-gradient(135deg,#0d9488,#0f766e); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.85rem; flex-shrink:0; box-shadow:0 2px 8px rgba(13,148,136,0.35);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow:hidden; line-height:1.35; flex:1; min-width:0;">
                    <div style="color:#e2e8f0; font-size:0.82rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="font-size:0.68rem; margin-top:1px;">
                        <span style="display:inline-block; background:{{ auth()->user()->role === 'admin' ? 'rgba(147,51,234,0.2)' : 'rgba(13,148,136,0.2)' }}; color:{{ auth()->user()->role === 'admin' ? '#c084fc' : '#5eead4' }}; padding:1px 7px; border-radius:20px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase;">
                            {{ ucfirst(auth()->user()->role ?? 'member') }}
                        </span>
                    </div>
                </div>
                {{-- Quick logout icon --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Sign Out" style="background:none; border:none; cursor:pointer; color:#475569; padding:5px; border-radius:6px; transition:all 0.15s; flex-shrink:0;" onmouseover="this.style.color='#f87171'; this.style.background='rgba(239,68,68,0.1)';" onmouseout="this.style.color='#475569'; this.style.background='none';">
                        <i class="fas fa-right-from-bracket" style="font-size:14px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div id="overlay" onclick="toggleSidebar()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:40; backdrop-filter:blur(2px);"></div>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════ --}}
    <div style="flex:1; display:flex; flex-direction:column; min-height:100vh; margin-left:248px; transition:margin-left 0.28s cubic-bezier(.4,0,.2,1);" id="main-content">

        {{-- Top Bar --}}
        <header style="background:#fff; border-bottom:1px solid #e2e8f0; position:sticky; top:0; z-index:30; height:56px; display:flex; align-items:center; padding:0 20px; gap:16px; box-shadow:0 1px 8px rgba(0,0,0,0.06);">
            {{-- Hamburger --}}
            <button onclick="toggleSidebar()" id="hamburger-btn" style="background:none; border:none; cursor:pointer; color:#64748b; font-size:18px; padding:4px; display:none; line-height:1;">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Breadcrumb --}}
            <div style="display:flex; align-items:center; gap:6px; font-size:0.82rem;">
                <span style="color:#94a3b8;">
                    <i class="fas fa-landmark" style="font-size:11px;"></i>
                    Samity
                </span>
                <span style="color:#cbd5e1;">/</span>
                <span style="color:#1e293b; font-weight:600;">@yield('title', 'Dashboard')</span>
            </div>

            {{-- Right side --}}
            <div style="margin-left:auto; display:flex; align-items:center; gap:10px;">

                {{-- Profile dropdown trigger --}}
                <div style="position:relative;" id="profile-wrapper">
                    <button id="profile-btn" onclick="toggleProfileMenu()" style="display:flex; align-items:center; gap:9px; background:none; border:1px solid transparent; border-radius:40px; padding:4px 10px 4px 4px; cursor:pointer; transition:all 0.18s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';" onmouseout="if(!document.getElementById('profile-menu').classList.contains('open')){this.style.background='none'; this.style.borderColor='transparent';}">
                        {{-- Avatar --}}
                        <div style="width:32px; height:32px; background:linear-gradient(135deg,#0d9488,#0f766e); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.83rem; flex-shrink:0; box-shadow:0 2px 6px rgba(13,148,136,0.35);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        {{-- Name + role (hidden on very small screens) --}}
                        <div style="text-align:left; line-height:1.2; display:none;" class="sm-show">
                            <div style="font-size:0.8rem; font-weight:700; color:#1e293b; white-space:nowrap; max-width:110px; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.68rem; color:#94a3b8;">{{ ucfirst(auth()->user()->role ?? 'member') }}</div>
                        </div>
                        {{-- Language badge --}}
                        <span style="font-size:0.65rem; font-weight:700; padding:1px 6px; border-radius:10px; background:#f0fdfa; color:#0d9488; border:1px solid #99f6e4; letter-spacing:0.04em; flex-shrink:0;">
                            {{ app()->getLocale() === 'bn' ? 'বাং' : 'EN' }}
                        </span>
                        <i class="fas fa-chevron-down" id="profile-chevron" style="font-size:10px; color:#94a3b8; transition:transform 0.2s; display:none;" class="sm-show"></i>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div id="profile-menu" style="display:none; position:absolute; top:calc(100% + 10px); right:0; width:232px; background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 12px 40px rgba(15,23,42,0.14), 0 2px 8px rgba(0,0,0,0.06); z-index:200; overflow:hidden; transform-origin:top right; opacity:0; transform:scale(0.94) translateY(-6px); transition:opacity 0.18s ease, transform 0.18s ease;">

                        {{-- User Info Header --}}
                        <div style="padding:14px 16px; background:linear-gradient(135deg,#f0fdfa,#f8fafc); border-bottom:1px solid #f1f5f9;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:40px; height:40px; background:linear-gradient(135deg,#0d9488,#0f766e); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:1rem; flex-shrink:0; box-shadow:0 2px 8px rgba(13,148,136,0.3);">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div style="overflow:hidden;">
                                    <div style="font-size:0.85rem; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                                    <div style="font-size:0.72rem; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div style="margin-top:10px;">
                                <span style="font-size:0.68rem; font-weight:700; padding:2px 10px; border-radius:20px; background:{{ auth()->user()->role === 'admin' ? '#faf5ff' : '#f0fdfa' }}; color:{{ auth()->user()->role === 'admin' ? '#9333ea' : '#0d9488' }}; border:1px solid {{ auth()->user()->role === 'admin' ? '#e9d5ff' : '#99f6e4' }}; text-transform:uppercase; letter-spacing:0.05em;">
                                    {{ ucfirst(auth()->user()->role ?? 'member') }}
                                </span>
                            </div>
                        </div>

                        {{-- Menu Items --}}
                        <div style="padding:6px;">

                            {{-- Change Password --}}
                            <a href="{{ route('change-password') }}" style="display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:8px; text-decoration:none; color:#374151; font-size:0.83rem; font-weight:500; transition:background 0.12s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <div style="width:28px; height:28px; border-radius:8px; background:#faf5ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="fas fa-key" style="font-size:11px; color:#9333ea;"></i>
                                </div>
                                {{ __('Change Password') }}
                            </a>

                            {{-- Language — single pill toggle --}}
                            @php $isEn = app()->getLocale() === 'en'; @endphp
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:9px 12px; border-radius:8px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:28px; height:28px; border-radius:8px; background:#f0fdfa; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fas fa-globe" style="font-size:11px; color:#0d9488;"></i>
                                    </div>
                                    <span style="font-size:0.83rem; font-weight:600; color:#374151;">{{ __('Language') }}</span>
                                </div>
                                {{-- Pill toggle --}}
                                <div style="display:flex; background:#f1f5f9; border-radius:20px; padding:3px; gap:0; border:1px solid #e2e8f0;">
                                    <a href="{{ route('language.switch', 'en') }}"
                                       style="padding:4px 11px; border-radius:16px; font-size:0.73rem; font-weight:700; text-decoration:none; transition:all 0.2s; white-space:nowrap;
                                              {{ $isEn ? 'background:#0d9488; color:#fff; box-shadow:0 1px 4px rgba(13,148,136,0.35);' : 'color:#94a3b8;' }}">
                                        EN
                                    </a>
                                    <a href="{{ route('language.switch', 'bn') }}"
                                       style="padding:4px 11px; border-radius:16px; font-size:0.73rem; font-weight:700; text-decoration:none; transition:all 0.2s; white-space:nowrap;
                                              {{ !$isEn ? 'background:#0d9488; color:#fff; box-shadow:0 1px 4px rgba(13,148,136,0.35);' : 'color:#94a3b8;' }}">
                                        বাংলা
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- Divider --}}
                        <div style="height:1px; background:#f1f5f9; margin:2px 0;"></div>

                        {{-- Sign Out --}}
                        <div style="padding:6px;">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" style="width:100%; display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:8px; border:none; background:transparent; cursor:pointer; color:#ef4444; font-size:0.83rem; font-weight:500; text-align:left; transition:background 0.12s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                    <div style="width:28px; height:28px; border-radius:8px; background:#fef2f2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fas fa-right-from-bracket" style="font-size:11px; color:#ef4444;"></i>
                                    </div>
                                    {{ __('Sign Out') }}
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </header>

        {{-- Page Content --}}
        <main style="flex:1; padding:24px 24px 16px;">

            @if(session('success'))
                <div style="display:flex; align-items:center; gap:12px; background:#f0fdf4; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:0.85rem;">
                    <i class="fas fa-circle-check" style="color:#22c55e; flex-shrink:0;"></i>
                    <span style="flex:1;">{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#86efac; cursor:pointer; font-size:13px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div style="display:flex; align-items:center; gap:12px; background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:0.85rem;">
                    <i class="fas fa-circle-exclamation" style="color:#ef4444; flex-shrink:0;"></i>
                    <span style="flex:1;">{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#fca5a5; cursor:pointer; font-size:13px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer style="text-align:center; font-size:0.75rem; color:#94a3b8; padding:14px; border-top:1px solid #e2e8f0; background:#fff;">
            &copy; {{ date('Y') }} Samity Management System &mdash; All rights reserved.
        </footer>
    </div>

    <style>
        @media (max-width: 1023px) {
            #main-content { margin-left: 0 !important; }
            #hamburger-btn { display: block !important; }
            #close-btn { display: block !important; }
            .sm-show { display: none !important; }
        }
        @media (min-width: 640px) {
            .sm-show { display: inline-block !important; }
        }
    </style>

    <style>
        /* Profile dropdown open state */
        #profile-menu.open {
            display: block !important;
            opacity: 1 !important;
            transform: scale(1) translateY(0) !important;
        }
    </style>

    <script>
        /* ── Profile Dropdown ── */
        function toggleProfileMenu() {
            const menu    = document.getElementById('profile-menu');
            const btn     = document.getElementById('profile-btn');
            const chevron = document.getElementById('profile-chevron');
            const isOpen  = menu.classList.contains('open');

            if (isOpen) {
                menu.classList.remove('open');
                btn.style.background    = 'none';
                btn.style.borderColor   = 'transparent';
                chevron.style.transform = 'rotate(0deg)';
            } else {
                menu.style.display = 'block';
                // Force reflow so transition plays
                menu.offsetHeight;
                menu.classList.add('open');
                btn.style.background  = '#f8fafc';
                btn.style.borderColor = '#e2e8f0';
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        // Close on outside click
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('profile-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const menu    = document.getElementById('profile-menu');
                const btn     = document.getElementById('profile-btn');
                const chevron = document.getElementById('profile-chevron');
                if (menu) {
                    menu.classList.remove('open');
                    setTimeout(() => { if (!menu.classList.contains('open')) menu.style.display = 'none'; }, 180);
                }
                if (btn)     { btn.style.background = 'none'; btn.style.borderColor = 'transparent'; }
                if (chevron) { chevron.style.transform = 'rotate(0deg)'; }
            }
        });

        let sidebarOpen = window.innerWidth >= 1024;

        function toggleSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('overlay');
            sidebarOpen    = !sidebarOpen;

            if (sidebarOpen) {
                sidebar.classList.remove('sidebar-hidden');
                overlay.style.display = 'block';
            } else {
                sidebar.classList.add('sidebar-hidden');
                overlay.style.display = 'none';
            }
        }

        // Initialize on mobile
        if (window.innerWidth < 1024) {
            document.getElementById('sidebar').classList.add('sidebar-hidden');
            sidebarOpen = false;
        }

        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('sidebar-hidden');
                overlay.style.display = 'none';
                sidebarOpen = true;
            } else if (!sidebarOpen) {
                sidebar.classList.add('sidebar-hidden');
            }
        });
    </script>

    <script>
    /* ── Global Modal Helpers ── */
    function openModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
    // Close modal on backdrop click
    document.addEventListener('click', function (e) {
        if (e.target.classList && e.target.classList.contains('sm-modal')) {
            e.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.sm-modal').forEach(m => {
                m.style.display = 'none';
            });
            document.body.style.overflow = '';
        }
    });
    /* ── Global Delete Helper ── */
    function confirmDelete(url, label) {
        document.getElementById('delete-label').textContent = label || 'this record';
        document.getElementById('delete-form').action = url;
        openModal('modal-delete-global');
    }
    </script>

    {{-- Global Delete Confirmation Modal --}}
    <div id="modal-delete-global" class="sm-modal" style="display:none; position:fixed; inset:0; z-index:300; background:rgba(15,23,42,0.65); align-items:center; justify-content:center; backdrop-filter:blur(3px);">
        <div style="background:#fff; border-radius:16px; width:380px; max-width:95vw; box-shadow:0 25px 60px rgba(0,0,0,0.3); overflow:hidden;">
            <div style="padding:28px 28px 20px; text-align:center;">
                <div style="width:56px; height:56px; background:#fef2f2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <i class="fas fa-trash-can" style="color:#ef4444; font-size:1.4rem;"></i>
                </div>
                <h3 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 8px;">{{ __('Delete Confirmation') }}</h3>
                <p style="font-size:0.85rem; color:#64748b; margin:0;">{{ __('Are you sure you want to delete') }} <strong id="delete-label"></strong>? {{ __('This action cannot be undone.') }}</p>
            </div>
            <div style="display:flex; gap:10px; padding:0 24px 24px;">
                <button type="button" onclick="closeModal('modal-delete-global')"
                    style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:10px; background:#f8fafc; color:#64748b; font-size:0.85rem; font-weight:600; cursor:pointer;">
                    {{ __('Cancel') }}
                </button>
                <form id="delete-form" method="POST" style="flex:1; margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        style="width:100%; padding:10px; border:none; border-radius:10px; background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; font-size:0.85rem; font-weight:600; cursor:pointer; box-shadow:0 2px 8px rgba(239,68,68,0.35);">
                        {{ __('Yes, Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
