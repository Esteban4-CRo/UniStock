<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\MaterialPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    /**
     * Muestra el listado de todas las salidas registradas.
     */
    public function index()
    {
        $salidas = Salida::with(['materialPrima', 'user'])->latest()->get();
        return view('salidas.index', compact('salidas'));
    }

    /**
     * Muestra el formulario para registrar una salida.
     */
    public function create(Request $request)
    {
        $materiales = MaterialPrima::where('activo', true)->get();
        $material_prima_id = $request->query('material_prima_id');
        return view('salidas.create', compact('materiales', 'material_prima_id'));
    }

    /**
     * Almacena una nueva salida de inventario y decrementa el stock de la materia prima.
     */
    public function store(Request $request)
    {
        $request->validate([
            'material_prima_id' => 'required|exists:material_primas,id',
            'cantidad' => 'required|integer|min:1',
            'lote' => 'nullable|string|max:100',
            'destino' => 'required|string|max:255',
            'motivo' => 'nullable|string'
        ], [
            'material_prima_id.required' => 'La materia prima es obligatoria.',
            'cantidad.required' => 'La cantidad a retirar es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
            'destino.required' => 'El destino del material es obligatorio.',
        ]);

        // Verificar stock disponible en la materia prima
        $material = MaterialPrima::find($request->material_prima_id);
        if ($material->cantidad < $request->cantidad) {
            return back()
                ->withErrors(['cantidad' => 'Stock insuficiente para esta salida. Cantidad disponible: ' . $material->cantidad . ' ' . $material->unidad_medida])
                ->withInput();
        }

        // Crear la salida
        $salida = new Salida($request->all());
        $salida->user_id = Auth::id();

        if ($salida->validarCantidad()) {
            $salida->registrarMovimiento();

            // Decrementar stock actual
            $material->actualizarStock(-$request->cantidad);

            // Verificar si el nuevo stock está en o por debajo del mínimo
            $nuevoStock = $material->fresh()->cantidad;
            if ($nuevoStock <= $material->stock_minimo) {
                try {
                    $alerts = [
                        [
                            'nombre' => $material->nombre,
                            'codigo' => $material->codigo,
                            'cantidad' => $nuevoStock,
                            'stock_minimo' => $material->stock_minimo
                        ]
                    ];
                    \Illuminate\Support\Facades\Mail::to(Auth::user()->email)->send(new \App\Mail\StockAlertMail($alerts));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('No se pudo enviar correo de alerta: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('salidas.index')
                         ->with('success', 'Salida de inventario registrada con éxito.');
    }

    /**
     * Anula una salida de inventario y devuelve la cantidad de stock al almacén.
     */
    public function anular(Salida $salida)
    {
        if (!Auth::user()->isSuperUsuario() && !Auth::user()->isGerente()) {
            abort(403, 'No tienes permisos para anular movimientos.');
        }

        if ($salida->anulado) {
            return back()->with('error', 'Esta salida ya ha sido anulada anteriormente.');
        }

        $material = $salida->materialPrima;

        // Anular transacción
        $salida->anulado = true;
        $salida->save();

        // Operación Inversa: Sumar la cantidad de la salida de nuevo al stock del material
        $material->actualizarStock($salida->cantidad);

        return redirect()->route('salidas.index')
                         ->with('success', 'Salida de inventario anulada con éxito y stock devuelto al almacén.');
    }

    /**
     * Muestra el formulario para editar una salida.
     */
    public function edit(Salida $salida)
    {
        if ($salida->anulado) {
            return redirect()->route('salidas.index')->with('error', 'No se puede editar una salida anulada.');
        }

        $puedeEditar = false;
        if (Auth::user()->isSuperUsuario() || Auth::user()->isGerente()) {
            $puedeEditar = true;
        } elseif (Auth::user()->isAlmacenista() && $salida->user_id === Auth::id() && $salida->created_at->diffInMinutes(now()) <= 5) {
            $puedeEditar = true;
        }

        if (!$puedeEditar) {
            return redirect()->route('salidas.index')->with('error', 'No tienes permisos para editar este movimiento (solo se permite a Almacenistas editar sus propios registros dentro de los primeros 5 minutos).');
        }

        $materiales = MaterialPrima::where('activo', true)->get();

        return view('salidas.edit', compact('salida', 'materiales'));
    }

    /**
     * Actualiza la salida de inventario.
     */
    public function update(Request $request, Salida $salida)
    {
        if ($salida->anulado) {
            return redirect()->route('salidas.index')->with('error', 'No se puede editar una salida anulada.');
        }

        $puedeEditar = false;
        if (Auth::user()->isSuperUsuario() || Auth::user()->isGerente()) {
            $puedeEditar = true;
        } elseif (Auth::user()->isAlmacenista() && $salida->user_id === Auth::id() && $salida->created_at->diffInMinutes(now()) <= 5) {
            $puedeEditar = true;
        }

        if (!$puedeEditar) {
            return redirect()->route('salidas.index')->with('error', 'No tienes permisos para editar este movimiento.');
        }

        $request->validate([
            'material_prima_id' => 'required|exists:material_primas,id',
            'cantidad' => 'required|integer|min:1',
            'lote' => 'nullable|string|max:100',
            'destino' => 'required|string|max:255',
            'motivo' => 'nullable|string'
        ], [
            'material_prima_id.required' => 'La materia prima es obligatoria.',
            'cantidad.required' => 'La cantidad a retirar es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
            'destino.required' => 'El destino del material es obligatorio.',
        ]);

        $oldMaterial = MaterialPrima::find($salida->material_prima_id);
        $newMaterial = MaterialPrima::find($request->material_prima_id);

        $oldQty = $salida->cantidad;
        $newQty = $request->cantidad;

        // Si cambia de material prima
        if ($oldMaterial->id !== $newMaterial->id) {
            // Validar stock en el nuevo material
            if ($newMaterial->cantidad < $newQty) {
                return back()->withErrors(['cantidad' => 'Stock insuficiente en el nuevo material.'])->withInput();
            }

            // Devolver al material anterior (ya que no se retira de él)
            $oldMaterial->actualizarStock($oldQty);
            // Retirar del nuevo material
            $newMaterial->actualizarStock(-$newQty);
        } else {
            // Es el mismo material, calcular la diferencia
            // Si la nueva cantidad es mayor, retiramos más stock
            // Si la nueva cantidad es menor, devolvemos stock
            $diff = $oldQty - $newQty; // Positivo si devolvemos, negativo si retiramos más
            if ($diff < 0 && $oldMaterial->cantidad < abs($diff)) {
                return back()->withErrors(['cantidad' => 'Stock insuficiente para ampliar la salida.'])->withInput();
            }
            $oldMaterial->actualizarStock($diff);
        }

        // Actualizar datos de la salida
        $salida->fill($request->all());
        $salida->save();

        return redirect()->route('salidas.index')->with('success', 'Salida de inventario actualizada con éxito.');
    }
}