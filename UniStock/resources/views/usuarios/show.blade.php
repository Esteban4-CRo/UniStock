@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">

<div class="usuarios-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user"></i> Detalles del Usuario</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    @if(auth()->user()->isSuperUsuario() || auth()->id() === $user->id)
                        <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif
                </div>
            </div>


            <div class="card" style="border-radius: 16px; overflow: hidden;">

                <div style="background: {{ $user->isProveedor() ? 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' : ($user->isGerente() ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : ($user->isSuperUsuario() ? 'linear-gradient(135deg, #000 0%, #434343 100%)' : 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)')) }}; padding: 2rem; text-align: center;">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 8px 20px rgba(0,0,0,0.3); margin-bottom: 1rem;">
                    @else
                        <div style="width: 150px; height: 150px; border-radius: 50%; background: white; display: inline-flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: bold; color: #667eea; border: 5px solid white; box-shadow: 0 8px 20px rgba(0,0,0,0.3); margin-bottom: 1rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <h3 style="color: white; margin-bottom: 0.5rem;">{{ $user->name }}</h3>
                    <div>{!! $user->role_badge !!}</div>
                </div>

                <div class="card-body" style="padding: 2rem;">

                    <div class="mb-4">
                        <h5 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                            <i class="fas fa-info-circle"></i> Información Básica
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                    <i class="fas fa-envelope"></i> EMAIL
                                </label>
                                <p style="font-size: 1.1rem; color: #2c3e50; margin: 0;">{{ $user->email }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                    <i class="fas fa-calendar"></i> REGISTRADO
                                </label>
                                <p style="font-size: 1.1rem; color: #2c3e50; margin: 0;">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>


                    @if($user->isProveedor() && $user->proveedor)
                        <div class="mb-4">
                            <h5 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                                <i class="fas fa-building"></i> Información de la Empresa
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                        <i class="fas fa-building"></i> EMPRESA
                                    </label>
                                    <p style="font-size: 1.1rem; color: #2c3e50; margin: 0;">{{ $user->proveedor->empresa }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                        <i class="fas fa-id-card"></i> RUC/NIT
                                    </label>
                                    <p style="font-size: 1.1rem; color: #2c3e50; margin: 0;">{{ $user->proveedor->ruc }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                        <i class="fas fa-phone"></i> TELÉFONO
                                    </label>
                                    <p style="font-size: 1.1rem; color: #2c3e50; margin: 0;">{{ $user->proveedor->telefono }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                        <i class="fas fa-map-marker-alt"></i> DIRECCIÓN
                                    </label>
                                    <p style="font-size: 1.1rem; color: #2c3e50; margin: 0;">{{ $user->proveedor->direccion }}</p>
                                </div>
                            </div>
                            
                            @if($user->proveedor->hasLocation())
                                <div class="mt-3">
                                    <label class="form-label" style="font-weight: 700; color: #6c757d; font-size: 0.85rem;">
                                        <i class="fas fa-map"></i> UBICACIÓN
                                    </label>
                                    <div id="map" style="width: 100%; height: 300px; border-radius: 12px; margin-top: 0.5rem;"></div>
                                </div>
                            @endif
                        </div>
                    @endif


                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        @if(auth()->user()->isSuperUsuario() || auth()->id() === $user->id)
                            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                        @if(auth()->user()->isSuperUsuario() && auth()->id() !== $user->id)
                            <form action="{{ route('usuarios.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($user->isProveedor() && $user->proveedor && $user->proveedor->hasLocation())
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
<script>
    function initMap() {
        const location = {
            lat: {{ $user->proveedor->latitud }},
            lng: {{ $user->proveedor->longitud }}
        };

        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: location,
            mapTypeControl: false,
            streetViewControl: false,
        });

        new google.maps.Marker({
            position: location,
            map: map,
            title: '{{ $user->proveedor->empresa }}'
        });
    }

    initMap();
</script>
@endif
@endsection
