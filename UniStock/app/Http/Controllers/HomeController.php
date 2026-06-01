<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialPrima;
use App\Models\Proveedor;
use App\Models\Ubicacion;
use App\Models\Entrada;
use App\Models\Salida;

class HomeController extends Controller
{
    public function index()
    {
        $counts = Cache::remember('home_counts_v1', 300, function () {
            $row = DB::selectOne("
                SELECT
                    (SELECT COUNT(*) FROM material_primas WHERE activo = true) AS total_materias,
                    (SELECT COUNT(*) FROM proveedores WHERE activo = true) AS total_proveedores,
                    (SELECT COUNT(*) FROM ubicaciones WHERE activo = true) AS total_ubicaciones,
                    (SELECT COUNT(*) FROM entradas) AS total_entradas,
                    (SELECT COUNT(*) FROM salidas) AS total_salidas
            ");
            return [
                'totalMaterias' => (int) ($row->total_materias ?? 0),
                'totalProveedores' => (int) ($row->total_proveedores ?? 0),
                'totalUbicaciones' => (int) ($row->total_ubicaciones ?? 0),
                'totalEntradas' => (int) ($row->total_entradas ?? 0),
                'totalSalidas' => (int) ($row->total_salidas ?? 0),
            ];
        });

        $materiales = Cache::remember('home_materiales_v1', 120, fn () =>
            MaterialPrima::with('ubicacion')->where('activo', true)->latest()->take(5)->get()
        );
        $entradas = Cache::remember('home_entradas_v1', 120, fn () =>
            Entrada::with(['materialPrima', 'proveedor'])->latest()->take(5)->get()
        );
        $salidas = Cache::remember('home_salidas_v1', 120, fn () =>
            Salida::with('materialPrima')->latest()->take(5)->get()
        );

        return view('home', array_merge($counts, compact(
            'materiales',
            'entradas',
            'salidas'
        )));
    }
}
