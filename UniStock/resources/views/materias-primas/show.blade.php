@extends('layouts.app')

@section('title', 'Ficha Técnica - UniStock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('materias-primas.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
        </a>
    </div>
    <div>
        <form action="{{ route('reports.material-history') }}" method="POST" target="_blank" class="d-inline">
            @csrf
            <input type="hidden" name="material_prima_id" value="{{ $materiales_prima->id }}">
            <button type="submit" class="btn btn-outline-danger px-3">
                <i class="fas fa-file-pdf me-2"></i> Generar Historial (PDF)
            </button>
        </form>
        @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente() || Auth::id() === $materiales_prima->user_id)
            <a href="{{ route('materias-primas.edit', $materiales_prima->id) }}" class="btn btn-primary px-3 ms-2">
                <i class="fas fa-edit me-2"></i> Editar Ficha
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Ficha Técnica General -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 py-3 bg-light">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle text-dark me-2"></i> Ficha Técnica del Insumo</h5>
            </div>
            <div class="card-body p-4">
                <h3 class="fw-bold text-dark mb-1">{{ $materiales_prima->nombre }}</h3>
                <span class="badge bg-light text-dark border font-monospace mb-4">{{ $materiales_prima->codigo }}</span>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-3 border">
                            <small class="text-muted d-block mb-1">Stock Disponible</small>
                            <span class="h4 fw-bold text-dark mb-0">{{ $materiales_prima->cantidad }}</span>
                            <span class="badge bg-dark text-white font-monospace ms-1" style="font-size: 0.7rem;">{{ $materiales_prima->unidad_medida }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-3 border">
                            <small class="text-muted d-block mb-1">Stock Mínimo</small>
                            <span class="h4 fw-bold text-muted mb-0">{{ $materiales_prima->stock_minimo }}</span>
                            <span class="badge bg-light text-dark border font-monospace ms-1" style="font-size: 0.7rem;">{{ $materiales_prima->unidad_medida }}</span>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <p class="mb-2 text-dark">
                            <i class="fas fa-barcode text-muted me-2" style="width: 20px;"></i>
                            <strong>Código de Lote:</strong>
                            <span class="text-muted font-monospace ms-1">{{ $materiales_prima->lote ?? 'Sin lote asignado' }}</span>
                        </p>
                        <p class="mb-2 text-dark">
                            <i class="fas fa-map-marker-alt text-muted me-2" style="width: 20px;"></i>
                            <strong>Ubicación Física:</strong>
                            @if($materiales_prima->ubicacion)
                                <span class="badge bg-dark text-white ms-1">{{ $materiales_prima->ubicacion->nombre_completo }}</span>
                            @else
                                <span class="badge bg-warning text-dark ms-1">No asignada</span>
                            @endif
                        </p>
                        <p class="mb-2 text-dark">
                            <i class="fas fa-dollar-sign text-muted me-2" style="width: 20px;"></i>
                            <strong>Precio Unitario:</strong>
                            <span class="text-dark fw-bold ms-1">${{ number_format($materiales_prima->precio, 2) }}</span>
                        </p>
                        <p class="mb-2 text-dark">
                            <i class="fas fa-calendar-alt text-muted me-2" style="width: 20px;"></i>
                            <strong>Fecha de Vencimiento:</strong>
                            @if($materiales_prima->fecha_caducidad)
                                <span class="text-muted ms-1">{{ $materiales_prima->fecha_caducidad->format('d/m/Y') }}</span>
                                @php
                                    $diasRestantes = now()->diffInDays($materiales_prima->fecha_caducidad, false);
                                @endphp
                                @if($diasRestantes < 0)
                                    <span class="badge bg-danger text-white py-1 px-2 ms-2">Vencido</span>
                                @elseif($diasRestantes <= 30)
                                    <span class="badge bg-warning text-dark py-1 px-2 ms-2">Por vencer</span>
                                @else
                                    <span class="badge bg-success text-white py-1 px-2 ms-2">Vigente</span>
                                @endif
                            @else
                                <span class="text-muted ms-1">No caduca</span>
                            @endif
                        </p>
                        <p class="mb-0 text-dark">
                            <i class="fas fa-user-circle text-muted me-2" style="width: 20px;"></i>
                            <strong>Registrado por:</strong>
                            <span class="text-muted ms-1">{{ $materiales_prima->user ? $materiales_prima->user->name : 'N/A' }}</span>
                        </p>
                    </div>
                </div>

                @if($materiales_prima->descripcion)
                <div class="mt-4 border-top pt-3">
                    <h6 class="fw-bold text-dark">Descripción o Especificaciones:</h6>
                    <p class="text-muted mb-0 small">{{ $materiales_prima->descripcion }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historial de Transacciones (Entradas y Salidas) -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3 bg-light">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-history text-dark me-2"></i> Historial de Movimientos de Inventario</h5>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs border-bottom px-4" id="transactionTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 fw-bold text-dark border-0" id="entradas-tab" data-bs-toggle="tab" data-bs-target="#entradas" type="button" role="tab"><i class="fas fa-arrow-down text-success me-1"></i> Entradas ({{ $materiales_prima->entradas->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold text-dark border-0 ms-3" id="salidas-tab" data-bs-toggle="tab" data-bs-target="#salidas" type="button" role="tab"><i class="fas fa-arrow-up text-danger me-1"></i> Salidas ({{ $materiales_prima->salidas->count() }})</button>
                    </li>
                </ul>

                <div class="tab-content" id="transactionTabsContent">
                    <!-- Pestaña Entradas -->
                    <div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 ps-4">Proveedor</th>
                                        <th class="py-3">Cantidad</th>
                                        <th class="py-3">Lote</th>
                                        <th class="py-3">Fecha</th>
                                        <th class="py-3">Operario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materiales_prima->entradas as $entrada)
                                        <tr>
                                            <td class="py-3 ps-4 fw-semibold text-dark">
                                                {{ $entrada->proveedor ? $entrada->proveedor->empresa : 'Desconocido' }}
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">RUC: {{ $entrada->proveedor ? $entrada->proveedor->ruc : 'N/A' }}</small>
                                            </td>
                                            <td class="py-3 text-success fw-bold">
                                                +{{ $entrada->cantidad }}
                                                <small class="text-muted fw-normal font-monospace" style="font-size: 0.75rem;">{{ $materiales_prima->unidad_medida }}</small>
                                            </td>
                                            <td class="py-3 font-monospace text-muted small">
                                                {{ $entrada->lote ?? 'N/A' }}
                                            </td>
                                            <td class="py-3 text-muted small">
                                                {{ $entrada->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="py-3 text-muted small">
                                                {{ $entrada->user ? $entrada->user->name : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-boxes fa-2x opacity-25 mb-2"></i>
                                                <p class="mb-0">No se registran entradas para esta materia prima.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pestaña Salidas -->
                    <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 ps-4">Destino</th>
                                        <th class="py-3">Cantidad</th>
                                        <th class="py-3">Lote</th>
                                        <th class="py-3">Fecha</th>
                                        <th class="py-3">Operario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materiales_prima->salidas as $salida)
                                        <tr>
                                            <td class="py-3 ps-4 fw-semibold text-dark">
                                                {{ $salida->destino }}
                                                @if($salida->motivo)
                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Motivo: {{ $salida->motivo }}</small>
                                                @endif
                                            </td>
                                            <td class="py-3 text-danger fw-bold">
                                                -{{ $salida->cantidad }}
                                                <small class="text-muted fw-normal font-monospace" style="font-size: 0.75rem;">{{ $materiales_prima->unidad_medida }}</small>
                                            </td>
                                            <td class="py-3 font-monospace text-muted small">
                                                {{ $salida->lote ?? 'N/A' }}
                                            </td>
                                            <td class="py-3 text-muted small">
                                                {{ $salida->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="py-3 text-muted small">
                                                {{ $salida->user ? $salida->user->name : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-boxes fa-2x opacity-25 mb-2"></i>
                                                <p class="mb-0">No se registran salidas para esta materia prima.</p>
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
    </div>
</div>
@endsection
