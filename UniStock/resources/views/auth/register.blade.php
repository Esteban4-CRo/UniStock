@extends('layouts.app')

@section('content')
<x-auth-card title="CREAR CUENTA">
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf
        <!-- Role Selection -->
        <div class="mb-4">
            <label class="bw-label mb-3 d-block text-center">Selecciona tu Rol</label>
            <div class="d-flex gap-3 justify-content-center">
                <div class="role-option flex-fill">
                    <input type="radio" name="role" id="role_almacenista" value="almacenista" required style="display:none;">
                    <label for="role_almacenista">
                        <i class="fas fa-boxes role-icon"></i>
                        <span style="font-weight: 600; letter-spacing: 1px;">Almacenista</span>
                    </label>
                </div>
                <div class="role-option flex-fill">
                    <input type="radio" name="role" id="role_proveedor" value="proveedor" style="display:none;">
                    <label for="role_proveedor">
                        <i class="fas fa-truck role-icon"></i>
                        <span style="font-weight: 600; letter-spacing: 1px;">Proveedor</span>
                    </label>
                </div>
            </div>
            @error('role')
                <div class="text-danger small mt-2 text-center">{{ $message }}</div>
            @enderror
        </div>
        <!-- Basic Fields -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="bw-label">Nombre Completo</label>
                <input type="text" name="name" class="form-control bw-input" value="{{ old('name') }}" placeholder="Ingresa tu nombre" required>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="bw-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control bw-input" value="{{ old('email') }}" placeholder="tucorreo@ejemplo.com" required>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="bw-label">Contraseña</label>
                <input type="password" name="password" class="form-control bw-input" placeholder="••••••••" required>
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="bw-label">Confirmar</label>
                <input type="password" name="password_confirmation" class="form-control bw-input" placeholder="••••••••" required>
            </div>
        </div>
        <!-- Provider Fields (hidden by default) -->
        <div id="proveedorFields" style="display: none; animation: fadeIn 0.5s;">
            <div class="separator">Datos de Proveedor</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="bw-label">Empresa</label>
                    <input type="text" name="empresa" class="form-control bw-input" value="{{ old('empresa') }}" placeholder="Nombre de tu empresa">
                </div>
                <div class="col-md-6">
                    <label class="bw-label">RUC / NIT</label>
                    <input type="text" name="ruc" class="form-control bw-input" value="{{ old('ruc') }}" placeholder="Identificación">
                </div>
                <div class="col-md-6">
                    <label class="bw-label">Teléfono</label>
                    <input type="text" name="telefono_proveedor" class="form-control bw-input" value="{{ old('telefono_proveedor') }}" placeholder="+1 234 567 890">
                </div>
                <div class="col-md-12">
                    <label class="bw-label">Dirección</label>
                    <input type="text" name="direccion_proveedor" id="direccion_proveedor" class="form-control bw-input" value="{{ old('direccion_proveedor') }}" placeholder="Busca tu dirección">
                </div>
                <div class="col-12">
                    <div style="border-radius: 15px; overflow: hidden; border: 1px solid #e2e8f0; height: 200px; background: #f0f4f8;">
                        <div id="map" style="height: 100%;"></div>
                    </div>
                    <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
                    <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">
                    <input type="hidden" name="ciudad" id="ciudad" value="{{ old('ciudad') }}">
                    <input type="hidden" name="pais" id="pais" value="{{ old('pais') }}">
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn w-100 bw-btn">Completar Registro</button>
        </div>
    </form>
</x-auth-card>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
<script src="{{ asset('js/usuarios.js') }}"></script>
<script>
    document.querySelectorAll('input[name="role"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'proveedor') {
                document.getElementById('proveedorFields').style.display = 'block';
            } else {
                document.getElementById('proveedorFields').style.display = 'none';
            }
        });
    });
</script>
@endsection
