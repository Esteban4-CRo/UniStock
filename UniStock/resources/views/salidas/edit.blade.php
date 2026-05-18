@extends('layouts.app')

@section('title', 'Editar Salida - UniStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="mb-4">
            <a href="{{ route('salidas.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Historial
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3">
                <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit me-2 text-dark"></i> Editar Salida de Inventario</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info py-2 px-3 mb-4" style="border-radius: 8px; border-left: 4px solid #0dcaf0; font-size: 0.85rem;">
                    <i class="fas fa-info-circle me-1"></i> <strong>Modo Edición Temporal:</strong> Si guardas cambios, el stock de las materias primas se recalculará sumando la cantidad anterior y restando la nueva.
                </div>

                <form action="{{ route('salidas.update', $salida->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Material y Destino</h5>

                    <div class="mb-3">
                        <label for="material_prima_id" class="form-label">Materia Prima a Retirar <span class="text-danger">*</span></label>
                        <select class="form-select @error('material_prima_id') is-invalid @enderror" id="material_prima_id" name="material_prima_id" required>
                            @foreach($materiales as $material)
                                <option value="{{ $material->id }}" {{ old('material_prima_id', $salida->material_prima_id) == $material->id ? 'selected' : '' }}>
                                    {{ $material->nombre }} (Cód: {{ $material->codigo }} | Stock Disponible: {{ $material->cantidad }} {{ $material->unidad_medida }})
                                </option>
                            @endforeach
                        </select>
                        @error('material_prima_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="destino" class="form-label">Destino / Uso del Insumo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('destino') is-invalid @enderror" id="destino" name="destino" value="{{ old('destino', $salida->destino) }}" placeholder="Ej. Área de Panificación" required>
                        @error('destino')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Detalle del Despacho</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="cantidad" class="form-label">Cantidad Retirada <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" value="{{ old('cantidad', $salida->cantidad) }}" min="1" placeholder="Ej. 10" required>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lote" class="form-label">Código de Lote a Retirar</label>
                            <input type="text" class="form-control @error('lote') is-invalid @enderror" id="lote" name="lote" value="{{ old('lote', $salida->lote) }}" placeholder="Ej. LOTE-1234">
                            @error('lote')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="motivo" class="form-label">Motivo o Justificación de Salida</label>
                            <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" rows="3" placeholder="Ej. Producción de lote semanal de pan molde.">{{ old('motivo', $salida->motivo) }}</textarea>
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold text-white">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('salidas.index') }}" class="btn btn-light py-2">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
