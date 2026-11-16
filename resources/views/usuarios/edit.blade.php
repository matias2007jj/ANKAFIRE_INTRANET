<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">

    @php
        $inputStyle = fn($campo) => "width:100%; padding:11px 16px; border:1px solid " . ($errors->has($campo) ? '#dc2626' : '#cbd5e1') . "; border-radius:8px; font-size:13px;";
        $labelStyle = "display:block; font-size:12px; font-weight:700; color:#52525b; margin-bottom:6px;";
        $errorStyle = "color:#dc2626; font-size:11px; margin-top:4px;";
    @endphp

    <div class="cliente-container" style="max-width: 900px;">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>Editar Usuario</h1>
                <p>Actualiza los datos de acceso de este usuario</p>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="table-container" style="padding: 28px;">
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid-2">
                    <div>
                        <label for="name" style="{{ $labelStyle }}">Nombre Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" required style="{{ $inputStyle('name') }}">
                        @error('name')
                            <p style="{{ $errorStyle }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" style="{{ $labelStyle }}">Usuario *</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $usuario->username) }}" required style="{{ $inputStyle('username') }}">
                        @error('username')
                            <p style="{{ $errorStyle }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" style="{{ $labelStyle }}">Nueva Contraseña <span style="font-weight:400; color:#a1a1aa;">(Opcional)</span></label>
                        <input type="password" name="password" id="password" placeholder="Dejar en blanco para mantener la actual" style="{{ $inputStyle('password') }}">
                        @error('password')
                            <p style="{{ $errorStyle }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" style="{{ $labelStyle }}">Rol de Usuario *</label>
                        <select name="role" id="role" required onchange="toggleClienteSelect()" style="{{ $inputStyle('role') }} background-color:#fff; color:#18181b;">
                            <option value="administrador" {{ old('role', $usuario->role) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="cliente" {{ old('role', $usuario->role) == 'cliente' ? 'selected' : '' }}>Cliente</option>
                        </select>
                        @error('role')
                            <p style="{{ $errorStyle }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="div_codigo_cliente" style="display: none; grid-column: 1 / -1;">
                        <label for="codigo_cliente" style="{{ $labelStyle }}">Asignar Empresa / Cliente *</label>
                        <select name="codigo_cliente" id="codigo_cliente" style="{{ $inputStyle('codigo_cliente') }} background-color:#fff; color:#18181b;">
                            <option value="">-- Seleccionar Empresa --</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->codigo_cliente }}" {{ old('codigo_cliente', $usuario->codigo_cliente) == $cliente->codigo_cliente ? 'selected' : '' }}>
                                [{{ $cliente->codigo_cliente }}] - {{ $cliente->razon_social }}
                            </option>
                            @endforeach
                        </select>
                        @error('codigo_cliente')
                            <p style="{{ $errorStyle }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Botones --}}
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:28px; border-top: 1px solid #f1f1f5; padding-top: 20px;">
                    <a href="{{ route('usuarios.index') }}" style="padding:11px 22px; border-radius:8px; font-weight:700; font-size:13px; background-color:#f4f4f5; color:#18181b; text-decoration:none; display:inline-flex; align-items:center;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-agregar" style="border:none;">
                        Actualizar Usuario
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
            }
        }
        document.addEventListener("DOMContentLoaded", toggleClienteSelect);
    </script>
</x-app-layout>