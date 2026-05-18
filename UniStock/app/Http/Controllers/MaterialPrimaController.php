<?php

namespace App\Http\Controllers;

use App\Models\MaterialPrima;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialPrimaController extends Controller
{
    /**
     * Muestra el listado de todas las materias primas registradas.
     */
    public function index()
    {
        $materiales = MaterialPrima::with(['user', 'ubicacion'])->where('activo', true)->get();
        return view('materias-primas.index', compact('materiales'));
    }

    /**
     * Muestra el formulario para registrar una nueva materia prima.
     */
    public function create()
    {
        $ubicaciones = Ubicacion::where('activo', true)->get();
        return view('materias-primas.create', compact('ubicaciones'));
    }

    /**
     * Almacena una nueva materia prima en el inventario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:material_primas,codigo',
            'nombre' => 'required|string|max:255|unique:material_primas,nombre',
            'descripcion' => 'nullable|string',
            'cantidad' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date|after_or_equal:today',
            'lote' => 'nullable|string|max:100',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ], [
            'codigo.required' => 'El código de materia prima es obligatorio.',
            'codigo.unique' => 'Este código ya está registrado.',
            'nombre.required' => 'El nombre de la materia prima es obligatorio.',
            'nombre.unique' => 'Este nombre de materia prima ya existe.',
            'cantidad.required' => 'La cantidad inicial es obligatoria.',
            'precio.required' => 'El precio unitario es obligatorio.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'stock_minimo.required' => 'El nivel mínimo de stock es obligatorio.',
            'ubicacion_id.required' => 'Debe asignar una ubicación física en el almacén.',
            'ubicacion_id.exists' => 'La ubicación seleccionada es inválida.',
            'fecha_caducidad.after_or_equal' => 'La fecha de caducidad no puede ser del pasado.',
        ]);

        $data = $request->only([
            'codigo', 'nombre', 'descripcion', 'cantidad', 'precio',
            'unidad_medida', 'stock_minimo', 'fecha_caducidad', 'lote', 'ubicacion_id'
        ]);
        $data['user_id'] = Auth::id();

        MaterialPrima::create($data);

        return redirect()->route('materias-primas.index')
                         ->with('success', 'Materia prima registrada exitosamente.');
    }

    /**
     * Muestra los detalles de una materia prima y su historial de movimientos.
     */
    public function show(MaterialPrima $materiales_prima)
    {
        // Cargar entradas y salidas ordenadas por fecha
        $materiales_prima->load(['user', 'ubicacion', 'entradas.user', 'entradas.proveedor', 'salidas.user']);
        return view('materias-primas.show', compact('materiales_prima'));
    }

    /**
     * Muestra el formulario para editar una materia prima.
     */
    public function edit(MaterialPrima $materiales_prima)
    {
        // Solo el Gerente o Administrador creadores/autorizados pueden editar
        if ($materiales_prima->user_id !== Auth::id() && !Auth::user()->isSuperUsuario()) {
            abort(403, 'No autorizado');
        }

        $ubicaciones = Ubicacion::where('activo', true)->get();
        return view('materias-primas.edit', compact('materiales_prima', 'ubicaciones'));
    }

    /**
     * Actualiza los datos de la materia prima.
     */
    public function update(Request $request, MaterialPrima $materiales_prima)
    {
        if ($materiales_prima->user_id !== Auth::id() && !Auth::user()->isSuperUsuario()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:material_primas,nombre,' . $materiales_prima->id,
            'descripcion' => 'nullable|string',
            'cantidad' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
            'lote' => 'nullable|string|max:100',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
        ], [
            'nombre.required' => 'El nombre de la materia prima es obligatorio.',
            'nombre.unique' => 'Este nombre de materia prima ya existe.',
            'cantidad.required' => 'El stock actual es obligatorio.',
            'precio.required' => 'El precio es obligatorio.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'ubicacion_id.required' => 'Debe asignar una ubicación física en el almacén.',
        ]);

        // No se permite modificar el código único una vez creado
        $materiales_prima->update($request->only([
            'nombre', 'descripcion', 'cantidad', 'precio',
            'unidad_medida', 'stock_minimo', 'fecha_caducidad', 'lote', 'ubicacion_id'
        ]));

        return redirect()->route('materias-primas.index')
                         ->with('success', 'Materia prima actualizada exitosamente.');
    }

    /**
     * Elimina una materia prima del sistema.
     */
    public function destroy(MaterialPrima $materiales_prima)
    {
        if ($materiales_prima->user_id !== Auth::id() && !Auth::user()->isSuperUsuario()) {
            abort(403, 'No autorizado');
        }

        $materiales_prima->update(['activo' => false]);

        return redirect()->route('materias-primas.index')
                         ->with('success', 'Materia prima inhabilitada exitosamente.');
    }
}
