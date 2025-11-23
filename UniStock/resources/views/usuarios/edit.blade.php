@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">

<div class="usuarios-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Editar Usuario</h2>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <form action="{{ route('usuarios.update', $user) }}" method="POST" enctype="multipart/form-data" id="userForm">
                @csrf
                @method('PUT')


                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user-tag"></i> Rol Actual
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> El rol de este usuario es: {!! $user->role_badge !!}
                        <br><small>El rol no puede ser modificado después de la creación.</small>
                    </div>
                </div>


                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user"></i> Información Básica
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-key"></i> Deja los campos de contraseña vacíos para mantener la actual.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto de Perfil</label>
                        @if($user->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto actual" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e9ecef;">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="photo-preview-container">
                            <img id="photoPreview" class="photo-preview" alt="Preview">
                        </div>
                    </div>
                </div>


                @if($user->isProveedor())
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-building"></i> Información de la Empresa
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empresa" class="form-label">Nombre de la Empresa *</label>
                                <input type="text" class="form-control @error('empresa') is-invalid @enderror" id="empresa" name="empresa" value="{{ old('empresa', $user->proveedor->empresa ?? '') }}" required>
                                @error('empresa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ruc" class="form-label">RUC/NIT *</label>
                                <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" value="{{ old('ruc', $user->proveedor->ruc ?? '') }}" required>
                                @error('ruc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono_proveedor" class="form-label">Teléfono *</label>
                                <input type="text" class="form-control @error('telefono_proveedor') is-invalid @enderror" id="telefono_proveedor" name="telefono_proveedor" value="{{ old('telefono_proveedor', $user->proveedor->telefono ?? '') }}" required>
                                @error('telefono_proveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="direccion_proveedor" class="form-label">Dirección *</label>
                                <input type="text" class="form-control @error('direccion_proveedor') is-invalid @enderror" id="direccion_proveedor" name="direccion_proveedor" value="{{ old('direccion_proveedor', $user->proveedor->direccion ?? '') }}" required>
                                @error('direccion_proveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" value="{{ old('ciudad', $user->proveedor->ciudad ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pais" class="form-label">País</label>
                                <input type="text" class="form-control" id="pais" name="pais" value="{{ old('pais', $user->proveedor->pais ?? 'Colombia') }}">
                            </div>
                        </div>


                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Ubicación en el Mapa</label>
                            <p class="text-muted small">Haz clic en el mapa o arrastra el marcador para actualizar la ubicación.</p>
                            <div id="map"></div>
                            <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud', $user->proveedor->latitud ?? '') }}">
                            <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud', $user->proveedor->longitud ?? '') }}">
                        </div>
                    </div>
                @endif


                <div class="form-section">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="fas fa-save"></i> Actualizar Usuario
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
@if($user->isProveedor())
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
@endif
@endsection
