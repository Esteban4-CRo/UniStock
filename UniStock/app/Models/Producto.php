<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock_actual',
        'precio',
        'user_id',
        'estado',
        'unidad_medida',
        'stock_minimo',
        'fecha_caducidad',
        'lote'
    ];

    // Métodos del diagrama (MateriaPrima)
    public function actualizarStock($cantidad) {
        $this->stock_actual += $cantidad;
        return $this->save();
    }

    public function verificarCaducidad() {
        if ($this->fecha_caducidad) {
            return now()->greaterThan($this->fecha_caducidad);
        }
        return false;
    }

    public function calcularDisponibilidad() {
        return $this->stock_actual - $this->stock_minimo;
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}