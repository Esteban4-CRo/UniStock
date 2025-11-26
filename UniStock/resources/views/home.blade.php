@extends('layouts.app')

@section('content')
<style>
/* ---------------------------
   ESTILOS DEL DASHBOARD
----------------------------*/
.dashboard-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-title i {
    color: #1abc9c;
    font-size: 28px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.card {
    background: #1e1e1e;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.4);
    color: #fff;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.6);
}

.card-number {
    font-size: 2rem;
    font-weight: bold;
    color: #1abc9c;
}

.card-label {
    margin-top: 5px;
    opacity: 0.7;
}
</style>

<div class="container">

    <h1 class="dashboard-title">
        <i class="fas fa-cube"></i> Panel Principal
    </h1>

    <!-- Estadísticas -->
    <div class="dashboard-grid">

        <div class="card">
            <div class="card-number">{{ $totalProductos }}</div>
            <div class="card-label">Productos Totales</div>
        </div>

        <div class="card">
            <div class="card-number">{{ $totalEntradas }}</div>
            <div class="card-label">Entradas Registradas</div>
        </div>

        <div class="card">
            <div class="card-number">{{ $totalSalidas }}</div>
            <div class="card-label">Salidas Registradas</div>
        </div>

    </div>


    <!-- Últimos productos -->
    <div class="card" style="margin-top: 2rem;">
        <h2 style="margin-bottom: 1rem; border-bottom: 1px solid #333; padding-bottom: 10px;">
            Productos Recientes
        </h2>

        @if($productos->count())
            <table class="table" style="color: #fff;">
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $p)
                    <tr>
                        <td>{{ $p->codigo }}</td>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $p->stock_actual }}</td>
                        <td>${{ number_format($p->precio,2) }}</td>
                        <td>
                            <a class="btn btn-success btn-sm" href="{{ route('entradas.create') }}?producto_id={{ $p->id }}">Entrada</a>
                            <a class="btn btn-danger btn-sm" href="{{ route('salidas.create') }}?producto_id={{ $p->id }}">Salida</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay productos.</p>
        @endif
    </div>

    <!-- Últimas Entradas -->
    <div class="card" style="margin-top: 2rem;">
        <div style="margin-bottom: 1rem; border-bottom: 1px solid #333; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">Últimas Entradas</h2>
            <a href="{{ route('entradas.index') }}" class="btn btn-sm btn-outline-light">Ver Todas</a>
        </div>

        @if($entradas->count())
            <table class="table" style="color: #fff;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entradas as $entrada)
                    <tr>
                        <td>{{ $entrada->producto->nombre }}</td>
                        <td><span class="badge bg-success">+{{ $entrada->cantidad }}</span></td>
                        <td>{{ $entrada->motivo }}</td>
                        <td>{{ $entrada->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="opacity: 0.7;">No hay entradas registradas.</p>
        @endif
    </div>

    <!-- Últimas Salidas -->
    <div class="card" style="margin-top: 2rem;">
        <div style="margin-bottom: 1rem; border-bottom: 1px solid #333; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">Últimas Salidas</h2>
            <a href="{{ route('salidas.index') }}" class="btn btn-sm btn-outline-light">Ver Todas</a>
        </div>

        @if($salidas->count())
            <table class="table" style="color: #fff;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salidas as $salida)
                    <tr>
                        <td>{{ $salida->producto->nombre }}</td>
                        <td><span class="badge bg-danger">-{{ $salida->cantidad }}</span></td>
                        <td>{{ $salida->motivo }}</td>
                        <td>{{ $salida->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="opacity: 0.7;">No hay salidas registradas.</p>
        @endif
    </div>

</div>


@endsection
