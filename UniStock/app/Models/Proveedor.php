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
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
