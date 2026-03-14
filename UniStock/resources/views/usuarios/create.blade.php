@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">

<div class="usuarios-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</h2>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <form action="{{ route('usuarios.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
                @csrf


                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user-tag"></i> Seleccionar Rol
                    </div>
                    <div class="role-selector">
                        <div class="role-option">
                            <input type="radio" name="role" id="role_super" value="super_usuario" {{ old('role') == 'super_usuario' ? 'checked' : '' }}>
                            <label for="role_super">
                                <i class="fas fa-crown" style="color: #FFD700;"></i>
                                <strong>Super Usuario</strong>
                                <small>Control total del sistema</small>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="role_gerente" value="gerente" {{ old('role') == 'gerente' ? 'checked' : '' }}>
                            <label for="role_gerente">
                                <i class="fas fa-user-tie" style="color: #667eea;"></i>
                                <strong>Gerente</strong>
                                <small>Gestión y supervisión</small>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="role_almacenista" value="almacenista" {{ old('role') == 'almacenista' ? 'checked' : '' }}>
                            <label for="role_almacenista">
                                <i class="fas fa-boxes" style="color: #11998e;"></i>
                                <strong>Almacenista</strong>
                                <small>Gestión de inventario</small>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="role_proveedor" value="proveedor" {{ old('role') == 'proveedor' ? 'checked' : '' }}>
                            <label for="role_proveedor">
                                <i class="fas fa-truck" style="color: #f5576c;"></i>
                                <strong>Proveedor</strong>
                                <small>Suministro de productos</small>
                            </label>
                        </div>
                    </div>
                    @error('role')
                        <div class="text-danger mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>


                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user"></i> Información Básica
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto de Perfil</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="photo-preview-container">
                            <img id="photoPreview" class="photo-preview" alt="Preview">
                        </div>
                    </div>
                </div>


                <div id="gerenteFields" class="form-section conditional-fields">
                    <div class="form-section-title">
                        <i class="fas fa-shield-alt"></i> Autenticación Especial
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Para crear un usuario con rol de <strong>Gerente</strong>, debes ingresar la contraseña de autorización del Super Usuario.
                    </div>
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Contraseña de Autorización *</label>
                        <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" placeholder="Ingresa la contraseña especial">
                        @error('admin_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>


                <div id="proveedorFields" class="form-section conditional-fields">
                    <div class="form-section-title">
                        <i class="fas fa-building"></i> Información de la Empresa
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="empresa" class="form-label">Nombre de la Empresa *</label>
                            <input type="text" class="form-control @error('empresa') is-invalid @enderror" id="empresa" name="empresa" value="{{ old('empresa') }}">
                            @error('empresa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ruc" class="form-label">RUC/NIT *</label>
                            <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" value="{{ old('ruc') }}">
                            @error('ruc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_proveedor" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control @error('telefono_proveedor') is-invalid @enderror" id="telefono_proveedor" name="telefono_proveedor" value="{{ old('telefono_proveedor') }}">
                            @error('telefono_proveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="direccion_proveedor" class="form-label">Dirección *</label>
                            <input type="text" class="form-control @error('direccion_proveedor') is-invalid @enderror" id="direccion_proveedor" name="direccion_proveedor" value="{{ old('direccion_proveedor') }}" placeholder="Ej: Calle 100 #15-20, Bogotá">
                            @error('direccion_proveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad" value="{{ old('ciudad') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control" id="pais" name="pais" value="{{ old('pais', 'Colombia') }}">
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-map-marker-alt"></i> Ubicación en el Mapa</label>
                        <p class="text-muted small">Haz clic en el mapa o arrastra el marcador para establecer la ubicación exacta del proveedor.</p>
                        <div id="map"></div>
                        <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud') }}">
                        <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud') }}">
                    </div>
                </div>


                <div class="form-section">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/usuarios.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
@endsection
