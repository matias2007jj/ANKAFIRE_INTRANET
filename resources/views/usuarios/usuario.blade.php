<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">

    <div class="cliente-container">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>Listado de Usuarios Registrados</h1>
                <p>Total: <strong>{{ $users->total() }}</strong> registros</p>
            </div>

            <div class="header-acciones" style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('usuarios.create') }}" class="btn-agregar">
                    + Agregar Usuario
                </a>
            </div>
        </div>

        @if (session('success'))
        <div class="alert-mensaje">
            {{ session('success') }}
        </div>
        @endif

        @if (session('mensaje'))
        <div class="alert-mensaje">
            {{ session('mensaje') }}
        </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" class="filtros-form">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por Nombre, Usuario...">
            <button type="submit" class="btn-buscar">Buscar</button>
        </form>

        {{-- Tabla de usuarios --}}
        <div class="table-container">
            <table class="tabla-centrada">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Empresa / Código</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td><strong>{{ $user->id }}</strong></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username ?? 'S/N' }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'administrador' ? 'badge-vencido' : 'badge-vigente' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->codigo_cliente ?? '-' }}</td>
                        <td>
                            <div class="acciones-fila">
                                <a href="{{ route('usuarios.edit', $user->id) }}" title="Editar" class="btn-fila btn-fila-editar" style="display: inline-flex; align-items: center; justify-content: center; padding: 4px;">
                                    <img src="{{ asset('img/usuario.png') }}" alt="Editar" class="icono-accion">
                                </a>
                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" data-confirm="¿Deseas eliminar al usuario {{ $user->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar" class="btn-fila btn-fila-eliminar" style="display: inline-flex; align-items: center; justify-content: center; padding: 4px;">
                                        <img src="{{ asset('img/elimina-usuario.png') }}" alt="Eliminar" class="icono-accion">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-muted" style="font-size: 12px; color: #71717a; margin-top: 12px;">
            Mostrando {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} de {{ $users->total() }} registros
        </p>

        {{-- Paginación --}}
        <div class="paginacion-wrap">
            {{ $users->appends(request()->query())->links() }}
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/confirmar.js') }}"></script>
</x-app-layout>