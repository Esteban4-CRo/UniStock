<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alerta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AlertaController extends Controller
{
    public function marcarLeida($id)
    {
        $alerta = Alerta::where('user_id', Auth::id())->findOrFail($id);
        $alerta->estado = 'leida';
        $alerta->save();

        Cache::forget('alertas_notif_' . Auth::id());

        return response()->json(['success' => true]);
    }

    public function marcarTodasLeidas()
    {
        Alerta::where('user_id', Auth::id())
              ->where('estado', 'activa')
              ->update(['estado' => 'leida']);

        Cache::forget('alertas_notif_' . Auth::id());

        return response()->json(['success' => true]);
    }
}
