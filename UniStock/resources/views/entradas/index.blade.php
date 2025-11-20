@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-arrow-down"></i> Registro de Entradas</h2>
            <a href="{{ route('entradas.create') }}" class="btn btn-danger">
                <i class="fas fa-plus-circle"></i> Nueva Entrada
            </a>
        </div>
    </div>
</div>

    @if($entradas->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="empty-state-title">No hay entradas</div>
                <div class="empty-state-text">Comienza creando tu primera entrada en el sistema</div>
                <a href="{{ route('entradas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Crear Entrada
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
                    @foreach($entradas as $entradas)
                        <tr>
                            <td><span class="badge badge-info">{{ $entradas->producto->codigo }}</span></td>
                            <td><strong>{{ $entradas->producto->nombre }}</strong></td>
                            <td>{{ substr($entradas->motivo ?? 'N/A', 0, 40) }}{{ strlen($entradas->motivo ?? '') > 40 ? '...' : '' }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-cubes"></i> {{ $entradas->cantidad }} unid.
                                </span>
                            </td>
                            <td>{{ $entradas->created_at->format('d/m/Y H:i') }}</td>

                             <td>{{ $entradas->updated_at->format('d/m/Y H:i') }}</td>

                            <td>
                                
                                <a href="{{ route('productos.edit', $entradas->producto->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('productos.destroy', $entradas->producto->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
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