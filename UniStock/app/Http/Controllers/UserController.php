<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = User::where('activo', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(12);
        
        return view('usuarios.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->gestionarUsuarios()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para crear usuarios.');
        }

        $roles = [];
        if (auth()->user()->isSuperUsuario()) {
            $roles[User::ROLE_SUPER_USUARIO] = 'Super Usuario';
            $roles[User::ROLE_GERENTE] = 'Gerente';
        }
        $roles[User::ROLE_ALMACENISTA] = 'Almacenista';
        $roles[User::ROLE_PROVEEDOR] = 'Proveedor';

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->gestionarUsuarios()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para crear usuarios.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:super_usuario,gerente,almacenista,proveedor',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->role === User::ROLE_GERENTE || $request->role === User::ROLE_SUPER_USUARIO) {
            $rules['admin_password'] = 'required';
        }

        if ($request->role === User::ROLE_PROVEEDOR) {
            $rules['empresa'] = 'required|string|max:255';
            $rules['ruc'] = 'required|string|unique:proveedores,ruc';
            $rules['telefono_proveedor'] = 'required|string';
            $rules['direccion_proveedor'] = 'required|string';
        }

        $request->validate($rules);

        // Si es un Gerente, no puede crear un Super Usuario ni un Gerente
        if (auth()->user()->isGerente() && in_array($request->role, [User::ROLE_SUPER_USUARIO, User::ROLE_GERENTE])) {
            return redirect()->back()
                ->with('error', 'No tienes permisos para asignar roles directivos (Super Usuario o Gerente).')
                ->withInput();
        }

        if ($request->role === User::ROLE_GERENTE || $request->role === User::ROLE_SUPER_USUARIO) {
            $adminPassword = env('ADMIN_CREATE_GERENTE_PASSWORD', 'pepelin123');
            if ($request->admin_password !== $adminPassword) {
                return back()->withErrors(['admin_password' => 'Contraseña de autorización incorrecta.'])->withInput();
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('users/photos', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'photo' => $photoPath,
        ]);

        if ($request->role === User::ROLE_PROVEEDOR) {
            Proveedor::create([
                'user_id' => $user->id,
                'empresa' => $request->empresa,
                'ruc' => $request->ruc,
                'telefono' => $request->telefono_proveedor,
                'direccion' => $request->direccion_proveedor,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'ciudad' => $request->ciudad,
                'pais' => $request->pais ?? 'Colombia',
            ]);
        }

        if ($request->filled('telefono') || $request->filled('documento_identidad')) {
            UserProfile::create([
                'user_id' => $user->id,
                'telefono' => $request->telefono,
                'documento_identidad' => $request->documento_identidad,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion,
            ]);
        }

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $usuario)
    {
        $usuario->load(['profile', 'proveedor']);
        return view('usuarios.show', ['user' => $usuario]);
    }

    public function edit(User $usuario)
    {
        if (!auth()->user()->gestionarUsuarios() && auth()->id() !== $usuario->id) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para editar este usuario.');
        }

        // Un gerente no puede editar a un Super Usuario ni a otro Gerente (excepto a sí mismo)
        if (auth()->user()->isGerente() && $usuario->id !== auth()->id() && ($usuario->isSuperUsuario() || $usuario->isGerente())) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para editar a este usuario directivo.');
        }

        $usuario->load(['profile', 'proveedor']);
        
        $roles = [];
        if (auth()->user()->isSuperUsuario()) {
            $roles[User::ROLE_SUPER_USUARIO] = 'Super Usuario';
            $roles[User::ROLE_GERENTE] = 'Gerente';
        }
        $roles[User::ROLE_ALMACENISTA] = 'Almacenista';
        $roles[User::ROLE_PROVEEDOR] = 'Proveedor';

        return view('usuarios.edit', ['user' => $usuario, 'roles' => $roles]);
    }

    public function update(Request $request, User $usuario)
    {
        if (!auth()->user()->gestionarUsuarios() && auth()->id() !== $usuario->id) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para actualizar este usuario.');
        }

        // Un gerente no puede editar a un Super Usuario ni a otro Gerente (excepto a sí mismo)
        if (auth()->user()->isGerente() && $usuario->id !== auth()->id() && ($usuario->isSuperUsuario() || $usuario->isGerente())) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para actualizar a este usuario directivo.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($usuario->isProveedor()) {
            $rules['empresa'] = 'required|string|max:255';
            $rules['ruc'] = 'required|string|unique:proveedores,ruc,' . ($usuario->proveedor->id ?? 'NULL');
            $rules['telefono_proveedor'] = 'required|string';
            $rules['direccion_proveedor'] = 'required|string';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email
        ];

        if (auth()->user()->isSuperUsuario() && $usuario->id !== auth()->id()) {
            $data['activo'] = $request->has('activo');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($usuario->photo) {
                Storage::disk('public')->delete($usuario->photo);
            }
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $usuario->update($data);

        if ($usuario->isProveedor()) {
            $proveedorData = [
                'empresa' => $request->empresa,
                'ruc' => $request->ruc,
                'telefono' => $request->telefono_proveedor,
                'direccion' => $request->direccion_proveedor,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'ciudad' => $request->ciudad,
                'pais' => $request->pais ?? 'Colombia',
            ];

            if ($usuario->proveedor) {
                $usuario->proveedor->update($proveedorData);
            } else {
                $usuario->proveedor()->create($proveedorData);
            }
        }

        if ($request->filled('telefono') || $request->filled('documento_identidad')) {
            $profileData = [
                'telefono' => $request->telefono,
                'documento_identidad' => $request->documento_identidad,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion,
            ];

            if ($usuario->profile) {
                $usuario->profile->update($profileData);
            } else {
                $usuario->profile()->create($profileData);
            }
        }

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        if (!auth()->user()->gestionarUsuarios()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para inhabilitar usuarios.');
        }

        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                             ->with('error', 'No puedes inhabilitar tu propia cuenta.');
        }

        // Un gerente no puede inhabilitar a un Super Usuario ni a otro Gerente
        if (auth()->user()->isGerente() && ($usuario->isSuperUsuario() || $usuario->isGerente())) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No tienes permisos para inhabilitar a este usuario directivo.');
        }

        $usuario->update([
            'activo' => false,
            'email' => $usuario->email . '.inactivo.' . time(),
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario inhabilitado exitosamente.');
    }
}

