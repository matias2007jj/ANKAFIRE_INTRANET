<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">
    <link rel="stylesheet" href="{{ asset('css/equipo-form.css') }}">

    @php
        // Es edición solo si el equipo YA existe en la base de datos
        $editando = isset($equipo) && $equipo->exists;

        $valor = fn($campo) => old($campo, $editando ? ($equipo->$campo ?? '') : '');

        // Convierte una fecha al formato del input e intenta con campos alternativos
        $fechaInput = function ($campos, $formato) use ($equipo, $editando) {
            if (!$editando) return '';
            
            $columnas = is_array($campos) ? $campos : [$campos];
            $fechaRaw = null;

            foreach ($columnas as $col) {
                if (!empty($equipo->$col)) {
                    $fechaRaw = $equipo->$col;
                    break;
                }
            }

            if (!$fechaRaw) return '';

            try {
                return \Carbon\Carbon::parse($fechaRaw)->format($formato);
            } catch (\Throwable $e) {
                return '';
            }
        };

        // Comparador flexible para selector de tipo de extintor
        $esMismoTipo = function ($tipoOpcion) use ($valor) {
            $valActual = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $valor('tipo_extintor'))));
            $valOpcion = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $tipoOpcion)));
            
            if ($valActual === $valOpcion) return true;
            return str_contains($valActual, 'pqs') && str_contains($valOpcion, 'pqs');
        };
    @endphp

    <div class="cliente-container" style="max-width: 900px;">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>{{ $editando ? 'Editar extintor' : 'Agregar extintor' }}</h1>
                <p>Cliente: {{ $cliente->razon_social ?? '' }} ({{ $cliente->codigo_cliente ?? '' }})</p>
            </div>
        </div>

        @if ($errors->any())
        <div class="alert-mensaje" style="background-color: #fef2f2; border-left-color: #dc2626; border-color: #fecaca; color: #991b1b;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Formulario --}}
        <div class="table-container" style="padding: 28px;">
            <form method="POST"
                  action="{{ $editando ? route('equipos.update', [$cliente->codigo_cliente, $equipo->id]) : route('equipos.store', $cliente->codigo_cliente) }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($editando) @method('PUT') @endif

                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Sede *</label>
                        <select name="codigo_sede" required class="form-input">
                            <option value="">-- Sin sede específica --</option>
                            @foreach (($sedes ?? []) as $sede)
                                <option value="{{ $sede->codigo_sede }}" @selected($valor('codigo_sede') == $sede->codigo_sede)>
                                    {{ $sede->nombre_sede }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Tipo de extintor *</label>
                        <select name="tipo_extintor" required class="form-input">
                            <option value="">-- Selecciona --</option>
                            @foreach (($tipos ?? []) as $tipo)
                                <option value="{{ $tipo }}" @selected($esMismoTipo($tipo))>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">N° de serie *</label>
                        <input type="text" name="numero_serie" value="{{ $valor('numero_serie') }}" required class="form-input">
                    </div>

                    <div>
                        <label class="form-label">N° interno *</label>
                        <input type="text" name="numero_interno" value="{{ $valor('numero_interno') }}"
                               placeholder="S/N si no aplica" required class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Capacidad de carga *</label>
                        <select name="capacidad_carga" required class="form-input">
                            <option value="">-- Selecciona --</option>
                            @foreach (($capacidades ?? []) as $capacidad)
                                <option value="{{ $capacidad }}" @selected($valor('capacidad_carga') == $capacidad)>{{ $capacidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Marca *</label>
                        <select name="marca" required class="form-input">
                            <option value="">-- Selecciona --</option>
                            @foreach (($marcas ?? []) as $marca)
                                <option value="{{ $marca }}" @selected($valor('marca') == $marca)>{{ $marca }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Año de fabricación *</label>
                        <input type="number" name="anio_fabricacion" value="{{ $valor('anio_fabricacion') }}"
                               min="1980" max="{{ date('Y') }}" required class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Estado *</label>
                        <select name="estado" required class="form-input">
                            @foreach (['Vigente', 'Vencido', 'De baja'] as $opcion)
                                <option value="{{ $opcion }}" @selected($valor('estado') == $opcion)>{{ $opcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Fecha último servicio *</label>
                        <input type="date" name="fecha_ultimo_servicio"
                               value="{{ old('fecha_ultimo_servicio', $fechaInput(['fecha_ultimo_servicio', 'fecha_servicio'], 'Y-m-d')) }}"
                               required class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Mes y año de último mantenimiento *</label>
                        <input type="month" name="mes_ultimo_mantenimiento"
                               value="{{ old('mes_ultimo_mantenimiento', $fechaInput(['mes_ultimo_mantenimiento', 'fecha_ultimo_mantenimiento', 'fecha_ultimo_servicio'], 'Y-m')) }}"
                               required class="form-input">
                        <p class="form-help-text">El próximo mantenimiento (mismo mes, +1 año) se calcula a partir de esta fecha.</p>
                    </div>

                    <div>
                        <label class="form-label">Mes y año de prueba hidrostática *</label>
                        <input type="month" name="mes_prueba_hidrostatica"
                               value="{{ old('mes_prueba_hidrostatica', $fechaInput(['mes_prueba_hidrostatica', 'fecha_prueba_hidrostatica', 'vencimiento_ph'], 'Y-m')) }}"
                               required class="form-input">
                        <p class="form-help-text">El vencimiento de PH (+5 años) se calcula a partir de esta fecha.</p>
                    </div>

                    <div>
                        <label class="form-label">N° de certificado *</label>
                        <input type="text" name="numero_certificado" value="{{ $valor('numero_certificado') }}" required class="form-input">
                        <p class="form-help-text">Si ya existe otro extintor con este mismo N°, sus documentos se reutilizan automáticamente.</p>
                    </div>
                </div>

                <hr class="form-divider">

                <p class="form-section-title">Documentos (PDF o imagen, máx. 10MB c/u)</p>

                <div class="form-grid-3">
                    <div>
                        <label class="form-label">Certificado de operatividad</label>
                        <input type="file" name="cert_operatividad" class="form-input-file">
                        @if ($editando && !empty($equipo->ruta_cert_operatividad))
                            <p class="form-help-text">Ya existe uno. Sube un archivo para reemplazarlo.</p>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Informe técnico</label>
                        <input type="file" name="informe_tecnico" class="form-input-file">
                        @if ($editando && !empty($equipo->ruta_informe_tecnico))
                            <p class="form-help-text">Ya existe uno. Sube un archivo para reemplazarlo.</p>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Certificado de PH</label>
                        <input type="file" name="cert_ph" class="form-input-file">
                        @if ($editando && !empty($equipo->ruta_cert_ph))
                            <p class="form-help-text">Ya existe uno. Sube un archivo para reemplazarlo.</p>
                        @endif
                    </div>
                </div>

                {{-- Botones --}}
                <div class="form-actions">
                    <a href="{{ route('clientes.dashboard', $cliente->codigo_cliente) }}" class="btn-cancelar">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-agregar" style="border:none;">
                        {{ $editando ? 'Guardar cambios' : 'Guardar extintor' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>