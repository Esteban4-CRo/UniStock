<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - UniStock</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; color: #333; }
        .navbar { background-color: #2c3e50; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; }
        .navbar-nav { display: flex; gap: 1rem; }
        .navbar-nav a { color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; transition: background-color 0.3s; }
        .navbar-nav a:hover { background-color: #34495e; }
        .container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .photo-section { text-align: center; margin-bottom: 2rem; }
        .photo-preview { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 1rem auto; display: block; border: 3px solid #ddd; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; margin-right: 0.5rem; }
        .btn-success { background-color: #27ae60; }
        .btn-danger { background-color: #e74c3c; }
        .btn-secondary { background-color: #95a5a6; }
        .error { color: #e74c3c; font-size: 0.875rem; margin-top: 0.25rem; }
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
            <a href="{{ route('profile.edit') }}">Mi Perfil</a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar Sesión</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h1 style="margin-bottom: 2rem;">Editar Perfil</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="photo-section">
                <h3>Foto de Perfil</h3>
                @if($user->photo)
                    <img src="{{ asset('storage/'.$user->photo) }}" alt="Foto de perfil" class="photo-preview">
                @else
                    <div style="width: 150px; height: 150px; border-radius: 50%; background-color: #ecf0f1; margin: 1rem auto; display: flex; align-items: center; justify-content: center; border: 3px solid #ddd;">
                        <span style="color: #bdc3c7; font-size: 3rem;">👤</span>
                    </div>
                @endif
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nombre *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="photo">Foto de Perfil</label>
                    <input type="file" id="photo" name="photo" accept="image/*">
                    @error('photo') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div style="display: flex; gap: 0.5rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    @if($user->photo)
                        <form action="{{ route('profile.deletePhoto') }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar foto?')">Eliminar Foto</button>
                        </form>
                    @endif
                    <a href="{{ route('home') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
