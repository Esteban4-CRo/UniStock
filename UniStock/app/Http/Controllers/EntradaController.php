<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\Producto;
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    public function index()
    {
        $entradas = Entrada::with('producto')->latest()->get();
        return view('entradas.index', compact('entradas'));
    }

    public function create(Request $request)
    {
        $productos = Producto::all();
        $producto_id = $request->query('producto_id');
        return view('entradas.create', compact('productos', 'producto_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string'
        ]);

        // Crear la entrada
        $entrada = new Entrada($request->all());
        
        if ($entrada->validarCantidad()) {
            $entrada->registrarMovimiento();

            // Actualizar el stock del producto usando el método del modelo (MateriaPrima)
            $producto = Producto::find($request->producto_id);
            $producto->actualizarStock($request->cantidad);
        }

        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada registrada exitosamente.');
    }
}