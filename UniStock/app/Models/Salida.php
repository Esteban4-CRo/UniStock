<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salidas';

    protected $fillable = [
        'material_prima_id',
        'user_id',
        'cantidad',
        'lote',
        'destino',
        'motivo',
        'anulado'
    ];

    protected $casts = [
        'anulado' => 'boolean',
    ];

    /**
     * Relación con la materia prima despachada.
     */
    public function materialPrima()
    {
        return $this->belongsTo(MaterialPrima::class, 'material_prima_id');
    }

    /**
     * Relación con el usuario (almacenista) que registró la salida.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Métodos del diagrama (MovimientoInventario)
    public function registrarMovimiento() {
        return $this->save();
    }

    public function validarCantidad() {
        return $this->cantidad > 0;
    }

    public function generarComprobante() {
        return "Comprobante de Salida #" . $this->id;
    }
}