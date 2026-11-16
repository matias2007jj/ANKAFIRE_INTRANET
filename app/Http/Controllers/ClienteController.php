<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Models\Cliente;
use App\Models\Sede;
use App\Models\Equipo;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // Un cliente no ve la lista completa de clientes, va directo a su propio dashboard
        if ($request->user() && $request->user()->role === 'cliente') {
            return redirect()->route('clientes.dashboard', $request->user()->codigo_cliente);
        }

        $query = Cliente::withCount('equipos');

        // Buscador para el Listado General de Clientes
        if ($request->filled('buscar')) {
            $texto = $request->input('buscar');
            $query->where(function ($q) use ($texto) {
                $q->where('razon_social', 'like', "%{$texto}%")
                    ->orWhere('nombre_comercial', 'like', "%{$texto}%")
                    ->orWhere('codigo_cliente', 'like', "%{$texto}%")
                    ->orWhere('ruc', 'like', "%{$texto}%");
            });
        }

        $clientes = $query->orderBy('razon_social')->paginate(15)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    // Método para renderizar el Dashboard de Vista Clientes con Filtros y Resumen
    public function dashboard(Request $request, $codigo_cliente)
    {
        $user = $request->user();

        // SEGURIDAD: Un usuario cliente solo puede acceder a su propia empresa
        if ($user && $user->role === 'cliente' && $user->codigo_cliente !== $codigo_cliente) {
            abort(403, 'No tienes autorización para ver el panel de esta empresa.');
        }

        $cliente = Cliente::where('codigo_cliente', $codigo_cliente)->firstOrFail();

        // 1. Obtener sedes directamente por codigo_cliente
        $sedes = Sede::where('codigo_cliente', $cliente->codigo_cliente)->get();

        // Si la tabla 'sedes' está vacía para este cliente, extrae las sedes grabadas en los equipos
        if ($sedes->isEmpty()) {
            $sedes = $cliente->equipos()
                ->whereNotNull('codigo_sede')
                ->where('codigo_sede', '!=', '')
                ->pluck('codigo_sede')
                ->unique()
                ->map(fn($item) => (object)[
                    'codigo_sede' => $item,
                    'nombre_sede' => $item
                ]);
        }

        // 2. Consulta de equipos
        $query = $cliente->equipos();

        // 3. Filtrar por Sede
        if ($request->filled('sede')) {
            $query->where('codigo_sede', $request->input('sede'));
        }

        // 4. Filtrar por Búsqueda (N° de serie o N° interno)
        if ($request->filled('buscar')) {
            $texto = $request->input('buscar');
            $query->where(function ($q) use ($texto) {
                $q->where('numero_serie', 'like', "%{$texto}%")
                    ->orWhere('numero_interno', 'like', "%{$texto}%");
            });
        }

        $equipos = $query->paginate(15)->withQueryString();

        // 5. Resumen
        $todosEquipos = $cliente->equipos;
        $resumen = [
            'total'      => $todosEquipos->count(),
            'vigentes'   => $todosEquipos->where('estado_visual', 'vigente')->count(),
            'por_vencer' => $todosEquipos->where('estado_visual', 'por_vencer')->count(),
            'vencidos'   => $todosEquipos->where('estado_visual', 'vencido')->count(),
        ];

        return view('clientes.vistaclientes', compact('cliente', 'sedes', 'equipos', 'resumen'));
    }

    public function create()
    {
        $cliente = new Cliente();
        return view('clientes.create', compact('cliente'));
    }

    /**
     * Reglas de validación compartidas entre store() y update().
     * $idExcluir permite que el RUC no choque contra el propio registro al editar.
     */
    protected function reglasValidacion($idExcluir = null)
    {
        $reglaRuc = 'required|digits:11|unique:clientes,ruc';
        if ($idExcluir) {
            $reglaRuc .= ',' . $idExcluir;
        }

        return [
            'razon_social'     => 'required|string|max:255',
            'nombre_comercial' => 'required|string|max:255',
            'ruc'              => $reglaRuc,
            'direccion'        => 'required|string|max:500',
            'provincia'        => 'required|string|max:255',
            'contacto'         => 'required|string|max:255',
            'cargo'            => 'required|string|max:255',
            'correo'           => 'required|email|max:255',
            'telefono'         => 'required|digits_between:1,9',
            'estado'           => 'required|string|in:VIGENTE,INACTIVO',
        ];
    }

    protected function mensajesValidacion()
    {
    return [
        'ruc.digits'              => 'El RUC debe tener exactamente 11 dígitos numéricos.',
        'ruc.unique'              => 'Ya existe un cliente registrado con este RUC.',
        'correo.email'            => 'El correo electrónico no tiene un formato válido.',
        'telefono.digits_between' => 'El teléfono debe contener solo números y máximo 9 dígitos.',
        'estado.in'               => 'El estado seleccionado no es válido.',
    ];
    }

    public function store(Request $request)
    {
        $datos = $request->validate(
            $this->reglasValidacion(),
            $this->mensajesValidacion()
        );

        $siguienteId = (Cliente::max('id') ?? 0) + 1;
        $datos['codigo_cliente'] = 'AF-CLI-' . str_pad($siguienteId, 3, '0', STR_PAD_LEFT);

        Cliente::create($datos);

        return redirect()
            ->route('principal')
            ->with('mensaje', 'Cliente registrado correctamente con el código ' . $datos['codigo_cliente']);
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.create', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $datos = $request->validate(
            $this->reglasValidacion($cliente->id),
            $this->mensajesValidacion()
        );

        $cliente->update($datos);

        return redirect()
            ->route('principal')
            ->with('mensaje', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);

        if (strtoupper($cliente->estado) === 'INACTIVO') {
            $cliente->estado = 'VIGENTE';
            $mensaje = 'El cliente ha sido activado nuevamente.';
        } else {
            $cliente->estado = 'INACTIVO';
            $mensaje = 'El cliente ha sido inactivado correctamente.';
        }

        $cliente->save();

        return redirect()->route('principal')->with('mensaje', $mensaje);
    }
}
