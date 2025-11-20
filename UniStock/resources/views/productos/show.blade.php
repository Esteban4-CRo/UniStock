@extends('layouts.app')

@section('content')
<style>
    .info-label {
        font-weight: 600;
        color: #7f8c8d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    .info-value {
        font-size: 1.25rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
    }
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .badge-activo {
        background-color: #27ae60;
        color: white;
    }
    .badge-inactivo {
        background-color: #e74c3c;
        color: white;
    }
    .owner-info {
        background-color: #ecf0f1;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white;">
                    <h1 class="mb-0">{{ $producto->nombre }}</h1>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <p class="info-label"><i class="fas fa-barcode"></i> Código</p>
                                <p class="info-value">
                                    <span class="badge badge-info">{{ $producto->codigo }}</span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <p class="info-label">Descripción</p>
                                <p class="info-value">{{ $producto->descripcion ?? 'N/A' }}</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="info-label">Stock Actual</p>
                                    <p class="info-value">
                                        <span class="badge" style="background-color: #3498db; color: white;">
                                            {{ $producto->stock_actual }} unidades
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="info-label">Precio</p>
                                    <p class="info-value text-success">
                                        ${{ number_format($producto->precio, 2) }}
                                    </p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="info-label">Estado</p>
                                <div>
                                    <span class="badge {{ $producto->estado == 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                                        {{ $producto->estado == 'activo' ? '✓ Activo' : '✗ Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="owner-info">
                                <p class="info-label mb-3">Propietario</p>
                                <p class="mb-0" style="color: #2c3e50; font-size: 1.1rem; font-weight: 500;">
                                    {{ $producto->user->name ?? 'Sistema' }}
                                </p>
                                <p style="color: #7f8c8d; font-size: 0.9rem; margin-top: 0.25rem;">
                                    {{ $producto->user->email ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-3 border-top flex-wrap">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        @if(Auth::check() && Auth::id() === $producto->user_id)
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas borrar este producto?');">
                                    <i class="fas fa-trash"></i> Borrar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0 mb-3">
                <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white;">
                    <h5 class="mb-0"><i class="fas fa-arrow-down"></i> Entrada</h5>
                </div>
                <div class="card-body">
                   <form action="{{ route('productos.entrada.store', $producto->id) }}" method="POST">
    @csrf
     <input type="hidden" name="producto_id" value="{{ $producto->id }}">
    <input type="number" name="cantidad" class="form-control mb-2" placeholder="Cantidad" min="1" required>
    <input type="text" name="motivo" class="form-control mb-2" placeholder="Motivo (opcional)">
    <button type="submit" class="btn btn-success w-100">Registrar Entrada</button>
        </form>
                </div>
            </div>

            <div class="card shadow border-0">
                <div class="card-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white;">
                    <h5 class="mb-0"><i class="fas fa-arrow-up"></i> Salida</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('productos.salida.store', $producto->id) }}" method="POST">
    @csrf
     <input type="hidden" name="producto_id" value="{{ $producto->id }}">
    <input type="number" name="cantidad" class="form-control mb-2" placeholder="Cantidad" min="1" required>
    <input type="text" name="motivo" class="form-control mb-2" placeholder="Motivo (opcional)">
    <button type="submit" class="btn btn-danger w-100">Registrar Salida</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
