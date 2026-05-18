@extends('layouts.app')

@section('title', 'Editar Entrada - UniStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="mb-4">
            <a href="{{ route('entradas.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Historial
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3">
                <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit me-2 text-dark"></i> Editar Entrada de Inventario</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info py-2 px-3 mb-4" style="border-radius: 8px; border-left: 4px solid #0dcaf0; font-size: 0.85rem;">
                    <i class="fas fa-info-circle me-1"></i> <strong>Modo Edición Temporal:</strong> Si guardas cambios, el stock de las materias primas se actualizará automáticamente restando la cantidad anterior y sumando la nueva.
                </div>

                <form action="{{ route('entradas.update', $entrada->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Origen y Destino</h5>

                    <div class="mb-3">
                        <label for="material_prima_id" class="form-label">Materia Prima <span class="text-danger">*</span></label>
                        <select class="form-select @error('material_prima_id') is-invalid @enderror" id="material_prima_id" name="material_prima_id" required>
                            @foreach($materiales as $material)
                                <option value="{{ $material->id }}" {{ old('material_prima_id', $entrada->material_prima_id) == $material->id ? 'selected' : '' }}>
                                    {{ $material->nombre }} (Cód: {{ $material->codigo }} | Stock: {{ $material->cantidad }} {{ $material->unidad_medida }})
                                </option>
                            @endforeach
                        </select>
                        @error('material_prima_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor de Origen <span class="text-danger">*</span></label>
                        <select class="form-select @error('proveedor_id') is-invalid @enderror" id="proveedor_id" name="proveedor_id" required>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $entrada->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                    {{ $proveedor->empresa }} (RUC: {{ $proveedor->ruc }} | {{ $proveedor->ciudad }})
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Detalle de Recepción</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="cantidad" class="form-label">Cantidad Ingresada <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" value="{{ old('cantidad', $entrada->cantidad) }}" min="1" placeholder="Ej. 50" required>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lote" class="form-label">Código de Lote Recibido</label>
                            <input type="text" class="form-control @error('lote') is-invalid @enderror" id="lote" name="lote" value="{{ old('lote', $entrada->lote) }}" placeholder="Ej. LOTE-NUEVO-77">
                            @error('lote')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="fecha_caducidad" class="form-label">Fecha de Caducidad de este Lote</label>
                            <input type="date" class="form-control @error('fecha_caducidad') is-invalid @enderror" id="fecha_caducidad" name="fecha_caducidad" value="{{ old('fecha_caducidad', $entrada->fecha_caducidad ? \Carbon\Carbon::parse($entrada->fecha_caducidad)->format('Y-m-d') : '') }}">
                            @error('fecha_caducidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="motivo" class="form-label">Motivo o Notas de Recepción</label>
                            <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" rows="3" placeholder="Ej. Abastecimiento mensual programado.">{{ old('motivo', $entrada->motivo) }}</textarea>
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold text-white">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('entradas.index') }}" class="btn btn-light py-2">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
