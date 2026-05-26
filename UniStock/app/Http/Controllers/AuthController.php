<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    /**
     * Handle Google OAuth callback.
     *
     * Captura el código y el issuer, registra el evento y redirige al dashboard.
     */
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->query('code');
        $iss = $request->query('iss');

        if (! $code) {
            // Si algo falla, devolvemos error 400
            return response()->json(['error' => 'No se recibió el código de Google'], 400);
        }

        // Log para depuración
        Log::info('Google callback: código recibido = ' . $code . ', iss = ' . $iss);

        // Aquí iría la lógica de intercambio con Supabase si fuera necesaria.

        // Redirigir al usuario a la zona interna de la app.
        // La URL base se define en .env (APP_URL) y se ajusta según entorno.
        $redirectUrl = config('services.google.redirect');
        // Si deseas redirigir a una ruta interna después del login, ajusta aquí.
        return redirect('/');
    }
}
