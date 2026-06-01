<div class="user-card">
    <div class="user-card-header">
        @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="user-avatar">
        @else
            <div class="user-avatar-placeholder">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <div class="user-info">
            <h5>{{ $user->name }}</h5>
            <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
            <div class="user-role-badge">
                {!! $user->role_badge !!}
            </div>
        </div>
    </div>

    <div class="user-card-body">
        <div class="user-detail">
            <i class="fas fa-calendar"></i>
            <span>Registrado: {{ $user->created_at->format('d/m/Y') }}</span>
        </div>
        @if($user->isProveedor() && $user->proveedor)
            <div class="user-detail">
                <i class="fas fa-building"></i>
                <span>{{ $user->proveedor->empresa }}</span>
            </div>
        @endif
    </div>

    <div class="user-card-actions">
        <a href="{{ route('usuarios.show', $user) }}" class="btn btn-info btn-sm" title="Ver">
            <i class="fas fa-eye"></i>
        </a>
        @if(auth()->user()->isSuperUsuario() || auth()->id() === $user->id)
            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-warning btn-sm" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        @endif
        @if(auth()->user()->isSuperUsuario() && auth()->id() !== $user->id)
            <form action="{{ route('usuarios.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete('{{ $user->name }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endif
    </div>
</div>