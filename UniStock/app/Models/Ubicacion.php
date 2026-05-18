<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'pasillo',
        'estante',
        'casillero',
        'descripcion',
        'activo',
    ];

    /**
     * Relación uno a muchos con MaterialPrima.
     * Una ubicación puede albergar múltiples materias primas.
     */
    public function materialesPrimas()
    {
        return $this->hasMany(MaterialPrima::class, 'ubicacion_id');
    }

    /**
     * Nombre legible completo de la ubicación física.
     * Ejemplo: "Pasillo A - Estante 3 - Casillero C"
     */
    public function getNombreCompletoAttribute()
    {
        return "Pasillo {$this->pasillo} - Estante {$this->estante} - Casillero {$this->casillero}";
    }
}
