<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     *     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->activo) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Su cuenta ha sido inhabilitada por el administrador. Comuníquese con soporte para más detalles.',
            ]);
        }

        return $next($request);
    }
}
