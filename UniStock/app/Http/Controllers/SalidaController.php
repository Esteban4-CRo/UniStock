<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Producto;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with('producto')->latest()->get();
        return view('salidas.index', compact('salidas'));
    }

    public function create(Request $request)
    {
        $productos = Producto::all();
        $producto_id = $request->query('producto_id');
        return view('salidas.create', compact('productos', 'producto_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string'
        ]);

        // Verificar stock disponible
        $producto = Producto::find($request->producto_id);
        if ($producto->stock_actual < $request->cantidad) {
            return back()->withErrors(['cantidad' => 'Stock insuficiente. Stock actual: ' . $producto->stock_actual]);
        }

        // Crear la salida
        $salida = new Salida($request->all());

        if ($salida->validarCantidad()) {
            $salida->registrarMovimiento();

            // Actualizar el stock del producto usando el método del modelo (MateriaPrima)
            // En salida restamos, así que pasamos la cantidad negativa
            $producto->actualizarStock(-$request->cantidad);
        }

        return redirect()->route('salidas.index')
                         ->with('success', 'Salida registrada exitosamente.');
    }
}