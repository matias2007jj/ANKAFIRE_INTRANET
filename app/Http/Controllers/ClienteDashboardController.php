<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Sede;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Exports\EquiposExport;
use Maatwebsite\Excel\Facades\Excel;

class ClienteDashboardController extends Controller
{
    public function show(Request $request, ?string $codigo = null)
    {
        return $this->procesarDashboard($request, $codigo);
    }

    public function vistaPrueba(Request $request, ?string $codigo = null)
    {
        return $this->procesarDashboard($request, $codigo);
    }

    /**
     * Filtros compartidos por el dashboard y las exportaciones (Excel / PDF),
     * para que lo que se ve en pantalla sea exactamente lo que se exporta.
     * Recibe un query builder o una relación y devuelve el mismo objeto filtrado.
     */
    private function aplicarFiltros($query, Request $request)
    {
        // Filtro por sede
        if ($request->filled('sede')) {
            $query->where('codigo_sede', $request->input('sede'));
        }

        // Búsqueda opcional por N° de serie o N° interno
        if ($request->filled('buscar')) {
            $texto = $request->input('buscar');
            $query->where(function ($q) use ($texto) {
                $q->where('numero_serie', 'like', "%{$texto}%")
                  ->orWhere('numero_interno', 'like', "%{$texto}%");
            });
        }

        return $query;
    }

    private function procesarDashboard(Request $request, ?string $codigo)
    {
        $user = $request->user();

        if (!$codigo && $user && isset($user->codigo_cliente) && $user->codigo_cliente) {
            $codigo = $user->codigo_cliente;
        }

        if (!$codigo) {
            abort(404, 'No se especificó un código de cliente.');
        }

        if ($user && $user->role === 'cliente' && $user->codigo_cliente !== $codigo) {
            abort(403, 'No tienes permiso para ver este cliente.');
        }

        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        // 1. Obtener sedes
        $sedes = Sede::where('codigo_cliente', $cliente->codigo_cliente)->get();

        if ($sedes->isEmpty()) {
            $sedes = $cliente->equipos()
                ->whereNotNull('codigo_sede')
                ->where('codigo_sede', '!=', '')
                ->pluck('codigo_sede')
                ->unique()
                ->map(function ($codigoSede) {
                    return (object)[
                        'codigo_sede' => $codigoSede,
                        'nombre_sede' => $codigoSede,
                    ];
                });
        }

        // 2. Consulta base de equipos + filtros
        $query = $this->aplicarFiltros($cliente->equipos()->with('sede'), $request);

        $hoy = Carbon::today();
        $limiteProximo = $hoy->copy()->addDays(60);

        $clasificar = function ($equipo) use ($hoy, $limiteProximo) {
            $vencimiento = $equipo->vencimiento_ph ? Carbon::parse($equipo->vencimiento_ph) : null;

            if (!$vencimiento) {
                $equipo->estado_visual = 'sin_dato';
            } elseif ($vencimiento->lt($hoy)) {
                $equipo->estado_visual = 'vencido';
            } elseif ($vencimiento->lte($limiteProximo)) {
                $equipo->estado_visual = 'por_vencer';
            } else {
                $equipo->estado_visual = 'vigente';
            }
        };

        $todos = (clone $query)->get();
        $todos->each($clasificar);

        $resumen = [
            'total'      => $todos->count(),
            'vigentes'   => $todos->where('estado_visual', 'vigente')->count(),
            'por_vencer' => $todos->where('estado_visual', 'por_vencer')->count(),
            'vencidos'   => $todos->where('estado_visual', 'vencido')->count(),
        ];

        $equipos = $query->orderBy('vencimiento_ph')->paginate(15)->withQueryString();
        $equipos->getCollection()->each($clasificar);

        return view('clientes.vistaclientes', compact('cliente', 'equipos', 'resumen', 'sedes'));
    }

    public function exportarExcel(Request $request, string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        // Mismos filtros que el dashboard (sede y búsqueda)
        $query = $this->aplicarFiltros(Equipo::where('codigo_cliente', $codigo), $request)
            ->orderBy('numero_interno');

        $nombreCliente = $cliente->razon_social ?? $cliente->nombre_comercial ?? $codigo;
        $nombreArchivo = "Reporte de extintores de {$nombreCliente}.xlsx";

        return Excel::download(new EquiposExport($query), $nombreArchivo);
    }

    public function exportarPdf(Request $request, string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        // Mismos filtros que el dashboard (sede y búsqueda)
        $equipos = $this->aplicarFiltros(Equipo::where('codigo_cliente', $codigo), $request)
            ->orderBy('numero_interno')
            ->get();

        $pdf = Pdf::loadView('clientes.pdf-equipos', compact('cliente', 'equipos'));

        $nombreCliente = $cliente->razon_social ?? $cliente->nombre_comercial ?? $codigo;
        $nombreArchivo = "Reporte de extintores de {$nombreCliente}.pdf";

        return $pdf->download($nombreArchivo);
    }
}