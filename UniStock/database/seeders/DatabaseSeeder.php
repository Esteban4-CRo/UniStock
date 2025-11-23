<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Proveedor;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $superUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_USUARIO,
        ]);

        $gerente = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'gerente@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_GERENTE,
        ]);

        UserProfile::create([
            'user_id' => $gerente->id,
            'telefono' => '+57 300 123 4567',
            'documento_identidad' => '1234567890',
        ]);

        $almacenista = User::create([
            'name' => 'María González',
            'email' => 'almacenista@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ALMACENISTA,
        ]);

        UserProfile::create([
            'user_id' => $almacenista->id,
            'telefono' => '+57 301 234 5678',
            'documento_identidad' => '9876543210',
        ]);

        $proveedor = User::create([
            'name' => 'Juan Pérez',
            'email' => 'proveedor@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PROVEEDOR,
        ]);

        Proveedor::create([
            'user_id' => $proveedor->id,
            'empresa' => 'Distribuidora El Sol S.A.S',
            'ruc' => '900123456-7',
            'telefono' => '+57 302 345 6789',
            'direccion' => 'Calle 100 #15-20, Bogotá',
            'latitud' => 4.7110,
            'longitud' => -74.0721,
            'ciudad' => 'Bogotá',
            'pais' => 'Colombia',
        ]);
    }
}

