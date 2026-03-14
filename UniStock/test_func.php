

use App\Models\User;
use App\Models\Producto;
use App\Models\Entrada;
use App\Models\Salida;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "--- INICIANDO PRUEBAS DE ESCRITORIO ---" . PHP_EOL;

// 1. Probar Creación de Usuario Almacenista
$user = User::create([
    'name' => 'Prueba Almacenista',
    'email' => 'almacenista@prueba.com',
    'password' => Hash::make('password123'),
    'role' => 'almacenista'
]);
echo "Usuario almacenista creado: " . $user->name . PHP_EOL;

// 2. Probar Creación de Producto
$producto = Producto::create([
    'codigo' => 'TEST-001',
    'nombre' => 'Caja de Pruebas',
    'descripcion' => 'Una caja de cartón para pruebas',
    'stock_actual' => 50,
    'precio' => 10.50,
    'user_id' => $user->id,
    'estado' => 'activo',
    'unidad_medida' => 'unidades',
    'stock_minimo' => 10
]);
echo "Producto creado: " . $producto->nombre . " (Stock: " . $producto->stock_actual . ")" . PHP_EOL;

// 3. Probar Entrada
$entrada = new Entrada([
    'producto_id' => $producto->id,
    'cantidad' => 20,
    'motivo' => 'Reabastecimiento de prueba'
]);
if ($entrada->validarCantidad()) {
    $entrada->registrarMovimiento();
    $producto->actualizarStock($entrada->cantidad);
    echo "Entrada registrada de 20. Nuevo stock: " . $producto->stock_actual . PHP_EOL;
} else {
    echo "Falla en entrada" . PHP_EOL;
}

// 4. Probar Salida
$salida = new Salida([
    'producto_id' => $producto->id,
    'cantidad' => 15,
    'motivo' => 'Venta de prueba'
]);
if ($salida->validarCantidad() && $producto->stock_actual >= $salida->cantidad) {
    $salida->registrarMovimiento();
    $producto->actualizarStock(-$salida->cantidad);
    echo "Salida registrada de 15. Nuevo stock: " . $producto->stock_actual . PHP_EOL;
} else {
    echo "Falla en salida" . PHP_EOL;
}

echo "--- PRUEBAS COMPLETADAS OK ---" . PHP_EOL;
