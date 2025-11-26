<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_SUPER_USUARIO = 'super_usuario';
    const ROLE_GERENTE = 'gerente';
    const ROLE_ALMACENISTA = 'almacenista';
    const ROLE_PROVEEDOR = 'proveedor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class);
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class);
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    public function isSuperUsuario()
    {
        return $this->role === self::ROLE_SUPER_USUARIO;
    }

    public function isGerente()
    {
        return $this->role === self::ROLE_GERENTE;
    }

    public function isAlmacenista()
    {
        return $this->role === self::ROLE_ALMACENISTA;
    }

    public function isProveedor()
    {
        return $this->role === self::ROLE_PROVEEDOR;
    }

    public function getRoleNameAttribute()
    {
        return match($this->role) {
            self::ROLE_SUPER_USUARIO => 'Super Usuario',
            self::ROLE_GERENTE => 'Gerente',
            self::ROLE_ALMACENISTA => 'Almacenista',
            self::ROLE_PROVEEDOR => 'Proveedor',
            default => 'Sin Rol',
        };
    }

    public function getRoleBadgeAttribute()
    {
        $colors = [
            self::ROLE_SUPER_USUARIO => 'background: linear-gradient(135deg, #000 0%, #434343 100%); color: #FFD700;',
            self::ROLE_GERENTE => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;',
            self::ROLE_ALMACENISTA => 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;',
            self::ROLE_PROVEEDOR => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;',
        ];

        $style = $colors[$this->role] ?? 'background: #gray; color: white;';
        
        return '<span class="badge" style="' . $style . ' padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem;">' 
               . $this->role_name . 
               '</span>';
    }

    public function getRoleIconAttribute()
    {
        return match($this->role) {
            self::ROLE_SUPER_USUARIO => 'fa-crown',
            self::ROLE_GERENTE => 'fa-user-tie',
            self::ROLE_ALMACENISTA => 'fa-boxes',
            self::ROLE_PROVEEDOR => 'fa-truck',
            default => 'fa-user',
        };
    }

    // Métodos del diagrama de clases (Usuario)
    public function login() {
        // Implementación manejada por Laravel Auth
        return true;
    }

    public function logOut() {
        // Implementación manejada por Laravel Auth
        return true;
    }

    public function cambiarContrasena($newPassword) {
        $this->password = bcrypt($newPassword);
        return $this->save();
    }

    // Métodos del diagrama (Administrador -> SuperUsuario)
    public function gestionarUsuarios() {
        return $this->isSuperUsuario();
    }

    public function configurarSistema() {
        return $this->isSuperUsuario();
    }

    public function generarReportesAuditoria() {
        return $this->isSuperUsuario();
    }

    // Métodos del diagrama (JefeLogistica -> Gerente)
    public function generarReportes() {
        return $this->isGerente();
    }

    public function configurarAlertas() {
        return $this->isGerente();
    }

    public function compararInventario() {
        return $this->isGerente();
    }

    // Métodos del diagrama (AuxiliarAlmacen -> Almacenista)
    public function consultarStock() {
        return $this->isAlmacenista();
    }

    public function verReporte() {
        return $this->isAlmacenista();
    }

    public function registrarEntradas() {
        return $this->isAlmacenista();
    }

    public function registrarSalidas() {
        return $this->isAlmacenista();
    }

    public function realizarAjustes() {
        return $this->isAlmacenista();
    }
}

