@extends('layouts.app')

@section('title', 'Panel Principal - UniStock')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold mb-1"><i class="fas fa-chart-pie me-2" style="color: var(--primary);"></i> <span class="animate-soft-blur">Panel de Control Logístico</span></h2>
        <p class="text-muted mb-0">Resumen y estado operativo del almacén de materias primas.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group">
            <a href="{{ route('materias-primas.create') }}" class="btn btn-outline-secondary btn-sm px-3"><i class="fas fa-plus me-1"></i> Materia Prima</a>
            <a href="{{ route('entradas.create') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1"><i class="fas fa-arrow-down me-1"></i> Registrar Entrada</a>
            <a href="{{ route('salidas.create') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1"><i class="fas fa-arrow-up me-1"></i> Registrar Salida</a>
        </div>
    </div>
</div>

<!-- Grid de Indicadores -->
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl">
        <div class="card-base glass p-3 text-center transition-card" style="transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <i class="fas fa-cubes text-info opacity-75 fs-4"></i>
                <span class="badge font-monospace" style="background: var(--primary); color: var(--bg); font-size: 0.7rem;">Activos</span>
            </div>
            <h3 class="fw-bold mb-1 mt-2 animate-spring-scale" style="font-size: 2.4rem;">{{ $totalMaterias }}</h3>
            <span class="small opacity-75 uppercase fw-semibold text-truncate d-block animate-micro-scale" style="font-size: 0.8rem; letter-spacing: 0.5px;">Materias Primas</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card-base glass h-100 p-3 text-center transition-card" style="transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <i class="fas fa-truck text-primary opacity-75 fs-4"></i>
                <span class="badge border font-monospace" style="background: var(--bot-bg); color: var(--text); font-size: 0.7rem;">Socios</span>
            </div>
            <h3 class="fw-bold mb-1 mt-2 animate-spring-scale" style="font-size: 2.4rem;">{{ $totalProveedores }}</h3>
            <span class="small text-muted uppercase fw-semibold text-truncate d-block animate-micro-scale" style="font-size: 0.8rem; letter-spacing: 0.5px;">Proveedores</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card-base glass h-100 p-3 text-center transition-card" style="transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <i class="fas fa-map-marker-alt text-warning opacity-75 fs-4"></i>
                <span class="badge border font-monospace" style="background: var(--bot-bg); color: var(--text); font-size: 0.7rem;">Zonas</span>
            </div>
            <h3 class="fw-bold mb-1 mt-2 animate-spring-scale" style="font-size: 2.4rem;">{{ $totalUbicaciones }}</h3>
            <span class="small text-muted uppercase fw-semibold text-truncate d-block animate-micro-scale" style="font-size: 0.8rem; letter-spacing: 0.5px;">Ubicaciones</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card-base glass h-100 p-3 text-center transition-card" style="transition: all 0.3s ease; border-left: 4px solid var(--bs-success) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <i class="fas fa-arrow-alt-circle-down text-success fs-4"></i>
                <span class="badge bg-success bg-opacity-10 text-success font-monospace" style="font-size: 0.7rem;">Ingresos</span>
            </div>
            <h3 class="fw-bold text-success mb-1 mt-2 animate-spring-scale" style="font-size: 2.4rem;">+{{ $totalEntradas }}</h3>
            <span class="small text-muted uppercase fw-semibold text-truncate d-block animate-micro-scale" style="font-size: 0.8rem; letter-spacing: 0.5px;">Entradas Totales</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card-base glass h-100 p-3 text-center transition-card" style="transition: all 0.3s ease; border-left: 4px solid var(--bs-danger) !important;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <i class="fas fa-arrow-alt-circle-up text-danger fs-4"></i>
                <span class="badge bg-danger bg-opacity-10 text-danger font-monospace" style="font-size: 0.7rem;">Despachos</span>
            </div>
            <h3 class="fw-bold text-danger mb-1 mt-2 animate-spring-scale" style="font-size: 2.4rem;">-{{ $totalSalidas }}</h3>
            <span class="small text-muted uppercase fw-semibold text-truncate d-block animate-micro-scale" style="font-size: 0.8rem; letter-spacing: 0.5px;">Salidas Totales</span>
        </div>
    </div>
</div>

<style>
.transition-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
</style>

<div class="row g-4">
    <!-- Tabla 1: Materias Primas Recientes -->
    <div class="col-12 col-xl-7">
        <div class="card glass shadow-sm border-0 h-100" style="background: transparent;">
            <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between" style="background: transparent;">
                <h5 class="fw-bold mb-0"><i class="fas fa-box me-2" style="color: var(--primary);"></i> Materias Primas Recientes</h5>
                <a href="{{ route('materias-primas.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold p-0" style="color: var(--primary);">Ver Inventario completo</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4">Nombre</th>
                                <th class="py-3">Ubicación</th>
                                <th class="py-3">Stock Actual</th>
                                <th class="py-3">Precio</th>
                                <th class="py-3 text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materiales as $p)
                                <tr>
                                    <td class="py-3 ps-4">
                                        <div class="fw-bold">{{ $p->nombre }}</div>
                                        <span class="font-monospace text-muted small" style="font-size: 0.7rem;">{{ $p->codigo }}</span>
                                    </td>
                                    <td class="py-3">
                                        @if($p->ubicacion)
                                            <span>{{ $p->ubicacion->nombre_completo }}</span>
                                        @else
                                            <span class="text-muted small">No asignada</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold">{{ $p->cantidad }}</span>
                                        <span class="text-muted font-monospace small" style="font-size: 0.75rem;">{{ $p->unidad_medida }}</span>
                                    </td>
                                    <td class="py-3 fw-semibold">
                                        ${{ number_format($p->precio, 2) }}
                                    </td>
                                    <td class="py-3 text-end pe-4">
                                        <a href="{{ route('entradas.create') }}?material_prima_id={{ $p->id }}" class="btn btn-sm border-0 me-1" style="background: var(--bot-bg); color: var(--text);" title="Entrada">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                        <a href="{{ route('salidas.create') }}?material_prima_id={{ $p->id }}" class="btn btn-sm border-0" style="background: var(--bot-bg); color: var(--text);" title="Salida">
                                            <i class="fas fa-arrow-up"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        No hay materias primas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Historiales Rápidos -->
    <div class="col-12 col-xl-5">
        <!-- Últimas Entradas -->
        <div class="card glass shadow-sm border-0 mb-4" style="background: transparent;">
            <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between" style="background: transparent;">
                <h6 class="fw-bold mb-0"><i class="fas fa-arrow-down text-success me-2"></i> Últimos Ingresos</h6>
                <a href="{{ route('entradas.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold p-0" style="color: var(--primary);">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <tbody>
                            @forelse($entradas as $entrada)
                                <tr>
                                    <td class="py-3 ps-3">
                                        <div class="fw-bold">{{ $entrada->materialPrima ? $entrada->materialPrima->nombre : 'Insumo Eliminado' }}</div>
                                        <small class="text-muted"><i class="fas fa-truck me-1"></i>{{ $entrada->proveedor ? $entrada->proveedor->empresa : 'N/A' }}</small>
                                    </td>
                                    <td class="py-3 text-success fw-bold">
                                        +{{ $entrada->cantidad }}
                                    </td>
                                    <td class="py-3 text-muted text-end pe-3" style="font-size: 0.75rem;">
                                        {{ $entrada->created_at->format('d/m H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        Sin entradas recientes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Últimas Salidas -->
        <div class="card glass shadow-sm border-0" style="background: transparent;">
            <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between" style="background: transparent;">
                <h6 class="fw-bold mb-0"><i class="fas fa-arrow-up text-danger me-2"></i> Últimos Despachos</h6>
                <a href="{{ route('salidas.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold p-0" style="color: var(--primary);">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <tbody>
                            @forelse($salidas as $salida)
                                <tr>
                                    <td class="py-3 ps-3">
                                        <div class="fw-bold">{{ $salida->materialPrima ? $salida->materialPrima->nombre : 'Insumo Merma' }}</div>
                                        <small class="text-muted"><i class="fas fa-location-arrow me-1"></i>{{ $salida->destino }}</small>
                                    </td>
                                    <td class="py-3 text-danger fw-bold">
                                        -{{ $salida->cantidad }}
                                    </td>
                                    <td class="py-3 text-muted text-end pe-3" style="font-size: 0.75rem;">
                                        {{ $salida->created_at->format('d/m H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        Sin salidas recientes.
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

<!-- Nueva Sección: Accesos Rápidos y Estado -->
<div class="row g-4 mt-1 mb-5">
    <!-- Estado General -->
    <div class="col-lg-8">
        <div class="card glass shadow-sm border-0 h-100" style="background: transparent; border-radius: 16px;">
            <div class="card-header border-0 py-3 pb-0" style="background: transparent;">
                <h6 class="fw-bold mb-0"><i class="fas fa-server me-2" style="color: var(--primary);"></i> Estado del Almacén</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center h-100">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Capacidad Ocupada (Aprox)</span>
                            <span class="small fw-bold text-primary">68%</span>
                        </div>
                        <div class="progress" style="height: 10px; background: rgba(0,0,0,0.1); border-radius: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 68%; background: var(--primary); border-radius: 10px;" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted small mt-2 mb-0" style="font-size: 0.8rem;">El stock general se encuentra en niveles estables. No hay alertas de sobrecapacidad inminente.</p>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="d-flex justify-content-center gap-4">
                            <div class="text-center">
                                <h4 class="fw-bold mb-0 animate-spring-scale" style="color: var(--primary);">+12</h4>
                                <span class="small text-muted">Mov. Hoy</span>
                            </div>
                            <div class="text-center">
                                <h4 class="fw-bold mb-0 animate-spring-scale text-success">100%</h4>
                                <span class="small text-muted">Sincronización</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="col-lg-4">
        <div class="card glass shadow-sm border-0 h-100" style="background: transparent; border-radius: 16px;">
            <div class="card-header border-0 py-3 pb-0" style="background: transparent;">
                <h6 class="fw-bold mb-0"><i class="fas fa-bolt me-2 text-warning"></i> Operaciones Rápidas</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2 justify-content-center">
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary text-start w-100 py-2 d-flex align-items-center" style="border-radius: 10px; border-color: rgba(255,255,255,0.1);">
                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; background: var(--bot-bg); border-radius: 8px;">
                        <i class="fas fa-file-pdf" style="color: var(--text);"></i>
                    </div>
                    <span class="fw-bold" style="color: var(--text);">Generar Reportes</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary text-start w-100 py-2 d-flex align-items-center" style="border-radius: 10px; border-color: rgba(255,255,255,0.1);">
                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; background: var(--bot-bg); border-radius: 8px;">
                        <i class="fas fa-user-cog" style="color: var(--text);"></i>
                    </div>
                    <span class="fw-bold" style="color: var(--text);">Mi Perfil</span>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
