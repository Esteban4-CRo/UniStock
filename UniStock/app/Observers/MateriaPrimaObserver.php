<?php

namespace App\Observers;

use App\Models\MaterialPrima;
use App\Models\Alerta;
use App\Models\User;
use App\Mail\StockAlertMail;
use Illuminate\Support\Facades\Mail;

class MateriaPrimaObserver
{
    /**
     * Handle the MaterialPrima "created" event.
     */
    public function created(MaterialPrima $materialPrima): void
    {
        //
    }

    /**
     * Handle the MaterialPrima "updated" event.
     */
    public function updated(MaterialPrima $materialPrima): void
    {
        // Check if stock dropped below minimum
        if ($materialPrima->cantidad < $materialPrima->stock_minimo && 
            $materialPrima->getOriginal('cantidad') >= $materialPrima->stock_minimo) {
            
            $mensaje = "El stock de {$materialPrima->nombre} (cód: {$materialPrima->codigo}) ha bajado a {$materialPrima->cantidad}. El mínimo es {$materialPrima->stock_minimo}.";
            
            // Create Alert for the user who owns it, or admin
            Alerta::create([
                'user_id' => $materialPrima->user_id,
                'tipo' => 'stock_bajo',
                'mensaje' => $mensaje,
                'estado' => 'activa'
            ]);

            // Send Mail
            $user = User::find($materialPrima->user_id);
            if ($user && $user->email) {
                $alertData = [
                    'codigo' => $materialPrima->codigo,
                    'nombre' => $materialPrima->nombre,
                    'cantidad' => $materialPrima->cantidad,
                    'stock_minimo' => $materialPrima->stock_minimo,
                ];
                Mail::to($user->email)->queue(new StockAlertMail([$alertData]));
            }
        }
    }

    /**
     * Handle the MaterialPrima "deleted" event.
     */
    public function deleted(MaterialPrima $materialPrima): void
    {
        //
    }

    /**
     * Handle the MaterialPrima "restored" event.
     */
    public function restored(MaterialPrima $materialPrima): void
    {
        //
    }

    /**
     * Handle the MaterialPrima "force deleted" event.
     */
    public function forceDeleted(MaterialPrima $materialPrima): void
    {
        //
    }
}
