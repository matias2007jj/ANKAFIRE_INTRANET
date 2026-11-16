<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · Anka Fire</title>
    <link rel="icon" type="image/png" href="{{ asset('img/anka-logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body class="login-body">
    <main class="login-wrap">
        <div class="login-card">

            {{-- Columna del formulario --}}
            <section class="login-form-col">

                <div class="login-content">
                    <h1>Iniciar sesión</h1>
                    <p class="login-sub">Ingresa para ver el estado de tus extintores.</p>

                    @if (session('status'))
                    <div class="alert-ok">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        {{-- Usuario --}}
                        <div class="field">
                            <label for="username" class="sr-only">Usuario</label>
                            <div class="input-wrap">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <input id="username" type="text" name="username" value="{{ old('username') }}"
                                    placeholder="Usuario" required autofocus autocomplete="username">
                            </div>
                            @error('username')
                            <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contraseña (RUC) --}}
                        <div class="field">
                            <label for="password" class="sr-only">Contraseña</label>
                            <div class="input-wrap">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="4" y="11" width="16" height="9" rx="2" />
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                                </svg>
                                <input id="password" type="password" name="password"
                                    placeholder="Contraseña" required autocomplete="current-password">
                                <button type="button" id="togglePassword" class="toggle-pass" aria-label="Mostrar contraseña">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                        <path class="slash" d="M4 4l16 16" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Recordarme / olvidé contraseña --}}
                        <div class="row-options">
                            <label for="remember_me" class="check">
                                <input id="remember_me" type="checkbox" name="remember">
                                Recordarme
                            </label>

                            
                        </div>

                        <button type="submit" class="btn-primary">Ingresar</button>
                    </form>
                </div>

                <p class="login-footer">© {{ date('Y') }} Anka Fire · Equipos contra incendios</p>
            </section>

            {{-- Panel derecho: logo grande y centrado con llamas animadas --}}
            <aside class="login-panel">
                <div class="logo-anim" id="logoAnim" role="img" aria-label="Anka Fire, equipos contra incendios">
                    <img id="logoImg" src="{{ asset('img/logo-anka-panel.png') }}" alt="" width="1100" height="924">
                    <canvas id="logoCanvas" aria-hidden="true"></canvas>
                </div>
                
                <div class="panel-bottom">
                    <p class="panel-text">Tus extintores, vencimientos y certificados en un solo lugar.</p>

                    <div class="panel-features">
                        <div class="feature-item">
                            <img src="{{ asset('img/certificado.png') }}" alt="Certificados de operatividad" class="feature-icon">
                            <span>Certificados<br>de operatividad</span>
                        </div>
                        <div class="feature-item">
                            <img src="{{ asset('img/fecha-vencimiento.png') }}" alt="Fechas de vencimiento" class="feature-icon">
                            <span>Fechas de<br>vencimiento</span>
                        </div>
                        <div class="feature-item">
                            <img src="{{ asset('img/info.png') }}" alt="Información por serie" class="feature-icon">
                            <span>Información<br>por serie</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <script src="{{ asset('js/login.js') }}"></script>
    <script src="{{ asset('js/logo-fuego.js') }}"></script>
</body>

</html>