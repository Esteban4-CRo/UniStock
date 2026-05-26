@extends('layouts.app')

@section('title', 'Proveedores - UniStock')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark"><i class="fas fa-truck text-dark me-2"></i> <span class="animate-soft-blur">Directorio de Proveedores</span></h2>
        <p class="text-muted mb-0">Gestione la lista de proveedores asociados al inventario de materias primas.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente())
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus me-2"></i> Registrar Proveedor
        </a>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4" style="width: 250px;">Empresa</th>
                        <th class="py-3">RUC / Identificación</th>
                        <th class="py-3">Teléfono</th>
                        <th class="py-3">Dirección y Ciudad</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-center">Ubicación GPS</th>
                        <th class="py-3 text-end pe-4" style="width: 200px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td class="py-3 ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-weight: 700;">
                                        {{ strtoupper(substr($proveedor->empresa, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $proveedor->empresa }}</h6>
                                        <small class="text-muted">Contacto: {{ $proveedor->user ? $proveedor->user->name : 'No asignado' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border fw-semibold">{{ $proveedor->ruc }}</span>
                            </td>
                            <td class="py-3 text-muted">
                                <i class="fas fa-phone-alt me-1 text-muted small"></i> {{ $proveedor->telefono }}
                            </td>
                            <td class="py-3">
                                <span class="d-block text-dark fw-medium">{{ $proveedor->direccion }}</span>
                                <small class="text-muted">{{ $proveedor->ciudad }}, {{ $proveedor->pais }}</small>
                            </td>
                            <td class="py-3">
                                @if($proveedor->estado_validacion === 'validado')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i> Validado</span>
                                @elseif($proveedor->estado_validacion === 'rechazado')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-times-circle me-1"></i> Rechazado</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning"><i class="fas fa-clock me-1"></i> Pendiente</span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                @if($proveedor->hasLocation())
                                    <a href="{{ $proveedor->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2 border-0">
                                        <i class="fas fa-map-marked-alt text-dark me-1"></i> Mapa
                                    </a>
                                @else
                                    <span class="text-muted small"><i class="fas fa-map-marker-slash"></i> N/A</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                <a href="{{ route('proveedores.show', $proveedor->id) }}" class="btn btn-sm btn-light border-0 me-1" title="Ver detalle">
                                    <i class="fas fa-eye text-dark"></i>
                                </a>
                                @if(Auth::user()->isSuperUsuario() || Auth::user()->isGerente())
                                    @if($proveedor->estado_validacion !== 'validado')
                                        <form action="{{ route('proveedores.validar', $proveedor->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estado_validacion" value="validado">
                                            <button type="submit" class="btn btn-sm btn-success bg-opacity-10 text-success border-0 me-1" title="Validar Proveedor" onclick="return confirm('¿Marcar proveedor como VALIDADO?');">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($proveedor->estado_validacion !== 'rechazado')
                                        <form action="{{ route('proveedores.validar', $proveedor->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estado_validacion" value="rechazado">
                                            <button type="submit" class="btn btn-sm btn-danger bg-opacity-10 text-danger border-0 me-1" title="Rechazar Proveedor" onclick="return confirm('¿Marcar proveedor como RECHAZADO?');">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif

                                <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn btn-sm btn-light border-0 me-1" title="Editar">
                                    <i class="fas fa-edit text-dark"></i>
                                </a>
                                <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este proveedor? Esto borrará su registro permanente.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0" title="Eliminar">
                                        <i class="fas fa-trash-alt text-dark"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-truck-loading fa-3x text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold">No hay proveedores registrados</h5>
                                <p class="text-muted mb-0">Los proveedores registrados aparecerán en esta sección para vincularlos con las entradas de materia prima.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
