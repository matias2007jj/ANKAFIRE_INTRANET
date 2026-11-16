<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">

    <div class="cliente-container" style="max-width: 800px;">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>Registrar Nuevo Usuario</h1>
                <p>Completa los datos para crear un nuevo acceso al sistema</p>
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
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                    <div>
                        <label for="role" style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Rol de Usuario *</label>
                        <select name="role" id="role" required onchange="toggleClienteSelect()" style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; background-color:#fff; color:#18181b;">
                            <option value="">-- Seleccionar Rol --</option>
                            <option value="administrador" {{ old('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="cliente" {{ old('role') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                        </select>
                    </div>

                    <div id="div_codigo_cliente" style="display: none;">
                        <label for="codigo_cliente" style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Asignar Empresa / Cliente *</label>
                        <select name="codigo_cliente" id="codigo_cliente" onchange="autocompletarDatosCliente()" style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; background-color:#fff; color:#18181b;">
                            <option value="" data-ruc="" data-contacto="">-- Seleccionar Empresa --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->codigo_cliente }}"
                                        data-ruc="{{ $cliente->ruc }}"
                                        data-contacto="{{ $cliente->contacto }}"
                                        {{ old('codigo_cliente') == $cliente->codigo_cliente ? 'selected' : '' }}>
                                    [{{ $cliente->codigo_cliente }}] - {{ $cliente->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="name" style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Nombre Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Nombre del contacto" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label for="username" style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Usuario *</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Ej. llamagas" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>

                    <div>
                        <label for="password" style="display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;">Contraseña (RUC de la empresa) *</label>
                        <input type="password" name="password" id="password" placeholder="Ingrese el RUC" required style="width:100%; padding:11px 16px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                    </div>
                </div>

                {{-- Botones --}}
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:28px;">
                    <a href="{{ route('usuarios.index') }}" style="padding:11px 22px; border-radius:8px; font-weight:700; font-size:13px; background-color:#f4f4f5; color:#18181b; text-decoration:none; display:inline-flex; align-items:center;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-agregar" style="border:none;">
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        function toggleClienteSelect() {
            const role = document.getElementById('role').value;
            const divCliente = document.getElementById('div_codigo_cliente');

            if (role === 'cliente') {
                divCliente.style.display = 'block';
            } else {
                divCliente.style.display = 'none';
                document.getElementById('codigo_cliente').value = '';
                document.getElementById('password').value = '';
            }
        }

        function autocompletarDatosCliente() {
            const selectCliente = document.getElementById('codigo_cliente');
            const selectedOption = selectCliente.options[selectCliente.selectedIndex];

            const ruc = selectedOption.getAttribute('data-ruc') || '';
            const contacto = selectedOption.getAttribute('data-contacto') || '';

            if (ruc) {
                document.getElementById('password').value = ruc;
            }
            if (contacto) {
                document.getElementById('name').value = contacto;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleClienteSelect();
            if (document.getElementById('codigo_cliente').value !== '') {
                autocompletarDatosCliente();
            }
        });
    </script>
</x-app-layout>