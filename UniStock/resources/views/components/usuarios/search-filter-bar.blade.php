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