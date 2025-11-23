@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Registro de Usuario</h3>
                </div>

                <div class="card-body p-5">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="userForm">
                        @csrf

                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-user-tag"></i> Tipo de Cuenta
                            </div>
                            
                            <div class="role-selector">
                                <div class="role-option">
                                    <input type="radio" name="role" id="role_super_usuario" value="super_usuario" required>
                                    <label for="role_super_usuario">
                                        <i class="fas fa-crown text-warning"></i>
                                        <span>Super Usuario</span>
                                    </label>
                                </div>
                                <div class="role-option">
                                    <input type="radio" name="role" id="role_gerente" value="gerente">
                                    <label for="role_gerente">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        <span>Gerente</span>
                                    </label>
                                </div>
                                <div class="role-option">
                                    <input type="radio" name="role" id="role_almacenista" value="almacenista">
                                    <label for="role_almacenista">
                                        <i class="fas fa-boxes text-success"></i>
                                        <span>Almacenista</span>
                                    </label>
                                </div>
                                <div class="role-option">
                                    <input type="radio" name="role" id="role_proveedor" value="proveedor">
                                    <label for="role_proveedor">
                                        <i class="fas fa-truck text-info"></i>
                                        <span>Proveedor</span>
                                    </label>
                                </div>
                            </div>
                            @error('role')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Información Básica
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre Completo</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div id="adminAuthFields" class="conditional-fields form-section">
                            <div class="form-section-title">
                                <i class="fas fa-lock"></i> Verificación de Rol Privilegiado
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Para registrarse con este rol, debe ingresar la contraseña de autorización administrativa.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña de Autorización</label>
                                <input type="password" name="admin_password" id="admin_password" class="form-control">
                            </div>
                        </div>

                        <div id="proveedorFields" class="conditional-fields form-section">
                            <div class="form-section-title">
                                <i class="fas fa-building"></i> Información de Proveedor
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre de la Empresa</label>
                                    <input type="text" name="empresa" class="form-control" value="{{ old('empresa') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">RUC / NIT</label>
                                    <input type="text" name="ruc" class="form-control" value="{{ old('ruc') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono de Contacto</label>
                                    <input type="text" name="telefono_proveedor" class="form-control" value="{{ old('telefono_proveedor') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion_proveedor" id="direccion_proveedor" class="form-control" value="{{ old('direccion_proveedor') }}" placeholder="Ingrese la dirección para buscar en el mapa">
                                </div>
                                
                                <div class="col-12">
                                    <div class="map-container">
                                        <div id="map"></div>
                                    </div>
                                    <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
                                    <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">
                                    <input type="hidden" name="ciudad" id="ciudad" value="{{ old('ciudad') }}">
                                    <input type="hidden" name="pais" id="pais" value="{{ old('pais') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-check"></i> Registrarse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
<script src="{{ asset('js/usuarios.js') }}"></script>
@endsection
