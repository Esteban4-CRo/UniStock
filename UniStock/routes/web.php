<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MaterialPrimaController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialPrimaHistoryReportController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\AlertaController;

Route::get('/', function () {
    return view('welcome_index');
})->name('welcome');

Auth::routes();

// AI Assistant Route
Route::post('/api/ai-chat', [\App\Http\Controllers\AiAssistantController::class, 'chat']);

// Google Socialite Routes
Route::get('/auth/google', [\App\Http\Controllers\Auth\LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\LoginController::class, 'handleGoogleCallback'])->name('google.callback');
// Removed duplicate Google callback route

// Añadir el middleware 'check-active' a todas las rutas protegidas
Route::middleware(['auth', 'prevent-back', 'check-active'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas para perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');

    // Rutas para usuarios (Administrador)
    Route::resource('usuarios', UserController::class)->parameters([
        'usuarios' => 'usuario'
    ]);

    // Rutas para proveedores
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    Route::put('/proveedores/{proveedor}/validar', [ProveedorController::class, 'validar'])->name('proveedores.validar');

    // Rutas para ubicaciones físicas de almacén
    Route::resource('ubicaciones', UbicacionController::class)->parameters([
        'ubicaciones' => 'ubicacion'
    ]);

    // Rutas para materias primas
    Route::resource('materias-primas', MaterialPrimaController::class)->parameters([
        'materias-primas' => 'materiales_prima'
    ]);

    // Rutas para entradas de inventario
    Route::get('/entradas', [EntradaController::class, 'index'])->name('entradas.index');
    Route::get('/entradas/create', [EntradaController::class, 'create'])->name('entradas.create');
    Route::post('/entradas', [EntradaController::class, 'store'])->name('entradas.store');
    Route::get('/entradas/{entrada}/edit', [EntradaController::class, 'edit'])->name('entradas.edit');
    Route::put('/entradas/{entrada}', [EntradaController::class, 'update'])->name('entradas.update');
    Route::post('/entradas/{entrada}/anular', [EntradaController::class, 'anular'])->name('entradas.anular');

    // Rutas para salidas de inventario
    Route::get('/salidas', [SalidaController::class, 'index'])->name('salidas.index');
    Route::get('/salidas/create', [SalidaController::class, 'create'])->name('salidas.create');
    Route::post('/salidas', [SalidaController::class, 'store'])->name('salidas.store');
    Route::get('/salidas/{salida}/edit', [SalidaController::class, 'edit'])->name('salidas.edit');
    Route::put('/salidas/{salida}', [SalidaController::class, 'update'])->name('salidas.update');
    Route::post('/salidas/{salida}/anular', [SalidaController::class, 'anular'])->name('salidas.anular');

    // Registrar entrada desde vista de materia prima
    Route::post('/materias-primas/{materiales_prima}/entrada', [EntradaController::class, 'store'])
        ->name('materias-primas.entrada.store');

    // Registrar salida desde vista de materia prima
    Route::post('/materias-primas/{materiales_prima}/salida', [SalidaController::class, 'store'])
        ->name('materias-primas.salida.store');

    // Generar reporte de historial de materia prima (PDF)
    Route::post('/reports/material-history', [MaterialPrimaHistoryReportController::class, 'generateReport'])
        ->name('reports.material-history');

    // Listado de reportes generados
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Descarga manual de base de datos sqlite (Copia de seguridad)
    Route::get('/backup/download', [\App\Http\Controllers\BackupController::class, 'download'])
        ->name('backup.download');

    // Exportación de Reportes a Excel (.csv compatible con Microsoft Excel)
    Route::get('/excel/inventario', [\App\Http\Controllers\ExcelExportController::class, 'exportInventario'])
        ->name('excel.inventario');
    Route::get('/excel/inventario/preview', [\App\Http\Controllers\ExcelExportController::class, 'previewInventario'])
        ->name('excel.inventario.preview');
    Route::get('/excel/movimientos', [\App\Http\Controllers\ExcelExportController::class, 'exportMovimientos'])
        ->name('excel.movimientos');
    Route::get('/excel/movimientos/preview', [\App\Http\Controllers\ExcelExportController::class, 'previewMovimientos'])
        ->name('excel.movimientos.preview');

    // Alertas
    Route::post('/alertas/marcar-todas', [AlertaController::class, 'marcarTodasLeidas'])->name('alertas.marcarTodas');
    Route::post('/alertas/{id}/marcar', [AlertaController::class, 'marcarLeida'])->name('alertas.marcar');