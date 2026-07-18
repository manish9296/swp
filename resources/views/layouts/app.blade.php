<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           PM-KUSUM design tokens — bright multi-color KPI dashboard
           style: white chrome, vivid blue "chart header" bars, and a
           rotating rainbow border palette for stat cards.
           ============================================================ */
        :root {
            --ink:        #1E293B;
            --muted:      #7C8A9A;
            --bg:         #F1F5FB;
            --surface:    #FFFFFF;
            --border:     #E6ECF3;

            --ui-blue:      #2F6FE0;
            --ui-blue-dark: #2558B8;
            --ui-blue-tint: #EAF1FE;

            /* Rainbow palette for KPI card borders/icons (cycled per card) */
            --c-green:  #16A34A;  --c-green-tint:  #E9F8EF;
            --c-orange: #F97316;  --c-orange-tint: #FFF1E6;
            --c-red:    #EF4444;  --c-red-tint:    #FDECEC;
            --c-purple: #8B5CF6;  --c-purple-tint: #F1ECFE;
            --c-teal:   #06B6D4;  --c-teal-tint:   #E6F9FC;
            --c-yellow: #EAB308;  --c-yellow-tint: #FEF8E1;
            --c-pink:   #EC4899;  --c-pink-tint:   #FDEBF3;
            --c-blue:   #2F6FE0;  --c-blue-tint:   #EAF1FE;

            --radius-lg: 14px;
            --radius-md: 10px;
            --radius-sm: 8px;
            --shadow-xs: 0 1px 2px rgba(30,41,59,.05);
            --shadow-md: 0 10px 26px -14px rgba(30,41,59,.25);
        }

        body {
            background-color: var(--bg);
            color: var(--ink);
            font-family: 'IBM Plex Sans', system-ui, sans-serif;
            font-size: .95rem;
        }

        h1, h2, h3, h4, h5, h6, .brand-word { font-family: 'Sora', system-ui, sans-serif; letter-spacing: -0.01em; }
        table, .table { font-variant-numeric: tabular-nums; }
        a { color: var(--ui-blue); }
        a:hover { color: var(--ui-blue-dark); }

        .page-header { margin-bottom: 1.25rem; }
        .page-header h4 { font-weight: 700; margin-bottom: .15rem; }
        .page-header .subtext { color: var(--muted); font-size: .85rem; }

        /* ---- Top bar: plain white, hamburger + bell + avatar ---- */
        .pk-topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-xs);
        }
        .pk-icon-btn {
            width: 38px; height: 38px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--bg); color: var(--ink); border: none; position: relative;
        }
        .pk-icon-btn:hover { background: var(--ui-blue-tint); color: var(--ui-blue); }
        .pk-notif-dot {
            position: absolute; top: 4px; right: 5px;
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--c-red); border: 2px solid #fff;
        }
        .pk-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--ui-blue); color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: .8rem;
        }

        /* ---- Sidebar brand ---- */
        .sidebar-brand {
            display: flex; align-items: center; gap: .6rem;
            padding: 1rem 1.1rem; border-bottom: 1px solid var(--border);
        }
        .pk-logomark {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--c-red); color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand .word { font-size: 1rem; font-weight: 700; line-height: 1.1; }
        .sidebar-brand .sub { font-size: .68rem; color: var(--muted); }

        /* ---- Layout shell ---- */
        .app-shell { display: flex; min-height: calc(100vh - 62px); }

        .sidebar {
            width: 264px;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: var(--ink);
            border-radius: var(--radius-sm);
            padding: .55rem .75rem;
            margin-bottom: .15rem;
            font-size: .88rem;
            display: flex; align-items: center; gap: .6rem;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link svg { flex-shrink: 0; opacity: .65; }
        .sidebar .nav-link:hover { background-color: var(--ui-blue-tint); }
        .sidebar .nav-link.active {
            background-color: var(--ui-blue-tint);
            color: var(--ui-blue-dark);
            border-left-color: var(--ui-blue);
            font-weight: 600;
        }
        .sidebar .nav-link.active svg { opacity: 1; }
        .sidebar-eyebrow {
            font-size: .7rem; font-weight: 600; letter-spacing: .06em;
            text-transform: uppercase; color: var(--muted);
        }

        .main-content { flex-grow: 1; min-width: 0; padding: 1.25rem; }

        @media (max-width: 991.98px) {
            .sidebar.d-lg-block { display: none !important; }
            .main-content { padding: 1rem; }
        }

        /* ---- Cards ---- */
        .card { border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-xs); }
        .card .card-body { padding: 1.25rem; }

        /* ---- KPI stat card: colored border + circular tinted icon ---- */
        .stat-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1rem 1.1rem;
            height: 100%;
            box-shadow: var(--shadow-xs);
            display: flex; align-items: center; gap: .9rem;
        }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-body { min-width: 0; flex: 1 1 auto; }
        .stat-value { font-family: 'Sora', sans-serif; font-size: 1.3rem; font-weight: 700; line-height: 1.1; overflow-wrap: break-word; }
        .stat-label { color: var(--muted); font-size: .78rem; margin-bottom: .1rem; overflow-wrap: break-word; }
        .stat-sub { color: var(--muted); font-size: .72rem; margin-top: .1rem; overflow-wrap: break-word; }

        .stat-card.b-green  { border-color: var(--c-green); }  .stat-card.b-green  .stat-icon { background: var(--c-green-tint);  color: var(--c-green); }
        .stat-card.b-orange { border-color: var(--c-orange); } .stat-card.b-orange .stat-icon { background: var(--c-orange-tint); color: var(--c-orange); }
        .stat-card.b-red    { border-color: var(--c-red); }    .stat-card.b-red    .stat-icon { background: var(--c-red-tint);    color: var(--c-red); }
        .stat-card.b-purple { border-color: var(--c-purple); } .stat-card.b-purple .stat-icon { background: var(--c-purple-tint); color: var(--c-purple); }
        .stat-card.b-teal   { border-color: var(--c-teal); }   .stat-card.b-teal   .stat-icon { background: var(--c-teal-tint);   color: var(--c-teal); }
        .stat-card.b-yellow { border-color: var(--c-yellow); } .stat-card.b-yellow .stat-icon { background: var(--c-yellow-tint); color: var(--c-yellow); }
        .stat-card.b-pink   { border-color: var(--c-pink); }   .stat-card.b-pink   .stat-icon { background: var(--c-pink-tint);   color: var(--c-pink); }
        .stat-card.b-blue   { border-color: var(--c-blue); }   .stat-card.b-blue   .stat-icon { background: var(--c-blue-tint);   color: var(--c-blue); }

        /* ---- Chart card with solid blue header bar ---- */
        .chart-card { padding: 0; overflow: hidden; }
        .chart-card-header {
            background: var(--ui-blue);
            color: #fff;
            padding: .8rem 1.1rem;
            display: flex; align-items: center; gap: .6rem;
            font-family: 'Sora', sans-serif; font-weight: 600; font-size: .95rem;
        }
        .chart-card-header .icon-chip {
            width: 26px; height: 26px; border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: inline-flex; align-items: center; justify-content: center;
        }
        .chart-card-body { padding: 1.25rem; }

        /* ---- Tabs (pill style, blue active) ---- */
        .pk-tabs .nav-link {
            border-radius: var(--radius-sm);
            color: var(--muted); font-weight: 600; font-size: .85rem;
            padding: .5rem 1rem; border: none;
        }
        .pk-tabs .nav-link.active { background: var(--ui-blue); color: #fff; }

        /* ---- Buttons ---- */
        .btn { border-radius: var(--radius-sm); font-weight: 500; }
        .btn-sm { border-radius: 7px; }
        .btn-success {
            --bs-btn-bg: var(--ui-blue); --bs-btn-border-color: var(--ui-blue);
            --bs-btn-hover-bg: var(--ui-blue-dark); --bs-btn-hover-border-color: var(--ui-blue-dark);
            --bs-btn-active-bg: var(--ui-blue-dark); --bs-btn-active-border-color: var(--ui-blue-dark);
            box-shadow: 0 6px 16px -8px rgba(47,111,224,.55);
        }
        .btn-outline-success {
            --bs-btn-color: var(--ui-blue); --bs-btn-border-color: var(--ui-blue);
            --bs-btn-hover-bg: var(--ui-blue); --bs-btn-hover-border-color: var(--ui-blue);
        }
        .btn-outline-primary {
            --bs-btn-color: var(--c-teal); --bs-btn-border-color: var(--c-teal);
            --bs-btn-hover-bg: var(--c-teal); --bs-btn-hover-border-color: var(--c-teal);
        }
        .btn-outline-secondary {
            --bs-btn-color: var(--muted); --bs-btn-border-color: var(--border);
            --bs-btn-hover-bg: var(--muted); --bs-btn-hover-border-color: var(--muted);
        }
        .btn-outline-dark {
            --bs-btn-color: var(--ink); --bs-btn-border-color: var(--ink);
            --bs-btn-hover-bg: var(--ink); --bs-btn-hover-border-color: var(--ink);
        }

        /* ---- Tables ---- */
        .table-responsive { border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); }
        .table { margin-bottom: 0; }
        .table thead th {
            background: var(--ui-blue) !important;
            color: #fff !important;
            font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .04em;
            border: none !important;
            padding: .8rem 1rem;
        }
        .table td { padding: .7rem 1rem; vertical-align: middle; border-color: var(--border); }
        .table tbody tr:hover { background-color: var(--ui-blue-tint); }

        /* ---- Status badges ---- */
        .badge { font-weight: 600; font-size: .72rem; padding: .4em .75em; border-radius: 999px; }
        .badge-Installed { background-color: var(--c-green-tint) !important; color: var(--c-green) !important; }
        .badge-Pending, .badge-Applied, .badge-Approved { background-color: var(--c-yellow-tint) !important; color: #9A7207 !important; }
        .badge-Under_Verification, .badge-Pending_Commissioning { background-color: var(--c-teal-tint) !important; color: #0A7C93 !important; }
        .badge-Rejected { background-color: var(--c-red-tint) !important; color: var(--c-red) !important; }
        .bg-success { background-color: var(--c-green-tint) !important; color: var(--c-green) !important; }
        .bg-danger { background-color: var(--c-red-tint) !important; color: var(--c-red) !important; }

        .alert-success { background-color: var(--ui-blue-tint); border-color: var(--ui-blue); color: var(--ui-blue-dark); border-radius: var(--radius-md); }

        .form-select, .form-control { border-radius: var(--radius-sm); border-color: var(--border); }
        .form-select:focus, .form-control:focus { border-color: var(--ui-blue); box-shadow: 0 0 0 .2rem rgba(47,111,224,.12); }

        /* ---- No animations anywhere (incl. Bootstrap's own fade /
               collapse / offcanvas transitions) — everything shows and
               hides instantly. ---- */
        *, *::before, *::after {
            transition: none !important;
            animation: none !important;
        }
    </style>
</head>
<body>
    <nav class="navbar pk-topbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <button class="pk-icon-btn d-lg-none me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Toggle menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <button class="pk-icon-btn d-none d-lg-inline-flex me-2" type="button" aria-label="Toggle sidebar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <div class="d-flex ms-auto align-items-center gap-2">
                {{-- <a href="{{ route('generation.summary') }}" class="btn btn-success btn-sm me-2 d-none d-md-inline-block">Generation Summary</a> --}}
                <button class="pk-icon-btn" type="button" aria-label="Notifications">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="pk-notif-dot"></span>
                </button>
                <span class="pk-avatar">PK</span>
            </div>
        </div>
    </nav>

    {{-- Offcanvas sidebar (mobile / tablet) --}}
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title brand-word mb-0"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('partials.sidebar')
        </div>
    </div>

    <div class="app-shell">
        {{-- Static sidebar (desktop / large screens) --}}
        <div class="sidebar d-none d-lg-block">
            <div class="sidebar-brand">
                <span class="pk-logomark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="4"/>
                        <line x1="12" y1="2" x2="12" y2="4.5"/><line x1="12" y1="19.5" x2="12" y2="22"/>
                        <line x1="2" y1="12" x2="4.5" y2="12"/><line x1="19.5" y1="12" x2="22" y2="12"/>
                        <line x1="4.9" y1="4.9" x2="6.6" y2="6.6"/><line x1="17.4" y1="17.4" x2="19.1" y2="19.1"/>
                        <line x1="4.9" y1="19.1" x2="6.6" y2="17.4"/><line x1="17.4" y1="6.6" x2="19.1" y2="4.9"/>
                    </svg>
                </span>
                <span>
                    <span class="word d-block">KLK Ventures</span>
                    <span class="sub d-block">Dashboard</span>
                </span>
            </div>
            @include('partials.sidebar')
        </div>

        <div class="main-content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')

                <div class="text-center text-muted small py-4">
                    Copyright &copy; KLK Ventures. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
