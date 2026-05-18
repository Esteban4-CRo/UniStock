@extends('layouts.app')

@section('title', 'Entradas de Inventario - UniStock')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark"><i class="fas fa-arrow-down text-dark me-2"></i> <span class="animate-soft-blur">Historial de Entradas</span></h2>
        <p class="text-muted mb-0">Listado completo de ingresos de materias primas y abastecimiento de inventario.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('entradas.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i> Nueva Entrada
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
                        <th class="py-3">Proveedor de Origen</th>
                        <th class="py-3">Cantidad Ingresada</th>
                        <th class="py-3">Lote Asignado</th>
                        <th class="py-3">Fecha de Ingreso</th>
                        <th class="py-3">Registrado Por</th>
                        <th class="py-3 text-truncate" style="max-width: 200px;">Motivo</th>
                        <th class="py-3 text-end pe-4" style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entradas as $entrada)
                        <tr @if($entrada->anulado) class="opacity-75 bg-light" style="background-color: #fafaf9;" @endif>
                            <td class="py-3 ps-4">
                                @if($entrada->materialPrima)
                                    <a href="{{ route('materias-primas.show', $entrada->materialPrima->id) }}" class="text-decoration-none fw-bold text-dark">
                                        {{ $entrada->materialPrima->nombre }}
                                    </a>
                                    <span class="badge bg-light text-dark border font-monospace ms-1" style="font-size: 0.7rem;">{{ $entrada->materialPrima->codigo }}</span>
                                @else
                                    <span class="text-muted font-italic">Material Eliminado</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($entrada->proveedor)
                                    <a href="{{ route('proveedores.show', $entrada->proveedor->id) }}" class="text-decoration-none fw-semibold text-dark">
                                        {{ $entrada->proveedor->empresa }}
                                    </a>
                                @else
                                    <span class="text-muted small">No especificado</span>
                                @endif
                            </td>
                            <td class="py-3 fw-bold @if($entrada->anulado) text-muted text-decoration-line-through @else text-success @endif">
                                +{{ $entrada->cantidad }}
                                <small class="text-muted font-monospace fw-normal" style="font-size: 0.75rem;">
                                    {{ $entrada->materialPrima ? $entrada->materialPrima->unidad_medida : '' }}
                                </small>
                                @if($entrada->anulado)
                                    <span class="badge bg-danger text-white ms-1" style="font-size: 0.65rem; text-decoration: none; display: inline-block;">Anulado</span>
                                @endif
                            </td>
                            <td class="py-3 font-monospace text-muted small">
                                {{ $entrada->lote ?? 'N/A' }}
                            </td>
                            <td class="py-3 text-muted small">
                                {{ $entrada->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 text-muted small">
                                <i class="fas fa-user-circle text-muted me-1"></i>
                                {{ $entrada->user ? $entrada->user->name : 'N/A' }}
                            </td>
                            <td class="py-3 text-muted small text-truncate" style="max-width: 200px;" title="{{ $entrada->motivo }}">
                                {{ $entrada->motivo ?? 'Sin notas' }}
                            </td>
                                <td class="py-3 text-end pe-4">
                                    @if(!$entrada->anulado)
                                        @php
                                            $puedeEditar = false;
                                            $puedeAnular = Auth::user()->isSuperUsuario() || Auth::user()->isGerente();
                                            
                                            if ($puedeAnular) {
                                                $puedeEditar = true;
                                            } elseif (Auth::user()->isAlmacenista() && $entrada->user_id === Auth::id() && $entrada->created_at->diffInMinutes(now()) <= 5) {
                                                $puedeEditar = true;
                                            }
                                        @endphp
                                        @if($puedeEditar)
                                            <a href="{{ route('entradas.edit', $entrada->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2 me-1" style="font-size: 0.78rem;" title="Editar Movimiento">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        @endif
                                        @if($puedeAnular)
                                            <form action="{{ route('entradas.anular', $entrada->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas ANULAR esta entrada de inventario? Se descontará la cantidad del stock actual.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.78rem;" title="Anular Movimiento">
                                                    <i class="fas fa-ban"></i> Anular
                                                </button>
                                            </form>
                                        @endif
                                        @if(!$puedeEditar && !$puedeAnular)
                                            <span class="text-muted small" title="Cerrado: Solo administradores pueden anular, o el creador dentro de los primeros 5 minutos puede editar"><i class="fas fa-lock"></i> Cerrado</span>
                                        @endif
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem; font-weight: 600;"><i class="fas fa-undo me-1"></i> Revertido</span>
                                    @endif
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-boxes fa-3x text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold">No hay entradas de inventario registradas</h5>
                                <p class="text-muted mb-0">Registre ingresos de materia prima para aumentar los niveles de stock.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection