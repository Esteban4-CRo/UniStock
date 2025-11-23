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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            color: #0b0b0b;
            min-height: 100vh;
        }


        .navbar {
            background: #000000;
            padding: 0.75rem 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .navbar-brand { color: #ffffff !important; font-weight: 800; font-size: 1.6rem; }

        .navbar-nav .nav-link { color: #ffffff !important; font-weight: 600; margin: 0 0.25rem; }
        .navbar-nav .nav-link:hover { color: #f0f0f0 !important; background: rgba(255,255,255,0.03); border-radius: 6px; }

        .dropdown-menu { background: #000000; border: 1px solid #222; }
        .dropdown-menu .dropdown-item { color: #fff; }
        .dropdown-menu .dropdown-item:hover { background: #111; }

        .user-avatar { width:36px; height:36px; border-radius:50%; object-fit:cover; margin-right:0.5rem; border:2px solid #fff; }


        .card { background: #ffffff; border: 1px solid #e9e9e9; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.04); }
        .card-header { background: #f7f7f7; color: #0b0b0b; border-bottom: 1px solid #eee; font-weight:700; }
        .card-body { padding: 1.5rem; }


        .btn { border-radius: 8px; font-weight:700; }
        .btn-primary { background: #0b0b0b; color: #fff; border: 1px solid #0b0b0b; }
        .btn-primary:hover { background: #1a1a1a; }
        .btn-secondary { background: #fff; color: #0b0b0b; border: 1px solid #0b0b0b; }
        .btn-secondary:hover { background: #f5f5f5; }
        .btn-danger { background: #000; color:#fff; border:1px solid #000; }


        .table thead th { background: #f7f7f7; color: #0b0b0b; border-bottom: 1px solid #e9e9e9; }
        .table tbody tr { background: #fff; }


        .form-control, .form-select { border:1px solid #ddd; border-radius:8px; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.12rem rgba(0,0,0,0.06); border-color: #0b0b0b; }

        .form-label { font-weight:700; color:#0b0b0b; }


        .badge { background:#000; color:#fff; padding:0.4rem 0.75rem; border-radius:999px; font-weight:700; }

        .container { max-width: 1200px; margin: 0 auto; }
        main { padding: 2rem 0; min-height: calc(100vh - 90px); }
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
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                                <i class="fas fa-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users"></i> Usuarios
                            </a>
                        </li>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
