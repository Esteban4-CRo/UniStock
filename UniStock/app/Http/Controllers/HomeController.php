<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialPrima;
use App\Models\Proveedor;
use App\Models\Ubicacion;
use App\Models\Entrada;
use App\Models\Salida;

class HomeController extends Controller
{
    public function index()
    {
        $totalMaterias = MaterialPrima::where('activo', true)->count();
        $totalProveedores = Proveedor::where('activo', true)->count();
        $totalUbicaciones = Ubicacion::where('activo', true)->count();
        $totalEntradas = Entrada::count();
        $totalSalidas = Salida::count();
        
        $materiales = MaterialPrima::with('ubicacion')->where('activo', true)->latest()->take(5)->get();
        $entradas = Entrada::with(['materialPrima', 'proveedor'])->latest()->take(5)->get();
        $salidas = Salida::with('materialPrima')->latest()->take(5)->get();

        return view('home', compact(
            'totalMaterias', 
            'totalProveedores',
            'totalUbicaciones',
            'totalEntradas', 
            'totalSalidas',
            'materiales',
            'entradas',
            'salidas'
        ));
    }
}