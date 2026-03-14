<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/usuarios';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:super_usuario,gerente,almacenista,proveedor'],
        ];

        if (isset($data['role']) && ($data['role'] === 'gerente' || $data['role'] === 'super_usuario')) {
            $rules['admin_password'] = ['required', 'string'];
        }

        if (isset($data['role']) && $data['role'] === 'proveedor') {
            $rules['empresa'] = ['required', 'string', 'max:255'];
            $rules['ruc'] = ['required', 'string', 'unique:proveedores,ruc'];
            $rules['telefono_proveedor'] = ['required', 'string'];
            $rules['direccion_proveedor'] = ['required', 'string'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        if ($data['role'] === 'gerente' || $data['role'] === 'super_usuario') {
            if ($data['admin_password'] !== env('ADMIN_CREATE_GERENTE_PASSWORD', 'pepelin123')) {
                 throw \Illuminate\Validation\ValidationException::withMessages([
                    'admin_password' => ['La contraseña de autorización es incorrecta.'],
                ]);
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        if ($data['role'] === 'proveedor') {
            $user->proveedor()->create([
                'empresa' => $data['empresa'],
                'ruc' => $data['ruc'],
                'telefono' => $data['telefono_proveedor'],
                'direccion' => $data['direccion_proveedor'],
                'latitud' => $data['latitud'] ?? null,
                'longitud' => $data['longitud'] ?? null,
                'ciudad' => $data['ciudad'] ?? null,
                'pais' => $data['pais'] ?? 'Colombia',
            ]);
        }

        return $user;
    }
}
