@extends('layouts.app')

@section('title', $titulo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}" class="text-decoration-none" style="color: #6b7280;">Reportes</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #111827; font-weight: 600;">Vista Previa de Excel</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-file-excel text-success" style="font-size: 1.8rem;"></i> {{ $titulo }}
        </h2>
    </div>
    <a href="{{ $download_route }}" class="btn btn-success d-flex align-items-center gap-2 px-4 py-2 shadow-sm" style="background: #16a34a; border-color: #16a34a; font-weight: 700;">
        <i class="fas fa-download"></i> Descargar archivo .csv
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-light border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="text-muted fw-semibold" style="font-size: 0.9rem;">
            <i class="fas fa-list me-1"></i> Mostrando {{ count($datos) }} registros en tiempo real
        </span>
        <span class="badge text-success bg-success-subtle border border-success-subtle px-3 py-1 fw-bold rounded-pill" style="font-size: 0.8rem; background: #f0fdf4; color: #16a34a;">
            Listo para exportar
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="sticky-top" style="z-index: 10;">
                    <tr>
                        @foreach($headers as $h)
                            <th class="bg-light text-muted fw-bold py-3 px-4 text-uppercase border-bottom" style="font-size: 0.78rem; letter-spacing: 0.5px; border-top: none;">
                                {{ $h }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if($tipo === 'inventario')
                        @forelse($datos as $m)
                            <tr>
                                <td class="px-4 py-3 fw-bold text-dark" style="font-family: monospace;">{{ $m->codigo }}</td>
                                <td class="px-4 py-3 fw-semibold text-dark">{{ $m->nombre }}</td>
                                <td class="px-4 py-3 text-muted" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $m->descripcion }}">
                                    {{ $m->descripcion ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($m->cantidad == 0)
                                        <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Sin Stock</span>
                                    @elseif($m->cantidad <= $m->stock_minimo)
                                        <span class="text-warning fw-bold"><i class="fas fa-exclamation-circle me-1"></i> {{ $m->cantidad }}</span>
                                    @else
                                        <span class="text-dark fw-semibold">{{ $m->cantidad }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-muted">{{ $m->unidad_medida }}</td>
                                <td class="px-4 py-3 text-muted">{{ $m->stock_minimo }}</td>
                                <td class="px-4 py-3 text-muted">{{ $m->lote ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($m->ubicacion)
                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.78rem;">
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i> {{ $m->ubicacion->pasillo }}-{{ $m->ubicacion->estante }}-{{ $m->ubicacion->casillero }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-muted">{{ $m->proveedores->first() ? $m->proveedores->first()->empresa : 'Sin asignar' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted opacity-50"></i>
                                    <p class="mb-0">No se encontraron registros de inventario.</p>
                                </td>
                            </tr>
                        @endforelse
                    @else
                        @forelse($datos as $mov)
                            <tr class="{{ $mov['anulado'] === 'SÍ' ? 'opacity-75 bg-light' : '' }}">
                                <td class="px-4 py-3">
                                    @if($mov['tipo'] === 'ENTRADA')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.75rem; background: #f0fdf4; color: #16a34a; font-weight: 700;">
                                            <i class="fas fa-arrow-down me-1"></i> ENTRADA
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" style="font-size: 0.75rem; background: #fef2f2; color: #dc2626; font-weight: 700;">
                                            <i class="fas fa-arrow-up me-1"></i> SALIDA
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-dark fw-semibold">{{ $mov['fecha'] }}</td>
                                <td class="px-4 py-3 text-dark fw-bold">{{ $mov['material'] }}</td>
                                <td class="px-4 py-3 text-muted" style="font-family: monospace;">{{ $mov['codigo'] }}</td>
                                <td class="px-4 py-3 fw-bold {{ $mov['tipo'] === 'ENTRADA' ? 'text-success' : 'text-danger' }}">
                                    {{ $mov['cantidad'] }} {{ $mov['unidad'] }}
                                </td>
                                <td class="px-4 py-3 text-muted">{{ $mov['unidad'] }}</td>
                                <td class="px-4 py-3 text-muted">{{ $mov['lote'] }}</td>
                                <td class="px-4 py-3 text-muted">{{ $mov['referencia_destino'] }}</td>
                                <td class="px-4 py-3 text-muted">{{ $mov['usuario'] }}</td>
                                <td class="px-4 py-3 text-muted" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $mov['motivo'] }}">
                                    {{ $mov['motivo'] }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($mov['anulado'] === 'SÍ')
                                        <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;">ANULADO</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 0.7rem;">ACTIVO</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-3x mb-3 text-muted opacity-50"></i>
                                    <p class="mb-0">No se encontraron movimientos registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
