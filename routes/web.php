<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteDashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrincipalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirección inicial según el rol del usuario
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'administrador') {
        return redirect()->route('principal');
    }
    return redirect()->route('clientes.dashboard', $user->codigo_cliente);
})->middleware(['auth', 'verified'])->name('dashboard');

// --- TODAS LAS RUTAS PROTEGIDAS POR AUTENTICACIÓN ---
Route::middleware(['auth'])->group(function () {

    // Ruta Principal (Listado de Clientes para el Administrador)
    Route::get('/principal', [PrincipalController::class, 'index'])->name('principal');

    // Redirección de /clientes a /principal para evitar pantalla en blanco
    Route::get('/clientes', function () {
        return redirect()->route('principal');
    })->name('clientes.index');

    // --- RUTAS DE USUARIOS ---
    Route::resource('usuarios', UserController::class);

    // --- RUTAS DE CLIENTES ---
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // Dashboard del Cliente
    Route::get('/clientes/{codigo}/dashboard', [ClienteDashboardController::class, 'show'])->name('clientes.dashboard');

    // --- RUTAS DE EQUIPOS ---
    Route::get('/clientes/{codigo}/equipos/nuevo', [EquipoController::class, 'create'])->name('equipos.create');
    Route::post('/clientes/{codigo}/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::get('/clientes/{codigo}/equipos/{id}/editar', [EquipoController::class, 'edit'])->name('equipos.edit');
    Route::put('/clientes/{codigo}/equipos/{id}', [EquipoController::class, 'update'])->name('equipos.update');
    Route::patch('/equipos/{equipo}/toggle-baja', [EquipoController::class, 'toggleBaja'])->name('equipos.toggleBaja');
    Route::delete('/clientes/{codigo}/equipos/{id}', [EquipoController::class, 'destroy'])->name('equipos.destroy');

    // Exportaciones y Pruebas
    Route::get('/clientes/{codigo}/exportar-excel', [ClienteDashboardController::class, 'exportarExcel'])->name('clientes.exportar.excel');
    Route::get('/clientes/{codigo}/exportar-pdf', [ClienteDashboardController::class, 'exportarPdf'])->name('clientes.exportar.pdf');
    Route::get('/vistacliente', [ClienteDashboardController::class, 'vistaPrueba'])->name('clientes.vistaprueba');
});

require __DIR__ . '/auth.php';