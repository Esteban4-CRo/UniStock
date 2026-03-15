<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Salida;
use App\Models\Producto;
use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;

class ProductHistoryReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $productId = $request->input('product_id');

        // Fetch product details
        $product = Producto::findOrFail($productId);

        // Fetch product history (entries and exits)
        $entries = Entrada::where('producto_id', $productId)->orderBy('created_at', 'desc')->get();
        $exits   = Salida::where('producto_id', $productId)->orderBy('created_at', 'desc')->get();

        // Generate PDF
        $pdf = Pdf::loadView('reports.product_history', [
            'product'       => $product,
            'entries'       => $entries,
            'exits'         => $exits,
            'generated_at'  => now()->format('d/m/Y H:i:s'),
            'generated_by'  => Auth::user()->name,
        ]);

        // Save report log in DB (who, what product, when)
        Reporte::create([
            'user_id'          => Auth::id(),
            'producto_id'      => $product->id,
            'reporte_nombre'   => 'Historial de Producto: ' . $product->nombre,
            'fecha_generacion' => now(),
        ]);

        // Return PDF download
        $cleanName = \Illuminate\Support\Str::slug($product->nombre, '_');
        $filename = 'Reporte_' . $cleanName . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
}