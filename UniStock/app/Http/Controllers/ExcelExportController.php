<?php

namespace App\Http\Controllers;

use App\Models\MaterialPrima;
use App\Models\Entrada;
use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExcelExportController extends Controller
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

    /**
     * Exporta el Inventario General actual a un archivo Excel (.csv).
     */
    public function exportInventario()
    {
        $materiales = MaterialPrima::with(['ubicacion', 'proveedores'])->where('activo', true)->get();

        $filename = 'Inventario_UniStock_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $file = fopen('php://temp', 'r+');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

        // Encabezados
        fputcsv($file, [
            'Código',
            'Nombre',
            'Descripción',
            'Stock Actual',
            'Unidad',
            'Stock Mínimo',
            'Lote Actual',
            'Ubicación',
            'Último Proveedor'
        ], ';');

        foreach ($materiales as $m) {
            fputcsv($file, [
                $m->codigo,
                $m->nombre,
                $m->descripcion ?? 'N/A',
                $m->cantidad,
                $m->unidad_medida,
                $m->stock_minimo,
                $m->lote ?? 'N/A',
                $m->ubicacion ? $m->ubicacion->pasillo . ' - Estante ' . $m->ubicacion->estante . ' - Casillero ' . $m->ubicacion->casillero : 'Sin Asignar',
                $m->proveedores->first() ? $m->proveedores->first()->empresa : 'Sin Asignar'
            ], ';');
        }

        rewind($file);
        $csvContent = stream_get_contents($file);
        fclose($file);

        return response($csvContent, 200, $headers);
    }

    /**
     * Exporta todo el Historial de Movimientos (Entradas y Salidas) a un archivo Excel (.csv).
     */
    public function exportMovimientos()
    {
        $entradas = Entrada::with(['materialPrima', 'user', 'proveedor'])->latest()->get();
        $salidas = Salida::with(['materialPrima', 'user'])->latest()->get();

        $movimientos = [];

        foreach ($entradas as $e) {
            $movimientos[] = [
                'tipo' => 'ENTRADA',
                'fecha' => $e->created_at->format('d/m/Y H:i'),
                'material' => $e->materialPrima ? $e->materialPrima->nombre : 'Material Eliminado',
                'codigo' => $e->materialPrima ? $e->materialPrima->codigo : 'N/A',
                'cantidad' => '+' . $e->cantidad,
                'unidad' => $e->materialPrima ? $e->materialPrima->unidad_medida : '',
                'lote' => $e->lote ?? 'N/A',
                'referencia_destino' => $e->proveedor ? 'Proveedor: ' . $e->proveedor->empresa : 'N/A',
                'usuario' => $e->user ? $e->user->name : 'N/A',
                'motivo' => $e->motivo ?? 'N/A',
                'anulado' => $e->anulado ? 'SÍ' : 'NO'
            ];
        }

        foreach ($salidas as $s) {
            $movimientos[] = [
                'tipo' => 'SALIDA',
                'fecha' => $s->created_at->format('d/m/Y H:i'),
                'material' => $s->materialPrima ? $s->materialPrima->nombre : 'Material Eliminado',
                'codigo' => $s->materialPrima ? $s->materialPrima->codigo : 'N/A',
                'cantidad' => '-' . $s->cantidad,
                'unidad' => $s->materialPrima ? $s->materialPrima->unidad_medida : '',
                'lote' => $s->lote ?? 'N/A',
                'referencia_destino' => 'Destino: ' . $s->destino,
                'usuario' => $s->user ? $s->user->name : 'N/A',
                'motivo' => $s->motivo ?? 'N/A',
                'anulado' => $s->anulado ? 'SÍ' : 'NO'
            ];
        }

        // Ordenar movimientos por fecha descendente
        usort($movimientos, function($a, $b) {
            return strtotime(str_replace('/', '-', $b['fecha'])) - strtotime(str_replace('/', '-', $a['fecha']));
        });

        $filename = 'Historial_Movimientos_UniStock_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $file = fopen('php://temp', 'r+');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

        // Encabezados
        fputcsv($file, [
            'Tipo de Movimiento',
            'Fecha y Hora',
            'Materia Prima',
            'Código',
            'Cantidad',
            'Unidad',
            'Lote',
            'Detalle (Origen/Destino)',
            'Registrado Por',
            'Motivo',
            'Anulado'
        ], ';');

        foreach ($movimientos as $m) {
            fputcsv($file, [
                $m['tipo'],
                $m['fecha'],
                $m['material'],
                $m['codigo'],
                $m['cantidad'],
                $m['unidad'],
                $m['lote'],
                $m['referencia_destino'],
                $m['usuario'],
                $m['motivo'],
                $m['anulado']
            ], ';');
        }

        rewind($file);
        $csvContent = stream_get_contents($file);
        fclose($file);

        return response($csvContent, 200, $headers);
    }

    /**
     * Muestra una vista previa del Inventario General.
     */
    public function previewInventario()
    {
        $materiales = MaterialPrima::with(['ubicacion', 'proveedores'])->where('activo', true)->get();
        
        return view('reportes.excel_preview', [
            'titulo' => 'Vista Previa - Inventario Actual',
            'tipo' => 'inventario',
            'headers' => ['Código', 'Nombre', 'Descripción', 'Stock Actual', 'Unidad', 'Stock Mínimo', 'Lote', 'Ubicación', 'Último Proveedor'],
            'datos' => $materiales,
            'download_route' => route('excel.inventario')
        ]);
    }

    /**
     * Muestra una vista previa del Historial de Movimientos.
     */
    public function previewMovimientos()
    {
        $entradas = Entrada::with(['materialPrima', 'user', 'proveedor'])->latest()->get();
        $salidas = Salida::with(['materialPrima', 'user'])->latest()->get();

        $movimientos = [];

        foreach ($entradas as $e) {
            $movimientos[] = [
                'tipo' => 'ENTRADA',
                'fecha' => $e->created_at->format('d/m/Y H:i'),
                'material' => $e->materialPrima ? $e->materialPrima->nombre : 'Material Eliminado',
                'codigo' => $e->materialPrima ? $e->materialPrima->codigo : 'N/A',
                'cantidad' => '+' . $e->cantidad,
                'unidad' => $e->materialPrima ? $e->materialPrima->unidad_medida : '',
                'lote' => $e->lote ?? 'N/A',
                'referencia_destino' => $e->proveedor ? 'Proveedor: ' . $e->proveedor->empresa : 'N/A',
                'usuario' => $e->user ? $e->user->name : 'N/A',
                'motivo' => $e->motivo ?? 'N/A',
                'anulado' => $e->anulado ? 'SÍ' : 'NO'
            ];
        }

        foreach ($salidas as $s) {
            $movimientos[] = [
                'tipo' => 'SALIDA',
                'fecha' => $s->created_at->format('d/m/Y H:i'),
                'material' => $s->materialPrima ? $s->materialPrima->nombre : 'Material Eliminado',
                'codigo' => $s->materialPrima ? $s->materialPrima->codigo : 'N/A',
                'cantidad' => '-' . $s->cantidad,
                'unidad' => $s->materialPrima ? $s->materialPrima->unidad_medida : '',
                'lote' => $s->lote ?? 'N/A',
                'referencia_destino' => 'Destino: ' . $s->destino,
                'usuario' => $s->user ? $s->user->name : 'N/A',
                'motivo' => $s->motivo ?? 'N/A',
                'anulado' => $s->anulado ? 'SÍ' : 'NO'
            ];
        }

        usort($movimientos, function($a, $b) {
            return strtotime(str_replace('/', '-', $b['fecha'])) - strtotime(str_replace('/', '-', $a['fecha']));
        });

        return view('reportes.excel_preview', [
            'titulo' => 'Vista Previa - Historial de Movimientos',
            'tipo' => 'movimientos',
            'headers' => ['Tipo', 'Fecha y Hora', 'Materia Prima', 'Código', 'Cantidad', 'Unidad', 'Lote', 'Detalle', 'Registrado Por', 'Motivo', 'Anulado'],
            'datos' => $movimientos,
            'download_route' => route('excel.movimientos')
        ]);
    }
}
