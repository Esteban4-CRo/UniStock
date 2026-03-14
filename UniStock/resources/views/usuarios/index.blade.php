@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">

<div class="usuarios-container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                @if(auth()->user()->isSuperUsuario())
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="search-filter-bar">
        <form method="GET" action="{{ route('usuarios.index') }}" class="d-flex gap-3 flex-wrap w-100 align-items-center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" id="searchInput" placeholder="Buscar por nombre o email..." value="{{ request('search') }}" class="form-control">
            </div>
            
            <div class="filter-role">
                <button type="submit" name="role" value="" class="btn {{ !request('role') ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <i class="fas fa-users"></i> Todos
                </button>
                <button type="submit" name="role" value="super_usuario" class="btn {{ request('role') == 'super_usuario' ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <i class="fas fa-crown"></i> Super Usuario
                </button>
                <button type="submit" name="role" value="gerente" class="btn {{ request('role') == 'gerente' ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <i class="fas fa-user-tie"></i> Gerente
                </button>
                <button type="submit" name="role" value="almacenista" class="btn {{ request('role') == 'almacenista' ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <i class="fas fa-boxes"></i> Almacenista
                </button>
                <button type="submit" name="role" value="proveedor" class="btn {{ request('role') == 'proveedor' ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <i class="fas fa-truck"></i> Proveedor
                </button>
            </div>
        </form>
    </div>

    @if($users->isEmpty())
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
    @else
        <div class="users-grid">
            @foreach($users as $user)
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
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    @endif
</div>

<script src="{{ asset('js/usuarios.js') }}"></script>
<script>
    function confirmDelete(userName) {
        return confirm(`¿Estás seguro de que deseas eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`);
    }
</script>
@endsection

