<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'mensaje',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Métodos del diagrama
    public function generarAlerta()
    {
        return $this->save();
    }

    public function enviarAlerta()
    {
        // Lógica para enviar alerta
        return "Alerta enviada: " . $this->mensaje;
    }

    public function verificarCondicion()
    {
        return $this->estado === 'activa';
    }
}
