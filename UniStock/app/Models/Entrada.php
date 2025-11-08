<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'cantidad',
        'motivo'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}