<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'user_id',
        'empresa',
        'ruc',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
        'ciudad',
        'pais',
        'activo',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación many-to-many con MaterialPrima a través de auxiliar_almacena
    public function materialesPrimas()
    {
        return $this->belongsToMany(MaterialPrima::class, 'auxiliar_almacena')
                    ->withPivot('cantidad', 'fecha_almacenamiento')
                    ->withTimestamps();
    }

    /**
     * Relación con las entradas de materias primas que provienen de este proveedor.
     */
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'proveedor_id');
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitud && $this->longitud) {
            return "https://www.google.com/maps?q={$this->latitud},{$this->longitud}";
        }
        return null;
    }

    public function hasLocation()
    {
        return !is_null($this->latitud) && !is_null($this->longitud);
    }

    // Métodos del diagrama (Proveedor)
    public function registrarProveedor() {
        return $this->save();
    }

    public function validarProveedor() {
        return !empty($this->ruc) && !empty($this->empresa);
    }

    public function generarReporteCompras() {
        // Lógica para generar reporte
        return "Reporte de compras generado para " . $this->empresa;
    }
}
