@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-plus-circle"></i> Registrar nueva entrada</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('entradas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Producto <span class="badge badge-info">Único</span></label>
                        <select id="producto_id" class="form-select form-select-lg rounded-pill shadow-sm" name="producto_id" required>
                            
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" 
                            {{ old('producto_id', $producto_id) == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }} (Stock: {{ $producto->stock_actual }})
                        </option>
                        @endforeach
                    </select>
                    @error('producto_id') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" min="1" value="{{ old('cantidad') }}" required>
                        @error('cantidad') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de la entrada</label>
                        <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" rows="3">{{ old('motivo') }}</textarea>
                        @error('motivo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                   

                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="fas fa-save"></i> Guardar Entrada
                        </button>
                        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection