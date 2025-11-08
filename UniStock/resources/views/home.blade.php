<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniStock - Sistema de Inventario</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .navbar {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .navbar-nav {
            display: flex;
            gap: 1rem;
        }
        
        .navbar-nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .navbar-nav a:hover {
            background-color: #34495e;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }
        
        .section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 0.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: #27ae60;
        }
        
        .btn-success:hover {
            background-color: #219a52;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">UniStock</div>
        <div class="navbar-nav">
            <a href="{{ route('home') }}">Inicio</a>
            <a href="{{ route('productos.index') }}">Productos</a>
            <a href="{{ route('entradas.index') }}">Entradas</a>
            <a href="{{ route('salidas.index') }}">Salidas</a>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Cerrar Sesión
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </nav>

    <div class="container">
        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $totalProductos }}</div>
                <div class="stat-label">Productos Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $totalEntradas }}</div>
                <div class="stat-label">Entradas Registradas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $totalSalidas }}</div>
                <div class="stat-label">Salidas Registradas</div>
            </div>
        </div>

        <!-- Productos Recientes -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title">Productos Recientes</h2>
                <a href="{{ route('productos.create') }}" class="btn btn-success">Nuevo Producto</a>
            </div>
            
            @if($productos->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Stock</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td>{{ $producto->nombre }}</td>
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
            @else
                <div class="empty-state">
                    <p>No hay productos registrados</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-success">Agregar Primer Producto</a>
                </div>
            @endif
        </div>

        <!-- Últimas Entradas -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title">Últimas Entradas</h2>
                <a href="{{ route('entradas.create') }}" class="btn">Nueva Entrada</a>
            </div>
            
            @if($entradas->count() > 0)
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
            @else
                <div class="empty-state">
                    <p>No hay entradas registradas</p>
                </div>
            @endif
        </div>

        <!-- Últimas Salidas -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title">Últimas Salidas</h2>
                <a href="{{ route('salidas.create') }}" class="btn btn-danger">Nueva Salida</a>
            </div>
            
            @if($salidas->count() > 0)
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
                        @foreach($salidas as $salida)
                        <tr>
                            <td>{{ $salida->producto->nombre }}</td>
                            <td>{{ $salida->cantidad }}</td>
                            <td>{{ $salida->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $salida->motivo ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>No hay salidas registradas</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>