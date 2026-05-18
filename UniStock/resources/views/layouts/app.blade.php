@php
    $alertasNotif = [];
    if (Auth::check()) {
        // 1. Sin Stock
        $sinStock = \App\Models\MaterialPrima::where('activo', true)->where('cantidad', 0)->get();
        foreach ($sinStock as $item) {
            $alertasNotif[] = [
                'tipo' => 'sin_stock',
                'nivel' => 'danger',
                'icono' => 'fas fa-exclamation-triangle',
                'titulo' => '¡Sin Stock!',
                'mensaje' => "El insumo {$item->nombre} ({$item->codigo}) se ha quedado sin stock.",
                'ruta' => route('materias-primas.show', $item->id),
                'tiempo' => 'Ahora mismo'
            ];
        }

        // 2. Stock Bajo
        $stockBajo = \App\Models\MaterialPrima::where('activo', true)
            ->where('cantidad', '>', 0)
            ->whereColumn('cantidad', '<=', 'stock_minimo')
            ->get();
        foreach ($stockBajo as $item) {
            $alertasNotif[] = [
                'tipo' => 'stock_bajo',
                'nivel' => 'warning',
                'icono' => 'fas fa-exclamation-circle',
                'titulo' => 'Stock Bajo',
                'mensaje' => "{$item->nombre} ({$item->codigo}) está por debajo del stock mínimo ({$item->cantidad} {$item->unidad_medida} restante).",
                'ruta' => route('materias-primas.show', $item->id),
                'tiempo' => 'Atención requerida'
            ];
        }

        // 3. Próximos a vencer (menos de 30 días)
        $expiring = \App\Models\MaterialPrima::where('activo', true)
            ->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', now()->addDays(30))
            ->where('fecha_caducidad', '>=', now())
            ->get();
        foreach ($expiring as $item) {
            $dias = now()->diffInDays($item->fecha_caducidad);
            $alertasNotif[] = [
                'tipo' => 'por_vencer',
                'nivel' => 'info',
                'icono' => 'fas fa-hourglass-half',
                'titulo' => 'Próximo a Vencer',
                'mensaje' => "Lote de {$item->nombre} ({$item->codigo}) vence en {$dias} días.",
                'ruta' => route('materias-primas.show', $item->id),
                'tiempo' => 'Urgencia media'
            ];
        }

        // 4. Últimas entradas registradas en las últimas 12 horas
        $recentEntradas = \App\Models\Entrada::with('materialPrima')
            ->where('anulado', false)
            ->where('created_at', '>=', now()->subHours(12))
            ->get();
        foreach ($recentEntradas as $e) {
            if ($e->materialPrima) {
                $alertasNotif[] = [
                    'tipo' => 'nueva_entrada',
                    'nivel' => 'success',
                    'icono' => 'fas fa-arrow-down',
                    'titulo' => 'Nueva Entrada',
                    'mensaje' => "Se ingresaron +{$e->cantidad} {$e->materialPrima->unidad_medida} de {$e->materialPrima->nombre}.",
                    'ruta' => route('entradas.index'),
                    'tiempo' => $e->created_at->diffForHumans()
                ];
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UniStock')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #FAFAF9;
            color: #0C0A09;
            min-height: 100vh;
        }

        /* Transiciones globales y micro-interacciones suaves */
        .btn, .nav-link, .card, .form-control, .form-select, .dropdown-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Cursor pointer obligatorio para elementos interactivos */
        .btn, .nav-link, .dropdown-item, .form-check-input, .pagination a, th[style*="cursor"] {
            cursor: pointer;
        }

        .navbar {
            background: #1C1917;
            padding: 0.75rem 1.25rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border-bottom: 1px solid #2E2A27;
        }

        .navbar-brand { 
            color: #ffffff !important; 
            font-weight: 700; 
            font-size: 1.5rem; 
            letter-spacing: -0.5px;
        }

        .navbar-nav .nav-link { 
            color: #E7E5E4 !important; 
            font-weight: 500; 
            margin: 0 0.15rem; 
            padding: 0.5rem 0.85rem !important;
            font-size: 0.92rem;
        }
        
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active { 
            color: #ffffff !important; 
            background: #2E2A27; 
            border-radius: 8px; 
        }

        .dropdown-menu { 
            background: #1C1917; 
            border: 1px solid #2E2A27; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 8px;
            padding: 0.4rem;
        }
        
        .dropdown-menu .dropdown-item { 
            color: #E7E5E4; 
            font-weight: 500;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .dropdown-menu .dropdown-item:hover { 
            background: #2E2A27; 
            color: #ffffff;
        }

        .user-avatar { 
            width: 30px; 
            height: 30px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-right: 0.5rem; 
            border: 1.5px solid #2E2A27; 
        }

        /* Tarjetas Premium */
        .card { 
            background: #ffffff; 
            border: 1px solid #E7E5E4; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.02); 
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(28,25,23,0.06);
        }
        
        .card-header { 
            background: #FAFAF9; 
            color: #0C0A09; 
            border-bottom: 1px solid #E7E5E4; 
            font-weight: 700; 
            padding: 1rem 1.5rem;
        }
        
        .card-body { 
            padding: 1.5rem; 
        }

        /* Botones e Indicadores Premium */
        .btn { 
            border-radius: 8px; 
            font-weight: 600; 
            padding: 0.5rem 1.25rem;
            font-size: 0.9rem;
        }
        
        .btn-primary { 
            background: #1C1917; 
            color: #ffffff; 
            border: 1px solid #1C1917; 
        }
        
        .btn-primary:hover { 
            background: #2E2A27; 
            border-color: #2E2A27;
        }
        
        .btn-secondary { 
            background: #ffffff; 
            color: #1C1917; 
            border: 1px solid #E7E5E4; 
        }
        
        .btn-secondary:hover { 
            background: #F5F5F4; 
            border-color: #D6D3D1;
        }
        
        .btn-danger { 
            background: #991B1B; 
            color: #ffffff; 
            border: 1px solid #991B1B; 
        }
        
        .btn-danger:hover {
            background: #7F1D1D;
            border-color: #7F1D1D;
        }

        /* Tablas */
        .table thead th { 
            background: #F5F5F4; 
            color: #44403C; 
            border-bottom: 2px solid #E7E5E4; 
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.85rem 1rem;
        }
        
        .table tbody tr { 
            background: #ffffff; 
        }
        
        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #E7E5E4;
            color: #44403C;
        }

        /* Formulario */
        .form-control, .form-select { 
            border: 1px solid #D6D3D1; 
            border-radius: 8px; 
            padding: 0.55rem 0.85rem;
            font-size: 0.92rem;
            color: #0C0A09;
            background-color: #ffffff;
        }
        
        .form-control:focus, .form-select:focus { 
            box-shadow: 0 0 0 3px rgba(202,138,4,0.15); 
            border-color: #CA8A04; 
        }

        .form-label { 
            font-weight: 600; 
            color: #1C1917; 
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        .badge { 
            background: #44403C; 
            color: #ffffff; 
            padding: 0.4rem 0.8rem; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 0.78rem;
        }

        .container { 
            max-width: 1240px; 
            margin: 0 auto; 
        }
        
        main { 
            padding: 2.5rem 0; 
            min-height: calc(100vh - 90px); 
        }

        /* Animaciones para carga o cambios de estado */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-cube"></i> UniStock
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a>
                        </li>
                        @if(Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a>
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
                            <a class="nav-link {{ request()->routeIs('materias-primas.*') ? 'active' : '' }}" href="{{ route('materias-primas.index') }}">
                                <i class="fas fa-dolly"></i> Materias Primas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" href="{{ route('proveedores.index') }}">
                                <i class="fas fa-truck"></i> Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}" href="{{ route('ubicaciones.index') }}">
                                <i class="fas fa-map-marker-alt"></i> Ubicaciones
                            </a>
                        </li>
                        @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users"></i> Usuarios
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}" href="{{ route('entradas.index') }}">
                                <i class="fas fa-arrow-down"></i> Entradas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('salidas.*') ? 'active' : '' }}" href="{{ route('salidas.index') }}">
                                <i class="fas fa-arrow-up"></i> Salidas
                            </a>
                        </li>
                        @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                    <i class="fas fa-file-pdf"></i> Reportes
                                </a>
                            </li>
                        @endif
                        <!-- Notificaciones Dropdown -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative dropdown-toggle no-caret" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-top: 0.75rem;">
                                <i class="fas fa-bell" style="font-size: 1.2rem; color: #4b5563;"></i>
                                @if(count($alertasNotif) > 0)
                                    <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.5em; top: 8px !important; left: 16px !important;">
                                        {{ count($alertasNotif) }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border py-0" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 12px; font-size: 0.85rem; background: #ffffff !important; border-color: #e5e7eb !important;">
                                <li class="dropdown-header bg-light py-2 text-dark border-bottom d-flex justify-content-between align-items-center" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                    <span class="fw-bold"><i class="fas fa-bell me-1"></i> Notificaciones</span>
                                    <span class="badge bg-secondary text-white">{{ count($alertasNotif) }} nuevas</span>
                                </li>
                                @if(count($alertasNotif) == 0)
                                    <li class="py-4 text-center text-muted" style="background: #ffffff !important;">
                                        <i class="fas fa-check-circle fa-2x text-success opacity-50 mb-2"></i>
                                        <p class="mb-0 small" style="color: #6b7280 !important;">¡Todo al día! No hay alertas.</p>
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
                                        <li style="background: #ffffff !important;">
                                            <a class="dropdown-item d-flex gap-3 py-2 text-wrap notification-item" href="{{ $a['ruta'] }}">
                                                <div class="d-flex align-items-center justify-content-center text-{{ $a['nivel'] }}" style="width:30px; height:30px; border-radius:50%; background: #f3f4f6; flex-shrink: 0;">
                                                    <i class="{{ $a['icono'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold text-dark small d-flex justify-content-between align-items-center" style="color: #1f2937 !important;">
                                                        {{ $a['titulo'] }}
                                                        <span class="text-muted-custom fw-normal" style="font-size:0.7rem;">{{ $a['tiempo'] }}</span>
                                                    </div>
                                                    <div class="text-muted-custom small mt-1" style="font-size:0.78rem; line-height:1.2;">
                                                        {{ $a['mensaje'] }}
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                @if(Auth::user()->photo)
                                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" class="user-avatar">
                                @else
                                    <i class="fas fa-user-circle"></i>
                                @endif
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                @if(Auth::user()->isSuperUsuario())
                                    <li><a class="dropdown-item" href="{{ route('backup.download') }}"><i class="fas fa-database text-info"></i> Respaldar BD</a></li>
                                @endif
                                <li><hr class="dropdown-divider" style="background: rgba(255,255,255,0.2)"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
        <div class="container">

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
            $alertaPrioritaria = null;
            foreach (['danger', 'warning', 'info', 'success'] as $nivel) {
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
            <div id="loginNotificationToast" class="toast-floating shadow-lg d-flex gap-3" style="position: fixed; bottom: 24px; right: 24px; z-index: 1060; background: #ffffff; border-left: 5px solid var(--toast-border-color); border-radius: 12px; padding: 1.25rem; width: 360px; max-width: 90%; animation: slideInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center justify-content-center text-{{ $alertaPrioritaria['nivel'] }}" style="width: 42px; height: 42px; border-radius: 50%; background: #f8f9fa; flex-shrink: 0; font-size: 1.25rem;">
                    <i class="{{ $alertaPrioritaria['icono'] }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $alertaPrioritaria['titulo'] }}</h6>
                        <button type="button" class="btn-close" style="font-size: 0.75rem;" onclick="closeLoginToast()"></button>
                    </div>
                    <p class="text-muted small mb-2" style="font-size: 0.82rem; line-height: 1.3;">{{ $alertaPrioritaria['mensaje'] }}</p>
                    <a href="{{ $alertaPrioritaria['ruta'] }}" class="btn btn-sm btn-light py-1 px-3" style="font-size: 0.75rem; font-weight: 700; border: 1px solid #e5e7eb;">
                        Ver Detalle <i class="fas fa-chevron-right ms-1" style="font-size: 0.65rem;"></i>
                    </a>
                </div>
            </div>

            <style>
                :root {
                    --toast-border-color: #ef4444;
                }
                @if($alertaPrioritaria['nivel'] === 'warning')
                    :root { --toast-border-color: #f59e0b; }
                @elseif($alertaPrioritaria['nivel'] === 'info')
                    :root { --toast-border-color: #3b82f6; }
                @elseif($alertaPrioritaria['nivel'] === 'success')
                    :root { --toast-border-color: #10b981; }
                @endif

                @keyframes slideInRight {
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
</body>
</html>
