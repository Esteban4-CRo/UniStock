@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-box"></i> Gestión de Productos</h2>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
        </div>
    </div>
</div>

@if($productos->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="empty-state-title">No hay productos</div>
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
                        <th><i class="fas fa-box"></i> Nombree</th>
                        <th><i class="fas fa-align-left"></i> Descripción</th>
                        <th><i class="fas fa-cubes"></i> Stock</th>
                        <th><i class="fas fa-toggle-on"></i> Estado</th>
                        <th><i class="fas fa-dollar-sign"></i> Precio</th>
                        <th><i class="fas fa-cogs"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        <tr>
                            <td><span class="badge badge-info">{{ $producto->codigo }}</span></td>
                            <td><strong>{{ $producto->nombre }}</strong></td>
                            <td>{{ substr($producto->descripcion ?? 'N/A', 0, 40) }}{{ strlen($producto->descripcion ?? '') > 40 ? '...' : '' }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-cubes"></i> {{ $producto->stock_actual }} unid.
                                </span>
                            </td>
                            <td>
                                @if($producto->estado == 'activo')
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>
                                @endif
                            </td>
                            <td><strong class="text-success">${{ number_format($producto->precio, 2) }}</strong></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('productos.show', $producto) }}" class="btn btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('entradas.create') }}?producto_id={{ $producto->id }}" class="btn btn-success" title="Entrada">
                                        <i class="fas fa-arrow-down"></i>
                                    </a>
                                    <a href="{{ route('salidas.create') }}?producto_id={{ $producto->id }}" class="btn btn-warning" title="Salida">
                                        <i class="fas fa-arrow-up"></i>
                                    </a>
                                    @if(auth()->id() === $producto->user_id)
                                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection