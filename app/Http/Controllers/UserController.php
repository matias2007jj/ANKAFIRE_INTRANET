<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $users = User::when($buscar, function ($query, $buscar) {
            return $query->where('name', 'like', "%{$buscar}%")
                ->orWhere('username', 'like', "%{$buscar}%");
        })->orderBy('id', 'desc')->paginate(10);

        // Cambiado 'usuarios.index' por 'usuarios.usuario'
        return view('usuarios.usuario', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        // Ordenar alfabéticamente por Razón Social
        $clientes = Cliente::orderBy('razon_social', 'asc')->get();
        return view('usuarios.create', compact('clientes'));
    }

    /**
     * Guarda el usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'username'       => 'required|string|max:255|unique:users,username',
            'password'       => 'required|string|min:6',
            'role'           => 'required|in:administrador,cliente',
            'codigo_cliente' => 'nullable|required_if:role,cliente|exists:clientes,codigo_cliente',
        ]);

        User::create([
            'name'           => $request->name,
            'username'       => $request->username,
            'email'          => $request->username . '@ankafire.com', // Por compatibilidad
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'codigo_cliente' => $request->role === 'cliente' ? $request->codigo_cliente : null,
        ]);

        return redirect()->route('usuarios.index')->with('mensaje', 'Usuario registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un usuario.
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $clientes = Cliente::orderBy('razon_social', 'asc')->get();
        return view('usuarios.edit', compact('usuario', 'clientes'));
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'username'       => 'required|string|max:255|unique:users,username,' . $usuario->id,
            'password'       => 'nullable|string|min:6',
            'role'           => 'required|in:administrador,cliente',
            'codigo_cliente' => 'nullable|required_if:role,cliente|exists:clientes,codigo_cliente',
        ]);

        $data = [
            'name'           => $request->name,
            'username'       => $request->username,
            'email'          => $request->username . '@ankafire.com',
            'role'           => $request->role,
            'codigo_cliente' => $request->role === 'cliente' ? $request->codigo_cliente : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('mensaje', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('mensaje', 'Usuario eliminado correctamente.');
    }
}
