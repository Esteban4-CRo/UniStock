<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Entrada - UniStock</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; color: #333; }
        .navbar { background-color: #2c3e50; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; }
        .navbar-nav { display: flex; gap: 1rem; }
        .navbar-nav a { color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: background-color 0.3s; }
        .navbar-nav a:hover { background-color: #34495e; }
        .container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; margin-right: 0.5rem; }
        .btn-success { background-color: #27ae60; }
        .btn-secondary { background-color: #95a5a6; }
        .error { color: #e74c3c; font-size: 0.875rem; margin-top: 0.25rem; }
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
        <div class="card">
            <h1 style="margin-bottom: 2rem;">Registrar Entrada de Inventario</h1>

            <form action="{{ route('entradas.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="producto_id">Producto *</label>
                    <select id="producto_id" name="producto_id" required>
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" 
                            {{ old('producto_id', $producto_id) == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }} (Stock: {{ $producto->stock_actual }})
                        </option>
                        @endforeach
                    </select>
                    @error('producto_id') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad *</label>
                    <input type="number" id="cantidad" name="cantidad" value="{{ old('cantidad', 1) }}" min="1" required>
                    @error('cantidad') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo de la Entrada</label>
                    <textarea id="motivo" name="motivo" rows="3" placeholder="Ej: Compra, Donación, Devolución...">{{ old('motivo') }}</textarea>
                    @error('motivo') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div style="display: flex; gap: 0.5rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-success">Registrar Entrada</button>
                    <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>