<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">

    <div class="cliente-container">
        <img src="{{ asset('img/banner.png') }}" alt="Plataforma de Gestión y Certificación Anka Fire" class="w-full rounded-lg mb-7">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>{{ $cliente->razon_social }}</h1>
                <p>
                    Código: <strong>{{ $cliente->codigo_cliente }}</strong> ·
                    RUC: <strong>{{ $cliente->ruc }}</strong> ·
                    {{ $cliente->provincia }}
                </p>
            </div>

            {{-- Ocultar botón de agregar si el usuario es de tipo cliente --}}
            @if (auth()->user()->role !== 'cliente')
            <div class="header-acciones" style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('equipos.create', $cliente->codigo_cliente) }}" class="btn-agregar">
                    + Agregar extintor
                </a>
            </div>
            @endif
        </div>

        @if (session('mensaje'))
        <div class="alert-mensaje">
            {{ session('mensaje') }}
        </div>
        @endif

        {{-- Tarjetas resumen --}}
        <div class="resumen-grid">
            <div class="card-resumen border-black">
                <div class="card-info">
                    <div class="card-resumen-texto">
                        <p>Total de equipos</p>
                        <h3>{{ $resumen['total'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon">
                        <img src="{{ asset('img/icono-extintor1.png') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="card-resumen border-green">
                <div class="card-info">
                    <div class="card-resumen-texto">
                        <p>Vigentes</p>
                        <h3 class="text-green">{{ $resumen['vigentes'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon">
                        <img src="{{ asset('img/icono-extintor2.png') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="card-resumen border-orange">
                <div class="card-info">
                    <div class="card-resumen-texto">
                        <p>Por vencer (60 días)</p>
                        <h3 class="text-orange">{{ $resumen['por_vencer'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon">
                        <img src="{{ asset('img/icono-extintor3.png') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="card-resumen border-red">
                <div class="card-info">
                    <div class="card-resumen-texto">
                        <p>Vencidos</p>
                        <h3 class="text-red">{{ $resumen['vencidos'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon">
                        <img src="{{ asset('img/icono-extintor4.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="filtros-form">
            <select name="sede" onchange="this.form.submit()">
                <option value="">Todas las sedes</option>
                @foreach (($sedes ?? []) as $s)
                @php
                $codigoSede = $s->codigo_sede ?? $s->id ?? $s;
                $nombreSede = $s->nombre_sede ?? $s->nombre ?? $codigoSede;
                @endphp
                <option value="{{ $codigoSede }}" @selected(request('sede')==$codigoSede)>
                    {{ $nombreSede }}
                </option>
                @endforeach
            </select>

            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por N° serie o N° interno">

            <button type="submit" class="btn-buscar">Buscar</button>
        </form>

        {{-- Botones de exportar (conservan los filtros activos de sede y búsqueda) --}}
        <div class="tabla-acciones">
            <a href="{{ route('clientes.exportar.excel', $cliente->codigo_cliente) }}?{{ http_build_query(request()->only('sede', 'buscar')) }}" class="btn-icono btn-icono-excel" title="Exportar a Excel">
                <img src="{{ asset('img/icono-excel.png') }}" alt="Excel">
            </a>

            <a href="{{ route('clientes.exportar.pdf', $cliente->codigo_cliente) }}?{{ http_build_query(request()->only('sede', 'buscar')) }}" class="btn-icono btn-icono-pdf" title="Exportar a PDF">
                <img src="{{ asset('img/icono-pdf.png') }}" alt="PDF">
            </a>
        </div>

        {{-- Tabla de extintores --}}
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>N° Serie</th>
                        <th>N° Interno</th>
                        <th>Sede</th>
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Marca</th>
                        <th>Año Fabricación</th>
                        <th>Fecha Últ. Servicio</th>
                        <th>Próx. Mantenimiento</th>
                        <th>Fecha Prueba PH</th>
                        <th>Venc. Prueba PH</th>
                        <th>Estado</th>
                        <th>DOCUMENTACIÓN</th>
                        @if (auth()->user()->role !== 'cliente')
                        <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                    $mesAnio = function($fecha) {
                        if (!$fecha) return 'S/N';
                        try {
                            $c = is_string($fecha) ? \Carbon\Carbon::parse($fecha) : $fecha;
                            return strtoupper($c->locale('es')->isoFormat('MMM-YYYY'));
                        } catch (\Exception $e) {
                            return strtoupper($fecha);
                        }
                    };
                    $enlace = fn($ruta) => str_starts_with($ruta, 'http') ? $ruta : \Illuminate\Support\Facades\Storage::url($ruta);
                    @endphp

                    @forelse ($equipos as $equipo)
                    <tr>
                        <td><strong>{{ $equipo->numero_serie }}</strong></td>
                        <td>{{ $equipo->numero_interno ?? 'S/N' }}</td>
                        <td>
                            {{ $equipo->sede->nombre_sede ?? $equipo->sede->nombre ?? $equipo->codigo_sede ?? $equipo->sede ?? 'S/N' }}
                        </td>
                        <td>{{ $equipo->tipo_extintor }}</td>
                        <td>{{ $equipo->capacidad_carga }}</td>
                        <td>{{ $equipo->marca }}</td>
                        <td>{{ $equipo->anio_fabricacion ?? 'S/N' }}</td>
                        <td>{{ $mesAnio($equipo->fecha_ultimo_servicio) }}</td>
                        <td>{{ $equipo->proximo_mantenimiento ?? 'S/N' }}</td>
                        <td>{{ $mesAnio($equipo->fecha_prueba_hidrostatica) }}</td>
                        <td>{{ $mesAnio($equipo->vencimiento_ph) }}</td>
                        <td>
                            @php
                            $colores = [
                                'vigente' => 'badge-vigente',
                                'por_vencer' => 'badge-por-vencer',
                                'vencido' => 'badge-vencido',
                                'de_baja' => 'badge-de-baja',
                                'de baja' => 'badge-de-baja',
                                'debaja' => 'badge-de-baja',
                                'sin_dato' => 'badge-sin-dato',
                            ];
                            $etiquetas = [
                                'vigente' => 'Vigente',
                                'por_vencer' => 'Por vencer',
                                'vencido' => 'Vencido',
                                'de_baja' => 'De baja',
                                'de baja' => 'De baja',
                                'debaja' => 'De baja',
                                'sin_dato' => 'Sin dato',
                            ];

                            $estBd = strtolower(trim($equipo->estado ?? ''));
                            $estVis = strtolower(trim($equipo->estado_visual ?? ''));

                            $esDeBaja = in_array($estBd, ['de baja', 'de_baja', 'debaja']);
                            $esVencido = in_array($estVis, ['vencido']) || in_array($estBd, ['vencido']);

                            if ($esDeBaja) {
                                $estado = 'de_baja';
                            } else {
                                $estado = $estVis ?: 'sin_dato';
                            }

                            // Muestra el botón verde de habilitar/renovar si el extintor está "De baja" o "Vencido"
                            $mostrarHabilitar = $esDeBaja || $esVencido;
                            @endphp
                            <span class="badge {{ $colores[$estado] ?? 'badge-sin-dato' }}">
                                {{ $etiquetas[$estado] ?? 'Sin dato' }}
                            </span>
                        </td>
                        <td>
                            <div class="certificados-links">
                                @if ($equipo->ruta_cert_operatividad)
                                <a href="{{ $enlace($equipo->ruta_cert_operatividad) }}" target="_blank">Cert. Operatividad</a>
                                @endif
                                @if ($equipo->ruta_informe_tecnico)
                                <a href="{{ $enlace($equipo->ruta_informe_tecnico) }}" target="_blank">Inf. Técnico</a>
                                @endif
                                @if ($equipo->ruta_cert_ph)
                                <a href="{{ $enlace($equipo->ruta_cert_ph) }}" target="_blank">PH</a>
                                @endif
                            </div>
                        </td>
                        @if (auth()->user()->role !== 'cliente')
                        <td>
                            <div class="acciones-fila" style="display: flex; gap: 6px; align-items: center;">
                                <a href="{{ route('equipos.edit', [$cliente->codigo_cliente, $equipo->id]) }}" title="Editar" class="btn-fila btn-fila-editar" style="text-decoration: none;">
                                    <img src="{{ asset('img/editar-extintor.png') }}" alt="Editar" class="icono-accion">
                                </a>

                                {{-- Cambiar entre dar de baja y habilitar/renovar --}}
                                <form action="{{ route('equipos.toggleBaja', $equipo->id) }}" method="POST" style="margin: 0;" data-confirm="{{ $mostrarHabilitar ? '¿Deseas habilitar/renovar este extintor a estado Vigente?' : '¿Deseas dar de baja este extintor?' }}">
                                    @csrf
                                    @method('PATCH')
                                    @if ($mostrarHabilitar)
                                    <button type="submit" title="Habilitar / Renovar extintor" class="btn-fila btn-fila-habilitar">
                                        <img src="{{ asset('img/habilitar-extintor.png') }}" alt="Habilitar" class="icono-accion">
                                    </button>
                                    @else
                                    <button type="submit" title="Dar de baja" class="btn-fila btn-fila-eliminar">
                                        <img src="{{ asset('img/eliminar-extintor.png') }}" alt="Dar de baja" class="icono-accion">
                                    </button>
                                    @endif

                                </form>

                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role !== 'cliente' ? 14 : 13 }}" class="text-center">No se encontraron extintores con estos filtros.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="paginacion-wrap">
            {{ $equipos->withQueryString()->links() }}
        </div>

    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/confirmar.js') }}"></script>