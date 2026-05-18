<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    /**
     * Muestra el listado de ubicaciones físicas de almacén.
     */
    public function index()
    {
        $ubicaciones = Ubicacion::withCount(['materialesPrimas' => function($q) {
            $q->where('activo', true);
        }])->where('activo', true)->paginate(10);
        return view('ubicaciones.index', compact('ubicaciones'));
    }

    /**
     * Muestra el formulario para crear una nueva ubicación.
     */
    public function create()
    {
        return view('ubicaciones.create');
    }

    /**
     * Almacena una nueva ubicación física con validación de combinación única.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pasillo' => 'required|string|max:50',
            'estante' => 'required|string|max:50',
            'casillero' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'pasillo.required' => 'El campo pasillo es obligatorio.',
            'estante.required' => 'El campo estante es obligatorio.',
            'casillero.required' => 'El campo casillero es obligatorio.',
        ]);

        // Validar combinación única en tiempo de ejecución
        $duplicado = Ubicacion::where('pasillo', $request->pasillo)
                              ->where('estante', $request->estante)
                              ->where('casillero', $request->casillero)
                              ->exists();

        if ($duplicado) {
            return back()
                ->withErrors(['pasillo' => 'La combinación física (Pasillo, Estante, Casillero) ya se encuentra registrada en el almacén.'])
                ->withInput();
        }

        Ubicacion::create($request->all());

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación física registrada correctamente.');
    }

    /**
     * Muestra la vista de edición de una ubicación.
     */
    public function edit(Ubicacion $ubicacion)
    {
        return view('ubicaciones.edit', compact('ubicacion'));
    }

    /**
     * Actualiza una ubicación física validando duplicidad de coordenadas de pasillo/estante/casillero.
     */
    public function update(Request $request, Ubicacion $ubicacion)
    {
        $request->validate([
            'pasillo' => 'required|string|max:50',
            'estante' => 'required|string|max:50',
            'casillero' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Validar combinación única excluyendo la actual
        $duplicado = Ubicacion::where('pasillo', $request->pasillo)
                              ->where('estante', $request->estante)
                              ->where('casillero', $request->casillero)
                              ->where('id', '!=', $ubicacion->id)
                              ->exists();

        if ($duplicado) {
            return back()
                ->withErrors(['pasillo' => 'La combinación física (Pasillo, Estante, Casillero) ya está asignada a otra ubicación.'])
                ->withInput();
        }

        $ubicacion->update($request->all());

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación física actualizada correctamente.');
    }

    /**
     * Elimina una ubicación física, impidiendo la operación si posee materias primas activas vinculadas.
     */
    public function destroy(Ubicacion $ubicacion)
    {
        if ($ubicacion->materialesPrimas()->where('activo', true)->count() > 0) {
            return back()->with('error', 'No se puede inhabilitar la ubicación porque tiene materias primas activas almacenadas en ella. Reasigne los materiales primero.');
        }

        $ubicacion->update(['activo' => false]);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación física inhabilitada con éxito.');
    }
}
