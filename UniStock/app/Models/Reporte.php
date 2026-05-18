<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'material_prima_id',
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

    public function materialPrima()
    {
        return $this->belongsTo(MaterialPrima::class, 'material_prima_id');
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
