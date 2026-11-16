<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Sede;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipoController extends Controller
{
    public function create(string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();
        $sedes = Sede::where('codigo_cliente', $cliente->codigo_cliente)->get();
        $editando = false;
        $equipo = null; // Se define la variable nula para la vista de creación

        [$tipos, $marcas, $capacidades] = $this->catalogos();

        return view('equipos.create', compact('cliente', 'sedes', 'tipos', 'marcas', 'capacidades', 'editando', 'equipo'));
    }

    public function store(Request $request, string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        $datos = $this->validarDatos($request);
        $datos = $this->calcularFechas($datos);
        $datos['codigo_cliente'] = $cliente->codigo_cliente;
        $datos = $this->procesarArchivos($request, $datos);

        Equipo::create($datos);

        return redirect()
            ->route('clientes.dashboard', $cliente->codigo_cliente)
            ->with('mensaje', 'Extintor agregado correctamente.');
    }

    public function edit(string $codigo, $id)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();
        $equipo = Equipo::findOrFail($id);
        $sedes = Sede::where('codigo_cliente', $cliente->codigo_cliente)->get();
        $editando = true;

        [$tipos, $marcas, $capacidades] = $this->catalogos();

        return view('equipos.create', compact('cliente', 'sedes', 'tipos', 'marcas', 'capacidades', 'equipo', 'editando'));
    }

    public function update(Request $request, string $codigo, $id)
    {
        $equipo = Equipo::findOrFail($id);

        $datos = $this->validarDatos($request);
        $datos = $this->calcularFechas($datos, $equipo);
        $datos = $this->procesarArchivos($request, $datos, $equipo);

        // Asegurar que el estado recibido reemplace al anterior
        if ($request->filled('estado')) {
            $datos['estado'] = $request->input('estado');
        }

        $equipo->update($datos);

        return redirect()
            ->route('clientes.dashboard', $equipo->codigo_cliente)
            ->with('mensaje', 'Extintor actualizado correctamente.');
    }

    public function toggleBaja(Equipo $equipo)
    {
        $estBd = strtolower(trim($equipo->estado ?? ''));

        // Si el estado es "vencido" o "de baja", cambia directamente a "Vigente" al presionar el botón
        if (in_array($estBd, ['de baja', 'de_baja', 'debaja', 'vencido'])) {
            $equipo->estado = 'Vigente';
            $mensaje = 'El extintor ha sido renovado/reactivado a Vigente correctamente.';
        } else {
            // Si estaba "Vigente", pasa a "De baja"
            $equipo->estado = 'De baja';
            $mensaje = 'El extintor ha sido dado de baja correctamente.';
        }

        $equipo->save();

        return redirect()->back()->with('mensaje', $mensaje);
    }

    public function destroy(Equipo $equipo)
    {
        $codigoCliente = $equipo->codigo_cliente;
        $equipo->delete();

        return redirect()
            ->route('clientes.dashboard', $codigoCliente)
            ->with('mensaje', 'Extintor eliminado.');
    }

    /** Catálogos para los <select> del formulario */
    private function catalogos(): array
    {
        return [
            DB::table('tipos_extintor')->orderBy('nombre')->pluck('nombre'),
            DB::table('marcas')->orderBy('nombre')->pluck('nombre'),
            DB::table('capacidades')->orderBy('valor')->pluck('valor'),
        ];
    }

    /** Validación compartida entre crear y editar */
    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'codigo_sede' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'numero_interno' => 'nullable|string|max:255',
            'tipo_extintor' => 'required|string',
            'capacidad_carga' => 'nullable|string',
            'marca' => 'nullable|string',
            'anio_fabricacion' => 'nullable|integer|min:1980|max:' . date('Y'),
            'estado' => 'required|string|in:Vigente,Vencido,De baja',
            'fecha_ultimo_servicio' => 'nullable|date',
            'mes_ultimo_mantenimiento' => 'nullable|date_format:Y-m',
            'mes_prueba_hidrostatica' => 'nullable|date_format:Y-m',
            'numero_certificado' => 'nullable|string|max:255',
            'cert_operatividad' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'informe_tecnico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'cert_ph' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
    }

    /**
     * Calcula proximo_mantenimiento y vencimiento_ph respetando el estado.
     */
    private function calcularFechas(array $datos, ?Equipo $equipo = null): array
    {
        // Próximo mantenimiento (+1 año)
        if (!empty($datos['mes_ultimo_mantenimiento'])) {
            $fechaMant = Carbon::createFromFormat('Y-m', $datos['mes_ultimo_mantenimiento'])->startOfMonth();
            $datos['fecha_ultimo_mantenimiento'] = $fechaMant->toDateString();
            $datos['proximo_mantenimiento'] = strtoupper(
                $fechaMant->copy()->addYear()->locale('es')->isoFormat('MMM-YYYY')
            );
        } elseif (!$equipo) {
            $fechaMant = now();
            $datos['fecha_ultimo_mantenimiento'] = $fechaMant->toDateString();
            $datos['proximo_mantenimiento'] = strtoupper(
                $fechaMant->copy()->addYear()->locale('es')->isoFormat('MMM-YYYY')
            );
        }

        // Vencimiento PH (+5 años)
        if (!empty($datos['mes_prueba_hidrostatica'])) {
            $fechaPH = Carbon::createFromFormat('Y-m', $datos['mes_prueba_hidrostatica'])->startOfMonth();
            $datos['fecha_prueba_hidrostatica'] = $fechaPH->toDateString();
            $datos['vencimiento_ph'] = $fechaPH->copy()->addYears(5)->toDateString();
        } elseif (!$equipo) {
            $datos['vencimiento_ph'] = now()->addYears(5)->toDateString();
        }

        unset($datos['mes_ultimo_mantenimiento'], $datos['mes_prueba_hidrostatica']);

        return $datos;
    }

    /**
     * Procesamiento de archivos subidos
     */
    private function procesarArchivos(Request $request, array $datos, ?Equipo $equipo = null): array
    {
        $existente = null;
        if (!empty($datos['numero_certificado'])) {
            $existente = Equipo::where('numero_certificado', $datos['numero_certificado'])
                ->when($equipo, fn($q) => $q->where('id', '!=', $equipo->id))
                ->latest()
                ->first();
        }

        foreach (
            [
                'cert_operatividad' => 'ruta_cert_operatividad',
                'informe_tecnico' => 'ruta_informe_tecnico',
                'cert_ph' => 'ruta_cert_ph',
            ] as $campoArchivo => $campoRuta
        ) {
            if ($request->hasFile($campoArchivo)) {
                $datos[$campoRuta] = $request->file($campoArchivo)->store('certificados', 'public');
            } elseif ($existente && $existente->$campoRuta) {
                $datos[$campoRuta] = $existente->$campoRuta;
            }
        }

        unset($datos['cert_operatividad'], $datos['informe_tecnico'], $datos['cert_ph']);

        return $datos;
    }
}