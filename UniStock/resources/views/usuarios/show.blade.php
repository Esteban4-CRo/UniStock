@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-user"></i> Detalles del Usuario</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);">
                    @else
                        <i class="fas fa-user-circle" style="font-size: 5rem; color: #bdc3c7;"></i>
                    @endif
                </div>

                <div class="info-box mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Nombre</label>
                    <p class="mb-0" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">{{ $user->name }}</p>
                </div>

                <div class="info-box mb-3">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <p class="mb-0" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">{{ $user->email }}</p>
                </div>

                <div class="info-box mb-4">
                    <label class="form-label"><i class="fas fa-calendar"></i> Registrado</label>
                    <p class="mb-0" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Sin registro' }}</p>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary flex-grow-1">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    @if(Auth::id() !== $user->id)
                        <form action="{{ route('usuarios.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
</style>
@endsection
