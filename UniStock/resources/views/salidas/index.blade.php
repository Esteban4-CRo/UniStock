@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-arrow-up"></i> Registro de Salidas</h2>
            <a href="{{ route('salidas.create') }}" class="btn btn-danger">
                <i class="fas fa-plus-circle"></i> Nueva Salida
            </a>
        </div>
    </div>
</div>

    @if($salidas->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="empty-state-title">No hay salidas</div>
                <div class="empty-state-text">Comienza creando tu primer producto en el sistema</div>
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Crear Producto
                        </a>
                    </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-barcode"></i> Código</th>
                        <th><i class="fas fa-box"></i> Producto</th>
                        <th><i class="fas fa-align-left"></i> Motivo</th>
                        <th><i class="fas fa-cubes"></i> Cantidad</th>
                        <th><i class="fas fa-clock"></i> Fecha origen</th>
                        <th><i class="fas fa-stopwatch"></i> Última actualización</th>
                        <th><i class="fas fa-cogs"></i> Acciones</th>
             
                    </tr>
                </thead>
                <tbody>
                    @foreach($salidas as $salidas)
                        <tr>
                            <td><span class="badge badge-info">{{ $salidas->producto->codigo }}</span></td>
                            <td><strong>{{ $salidas->producto->nombre }}</strong></td>
                            <td>{{ substr($salidas->motivo ?? 'N/A', 0, 40) }}{{ strlen($salida->motivo ?? '') > 40 ? '...' : '' }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-cubes"></i> {{ $salidas->cantidad }} unid.
                                </span>
                            </td>
                            <td>{{ $salidas->created_at->format('d/m/Y H:i') }}</td>

                            <td>{{ $salidas->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                
                                <a href="{{ route('productos.edit', $salidas->producto->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('productos.destroy', $salidas->producto->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
