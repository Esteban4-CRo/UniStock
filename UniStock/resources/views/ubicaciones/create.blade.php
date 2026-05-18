@extends('layouts.app')

@section('title', 'Registrar Ubicación - UniStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="mb-4">
            <a href="{{ route('ubicaciones.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a la Estructura
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3">
                <h4 class="fw-bold text-dark mb-0"><i class="fas fa-map-marker-alt me-2 text-dark"></i> Nueva Ubicación Física</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('ubicaciones.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="pasillo" class="form-label">Pasillo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pasillo') is-invalid @enderror" id="pasillo" name="pasillo" value="{{ old('pasillo') }}" placeholder="Ej. A, B, Seco, Frío" required>
                        @error('pasillo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="estante" class="form-label">Estante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('estante') is-invalid @enderror" id="estante" name="estante" value="{{ old('estante') }}" placeholder="Ej. 1, 2, Superior, Inferior" required>
                        @error('estante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="casillero" class="form-label">Casillero / Nivel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('casillero') is-invalid @enderror" id="casillero" name="casillero" value="{{ old('casillero') }}" placeholder="Ej. A, B, C, Nivel 1" required>
                        @error('casillero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label">Descripción o Notas</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Ej. Almacén principal de harinas a temperatura ambiente.">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold text-white">
                            <i class="fas fa-save me-2"></i> Guardar Ubicación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
