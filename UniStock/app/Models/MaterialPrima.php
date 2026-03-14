<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPrima extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad',
        'unidad_medida',
        'stock_minimo',
        'stock_maximo',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    // Relación many-to-many con Proveedor a través de auxiliar_almacena
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'auxiliar_almacena')
                    ->withPivot('cantidad', 'fecha_almacenamiento')
                    ->withTimestamps();
    }

    // Métodos del diagrama
    public function actualizarStock($cantidad)
    {
        $this->cantidad += $cantidad;
        return $this->save();
    }

    public function verificarDisponibilidad()
    {
        return $this->cantidad > $this->stock_minimo;
    }

    public function calcularCostoTotal()
    {
        // Lógica para calcular costo
        return $this->cantidad * 100; // Ejemplo
    }
}
