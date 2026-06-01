<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="empty-state-title">No hay usuarios</div>
            <div class="empty-state-text">
                @if(request('search') || request('role'))
                    No se encontraron usuarios con los filtros aplicados.
                @else
                    Comienza creando el primer usuario del sistema.
                @endif
            </div>
            @if(auth()->user()->isSuperUsuario() && !request('search') && !request('role'))
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Crear Usuario
                </a>
            @endif
        </div>
    </div>
</div>