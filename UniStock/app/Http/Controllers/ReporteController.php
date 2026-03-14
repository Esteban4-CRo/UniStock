<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        // Get paginated recent reports with user information
        $reportes = Reporte::with(['user', 'producto'])->orderBy('fecha_generacion', 'desc')->paginate(15);
        $productos = \App\Models\Producto::all();
        
        return view('reportes.index', compact('reportes', 'productos'));
    }
}
