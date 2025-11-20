<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('user')->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos',
            'nombre' => 'required|string|max:255|unique:productos',
            'descripcion' => 'nullable|string',
            'stock_actual' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0'
        ]);

        $data = $request->only(['codigo', 'nombre', 'descripcion', 'stock_actual', 'precio']);
        $data['user_id'] = Auth::id();

        Producto::create($data);

        return redirect()->route('productos.index')
                         ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        if ($producto->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        if ($producto->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'descripcion' => 'nullable|string',
            'stock_actual' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0'
        ]);

        // No permitir editar el código
        $producto->update($request->only(['nombre', 'descripcion', 'stock_actual', 'precio']));

        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $producto->delete();

        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado exitosamente.');
    }

    // ... otros métodos (show, edit, update, destroy)
}