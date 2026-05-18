<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\MaterialPrima;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperUsuario() && !auth()->user()->isGerente()) {
                abort(403, 'No tienes permiso para acceder a esta sección.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Get paginated recent reports with user information
        $reportes = Reporte::with(['user', 'materialPrima'])->orderBy('fecha_generacion', 'desc')->paginate(15);
        $materiales = MaterialPrima::all();
        
        return view('reportes.index', compact('reportes', 'materiales'));
    }
}
