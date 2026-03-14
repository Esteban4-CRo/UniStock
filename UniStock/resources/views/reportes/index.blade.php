@extends('layouts.app')

@section('title', 'Historial de Reportes — UniStock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 style="font-weight:800; font-size:1.5rem;">
        <i class="fas fa-file-pdf text-danger"></i> Historial de Reportes
    </h2>
    <span class="badge" style="background:#0b0b0b; font-size:0.85rem; padding: 0.5rem 1rem;">
        {{ $reportes->total() }} reportes generados
    </span>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tipo / Producto</th>
                        <th>Solicitado por</th>
                        <th>Fecha de Generación</th>
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
                                @if($reporte->producto)
                                    <small class="text-muted">
                                        <i class="fas fa-box me-1"></i>
                                        Código: {{ $reporte->producto->codigo ?? '—' }}
                                        &bull;
                                        Stock: {{ $reporte->producto->stock_actual }} uds.
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-file-pdf text-muted" style="font-size:2.5rem;"></i>
                                <p class="mt-2 text-muted">Aún no se han generado reportes.<br>
                                    Abre un <a href="{{ route('productos.index') }}">producto</a> y haz clic en <strong>Generar Reporte PDF</strong>.
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
