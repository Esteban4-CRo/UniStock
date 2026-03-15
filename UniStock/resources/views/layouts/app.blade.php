<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UniStock')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>
        :root {
            --primary: #0b0b0b;
            --accent: #1abc9c;
            --accent-light: #eafaf6;
            --bg: #f4f6f9;
            --card-bg: #ffffff;
            --text: #1a1a2e;
            --muted: #6c757d;
            --border: #e8ecf0;
            --nav-h: 62px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── NAV ─────────────────────────────── */
        .navbar {
            background: #000;
            padding: 0 1.25rem;
            height: var(--nav-h);
            box-shadow: 0 2px 16px rgba(0,0,0,0.18);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: 800;
            font-size: 1.45rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand .brand-icon {
            width: 34px; height: 34px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.82) !important;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.5rem 0.7rem !important;
            border-radius: 7px;
            transition: all .2s;
            display: flex; align-items: center; gap: 0.35rem;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.1);
        }
        .navbar-nav .nav-link.active { background: var(--accent) !important; color: #fff !important; }

        .navbar-toggler { border-color: rgba(255,255,255,0.3); }
        .navbar-toggler-icon { filter: invert(1); }

        /* Mobile nav */
        @media (max-width: 991px) {
            #navbarNav {
                background: #111;
                border-radius: 0 0 16px 16px;
                padding: 0.75rem 1rem 1rem;
                border-top: 1px solid #222;
                animation: slideDown .2s ease;
            }
            @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
            .navbar-nav { gap: 0.2rem; }
            .navbar-nav .nav-link { font-size: 0.95rem; padding: 0.65rem 1rem !important; }
        }

        .dropdown-menu {
            background: #0d0d0d;
            border: 1px solid #222;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            padding: 0.5rem;
        }
        .dropdown-menu .dropdown-item { color: #eee; border-radius: 8px; padding: 0.55rem 1rem; font-size: 0.9rem; transition: .2s; }
        .dropdown-menu .dropdown-item:hover { background: #1a1a1a; color: #fff; }
        .dropdown-divider { border-color: #333; }

        .user-avatar { width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--accent); }
        .avatar-placeholder {
            width:34px; height:34px; border-radius:50%;
            background: var(--accent); color:#fff;
            display:inline-flex; align-items:center; justify-content:center;
            font-size:.9rem; margin-right:.35rem;
        }

        /* ── MAIN ─────────────────────────────── */
        main {
            padding: 1.75rem 0 3rem;
            min-height: calc(100vh - var(--nav-h));
        }

        .container { max-width: 1200px; }

        /* ── CARDS ────────────────────────────── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: box-shadow .25s;
        }
        .card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.09); }
        .card-header {
            background: #fafafa;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            border-radius: 14px 14px 0 0 !important;
            padding: 1rem 1.25rem;
        }
        .card-body { padding: 1.4rem; }

        /* ── STAT CARDS ───────────────────────── */
        .stat-card {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 1.4rem 1.6rem;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1.1rem;
            transition: transform .25s, box-shadow .25s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .stat-icon {
            width: 54px; height: 54px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-icon.green { background: #eafaf6; color: var(--accent); }
        .stat-icon.blue  { background: #e8f4fd; color: #3498db; }
        .stat-icon.red   { background: #fdecea; color: #e74c3c; }
        .stat-icon.purple{ background: #f0ebff; color: #8e44ad; }
        .stat-number { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-label  { font-size: 0.82rem; color: var(--muted); margin-top: 2px; }

        /* ── BUTTONS ──────────────────────────── */
        .btn { border-radius: 9px; font-weight: 600; font-size: 0.88rem; transition: all .2s; }
        .btn-primary { background: #0b0b0b; color: #fff; border: none; }
        .btn-primary:hover { background: #222; }
        .btn-success { background: var(--accent); color: #fff; border: none; }
        .btn-success:hover { background: #16a085; }
        .btn-danger { background: #e74c3c; color:#fff; border: none; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #f4f4f4; color: #333; border: 1px solid var(--border); }
        .btn-secondary:hover { background: #e8e8e8; color: #000; }
        .btn-outline-secondary { border-color: var(--border); color: var(--muted); }

        /* ── TABLES ───────────────────────────── */
        .table { font-size: 0.9rem; }
        .table thead th {
            background: #f7f8fa;
            color: #555;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
            padding: 0.85rem 1rem;
        }
        .table tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-color: var(--border); }
        .table-hover tbody tr:hover { background: #fafbfc; }
        .table-responsive { border-radius: 12px; overflow: hidden; }

        /* ── FORMS ────────────────────────────── */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 0.93rem;
            padding: 0.65rem 0.9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(26,188,156,.12);
        }
        .form-label { font-weight: 600; font-size: 0.88rem; color: #444; margin-bottom: .4rem; }
        .input-group-text { border-radius: 9px 0 0 9px; background: #f4f4f4; border: 1.5px solid var(--border); }

        /* ── BADGES ───────────────────────────── */
        .badge { padding: 0.4rem 0.8rem; border-radius: 999px; font-weight: 600; font-size: 0.76rem; }

        /* ── ALERTS ───────────────────────────── */
        .alert { border-radius: 12px; border: none; font-size: 0.9rem; }
        .alert-success { background: #eafaf6; color: #0e7c5e; }
        .alert-danger  { background: #fdecea; color: #a93226; }
        .alert-warning { background: #fef9e7; color: #b7770d; }

        /* ── PAGE HEADER ──────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .page-header h1, .page-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
        }

        /* ── FOOTER ───────────────────────────── */
        footer {
            background: #000;
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 1.2rem;
            font-size: 0.8rem;
        }

        /* ── MOBILE ADJUSTMENTS ───────────────── */
        @media (max-width: 768px) {
            main { padding: 1.2rem 0 2rem; }
            .container { padding: 0 1rem; }
            .card-body { padding: 1rem; }
            .stat-card { padding: 1rem 1.1rem; }
            .stat-number { font-size: 1.6rem; }
            .page-header h1, .page-header h2 { font-size: 1.25rem; }
            .btn { font-size: 0.84rem; padding: 0.5rem 0.9rem; }
            .table { font-size: 0.82rem; }
            .table thead th, .table tbody td { padding: 0.65rem 0.7rem; }
            /* Stack stat cards on very small screens */
            .stats-row { gap: 0.75rem !important; }
            .stats-row > div { min-width: 0; }
        }

        @media (max-width: 480px) {
            .page-header { flex-direction: column; align-items: flex-start; }
            .page-header .btn { width: 100%; text-align: center; }
        }

        /* ── MISC ─────────────────────────────── */
        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.35; }

        .text-accent { color: var(--accent); }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid" style="max-width:1300px;margin:0 auto;">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-icon"><i class="fas fa-cube"></i></span>
                UniStock
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                        </li>
                        @if(Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Registrarse</a>
                            </li>
                        @endif
                    @endguest

                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                                <i class="fas fa-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}" href="{{ route('entradas.index') }}">
                                <i class="fas fa-arrow-circle-down"></i> Entradas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('salidas.*') ? 'active' : '' }}" href="{{ route('salidas.index') }}">
                                <i class="fas fa-arrow-circle-up"></i> Salidas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                <i class="fas fa-file-pdf"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                @if(Auth::user()->photo)
                                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" class="user-avatar">
                                @else
                                    <span class="avatar-placeholder"><i class="fas fa-user" style="font-size:.75rem;"></i></span>
                                @endif
                                <span class="d-none d-lg-inline" style="font-size:.87rem;font-weight:600;">{{ Str::limit(Auth::user()->name, 14) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <div style="padding:.6rem 1rem .4rem; font-size:.82rem; color:#888;">
                                        {{ Auth::user()->email }}
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i> Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container px-3 px-md-4">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <strong>Verifica los siguientes errores:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer>
        <i class="fas fa-cube me-1"></i> UniStock &copy; {{ date('Y') }} — Sistema de Gestión de Inventario
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
