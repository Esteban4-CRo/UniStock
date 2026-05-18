@extends('layouts.app')

@section('title', 'Editar Proveedor - UniStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="mb-4">
            <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Directorio
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-3">
                <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit me-2 text-dark"></i> Editar Proveedor: {{ $proveedor->empresa }}</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Información Corporativa</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="empresa" class="form-label">Razón Social / Empresa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('empresa') is-invalid @enderror" id="empresa" name="empresa" value="{{ old('empresa', $proveedor->empresa) }}" placeholder="Ej. Distribuidora Central S.A.S." required>
                            @error('empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="ruc" class="form-label">RUC / NIT / Identificación <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" value="{{ old('ruc', $proveedor->ruc) }}" placeholder="Ej. 900123456-7" required>
                            @error('ruc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono de Contacto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" placeholder="Ej. +57 300 123 4567" required>
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Dirección y Localización</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="direccion" class="form-label">Dirección Fiscal / Física <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}" placeholder="Ej. Calle 26 # 45-12, Oficina 302" required>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" value="{{ old('ciudad', $proveedor->ciudad) }}">
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control @error('pais') is-invalid @enderror" id="pais" name="pais" value="{{ old('pais', $proveedor->pais) }}">
                            @error('pais')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="latitud" class="form-label">Latitud (GPS)</label>
                            <input type="number" step="any" class="form-control @error('latitud') is-invalid @enderror" id="latitud" name="latitud" value="{{ old('latitud', $proveedor->latitud) }}" placeholder="Ej. 4.7110">
                            @error('latitud')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="longitud" class="form-label">Longitud (GPS)</label>
                            <input type="number" step="any" class="form-control @error('longitud') is-invalid @enderror" id="longitud" name="longitud" value="{{ old('longitud', $proveedor->longitud) }}" placeholder="Ej. -74.0721">
                            @error('longitud')
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
