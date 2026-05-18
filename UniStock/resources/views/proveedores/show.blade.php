@extends('layouts.app')

@section('title', 'Detalle del Proveedor - UniStock')

@section('content')
<div class="mb-4">
    <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver al Directorio
    </a>
</div>

<div class="row g-4">
    <!-- Columna 1: Tarjeta del Proveedor -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 text-center">
                <div class="avatar-large bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 72px; height: 72px; font-size: 2rem; font-weight: 700;">
                    {{ strtoupper(substr($proveedor->empresa, 0, 2)) }}
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $proveedor->empresa }}</h4>
                <span class="badge bg-light text-dark border fw-semibold mb-3">{{ $proveedor->ruc }}</span>

                <div class="border-top pt-3 text-start">
                    <p class="mb-2 text-dark">
                        <i class="fas fa-phone-alt text-muted me-2" style="width: 20px;"></i>
                        <strong>Teléfono:</strong> <span class="text-muted ms-1">{{ $proveedor->telefono }}</span>
                    </p>
                    <p class="mb-2 text-dark">
                        <i class="fas fa-map-marker-alt text-muted me-2" style="width: 20px;"></i>
                        <strong>Dirección:</strong> <span class="text-muted ms-1">{{ $proveedor->direccion }}</span>
                    </p>
                    <p class="mb-2 text-dark">
                        <i class="fas fa-city text-muted me-2" style="width: 20px;"></i>
                        <strong>Ciudad:</strong> <span class="text-muted ms-1">{{ $proveedor->ciudad }}, {{ $proveedor->pais }}</span>
                    </p>
                </div>
            </div>
        </div>

        @if($proveedor->hasLocation())
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-dark me-2"></i> Localización Geográfica</h5>
            </div>
            <div class="card-body p-3">
                <div class="bg-light p-3 rounded-3 text-center mb-3">
                    <p class="small text-muted mb-1">Coordenadas del proveedor:</p>
                    <code class="text-dark fw-bold">{{ $proveedor->latitud }}, {{ $proveedor->longitud }}</code>
                </div>
                <div class="d-grid">
                    <a href="{{ $proveedor->google_maps_url }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="fas fa-external-link-alt me-2 text-dark"></i> Abrir en Google Maps
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Columna 2: Historial de Envíos/Entradas -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-history text-dark me-2"></i> Historial de Suministros</h5>
                <span class="badge bg-dark text-white rounded-pill">{{ $proveedor->entradas->count() }} Entradas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4">Materia Prima</th>
                                <th class="py-3">Cantidad</th>
                                <th class="py-3">Lote</th>
                                <th class="py-3">Fecha de Ingreso</th>
                                <th class="py-3">Registrado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proveedor->entradas as $entrada)
                                <tr>
                                    <td class="py-3 ps-4">
                                        @if($entrada->materialPrima)
                                            <a href="{{ route('materias-primas.show', $entrada->materialPrima->id) }}" class="text-decoration-none fw-bold text-dark">
                                                {{ $entrada->materialPrima->nombre }}
                                            </a>
                                            <span class="d-block text-muted small">Cód: {{ $entrada->materialPrima->codigo }}</span>
                                        @else
                                            <span class="text-muted">Material Eliminado</span>
                                        @endif
                                    </td>
                                    <td class="py-3 fw-bold text-dark">
                                        +{{ $entrada->cantidad }}
                                        <small class="text-muted font-monospace fw-normal">{{ $entrada->materialPrima ? $entrada->materialPrima->unidad_medida : '' }}</small>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-muted font-monospace">{{ $entrada->lote ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3 text-muted">
                                        {{ $entrada->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 text-muted">
                                        {{ $entrada->user ? $entrada->user->name : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="mb-3">
                                            <i class="fas fa-boxes fa-2x opacity-25"></i>
                                        </div>
                                        <span>Este proveedor no ha registrado ningún suministro de materia prima aún.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
