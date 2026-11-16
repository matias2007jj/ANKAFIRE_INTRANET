<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>anka fire</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/anka-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer (incrustado directo aquí, sin archivo aparte) -->
<div class="relative bg-white">
    {{-- Cuña decorativa que simula el corte diagonal superior del footer --}}
    <div class="absolute inset-x-0 -top-1 h-10 bg-gray-950 [clip-path:polygon(0_100%,100%_0,100%_100%)]"></div>

    <footer class="relative bg-gray-950 pt-14 pb-8 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Tarjetas de contacto --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-16">
                <div class="relative bg-gray-800/60 rounded-md pt-4 pb-5 px-5 overflow-hidden">
                    <span class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-red-600 to-orange-500"></span>
                    <div class="flex items-center gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-600 text-white">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884 10 9.882l7.997-3.998A2 2 0 0 0 16 4H4a2 2 0 0 0-1.997 1.884Z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.118Z"/></svg>
                        </span>
                        <div>
                            <p class="text-white font-bold text-lg leading-tight">+51 981 333 579</p>
                            <p class="text-sm text-gray-400">Llámanos</p>
                        </div>
                    </div>
                </div>

                <div class="relative bg-red-600 rounded-md pt-4 pb-5 px-5 overflow-hidden">
                    <span class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-red-400 to-orange-400"></span>
                    <div class="flex items-center gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-red-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2.94 6.412A2 2 0 0 1 4.5 5h11a2 2 0 0 1 1.559 1.412L10 10.882 2.94 6.412Z"/><path d="M18 8.118V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.118l7.485 4.671a1 1 0 0 0 1.03 0L18 8.118Z"/></svg>
                        </span>
                        <div>
                            <p class="text-white font-bold text-lg leading-tight">ventas@ankaqhse.com</p>
                            <p class="text-sm text-red-100">Escribenos</p>
                        </div>
                    </div>
                </div>

                <div class="relative bg-gray-800/60 rounded-md pt-4 pb-5 px-5 overflow-hidden">
                    <span class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-red-600 to-orange-500"></span>
                    <div class="flex items-center gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-600 text-white">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.69 18.933a.75.75 0 0 0 .62 0c.058-.026 6.69-3.02 6.69-9.183a7 7 0 1 0-14 0c0 6.163 6.632 9.157 6.69 9.183ZM10 12.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/></svg>
                        </span>
                        <div>
                            <p class="text-white font-bold text-lg leading-tight">Mz M Lote 18 Sector 2</p>
                            <p class="text-white font-bold text-lg leading-tight -mt-0.5">Grupo 24A - VES</p>
                            <p class="text-sm text-gray-400">Visítanos</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columnas: marca, servicios, productos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
                <div>
                    <a href="{{ route('principal') }}" class="inline-block mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="Anka Fire" class="h-12 w-auto object-contain">
                    </a>
                    <p class="text-sm leading-relaxed text-gray-400 max-w-xs">
                        Especializados en equipos, sistemas y accesorios contra incendios. Comprometidos con la seguridad, la calidad y la protección de vidas y bienes.
                    </p>
                    <div class="flex gap-3 mt-5">
                        <a href="#" aria-label="Facebook" class="flex h-9 w-9 items-center justify-center rounded bg-red-600 text-white hover:bg-red-500 transition">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.5-3.89 3.78-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram" class="flex h-9 w-9 items-center justify-center rounded bg-red-600 text-white hover:bg-red-500 transition">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c2.72 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.22.6 1.77 1.15.55.55.9 1.11 1.15 1.77.25.64.42 1.37.47 2.43.05 1.06.06 1.4.06 4.12s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43a4.9 4.9 0 0 1-1.15 1.77 4.9 4.9 0 0 1-1.77 1.15c-.64.25-1.37.42-2.43.47-1.06.05-1.4.06-4.12.06s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.9 4.9 0 0 1-1.77-1.15 4.9 4.9 0 0 1-1.15-1.77c-.25-.64-.42-1.37-.47-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.26-.66.6-1.22 1.15-1.77A4.9 4.9 0 0 1 5.45.53C6.09.28 6.82.11 7.88.06 8.94.01 9.28 0 12 0Zm0 5.4A6.6 6.6 0 1 0 12 18.6 6.6 6.6 0 0 0 12 5.4Zm0 10.88A4.28 4.28 0 1 1 12 7.72a4.28 4.28 0 0 1 0 8.56Zm6.8-11.13a1.54 1.54 0 1 1-3.08 0 1.54 1.54 0 0 1 3.08 0Z"/></svg>
                        </a>
                        <a href="#" aria-label="LinkedIn" class="flex h-9 w-9 items-center justify-center rounded bg-red-600 text-white hover:bg-red-500 transition">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13ZM7.12 20.45H3.56V9h3.56v11.45Z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Servicios</h3>
                    <ul class="space-y-3 text-sm">
                        @foreach ([
                            'Recarga y mantenimiento de extintores',
                            'Prueba hidrostática de extintores',
                            'Inspección de extintores (NTP 350.043-1)',
                            'Capacitaciones',
                            'Analisis de riesgo',
                            'Mantenimiento de sistema contra incendios',
                        ] as $servicio)
                            <li class="flex items-start gap-2">
                                <svg class="h-4 w-4 mt-0.5 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
                                <a href="#" class="hover:text-white transition">{{ $servicio }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Productos</h3>
                    <ul class="space-y-3 text-sm">
                        @foreach ([
                            'Extintores',
                            'Pedestales y fundas',
                            'Señalizaciones de seguridad',
                            'Gabinetes',
                        ] as $producto)
                            <li class="flex items-start gap-2">
                                <svg class="h-4 w-4 mt-0.5 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
                                <a href="#" class="hover:text-white transition">{{ $producto }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Barra inferior --}}
            <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
                <p>Diseño web por: <a href="#" class="hover:text-gray-300 transition">MN & MA Tech</a></p>
                <p>Copyright &copy; {{ now()->year }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
        </div>
    </body>
</html>