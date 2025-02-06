<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-trabajadores', ['only' => ['index']]);
        $this->middleware('permission:agregar-trabajadores', ['only' => ['create', 'store']]);
        $this->middleware('permission:actualizar-trabajadores', ['only' => ['update', 'edit']]);
        $this->middleware('permission:eliminar-trabajadores', ['only' => ['destroy']]);
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('role-permission.workers.index', ['users' => $users]);
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('role-permission.workers.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'roles' => 'required',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'dni' => 'required|string|unique:users,dni',
            'correo' => 'required|string|max:255|unique:users,correo',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'dni' => $request->dni,
            'correo' => $request->correo,
            'user_register' => auth()->user()->id,
            'codigo' => $this->generateCode(),
        ]);

        $user->syncRoles($request->roles);

        return redirect('/users')->with('status', 'Trabajador creado con éxito');
    }
    public function generateCode()
    {
        $lastCodigo = User::max('codigo') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRoles = $user->roles->pluck('name', 'name')->all();
        return view('role-permission.workers.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|max:20',
            'roles' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'dni' => $request->dni,
            'correo' => $request->correo,
            'user_update' => auth()->user()->id
        ];

        if (!empty($request->password)) {
            $data += [
                'password' => Hash::make($request->password),
            ];
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect('/users')->with('status', 'Trabajador actualizado con éxito');
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        $message = $user->status == 1
            ? 'El Trabajador ha sido activado con éxito.'
            : 'El Trabajador ha sido desactivado con éxito.';

        return redirect('/users')->with('status', $message);
    }
}
