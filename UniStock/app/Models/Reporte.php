<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'producto_id',
        'reporte_nombre',
        'fecha_generacion',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'contenido',
        'formato',
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Métodos del diagrama
    public function generarReporte()
    {
        // Lógica para generar reporte
        return $this->save();
    }

    public function exportarPDF()
    {
        $this->formato = 'pdf';
        return "Reporte exportado en PDF";
    }

    public function exportarExcel()
    {
        $this->formato = 'excel';
        return "Reporte exportado en Excel";
    }
}
