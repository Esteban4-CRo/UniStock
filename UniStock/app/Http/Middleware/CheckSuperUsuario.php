<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperUsuario
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isSuperUsuario()) {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos para acceder a esta sección. Solo Super Usuarios pueden realizar esta acción.');
        }

        return $next($request);
    }
}
