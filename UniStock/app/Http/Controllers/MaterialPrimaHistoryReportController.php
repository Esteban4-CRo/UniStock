<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Salida;
use App\Models\MaterialPrima;
use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;

class MaterialPrimaHistoryReportController extends Controller
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

    public function generateReport(Request $request)
    {
        $materialPrimaId = $request->input('material_prima_id');

        // Buscar materia prima
        $material = MaterialPrima::findOrFail($materialPrimaId);

        // Obtener historial (entradas y salidas) con relaciones de usuarios y proveedores
        $entries = Entrada::with(['user', 'proveedor'])->where('material_prima_id', $materialPrimaId)->orderBy('created_at', 'desc')->get();
        $exits   = Salida::with(['user'])->where('material_prima_id', $materialPrimaId)->orderBy('created_at', 'desc')->get();

        // Generar PDF
        $pdf = Pdf::loadView('reports.material_history', [
            'material'      => $material,
            'entries'       => $entries,
            'exits'         => $exits,
            'generated_at'  => now()->format('d/m/Y H:i:s'),
            'generated_by'  => Auth::user()->name,
        ]);

        // Registrar registro en la tabla de reportes
        Reporte::create([
            'user_id'           => Auth::id(),
            'material_prima_id' => $material->id,
            'reporte_nombre'    => 'Historial de Materia Prima: ' . $material->nombre,
            'fecha_generacion'  => now(),
        ]);

        // Transmitir (stream) el PDF en el navegador para que se abra directamente
        $cleanName = \Illuminate\Support\Str::slug($material->nombre, '_');
        $filename = 'Reporte_Historial_' . $cleanName . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($filename, ['Attachment' => false]);
    }
}
