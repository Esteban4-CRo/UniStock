<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\MaterialPrima;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntradaController extends Controller
{
    /**
     * Listado de todas las entradas registradas con sus relaciones correspondientes.
     */
    public function index()
    {
        $entradas = Entrada::with(['materialPrima', 'user', 'proveedor'])->latest()->get();
        return view('entradas.index', compact('entradas'));
    }

    /**
     * Formulario de creación de entradas.
     */
    public function create(Request $request)
    {
        $materiales = MaterialPrima::where('activo', true)->get();
        $proveedores = Proveedor::where('activo', true)->get();
        $material_prima_id = $request->query('material_prima_id');
        
        return view('entradas.create', compact('materiales', 'proveedores', 'material_prima_id'));
    }

    /**
     * Registra una nueva entrada de materia prima en el sistema.
     */
    public function store(Request $request)
    {
        $request->validate([
            'material_prima_id' => 'required|exists:material_primas,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'cantidad' => 'required|integer|min:1',
            'lote' => 'nullable|string|max:100',
            'fecha_caducidad' => 'nullable|date',
            'motivo' => 'nullable|string'
        ], [
            'material_prima_id.required' => 'La materia prima es obligatoria.',
            'proveedor_id.required' => 'El proveedor es obligatorio (las materias primas deben tener un origen).',
            'cantidad.required' => 'La cantidad ingresada es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
        ]);

        // Crear la transacción de entrada
        $entrada = new Entrada($request->all());
        $entrada->user_id = Auth::id();

        if ($entrada->validarCantidad()) {
            $entrada->registrarMovimiento();

            // Obtener y actualizar la materia prima asociada
            $material = MaterialPrima::find($request->material_prima_id);
            $material->actualizarStock($request->cantidad);

            // Opción B Simple: Actualizar lote y caducidad global en la ficha del material con la última entrada
            if ($request->filled('lote')) {
                $material->lote = $request->lote;
            }
            if ($request->filled('fecha_caducidad')) {
                $material->fecha_caducidad = $request->fecha_caducidad;
            }
            $material->save();
        }

        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada de inventario registrada con éxito.');
    }

    /**
     * Anula una entrada de inventario y realiza la operación inversa en el stock.
     */
    public function anular(Entrada $entrada)
    {
        if (!Auth::user()->isSuperUsuario() && !Auth::user()->isGerente()) {
            abort(403, 'No tienes permisos para anular movimientos.');
        }

        if ($entrada->anulado) {
            return back()->with('error', 'Esta entrada ya ha sido anulada anteriormente.');
        }

        $material = $entrada->materialPrima;

        // Validar si al restar esta entrada el stock quedaría negativo
        if ($material->cantidad < $entrada->cantidad) {
            return back()->with('error', 'No se puede anular esta entrada porque el stock actual (' . $material->cantidad . ' ' . $material->unidad_medida . ') quedaría negativo.');
        }

        // Anular transacción
        $entrada->anulado = true;
        $entrada->save();

        // Operación Inversa: Restar la cantidad ingresada del stock del material
        $material->actualizarStock(-$entrada->cantidad);

        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada de inventario anulada con éxito y stock revertido.');
    }

    /**
     * Muestra el formulario para editar una entrada.
     */
    public function edit(Entrada $entrada)
    {
        if ($entrada->anulado) {
            return redirect()->route('entradas.index')->with('error', 'No se puede editar una entrada anulada.');
        }

        $puedeEditar = false;
        if (Auth::user()->isSuperUsuario() || Auth::user()->isGerente()) {
            $puedeEditar = true;
        } elseif (Auth::user()->isAlmacenista() && $entrada->user_id === Auth::id() && $entrada->created_at->diffInMinutes(now()) <= 5) {
            $puedeEditar = true;
        }

        if (!$puedeEditar) {
            return redirect()->route('entradas.index')->with('error', 'No tienes permisos para editar este movimiento (solo se permite a Almacenistas editar sus propios registros dentro de los primeros 5 minutos).');
        }

        $materiales = MaterialPrima::where('activo', true)->get();
        $proveedores = Proveedor::where('activo', true)->get();

        return view('entradas.edit', compact('entrada', 'materiales', 'proveedores'));
    }

    /**
     * Actualiza la entrada de inventario.
     */
    public function update(Request $request, Entrada $entrada)
    {
        if ($entrada->anulado) {
            return redirect()->route('entradas.index')->with('error', 'No se puede editar una entrada anulada.');
        }

        $puedeEditar = false;
        if (Auth::user()->isSuperUsuario() || Auth::user()->isGerente()) {
            $puedeEditar = true;
        } elseif (Auth::user()->isAlmacenista() && $entrada->user_id === Auth::id() && $entrada->created_at->diffInMinutes(now()) <= 5) {
            $puedeEditar = true;
        }

        if (!$puedeEditar) {
            return redirect()->route('entradas.index')->with('error', 'No tienes permisos para editar este movimiento.');
        }

        $request->validate([
            'material_prima_id' => 'required|exists:material_primas,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'cantidad' => 'required|integer|min:1',
            'lote' => 'nullable|string|max:100',
            'fecha_caducidad' => 'nullable|date',
            'motivo' => 'nullable|string'
        ], [
            'material_prima_id.required' => 'La materia prima es obligatoria.',
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'cantidad.required' => 'La cantidad ingresada es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
        ]);

        $oldMaterial = MaterialPrima::find($entrada->material_prima_id);
        $newMaterial = MaterialPrima::find($request->material_prima_id);

        $oldQty = $entrada->cantidad;
        $newQty = $request->cantidad;

        // Si cambia de material prima
        if ($oldMaterial->id !== $newMaterial->id) {
            // Validar que el stock del material anterior no quede negativo al restarle la cantidad anterior
            if ($oldMaterial->cantidad < $oldQty) {
                return back()->withErrors(['material_prima_id' => 'No se puede cambiar el material porque el stock del material anterior quedaría negativo.'])->withInput();
            }

            // Descontar del material anterior
            $oldMaterial->actualizarStock(-$oldQty);
            // Sumar al nuevo material
            $newMaterial->actualizarStock($newQty);
        } else {
            // Es el mismo material, calcular la diferencia
            $diff = $newQty - $oldQty;
            if ($diff < 0 && $oldMaterial->cantidad < abs($diff)) {
                return back()->withErrors(['cantidad' => 'No se puede reducir la cantidad porque el stock actual quedaría negativo.'])->withInput();
            }
            $oldMaterial->actualizarStock($diff);
        }

        // Actualizar datos de la entrada
        $entrada->fill($request->all());
        $entrada->save();

        // Actualizar lote y caducidad en el nuevo material
        if ($request->filled('lote')) {
            $newMaterial->lote = $request->lote;
        }
        if ($request->filled('fecha_caducidad')) {
            $newMaterial->fecha_caducidad = $request->fecha_caducidad;
        }
        $newMaterial->save();

        return redirect()->route('entradas.index')->with('success', 'Entrada de inventario actualizada con éxito.');
    }
}