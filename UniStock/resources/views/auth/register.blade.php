@extends('layouts.app')

@section('content')
<style>
    /* Diseño en Blanco y Negro - Innovador y Elegante */
    body {
        background-color: #050505;
        color: #ffffff;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .bw-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 1);
        overflow: hidden;
    }

    .bw-input {
        background: transparent !important;
        border: none !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 0 !important;
        color: #ffffff !important;
        padding: 0.75rem 0 !important;
        transition: all 0.3s ease !important;
        box-shadow: none !important;
    }

    .bw-input:focus {
        border-bottom-color: #ffffff !important;
        background: rgba(255, 255, 255, 0.05) !important;
        padding-left: 0.5rem !important;
    }

    .bw-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .bw-label {
        color: #aaaaaa;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
        margin-bottom: 0;
    }

    .bw-btn {
        background-color: #ffffff;
        color: #000000;
        font-weight: 700;
        border-radius: 30px;
        padding: 1rem;
        border: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .bw-btn:hover {
        background-color: #cccccc;
        transform: translateY(-2px);
    }

    .bw-btn-google {
        background-color: transparent;
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 30px;
        padding: 0.75rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .bw-btn-google:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: #ffffff;
    }

    .role-option label {
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        cursor: pointer;
        display: block;
        text-align: center;
        color: rgba(255, 255, 255, 0.6);
        transition: all 0.3s;
    }

    .role-option input[type="radio"]:checked + label {
        border-color: #ffffff;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.05);
        transform: scale(1.02);
    }

    .role-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }

    .separator {
        display: flex;
        align-items: center;
        text-align: center;
        color: rgba(255, 255, 255, 0.4);
        margin: 2rem 0;
    }

    .separator::before,
    .separator::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .separator not(:empty)::before {
        margin-right: 1em;
    }

    .separator not(:empty)::after {
        margin-left: 1em;
    }

    .logo-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .logo-container img {
        max-height: 80px;
        filter: brightness(0) invert(1); /* Convierte el logo a blanco puro */
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="bw-card">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Logo Aquí -->
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="UniStock Logo" onerror="this.onerror=null; this.src=''; this.alt='[ IMAGEN DEL LOGO AQUÍ ]'; this.style.color='white';">
                    </div>

                    <div class="text-center mb-5">
                        <h2 style="font-weight: 800; letter-spacing: -1px;">CREAR CUENTA</h2>
                        <p style="color: #888;">Únete a la plataforma de inventario del futuro</p>
                    </div>

                    <!-- Botón Google -->
                    <a href="{{ route('google.login') }}" class="btn w-100 mb-3 d-flex align-items-center justify-content-center bw-btn-google">
                        <i class="fab fa-google me-2" style="font-size: 1.2rem;"></i> Continuar con Google
                    </a>

                    <div class="separator">O</div>

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf
                        
                        <!-- Selección de Rol -->
                        <div class="mb-5">
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

                        <!-- Campos Básicos -->
                        <div class="row g-4 mb-5">
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

                        <!-- Campos Proveedor -->
                        <div id="proveedorFields" style="display: none; animation: fadeIn 0.5s;">
                            <div class="separator">Datos de Proveedor</div>
                            <div class="row g-4 mb-4">
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
                                    <div style="border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); height: 200px; background: #111;">
                                        <div id="map" style="height: 100%;"></div>
                                    </div>
                                    <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
                                    <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">
                                    <input type="hidden" name="ciudad" id="ciudad" value="{{ old('ciudad') }}">
                                    <input type="hidden" name="pais" id="pais" value="{{ old('pais') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn w-100 bw-btn">
                                Completar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
