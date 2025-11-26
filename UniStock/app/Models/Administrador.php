<?php

namespace App\Models;

class Administrador extends User
{
    // Administrador hereda de User (Single Table Inheritance)
    // Se identifica por role = 'super_usuario'

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('administrador', function ($query) {
            $query->where('role', User::ROLE_SUPER_USUARIO);
        });
    }

    // Métodos del diagrama (heredados y propios)
    public function gestionarUsuarios()
    {
        return User::all();
    }

    public function configurarSistema(array $config)
    {
        return Configuracion::obtenerConfiguracion()->establecerConfiguracion($config);
    }

    public function generarReporteGeneral()
    {
        return "Reporte general del sistema";
    }
}
