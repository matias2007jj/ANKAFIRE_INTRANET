<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">

    <div class="cliente-container" style="max-width: 900px;">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>{{ $cliente->exists ? 'Editar Cliente' : 'Registrar Nuevo Cliente' }}</h1>
                <p>{{ $cliente->exists ? 'Actualiza los datos de la empresa cliente' : 'Completa los datos para registrar una nueva empresa cliente' }}</p>
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
            <form action="{{ $cliente->exists ? route('clientes.update', $cliente->id) : route('clientes.store') }}" method="POST">
                @csrf
                @if($cliente->exists)
                @method('PUT')
                @endif

                <div class="form-grid-2">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Razón Social *</label>
                        <input type="text" name="razon_social" value="{{ old('razon_social', $cliente->razon_social) }}" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Nombre Comercial *</label>
                        <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $cliente->nombre_comercial) }}" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">RUC *</label>
                        <input type="text" name="ruc" value="{{ old('ruc', $cliente->ruc) }}" inputmode="numeric" pattern="[0-9]*" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'')" style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label for="provincia" style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Departamento / Provincia *</label>
                        <select name="provincia" id="provincia" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; background-color:#fff; color:#18181b;">
                            <option value="">-- Seleccione un Departamento --</option>
                            @foreach ([
                            'AMAZONAS', 'ANCASH', 'APURIMAC', 'AREQUIPA', 'AYACUCHO',
                            'CAJAMARCA', 'CALLAO', 'CUSCO', 'HUANCAVELICA', 'HUANUCO',
                            'ICA', 'JUNIN', 'LA LIBERTAD', 'LAMBAYEQUE', 'LIMA',
                            'LORETO', 'MADRE DE DIOS', 'MOQUEGUA', 'PASCO', 'PIURA',
                            'PUNO', 'SAN MARTIN', 'TACNA', 'TUMBES', 'UCAYALI'
                            ] as $dep)
                            <option value="{{ $dep }}" @selected(old('provincia', $cliente->provincia ?? '') == $dep)>
                                {{ $dep }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Dirección *</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Contacto *</label>
                        <input type="text" name="contacto" value="{{ old('contacto', $cliente->contacto) }}" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Cargo *</label>
                        <input type="text" name="cargo" value="{{ old('cargo', $cliente->cargo) }}" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Correo Electrónico *</label>
                        <input type="email" name="correo" value="{{ old('correo', $cliente->correo) }}" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Teléfono *</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" inputmode="numeric" pattern="[0-9]*" maxlength="9" required oninput="this.value=this.value.replace(/[^0-9]/g,'')" style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Estado *</label>
                        <select name="estado" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; background-color:#fff; color:#18181b;">
                            <option value="VIGENTE" {{ old('estado', $cliente->estado) == 'VIGENTE' ? 'selected' : '' }}>VIGENTE</option>
                            <option value="INACTIVO" {{ old('estado', $cliente->estado) == 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
                        </select>
                    </div>
                </div>

                {{-- Botones --}}
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:28px; border-top: 1px solid #f1f1f5; padding-top: 20px;">
                    <a href="{{ route('principal') }}" style="padding:11px 22px; border-radius:8px; font-weight:700; font-size:13px; background-color:#f4f4f5; color:#18181b; text-decoration:none; display:inline-flex; align-items:center;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-agregar" style="border:none;">
                        {{ $cliente->exists ? 'Actualizar Cliente' : 'Guardar Cliente' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>