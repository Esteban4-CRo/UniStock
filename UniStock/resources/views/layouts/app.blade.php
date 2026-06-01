@php
    $alertasNotif = [];
    if (Auth::check()) {
        try {
            $cacheKey = 'alertas_notif_' . Auth::id();
            $alertasNotif = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () {
                $rows = \App\Models\Alerta::where('user_id', Auth::id())
                    ->where('estado', 'activa')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();

                $items = [];
                foreach ($rows as $alerta) {
                    $icono = 'fas fa-bell';
                    $nivel = 'info';
                    $titulo = 'Notificación';

                    if ($alerta->tipo === 'stock_bajo') {
                        $icono = 'fas fa-exclamation-triangle';
                        $nivel = 'danger';
                        $titulo = 'Alerta de Stock';
                    } elseif ($alerta->tipo === 'vencimiento') {
                        $icono = 'fas fa-hourglass-half';
                        $nivel = 'warning';
                        $titulo = 'Vencimiento';
                    }

                    $items[] = [
                        'id' => $alerta->id,
                        'tipo' => $alerta->tipo,
                        'nivel' => $nivel,
                        'icono' => $icono,
                        'titulo' => $titulo,
                        'mensaje' => $alerta->mensaje,
                        'ruta' => '#',
                        'tiempo' => $alerta->created_at->diffForHumans(),
                    ];
                }
                return $items;
            });
        } catch (\Exception $e) {
            $alertasNotif = [];
        }
    }
@endphp
<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UniStock')</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"></noscript>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"></noscript>
    @vite(['resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --bg-main: #f0f4f8;
            --text-main: #1a1a2e;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-hover-border: #cbd5e1;
            --nav-bg: rgba(255, 255, 255, 0.95);
            --hover-bg: rgba(37, 99, 235, 0.06);
            --btn-primary-bg: #2563eb;
            --btn-primary-text: #ffffff;
            --logo-filter: none;
            --input-bg: #f8fafc;
            --shadow-color: rgba(0, 50, 100, 0.08);
            --accent: #2563eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Transiciones globales */
        .btn,
        .nav-link,
        .card,
        .form-control,
        .form-select,
        .dropdown-item {
            transition: all 0.3s ease;
        }

        .btn,
        .nav-link,
        .dropdown-item,
        .form-check-input,
        .pagination a,
        th[style*="cursor"] {
            cursor: pointer;
        }

        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--card-border);
        }

        .navbar-brand {
            color: var(--text-main) !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            max-height: 100px;
            /* Logo más grande */
            filter: var(--logo-filter);
            transition: filter 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            margin: 0 0.15rem;
            padding: 0.5rem 0.85rem !important;
            font-size: 0.92rem;
            border-radius: 8px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--text-main) !important;
            background: var(--hover-bg);
        }

        .dropdown-menu {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px var(--shadow-color);
            border-radius: 12px;
            padding: 0.5rem;
        }

        .dropdown-menu .dropdown-item {
            color: var(--text-main);
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .dropdown-menu .dropdown-item:hover {
            background: var(--hover-bg);
            color: var(--text-main);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
            transition: transform 0.3s ease;
            margin-right: 0.5rem;
            border: 2px solid var(--card-border);
            background: var(--card-bg);
        }

        /* Tarjetas */
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            color: var(--text-main);
        }

        .card:hover {
            border-color: var(--card-hover-border);
            box-shadow: 0 12px 30px var(--shadow-color);
        }

        .card-header {
            background: transparent;
            color: var(--text-main);
            border-bottom: 1px solid var(--card-border);
            font-weight: 700;
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Botones */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-primary {
            background: var(--btn-primary-bg);
            color: var(--btn-primary-text);
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--card-border);
        }

        .btn-secondary:hover {
            background: var(--hover-bg);
            border-color: var(--text-main);
        }

        .btn-danger {
            background: transparent;
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }

        .btn-danger:hover {
            background: #ff4d4d;
            color: #ffffff;
        }

        /* Tablas */
        .table {
            color: var(--text-main);
        }

        .table thead th {
            background: var(--hover-bg);
            color: var(--text-muted);
            border-bottom: 1px solid var(--card-border);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table tbody tr {
            background: transparent;
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background: var(--hover-bg);
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--card-border);
            color: var(--text-main);
        }

        /* Formulario */
        .form-control,
        .form-select {
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .form-control:focus,
        .form-select:focus {
            background: var(--input-bg);
            box-shadow: none;
            border-color: var(--text-main);
            color: var(--text-main);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge {
            background: var(--hover-bg);
            color: var(--text-main);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            border: 1px solid var(--card-border);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
        }

        main {
            padding: 2.5rem 0;
            min-height: calc(100vh - 90px);
        }

        /* Modal */
        .modal-content {
            background: var(--bg-main);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            color: var(--text-main);
        }

        .modal-header {
            border-bottom: 1px solid var(--card-border);
        }

        .modal-footer {
            border-top: 1px solid var(--card-border);
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}" />
</head>

<body>

    <nav class="navbar navbar-expand-lg sticky-top glass"
        style="z-index: 1040; border-bottom: 1px solid var(--card-border); padding: 0.5rem 1.5rem;">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}" style="padding: 0.25rem 0;">
                <img src="{{ asset('images/logo.png') }}" alt="UniStock Logo"
                    onerror="this.onerror=null; this.src=''; this.alt='UniStock'; this.style.fontSize='1.8rem'; this.style.fontWeight='800'; this.style.color='#2563eb';"
                    style="max-height: 70px; filter: var(--logo-filter); transition: transform 0.3s ease;"
                    onmouseover="this.style.transform='scale(1.05)'"
                    onmouseout="this.style.transform='scale(1)'">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('materias-primas.*') ? 'active' : '' }}"
                                href="{{ route('materias-primas.index') }}">
                                <i class="fas fa-dolly"></i> Materias Primas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}"
                                href="{{ route('proveedores.index') }}">
                                <i class="fas fa-truck"></i> Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}"
                                href="{{ route('ubicaciones.index') }}">
                                <i class="fas fa-map-marker-alt"></i> Ubicaciones
                            </a>
                        </li>
                        @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                                    href="{{ route('usuarios.index') }}">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}"
                                href="{{ route('entradas.index') }}">
                                <i class="fas fa-arrow-down"></i> Entradas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('salidas.*') ? 'active' : '' }}"
                                href="{{ route('salidas.index') }}">
                                <i class="fas fa-arrow-up"></i> Salidas
                            </a>
                        </li>
                        @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                                    href="{{ route('reportes.index') }}">
                                    <i class="fas fa-file-pdf"></i> Reportes
                                </a>
                            </li>
                        @endif
                        <!-- Notificaciones Dropdown -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative dropdown-toggle no-caret" href="#"
                                id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                style="padding-top: 0.75rem;">
                                <i class="fas fa-bell" style="font-size: 1.2rem; color: #4b5563;"></i>
                                @if(count($alertasNotif) > 0)
                                    <span
                                        class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.65rem; padding: 0.25em 0.5em; top: 8px !important; left: 16px !important;">
                                        {{ count($alertasNotif) }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border py-0"
                                aria-labelledby="notificationDropdown"
                                style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 12px; font-size: 0.85rem; background: #ffffff !important; border-color: #e5e7eb !important;">
                                <li class="dropdown-header bg-light py-2 text-dark border-bottom d-flex justify-content-between align-items-center"
                                    style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                    <span class="fw-bold"><i class="fas fa-bell me-1"></i> Notificaciones</span>
                                    <div>
                                        <span class="badge bg-secondary text-white me-1">{{ count($alertasNotif) }}
                                            nuevas</span>
                                        @if(count($alertasNotif) > 0)
                                            <button onclick="marcarTodasLeidas()"
                                                class="btn btn-sm btn-link text-decoration-none p-0 shadow-none"><i
                                                    class="fas fa-check-double" title="Marcar todas como leídas"></i></button>
                                        @endif
                                    </div>
                                </li>
                                @if(count($alertasNotif) == 0)
                                    <li class="py-4 text-center text-muted" style="background: #ffffff !important;">
                                        <i class="fas fa-check-circle fa-2x text-success opacity-50 mb-2"></i>
                                        <p class="mb-0 small" style="color: #6b7280 !important;">¡Todo al día! No hay alertas.
                                        </p>
                                    </li>
                                @else
                                    <style>
                                        .notification-item {
                                            background: #ffffff !important;
                                            color: #1f2937 !important;
                                            border-bottom: 1px solid #f3f4f6 !important;
                                            transition: background 0.15s ease !important;
                                        }

                                        .notification-item:hover {
                                            background: #f9fafb !important;
                                            color: #111827 !important;
                                        }

                                        .notification-item .text-muted-custom {
                                            color: #6b7280 !important;
                                        }
                                    </style>
                                    @foreach($alertasNotif as $a)
                                        <li style="background: #ffffff !important;" id="alerta-{{ $a['id'] }}">
                                            <div class="dropdown-item d-flex gap-3 py-2 text-wrap notification-item">
                                                <div class="d-flex align-items-center justify-content-center text-{{ $a['nivel'] }}"
                                                    style="width:30px; height:30px; border-radius:50%; background: #f3f4f6; flex-shrink: 0;">
                                                    <i class="{{ $a['icono'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold text-dark small d-flex justify-content-between align-items-center"
                                                        style="color: #1f2937 !important;">
                                                        {{ $a['titulo'] }}
                                                        <div>
                                                            <span class="text-muted-custom fw-normal"
                                                                style="font-size:0.7rem;">{{ $a['tiempo'] }}</span>
                                                            <button onclick="marcarLeida({{ $a['id'] }})"
                                                                class="btn btn-sm btn-link text-muted p-0 ms-1 shadow-none"><i
                                                                    class="fas fa-times" title="Descartar"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="text-muted-custom small mt-1"
                                                        style="font-size:0.78rem; line-height:1.2;">
                                                        {{ $a['mensaje'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                                role="button" data-bs-toggle="dropdown">
                                @php
                                    $photoUrl = Auth::user()->photo;
                                    if ($photoUrl && !str_starts_with($photoUrl, 'http')) {
                                        $photoUrl = asset('storage/' . $photoUrl);
                                    }
                                @endphp
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="Avatar" class="user-avatar"
                                        onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                                @else
                                    <div class="user-avatar d-flex align-items-center justify-content-center"
                                        style="background: #e2e8f0;">
                                        <i class="fas fa-user" style="color: #64748b;"></i>
                                    </div>
                                @endif
                                <span class="ms-2">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i>
                                        Mi Perfil</a></li>
                                @if(Auth::user()->isSuperUsuario())
                                    <li><a class="dropdown-item" href="{{ route('backup.download') }}"><i
                                                class="fas fa-database text-info"></i> Respaldar BD</a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider" style="background: rgba(255,255,255,0.2)">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>

        <div class="container mt-4">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> Por favor verifica los errores abajo
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif


            @yield('content')
        </div>
    </main>

    @if(Auth::check() && !session()->get('login_notified') && count($alertasNotif) > 0)
        @php
            // Encontrar la alerta más importante (danger > warning > info > success)
            $levels = ['danger', 'warning', 'info', 'success'];
            $alertaPrioritaria = null;
            foreach ($levels as $nivel) {
                foreach ($alertasNotif as $a) {
                    if ($a['nivel'] === $nivel) {
                        $alertaPrioritaria = $a;
                        break 2;
                    }
                }
            }
            session()->put('login_notified', true);
        @endphp

        @if($alertaPrioritaria)
            <!-- Toast Flotante Premium -->
            <div id="loginNotificationToast" class="toast-floating shadow-lg d-flex gap-3"
                style="position: fixed; bottom: 24px; right: 24px; z-index: 1060; background: #ffffff; border-left: 5px solid var(--toast-border-color); border-radius: 12px; padding: 1.25rem; width: 360px; max-width: 90%; animation: slideInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center justify-content-center text-{{ $alertaPrioritaria['nivel'] }}"
                    style="width: 42px; height: 42px; border-radius: 50%; background: #f8f9fa; flex-shrink: 0; font-size: 1.25rem;">
                    <i class="{{ $alertaPrioritaria['icono'] }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $alertaPrioritaria['titulo'] }}</h6>
                        <button type="button" class="btn-close" style="font-size: 0.75rem;"
                            onclick="closeLoginToast()"></button>
                    </div>
                    <p class="text-muted small mb-2" style="font-size: 0.82rem; line-height: 1.3;">
                        {{ $alertaPrioritaria['mensaje'] }}
                    </p>
                    <a href="{{ $alertaPrioritaria['ruta'] }}" class="btn btn-sm btn-light py-1 px-3"
                        style="font-size: 0.75rem; font-weight: 700; border: 1px solid #e5e7eb;">
                        Ver Detalle <i class="fas fa-chevron-right ms-1" style="font-size: 0.65rem;"></i>
                    </a>
                </div>
            </div>

            <style>
                :root {
                    --toast-border-color: #ef4444;
                }

                @if($alertaPrioritaria['nivel'] === 'warning')
                    :root {
                        --toast-border-color: #f59e0b;
                    }

                @elseif($alertaPrioritaria['nivel'] === 'info')
                    :root {
                        --toast-border-color: #3b82f6;
                    }

                @elseif($alertaPrioritaria['nivel'] === 'success')
                    :root {
                        --toast-border-color: #10b981;
                    }

                @endif @keyframes slideInRight {
                    from {
                        transform: translateX(120%);
                        opacity: 0;
                    }

                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                @keyframes fadeOutDown {
                    from {
                        transform: translateY(0);
                        opacity: 1;
                    }

                    to {
                        transform: translateY(120%);
                        opacity: 0;
                    }
                }
            </style>

            <script>
                function closeLoginToast() {
                    const toast = document.getElementById('loginNotificationToast');
                    if (toast) {
                        toast.style.animation = 'fadeOutDown 0.4s ease forwards';
                        setTimeout(() => {
                            toast.remove();
                        }, 400);
                    }
                }

                // Auto cerrar después de 10 segundos
                setTimeout(() => {
                    closeLoginToast();
                }, 10000);
            </script>
        @endif
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Soft Blur In - Per character for main titles
            const softBlurElements = document.querySelectorAll('.animate-soft-blur');
            softBlurElements.forEach(el => {
                const text = el.textContent.trim();
                el.textContent = '';
                el.style.display = 'inline-block';
                el.style.perspective = '900px';

                Array.from(text).forEach((char, index) => {
                    const span = document.createElement('span');
                    span.textContent = char;
                    if (char === ' ') span.innerHTML = '&nbsp;';

                    span.style.display = 'inline-block';
                    span.style.opacity = '0';
                    span.style.transform = 'translate3d(0, 9.28px, 0)';
                    span.style.filter = 'blur(12px)';
                    span.style.willChange = 'transform, opacity, filter';

                    el.appendChild(span);

                    span.animate([
                        { opacity: 0, transform: 'translate3d(0, 9.28px, 0)', filter: 'blur(12px)' },
                        { opacity: 1, transform: 'translate3d(0, 0, 0)', filter: 'blur(0px)' }
                    ], {
                        duration: 648,
                        delay: index * 18,
                        easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                        fill: 'forwards'
                    });
                });
            });

            // 2. Spring Scale In - For large metric numbers or highlighted elements
            const springElements = document.querySelectorAll('.animate-spring-scale');
            springElements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'scale(0.7)';
                el.style.display = 'inline-block';
                el.style.willChange = 'transform, opacity';

                el.animate([
                    { opacity: 0, transform: 'scale(0.7)' },
                    { opacity: 1, transform: 'scale(1)' }
                ], {
                    duration: 259,
                    delay: index * 68 + 150,
                    easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
                    fill: 'forwards'
                });
            });

            // 3. Micro Scale Fade - For subtitles/labels or UI cards
            const microElements = document.querySelectorAll('.animate-micro-scale');
            microElements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'scale(0.96)';
                el.style.willChange = 'transform, opacity';

                el.animate([
                    { opacity: 0, transform: 'scale(0.96)' },
                    { opacity: 1, transform: 'scale(1)' }
                ], {
                    duration: 432,
                    delay: index * 68 + 200,
                    easing: 'cubic-bezier(0.32, 0.72, 0, 1)',
                    fill: 'forwards'
                });
            });
        });
    </script>
    <script>
        // Theme is fixed to light mode — no toggle needed

        function marcarLeida(id) {
            fetch(`/alertas/${id}/marcar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    document.getElementById('alerta-' + id).style.display = 'none';
                }
            });
        }

        function marcarTodasLeidas() {
            fetch(`/alertas/marcar-todas`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                }
            });
        }
    </script>
    <!-- AI Assistant Widget -->
    <div id="ai-assistant-widget" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
        <!-- Chat Window -->
        <div id="ai-chat-window"
            style="display: none; width: 380px; height: 500px; background: rgba(15, 15, 15, 0.95); backdrop-filter: blur(20px); border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); flex-direction: column; overflow: hidden; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;">
            <!-- Header -->
            <div
                style="background: rgba(255,255,255,0.05); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div
                        style="width: 30px; height: 30px; border-radius: 50%; background: #ffffff; color: #000; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-robot"></i>
                    </div>
                    <span style="font-weight: 600; font-family: 'Inter', sans-serif;">Asistente UniStock</span>
                </div>
                <button onclick="toggleAiChat()"
                    style="background: none; border: none; color: white; cursor: pointer; opacity: 0.7; transition: opacity 0.2s;"><i
                        class="fas fa-times"></i></button>
            </div>

            {{-- Duplicate slider removed --}}

            <!-- Messages -->
            <div id="ai-chat-messages"
                style="flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; background: transparent;">
                <div
                    style="align-self: flex-start; background: rgba(255,255,255,0.08); color: #ffffff; padding: 12px 16px; border-radius: 14px; border-bottom-left-radius: 4px; max-width: 85%; font-size: 0.9rem; line-height: 1.4;">
                    ¡Hola! Soy tu asistente de UniStock con visión global. Pregúntame sobre el stock, los proveedores o
                    los reportes recientes.
                </div>
            </div>
            <!-- Input Area -->
            <div
                style="padding: 15px; background: rgba(255,255,255,0.03); border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 10px; align-items: center;">
                <button id="ai-mic-btn" onclick="toggleVoiceRecognition()"
                    style="background: transparent; border: 1px solid rgba(255,255,255,0.2); width: 42px; height: 42px; border-radius: 50%; cursor: pointer; color: #ffffff; transition: all 0.2s; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-microphone"></i>
                </button>
                <input type="text" id="ai-chat-input" placeholder="Escribe tu mensaje..."
                    style="flex: 1; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.5); color: #ffffff; border-radius: 20px; padding: 10px 16px; font-size: 0.9rem; outline: none;">
                <button onclick="sendAiMessage()"
                    style="background: #ffffff; border: none; width: 42px; height: 42px; border-radius: 50%; cursor: pointer; color: #000000; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>

        <!-- Floating Button -->
        <button id="ai-toggle-btn" onclick="toggleAiChat()"
            style="width: 60px; height: 60px; border-radius: 50%; background: #ffffff; color: #000000; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.6); cursor: pointer; font-size: 1.6rem; display: flex; justify-content: center; align-items: center; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <i class="fas fa-robot"></i>
        </button>
    </div>

    <style>
        #ai-toggle-btn:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .ai-message-user {
            align-self: flex-end;
            background: #ffffff;
            color: #000000;
            padding: 12px 16px;
            border-radius: 14px;
            border-bottom-right-radius: 4px;
            max-width: 85%;
            font-size: 0.9rem;
            line-height: 1.4;
            font-weight: 500;
        }

        .ai-message-bot {
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            padding: 12px 16px;
            border-radius: 14px;
            border-bottom-left-radius: 4px;
            max-width: 85%;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .ai-message-bot strong {
            color: #aaaaaa;
        }

        .ai-mic-active {
            background: rgba(255, 77, 77, 0.2) !important;
            color: #ff4d4d !important;
            border-color: #ff4d4d !important;
            animation: pulse-mic 1.5s infinite;
        }

        @keyframes pulse-mic {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 77, 77, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 77, 77, 0);
            }
        }
    </style>

    <script>
        const chatWindow = document.getElementById('ai-chat-window');
        const chatMessages = document.getElementById('ai-chat-messages');
        const chatInput = document.getElementById('ai-chat-input');
        const micBtn = document.getElementById('ai-mic-btn');
        let recognition = null;
        let isRecording = false;

        // Initialize Web Speech API for recognition if available
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.lang = 'es-ES';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = function () {
                isRecording = true;
                micBtn.classList.add('ai-mic-active');
            };

            recognition.onresult = function (event) {
                const speechResult = event.results[0][0].transcript;
                chatInput.value = speechResult;
                sendAiMessage();
            };

            recognition.onerror = function (event) {
                console.error('Speech recognition error', event.error);
                stopVoiceRecognition();
            };

            recognition.onend = function () {
                stopVoiceRecognition();
            };
        }

        function toggleAiChat() {
            if (chatWindow.style.display === 'none') {
                chatWindow.style.display = 'flex';
                chatInput.focus();
            } else {
                chatWindow.style.display = 'none';
                stopVoiceRecognition();
            }
        }

        function toggleVoiceRecognition() {
            if (!recognition) {
                alert("Tu navegador no soporta reconocimiento de voz.");
                return;
            }
            if (isRecording) {
                stopVoiceRecognition();
            } else {
                recognition.start();
            }
        }

        function stopVoiceRecognition() {
            if (recognition && isRecording) {
                recognition.stop();
            }
            isRecording = false;
            micBtn.classList.remove('ai-mic-active');
        }

        function appendMessage(text, isUser) {
            const div = document.createElement('div');
            div.className = isUser ? 'ai-message-user' : 'ai-message-bot';
            div.textContent = text;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function speakText(text) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'es-ES';
                window.speechSynthesis.speak(utterance);
            }
        }

        async function sendAiMessage() {
            const text = chatInput.value.trim();
            if (!text) return;

            // User message
            appendMessage(text, true);
            chatInput.value = '';

            // Loading indicator could go here

            try {
                const response = await fetch('/api/ai-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();

                if (response.ok) {
                    appendMessage(data.reply, false);
                    speakText(data.reply);
                } else {
                    appendMessage("Lo siento, tuve un error procesando eso.", false);
                }
            } catch (error) {
                console.error("Error AI:", error);
                appendMessage("Error de conexión con el asistente.", false);
            }
        }

        chatInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendAiMessage();
            }
        });
    </script>
</body>

</html>