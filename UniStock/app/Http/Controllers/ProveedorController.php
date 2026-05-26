<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa' => 'required',
            'ruc' => 'required|unique:proveedores',
            'telefono' => 'required',
            'direccion' => 'required',
        ]);

        $proveedor = new Proveedor($request->all());
        
        // Uso del método del diagrama
        if ($proveedor->validarProveedor()) {
            $proveedor->registrarProveedor();
            return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente.');
        }

        return back()->withErrors(['msg' => 'Proveedor inválido']);
    }

    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'empresa' => 'required',
            'ruc' => 'required|unique:proveedores,ruc,' . $proveedor->id,
            'telefono' => 'required',
            'direccion' => 'required',
        ]);

        $proveedor->fill($request->all());
        $proveedor->save();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->update(['activo' => false]);
        return redirect()->route('proveedores.index')->with('success', 'Proveedor inhabilitado exitosamente.');
    }

    public function validar(Request $request, Proveedor $proveedor)
    {
        if (!auth()->user()->isSuperUsuario() && !auth()->user()->isGerente()) {
            abort(403, 'No tienes permiso para validar proveedores.');
        }

        $request->validate([
            'estado_validacion' => 'required|in:validado,rechazado,pendiente'
        ]);

        $proveedor->estado_validacion = $request->estado_validacion;
        $proveedor->save();

        return back()->with('success', 'Estado de validación del proveedor actualizado.');
    }
}
