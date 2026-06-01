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

    <x-usuarios.search-filter-bar />

    @if($users->isEmpty())
        <x-usuarios.empty-state />
    @else
        <div class="users-grid">
            @foreach($users as $user)
                <x-usuarios.user-card :user="$user" />
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

