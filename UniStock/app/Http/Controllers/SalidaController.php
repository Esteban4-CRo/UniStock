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
        Salida::create($request->all());

        // Actualizar el stock del producto
        $producto->stock_actual -= $request->cantidad;
        $producto->save();

        return redirect()->route('salidas.index')
                         ->with('success', 'Salida registrada exitosamente.');
    }
}