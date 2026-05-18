<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Proveedor;
use App\Models\UserProfile;
use App\Models\Ubicacion;
use App\Models\MaterialPrima;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Crear Usuarios de prueba con estado 'activo' = true
        $superUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_USUARIO,
            'activo' => true,
        ]);

        $gerente = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'gerente@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_GERENTE,
            'activo' => true,
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
            'activo' => true,
        ]);

        UserProfile::create([
            'user_id' => $almacenista->id,
            'telefono' => '+57 301 234 5678',
            'documento_identidad' => '9876543210',
        ]);

        $proveedorUser = User::create([
            'name' => 'Juan Pérez',
            'email' => 'proveedor@unistock.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PROVEEDOR,
            'activo' => true,
        ]);

        // 2. Crear Proveedores
        $proveedor = Proveedor::create([
            'user_id' => $proveedorUser->id,
            'empresa' => 'Distribuidora El Sol S.A.S',
            'ruc' => '900123456-7',
            'telefono' => '+57 302 345 6789',
            'direccion' => 'Calle 100 #15-20, Bogotá',
            'latitud' => 4.7110,
            'longitud' => -74.0721,
            'ciudad' => 'Bogotá',
            'pais' => 'Colombia',
        ]);

        // 3. Crear Ubicaciones de Almacén Iniciales
        $ubicacionA = Ubicacion::create([
            'pasillo' => 'A',
            'estante' => '1',
            'casillero' => 'A',
            'descripcion' => 'Sector de materias secas y harinas',
        ]);

        $ubicacionB = Ubicacion::create([
            'pasillo' => 'B',
            'estante' => '2',
            'casillero' => 'B',
            'descripcion' => 'Sector de azúcares y endulzantes',
        ]);

        $ubicacionC = Ubicacion::create([
            'pasillo' => 'C',
            'estante' => '3',
            'casillero' => 'C',
            'descripcion' => 'Cámara de refrigeración de levaduras',
        ]);

        // 4. Crear Materias Primas Iniciales
        MaterialPrima::create([
            'codigo' => 'MAT-HAR-01',
            'nombre' => 'Harina de Trigo Industrial',
            'descripcion' => 'Sacos de harina de trigo refinada de 50kg para panificación',
            'cantidad' => 120,
            'unidad_medida' => 'bulto',
            'stock_minimo' => 20,
            'precio' => 45000.00,
            'lote' => 'LOTE-HAR-982',
            'fecha_caducidad' => now()->addMonths(6),
            'user_id' => $gerente->id,
            'ubicacion_id' => $ubicacionA->id,
        ]);

        MaterialPrima::create([
            'codigo' => 'MAT-AZU-02',
            'nombre' => 'Azúcar Blanca Refinada',
            'descripcion' => 'Bolsas de azúcar refinada de alta pureza de 25kg',
            'cantidad' => 80,
            'unidad_medida' => 'saco',
            'stock_minimo' => 15,
            'precio' => 32000.00,
            'lote' => 'LOTE-AZU-441',
            'fecha_caducidad' => now()->addMonths(12),
            'user_id' => $gerente->id,
            'ubicacion_id' => $ubicacionB->id,
        ]);

        MaterialPrima::create([
            'codigo' => 'MAT-LEV-03',
            'nombre' => 'Levadura Fresca Activa',
            'descripcion' => 'Bloques de levadura prensada para panadería, conservar refrigerada',
            'cantidad' => 15,
            'unidad_medida' => 'kg',
            'stock_minimo' => 5,
            'precio' => 18000.00,
            'lote' => 'LOTE-LEV-203',
            'fecha_caducidad' => now()->addDays(30),
            'user_id' => $gerente->id,
            'ubicacion_id' => $ubicacionC->id,
        ]);
    }
}
