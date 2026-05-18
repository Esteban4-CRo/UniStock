<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPrima extends Model
{
    use HasFactory;

    protected $table = 'material_primas';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'cantidad',
        'unidad_medida',
        'stock_minimo',
        'precio',
        'lote',
        'fecha_caducidad',
        'user_id',
        'ubicacion_id',
        'activo',
    ];

    protected $casts = [
        'fecha_caducidad' => 'date',
        'precio' => 'decimal:2',
    ];

    /**
     * Relación con el usuario creador (Gerente o Administrador).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la ubicación física de almacenamiento (1:N).
     */
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    /**
     * Relación con las entradas de inventario asociadas.
     */
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'material_prima_id');
    }

    /**
     * Relación con las salidas de inventario asociadas.
     */
    public function salidas()
    {
        return $this->hasMany(Salida::class, 'material_prima_id');
    }

    /**
     * Relación histórica many-to-many con Proveedores a través de auxiliar_almacena (opcional de soporte).
     */
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'auxiliar_almacena', 'material_prima_id', 'proveedor_id')
                    ->withPivot('cantidad', 'fecha_almacenamiento')
                    ->withTimestamps();
    }

    // --- Métodos del diagrama de clases adaptados ---

    /**
     * Incrementa o decrementa la cantidad de stock de la materia prima.
     */
    public function actualizarStock($cantidad)
    {
        $this->cantidad += $cantidad;
        return $this->save();
    }

    /**
     * Verifica si la materia prima ya caducó basándose en la fecha actual.
     */
    public function verificarCaducidad()
    {
        if ($this->fecha_caducidad) {
            return now()->greaterThan($this->fecha_caducidad);
        }
        return false;
    }

    /**
     * Calcula la cantidad que se puede comprometer o usar (stock disponible por encima del stock mínimo).
     */
    public function calcularDisponibilidad()
    {
        return $this->cantidad - $this->stock_minimo;
    }
}
