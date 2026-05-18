@extends('layouts.app')

@section('title', 'Ubicaciones de Almacén - UniStock')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark"><i class="fas fa-map-marker-alt text-dark me-2"></i> <span class="animate-soft-blur">Estructura del Almacén</span></h2>
        <p class="text-muted mb-0">Gestione los pasillos, estantes y casilleros físicos para la distribución de materias primas.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('ubicaciones.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i> Nueva Ubicación
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">Ubicación Física</th>
                        <th class="py-3">Pasillo</th>
                        <th class="py-3">Estante</th>
                        <th class="py-3">Casillero</th>
                        <th class="py-3">Descripción</th>
                        <th class="py-3 text-center">Materias Primas Vinculadas</th>
                        <th class="py-3 text-end pe-4" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ubicaciones as $ubicacion)
                        <tr>
                            <td class="py-3 ps-4 fw-bold text-dark">
                                <span class="d-inline-flex align-items-center">
                                    <span class="p-2 bg-light text-dark rounded-3 me-2 border">
                                        <i class="fas fa-warehouse text-dark"></i>
                                    </span>
                                    {{ $ubicacion->nombre_completo }}
                                </span>
                            </td>
                            <td class="py-3 text-muted">
                                <span class="badge bg-light text-dark border">{{ $ubicacion->pasillo }}</span>
                            </td>
                            <td class="py-3 text-muted">
                                <span class="badge bg-light text-dark border">{{ $ubicacion->estante }}</span>
                            </td>
                            <td class="py-3 text-muted">
                                <span class="badge bg-light text-dark border">{{ $ubicacion->casillero }}</span>
                            </td>
                            <td class="py-3 text-muted text-truncate" style="max-width: 250px;">
                                {{ $ubicacion->descripcion ?? 'Sin descripción' }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-dark text-white rounded-pill">
                                    {{ $ubicacion->materiales_primas_count }} Ítems
                                </span>
                            </td>
                            <td class="py-3 text-end pe-4">
                                <a href="{{ route('ubicaciones.edit', $ubicacion->id) }}" class="btn btn-sm btn-light border-0 me-1" title="Editar">
                                    <i class="fas fa-edit text-dark"></i>
                                </a>
                                <form action="{{ route('ubicaciones.destroy', $ubicacion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta ubicación física?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0" title="Eliminar">
                                        <i class="fas fa-trash-alt text-dark"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-map-signs fa-3x text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold">No hay ubicaciones registradas</h5>
                                <p class="text-muted mb-0">Comience creando ubicaciones para poder distribuir ordenadamente sus materias primas en el almacén.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $ubicaciones->links() }}
</div>
@endsection
