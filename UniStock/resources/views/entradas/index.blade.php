<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas - UniStock</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; color: #333; }
        .navbar { background-color: #2c3e50; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; }
        .navbar-nav { display: flex; gap: 1rem; }
        .navbar-nav a { color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: background-color 0.3s; }
        .navbar-nav a:hover { background-color: #34495e; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background-color: #34495e; color: white; }
        .alert { padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">UniStock</div>
        <div class="navbar-nav">
            <a href="{{ route('home') }}">Inicio</a>
            <a href="{{ route('productos.index') }}">Productos</a>
            <a href="{{ route('entradas.index') }}">Entradas</a>
            <a href="{{ route('salidas.index') }}">Salidas</a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar Sesión</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Registro de Entradas</h1>
            <a href="{{ route('entradas.create') }}" class="btn">Nueva Entrada</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entradas as $entrada)
                <tr>
                    <td>{{ $entrada->producto->nombre }}</td>
                    <td>{{ $entrada->cantidad }}</td>
                    <td>{{ $entrada->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $entrada->motivo ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($entradas->isEmpty())
        <div style="text-align: center; padding: 2rem; background: white; border-radius: 8px;">
            <p>No hay entradas registradas</p>
            <a href="{{ route('entradas.create') }}" class="btn">Registrar Primera Entrada</a>
        </div>
        @endif
    </div>
</body>
</html>