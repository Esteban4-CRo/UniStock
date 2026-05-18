@extends('layouts.app')

@section('title', 'Editar Materia Prima - UniStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8">
        <div class="mb-4">
            <a href="{{ route('materias-primas.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3">
                <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit me-2 text-dark"></i> Editar Materia Prima</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('materias-primas.update', $materiales_prima->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Información de Identificación</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código de Catálogo</label>
                            <input type="text" class="form-control" id="codigo" value="{{ $materiales_prima->codigo }}" disabled>
                            <small class="text-muted mt-1 d-block">El código del insumo no se puede modificar.</small>
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre del Insumo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $materiales_prima->nombre) }}" placeholder="Ej. Harina de Trigo Industrial de 50kg" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción Técnica</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Detalles sobre el material, propiedades o condiciones especiales de almacenamiento...">{{ old('descripcion', $materiales_prima->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Control de Stock y Costos</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="cantidad" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" value="{{ old('cantidad', $materiales_prima->cantidad) }}" min="0" required>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                            <select class="form-select @error('unidad_medida') is-invalid @enderror" id="unidad_medida" name="unidad_medida" required>
                                <option value="kg" {{ old('unidad_medida', $materiales_prima->unidad_medida) === 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                                <option value="gramo" {{ old('unidad_medida', $materiales_prima->unidad_medida) === 'gramo' ? 'selected' : '' }}>Gramo (g)</option>
                                <option value="saco" {{ old('unidad_medida', $materiales_prima->unidad_medida) === 'saco' ? 'selected' : '' }}>Saco</option>
                                option value="bulto" {{ old('unidad_medida', $materiales_prima->unidad_medida) === 'bulto' ? 'selected' : '' }}>Bulto</option>
                                <option value="litro" {{ old('unidad_medida', $materiales_prima->unidad_medida) === 'litro' ? 'selected' : '' }}>Litro (L)</option>
                                <option value="unidad" {{ old('unidad_medida', $materiales_prima->unidad_medida) === 'unidad' ? 'selected' : '' }}>Unidad</option>
                            </select>
                            @error('unidad_medida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="stock_minimo" class="form-label">Stock Mínimo Alerta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $materiales_prima->stock_minimo) }}" min="0" required>
                            @error('stock_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="precio" class="form-label">Precio Unitario ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('precio') is-invalid @enderror" id="precio" name="precio" value="{{ old('precio', $materiales_prima->precio) }}" placeholder="0.00" min="0" required>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="lote" class="form-label">Código de Lote</label>
                            <input type="text" class="form-control @error('lote') is-invalid @enderror" id="lote" name="lote" value="{{ old('lote', $materiales_prima->lote) }}" placeholder="Ej. LOTE-1234">
                            @error('lote')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_caducidad" class="form-label">Fecha de Caducidad</label>
                            <input type="date" class="form-control @error('fecha_caducidad') is-invalid @enderror" id="fecha_caducidad" name="fecha_caducidad" value="{{ old('fecha_caducidad', $materiales_prima->fecha_caducidad ? $materiales_prima->fecha_caducidad->format('Y-m-d') : '') }}">
                            @error('fecha_caducidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Distribución Logística</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="ubicacion_id" class="form-label">Ubicación Física Asignada <span class="text-danger">*</span></label>
                            <select class="form-select @error('ubicacion_id') is-invalid @enderror" id="ubicacion_id" name="ubicacion_id" required>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}" {{ old('ubicacion_id', $materiales_prima->ubicacion_id) == $ubicacion->id ? 'selected' : '' }}>
                                        {{ $ubicacion->nombre_completo }} ({{ $ubicacion->descripcion ?? 'Sin notas' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ubicacion_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold text-white">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
