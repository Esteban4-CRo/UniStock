@extends('layouts.app')

@section('title', 'Inicio — UniStock')

@section('content')

<div class="page-header">
    <div>
        <h1><i class="fas fa-cube text-accent me-2"></i>Panel Principal</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">Bienvenido, <strong>{{ Auth::user()->name }}</strong> &bull; {{ now()->format('d M Y') }}</p>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4 stats-row">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-box"></i></div>
            <div>
                <div class="stat-number">{{ $totalProductos }}</div>
                <div class="stat-label">Productos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-arrow-circle-down"></i></div>
            <div>
                <div class="stat-number">{{ $totalEntradas }}</div>
                <div class="stat-label">Entradas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-arrow-circle-up"></i></div>
            <div>
                <div class="stat-number">{{ $totalSalidas }}</div>
                <div class="stat-label">Salidas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-file-pdf"></i></div>
            <div>
                <div class="stat-number">{{ \App\Models\Reporte::count() }}</div>
                <div class="stat-label">Reportes</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Últimos Productos --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box text-accent me-1"></i> Productos Recientes</span>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
            </div>
            <div class="card-body p-0">
                @if($productos->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $p)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;font-size:.88rem;">{{ $p->nombre }}</div>
                                        <small class="text-muted">{{ $p->codigo }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $p->stock_actual <= ($p->stock_minimo ?? 5) ? 'bg-danger' : 'bg-success' }}" style="font-size:.76rem;">
                                            {{ $p->stock_actual }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a class="btn btn-success btn-sm" href="{{ route('entradas.create') }}?producto_id={{ $p->id }}"><i class="fas fa-plus"></i></a>
                                            <a class="btn btn-danger btn-sm" href="{{ route('salidas.create') }}?producto_id={{ $p->id }}"><i class="fas fa-minus"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state"><i class="fas fa-box-open d-block"></i>No hay productos registrados.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Últimas Entradas --}}
    <div class="col-12 col-lg-6">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-arrow-circle-down text-accent me-1"></i> Últimas Entradas</span>
                <a href="{{ route('entradas.index') }}" class="btn btn-secondary btn-sm">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @if($entradas->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Producto</th><th>Cantidad</th><th>Fecha</th></tr></thead>
                            <tbody>
                                @foreach($entradas as $e)
                                <tr>
                                    <td style="font-size:.87rem;">{{ $e->producto->nombre ?? '—' }}</td>
                                    <td><span class="badge bg-success">+{{ $e->cantidad }}</span></td>
                                    <td style="font-size:.8rem;color:#888;">{{ $e->created_at->format('d/m H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state py-3"><i class="fas fa-inbox d-block"></i>Sin entradas.</div>
                @endif
            </div>
        </div>

        {{-- Últimas Salidas --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-arrow-circle-up text-accent me-1"></i> Últimas Salidas</span>
                <a href="{{ route('salidas.index') }}" class="btn btn-secondary btn-sm">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @if($salidas->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Producto</th><th>Cantidad</th><th>Fecha</th></tr></thead>
                            <tbody>
                                @foreach($salidas as $s)
                                <tr>
                                    <td style="font-size:.87rem;">{{ $s->producto->nombre ?? '—' }}</td>
                                    <td><span class="badge bg-danger">-{{ $s->cantidad }}</span></td>
                                    <td style="font-size:.8rem;color:#888;">{{ $s->created_at->format('d/m H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state py-3"><i class="fas fa-inbox d-block"></i>Sin salidas.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
