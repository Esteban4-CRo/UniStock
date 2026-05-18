@extends('layouts.app')

@section('title', 'Historial de Reportes — UniStock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 style="font-weight:800; font-size:1.5rem;">
        <i class="fas fa-file-pdf text-danger"></i> <span class="animate-soft-blur">Reportes</span>
    </h2>
    <span class="badge bg-dark text-white" style="font-size:0.85rem; padding: 0.5rem 1rem;">
        {{ $reportes->total() }} reportes generados
    </span>
</div>

<div class="row mb-4">
    <div class="{{ Auth::user()->isSuperUsuario() ? 'col-md-7' : 'col-12' }}">
        <div class="card border-0 shadow-sm h-100" style="background:#f8f9fa;">
            <div class="card-body">
                <h5 style="font-weight:700;"><i class="fas fa-plus-circle me-1"></i> Generar Nuevo Reporte</h5>
                <p class="text-muted small mb-3">Selecciona una materia prima del catálogo para generar y descargar un archivo PDF con todo su historial de entradas y salidas.</p>
                <form action="{{ route('reports.material-history') }}" method="POST" target="_blank" class="d-flex gap-2 align-items-center" onsubmit="setTimeout(function(){ window.location.reload(); }, 1000);">
                    @csrf
                    <select name="material_prima_id" class="form-select" required style="max-width:350px;">
                        <option value="" disabled selected>-- Seleccionar materia prima --</option>
                        @foreach($materiales as $m)
                            <option value="{{ $m->id }}">{{ $m->codigo }} - {{ $m->nombre }} (Stock: {{ $m->cantidad }} {{ $m->unidad_medida }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-danger" style="font-weight:600;">
                        <i class="fas fa-file-pdf"></i> Generar Reporte PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
    @if(Auth::user()->isSuperUsuario())
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100" style="background:#0f172a; color:#fff;">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 style="font-weight:700; color:#38bdf8;"><i class="fas fa-database me-1"></i> Respaldo Manual del Sistema</h5>
                    <p class="text-white-50 small mb-3">Descarga una copia completa y de seguridad de la base de datos de UniStock para resguardar la información ante cualquier eventualidad.</p>
                </div>
                <div>
                    <a href="{{ route('backup.download') }}" class="btn btn-info w-100 text-dark" style="font-weight:700;">
                        <i class="fas fa-download me-1"></i> Descargar Respaldo (.sqlite)
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="card border-0 shadow-sm mb-4" style="background:#e0f2fe; border-left: 5px solid #0284c7 !important; transform: none !important;">
    <div class="card-body">
        <h5 class="mb-3" style="font-weight:700; color:#0369a1;"><i class="fas fa-file-excel me-1 text-success"></i> Exportación de Reportes Generales a Excel</h5>
        <div class="row g-3">
            <!-- Inventario Actual -->
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-white shadow-sm" style="border-radius: 12px !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-success" style="font-size: 1.5rem;"><i class="fas fa-file-excel"></i></div>
                        <div>
                            <strong class="text-dark d-block">Inventario Actual</strong>
                            <span class="text-muted small">Listado completo de insumos activos y su stock.</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('excel.inventario.preview') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%;" title="Visualizar Inventario" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('excel.inventario') }}" class="btn btn-sm btn-success text-white d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%; background: #16a34a; border-color: #16a34a;" title="Descargar CSV">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Historial de Movimientos -->
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-white shadow-sm" style="border-radius: 12px !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-success" style="font-size: 1.5rem;"><i class="fas fa-history"></i></div>
                        <div>
                            <strong class="text-dark d-block">Historial de Movimientos</strong>
                            <span class="text-muted small">Auditoría de todas las entradas y salidas registradas.</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('excel.movimientos.preview') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%;" title="Visualizar Movimientos" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('excel.movimientos') }}" class="btn btn-sm btn-success text-white d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%; background: #16a34a; border-color: #16a34a;" title="Descargar CSV">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tipo / Materia Prima</th>
                        <th>Solicitado por</th>
                        <th>Fecha de Generación</th>
                        <th class="text-end pe-4" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $reporte)
                        <tr>
                            <td class="text-muted">{{ $reporte->id }}</td>
                            <td>
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-1"></i>
                                    <strong>{{ $reporte->reporte_nombre }}</strong>
                                </div>
                                @if($reporte->materialPrima)
                                    <small class="text-muted">
                                        <i class="fas fa-box me-1"></i>
                                        Código: {{ $reporte->materialPrima->codigo ?? '—' }}
                                        &bull;
                                        Stock: {{ $reporte->materialPrima->cantidad }} {{ $reporte->materialPrima->unidad_medida }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($reporte->user)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($reporte->user->photo)
                                            <img src="{{ asset('storage/' . $reporte->user->photo) }}"
                                                 style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:2px solid #eee;">
                                        @else
                                            <div style="width:28px;height:28px;border-radius:50%;background:#0b0b0b;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-user" style="color:#fff;font-size:12px;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight:600;font-size:0.9rem;">{{ $reporte->user->name }}</div>
                                            <div class="text-muted" style="font-size:0.78rem;">{{ $reporte->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted"><i class="fas fa-cog me-1"></i>Sistema</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $reporte->fecha_generacion->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size:0.82rem;">
                                    <i class="fas fa-clock me-1"></i>{{ $reporte->fecha_generacion->format('H:i A') }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                @if($reporte->materialPrima)
                                    <form action="{{ route('reports.material-history') }}" method="POST" target="_blank" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="material_prima_id" value="{{ $reporte->material_prima_id }}">
                                        <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%; margin-left: auto;" title="Visualizar Reporte PDF">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size:0.75rem;" title="El insumo fue eliminado físicamente del sistema">
                                        <i class="fas fa-exclamation-circle me-1"></i> No disponible
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-file-pdf text-muted" style="font-size:2.5rem;"></i>
                                <p class="mt-2 text-muted">Aún no se han generado reportes.<br>
                                    Abre una <a href="{{ route('materias-primas.index') }}">materia prima</a> y haz clic en <strong>Generar Historial (PDF)</strong>.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reportes->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3">
            {{ $reportes->links() }}
        </div>
    @endif
</div>
@endsection
