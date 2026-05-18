@extends('layouts.app')

@section('title', 'Materias Primas - UniStock')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark"><i class="fas fa-dolly text-dark me-2"></i> <span class="animate-soft-blur">Inventario de Materias Primas</span></h2>
        <p class="text-muted mb-0">Gestión de stock, control de lotes, alertas de vencimiento y ubicación de insumos.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('materias-primas.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i> Registrar Materia Prima
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">Materia Prima</th>
                        <th class="py-3">Ubicación Física</th>
                        <th class="py-3">Stock Disponible</th>
                        <th class="py-3">Lote Actual</th>
                        <th class="py-3">Precio Unitario</th>
                        <th class="py-3">Vencimiento</th>
                        <th class="py-3 text-end pe-4" style="width: 250px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materiales as $material)
                        <tr>
                            <td class="py-3 ps-4">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $material->nombre }}</h6>
                                    <span class="badge bg-light text-dark border font-monospace mt-1">{{ $material->codigo }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                @if($material->ubicacion)
                                    <span class="d-inline-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-dark me-2"></i>
                                        <div>
                                            <span class="d-block text-dark fw-semibold small">{{ $material->ubicacion->nombre_completo }}</span>
                                            @if($material->ubicacion->descripcion)
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $material->ubicacion->descripcion }}</small>
                                            @endif
                                        </div>
                                    </span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-exclamation-triangle me-1"></i> No asignada</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @php
                                    $bajoStock = $material->cantidad <= $material->stock_minimo;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2 {{ $bajoStock ? 'text-danger' : 'text-dark' }}" style="font-size: 1.1rem;">
                                        {{ $material->cantidad }}
                                    </span>
                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.75rem;">
                                        {{ $material->unidad_medida }}
                                    </span>
                                    @if($bajoStock)
                                        <span class="ms-2 badge bg-danger text-white py-1 px-2" style="font-size: 0.7rem;">
                                            <i class="fas fa-exclamation-circle me-1"></i> STOCK BAJO
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Mínimo: {{ $material->stock_minimo }} {{ $material->unidad_medida }}</small>
                            </td>
                            <td class="py-3">
                                <span class="text-muted font-monospace">{{ $material->lote ?? 'Sin lote' }}</span>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-dark">${{ number_format($material->precio, 2) }}</span>
                            </td>
                            <td class="py-3">
                                @if($material->fecha_caducidad)
                                    @php
                                        $diasRestantes = now()->diffInDays($material->fecha_caducidad, false);
                                    @endphp
                                    @if($diasRestantes < 0)
                                        <span class="badge bg-danger text-white py-1 px-2">
                                            <i class="fas fa-times-circle me-1"></i> Vencido ({{ abs((int)$diasRestantes) }} días)
                                        </span>
                                    @elseif($diasRestantes <= 30)
                                        <span class="badge bg-warning text-dark py-1 px-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Vence en {{ $diasRestantes }} días
                                        </span>
                                    @else
                                        <span class="badge bg-success text-white py-1 px-2">
                                            <i class="fas fa-check-circle me-1"></i> Vigente
                                        </span>
                                    @endif
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ $material->fecha_caducidad->format('d/m/Y') }}</small>
                                @else
                                    <span class="text-muted small">No caduca</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('materias-primas.show', $material->id) }}" class="btn btn-sm btn-light border-0 me-1" title="Ver Ficha Técnica e Historial">
                                        <i class="fas fa-eye text-dark"></i>
                                    </a>
                                    <a href="{{ route('entradas.create') }}?material_prima_id={{ $material->id }}" class="btn btn-sm btn-light border-0 me-1" title="Registrar Entrada">
                                        <i class="fas fa-arrow-down text-dark"></i>
                                    </a>
                                    <a href="{{ route('salidas.create') }}?material_prima_id={{ $material->id }}" class="btn btn-sm btn-light border-0 me-1" title="Registrar Salida">
                                        <i class="fas fa-arrow-up text-dark"></i>
                                    </a>
                                    @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente() || Auth::id() === $material->user_id)
                                        <a href="{{ route('materias-primas.edit', $material->id) }}" class="btn btn-sm btn-light border-0 me-1" title="Editar">
                                            <i class="fas fa-edit text-dark"></i>
                                        </a>
                                        <form action="{{ route('materias-primas.destroy', $material->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta materia prima?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border-0" title="Eliminar">
                                                <i class="fas fa-trash-alt text-dark"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-box-open fa-3x text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold">No hay materias primas registradas</h5>
                                <p class="text-muted mb-0">Registre materias primas y asígnelas a sus ubicaciones de almacenamiento.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
