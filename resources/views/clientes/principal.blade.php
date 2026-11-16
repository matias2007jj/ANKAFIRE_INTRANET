<x-app-layout>
    <!-- Carga de la hoja de estilos propia de la tabla de clientes -->
    <link rel="stylesheet" href="{{ asset('css/principal.css') }}">

    <div class="py-6 bg-gray-100 min-h-screen">
        <!-- Contenedor ancho (96%) para dar espacio a todas las columnas -->
        <div class="max-w-[96%] mx-auto px-2 sm:px-4">
            <div class="anka-table-container bg-white p-6 rounded-xl shadow-sm border border-gray-100">

                {{-- Alerta de Notificación/Mensaje --}}
                @if (session('mensaje'))
                <div class="alert-mensaje mb-5 p-3 px-5 bg-emerald-100 text-emerald-800 rounded-lg font-semibold">
                    {{ session('mensaje') }}
                </div>
                @endif

                <!-- Cabecera con Título, Buscador y Botones -->
                <div class="flex flex-wrap justify-between items-center mb-5 gap-3">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-800">Listado de Clientes Registrados</h2>
                        <span class="text-sm text-gray-500 font-medium">Total: {{ $resumen['total'] ?? $clientes->total() }} registros</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Formulario de Búsqueda --}}
                        <form method="GET" action="{{ route('principal') }}" class="flex items-center gap-2">
                            <input
                                type="text"
                                name="buscar"
                                value="{{ request('buscar') }}"
                                placeholder="Buscar por Razón Social, RUC..."
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm outline-none focus:ring-2 focus:ring-red-500 w-64">
                            <button type="submit" class="anka-btn-primary bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md text-sm flex items-center gap-1.5 transition whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                            @if(request('buscar'))
                            <a href="{{ route('principal') }}" class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-3 rounded-md text-sm font-medium transition whitespace-nowrap">
                                Limpiar
                            </a>
                            @endif
                        </form>

                        <!-- Botón para Agregar Clientes -->
                        <a href="{{ route('clientes.create') }}" class="anka-btn-primary bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md text-sm flex items-center gap-1.5 transition whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Clientes
                        </a>
                    </div>
                </div>

                <!-- Tabla de Clientes -->
                <div class="anka-table-scroll-wrapper overflow-x-auto w-full">
                    <table class="anka-table w-full text-left border-collapse" style="min-width: 1200px;">
                        <thead>
                            <tr class="bg-red-600 text-white text-xs uppercase tracking-wider">
                                <th class="p-3 whitespace-nowrap">ID</th>
                                <th class="p-3 whitespace-nowrap">Código</th>
                                <th class="p-3 whitespace-nowrap">Razón Social</th>
                                <th class="p-3 whitespace-nowrap">Nombre Comercial</th>
                                <th class="p-3 whitespace-nowrap">RUC</th>
                                <th class="p-3 whitespace-nowrap">Dirección</th>
                                <th class="p-3 whitespace-nowrap">Provincia</th>
                                <th class="p-3 whitespace-nowrap">Contacto</th>
                                <th class="p-3 whitespace-nowrap">Cargo</th>
                                <th class="p-3 whitespace-nowrap">Correo</th>
                                <th class="p-3 whitespace-nowrap">Teléfono</th>
                                <th class="p-3 whitespace-nowrap">Estado</th>
                                <th class="p-3 text-center whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @forelse($clientes as $cliente)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3 font-bold whitespace-nowrap">{{ $cliente->id }}</td>
                                <td class="p-3 whitespace-nowrap">
                                    <a href="{{ url('clientes/'.$cliente->codigo_cliente.'/dashboard') }}" class="font-bold text-red-600 hover:underline">
                                        {{ $cliente->codigo_cliente }}
                                    </a>
                                </td>
                                <td class="p-3 font-semibold text-gray-800">{{ $cliente->razon_social }}</td>
                                <td class="p-3">{{ $cliente->nombre_comercial ?? '—' }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $cliente->ruc }}</td>
                                <td class="p-3 max-w-xs truncate" title="{{ $cliente->direccion }}">{{ $cliente->direccion ?? '—' }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $cliente->provincia ?? '—' }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $cliente->contacto ?? '—' }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $cliente->cargo ?? '—' }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $cliente->correo ?? '—' }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $cliente->telefono ?? '—' }}</td>

                                <!-- Columna de Estado Dinámico (Verde / Rojo con estilos inline forzados) -->
                                <td class="p-3 whitespace-nowrap">
                                    @if(strtoupper($cliente->estado) === 'INACTIVO')
                                    <span class="badge-estado" style="background-color: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 11px; display: inline-block;">
                                        INACTIVO
                                    </span>
                                    @else
                                    <span class="badge-estado" style="background-color: #d1fae5 !important; color: #059669 !important; border: 1px solid #6ee7b7 !important; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 11px; display: inline-block;">
                                        VIGENTE
                                    </span>
                                    @endif
                                </td>
                                <!-- Columna de Acciones -->
                                <td class="p-3 text-center whitespace-nowrap" onclick="event.stopPropagation();">
                                    <div class="anka-action-buttons flex justify-center gap-1">
                                        <a href="{{ route('clientes.edit', $cliente->id) }}" title="Editar" class="btn-editar p-1.5 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white rounded transition inline-flex items-center justify-center">
                                            <img src="{{ asset('img/editar-usuario.png') }}" alt="Editar" class="w-10 h-10">
                                        </a>

                                        <!-- Botón dinámico de Alternancia: Inactivar (rojo) / Activar (verde) -->
                                       <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="inline" data-confirm="{{ strtoupper($cliente->estado) === 'INACTIVO' ? '¿Deseas activar a este cliente?' : '¿Deseas inactivar a este cliente?' }}">
                                            @csrf
                                            @method('DELETE')
                                            @if(strtoupper($cliente->estado) === 'INACTIVO')
                                            <button type="submit" title="Activar" class="btn-activar p-1.5 rounded transition inline-flex items-center justify-center">
                                                <img src="{{ asset('img/activar-cliente.png') }}" alt="Activar" class="icono-activar">
                                            </button>
                                            @else
                                            <button type="submit" title="Inactivar" class="btn-eliminar p-1.5 border border-red-400 text-red-600 hover:bg-red-600 hover:text-white rounded transition inline-flex items-center justify-center">
                                                <img src="{{ asset('img/eliminar-cliente.png') }}" alt="Inactivar" class="w-12 h-12">
                                            </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                                @empty
                            <tr>
                                <td colspan="13" class="text-center text-gray-500 py-8">No se encontraron clientes registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Paginación: fuera de la tarjeta de la tabla, en su propia tarjeta blanca --}}
            <div class="clientes-paginacion bg-white p-4 rounded-xl shadow-sm border border-gray-100 mt-4 flex justify-center">
                {{ $clientes->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/confirmar.js') }}"></script>