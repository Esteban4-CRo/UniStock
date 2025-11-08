<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - UniStock</title>
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
        .btn-success { background-color: #27ae60; }
        .btn-danger { background-color: #e74c3c; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background-color: #34495e; color: white; }
        .actions { display: flex; gap: 0.5rem; }
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
            <h1>Gestión de Productos</h1>
            <a href="{{ route('productos.create') }}" class="btn btn-success">Nuevo Producto</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->descripcion ?? 'N/A' }}</td>
                    <td>{{ $producto->stock_actual }}</td>
                    <td>${{ number_format($producto->precio, 2) }}</td>
                    <td class="actions">
                        <a href="{{ route('entradas.create') }}?producto_id={{ $producto->id }}" class="btn">Entrada</a>
                        <a href="{{ route('salidas.create') }}?producto_id={{ $producto->id }}" class="btn btn-danger">Salida</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($productos->isEmpty())
        <div style="text-align: center; padding: 2rem; background: white; border-radius: 8px;">
            <p>No hay productos registrados</p>
            <a href="{{ route('productos.create') }}" class="btn btn-success">Agregar Primer Producto</a>
        </div>
        @endif
    </div>
</body>
</html>