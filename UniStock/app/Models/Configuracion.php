<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $fillable = [
        'nombre_sistema',
        'idioma',
        'moneda',
        'zona_horaria',
        'stock_minimo_global',
        'alertas_email',
        'modo_mantenimiento',
    ];

    protected $casts = [
        'alertas_email' => 'boolean',
        'modo_mantenimiento' => 'boolean',
    ];

    // Métodos del diagrama
    public function establecerConfiguracion(array $datos)
    {
        return $this->update($datos);
    }

    public function actualizarConfiguracion(array $datos)
    {
        return $this->update($datos);
    }

    public static function obtenerConfiguracion()
    {
        return self::first() ?? self::create([]);
    }
}
