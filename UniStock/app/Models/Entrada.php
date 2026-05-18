<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';

    protected $fillable = [
        'material_prima_id',
        'user_id',
        'proveedor_id',
        'cantidad',
        'lote',
        'fecha_caducidad',
        'motivo',
        'anulado'
    ];

    protected $casts = [
        'fecha_caducidad' => 'date',
        'anulado' => 'boolean',
    ];

    /**
     * Relación con la materia prima ingresada.
     */
    public function materialPrima()
    {
        return $this->belongsTo(MaterialPrima::class, 'material_prima_id');
    }

    /**
     * Relación con el usuario (almacenista) que registró la entrada.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el proveedor de origen de la materia prima.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    // Métodos del diagrama (MovimientoInventario)
    public function registrarMovimiento() {
        return $this->save();
    }

    public function validarCantidad() {
        return $this->cantidad > 0;
    }

    public function generarComprobante() {
        return "Comprobante de Entrada #" . $this->id;
    }
}