<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Models\MaterialPrima;
use App\Models\Proveedor;
use App\Models\Reporte;
use App\Models\User;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Mensaje vacío'], 400);
        }

        $apiKey = env('GROQ_API_KEY');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key de Groq no configurada'], 500);
        }

        try {
            // Recopilar contexto de la base de datos
            $totalMateriales = MaterialPrima::count();
            $materialesBajoStock = MaterialPrima::whereColumn('cantidad', '<=', 'stock_minimo')->get();
            $nombresBajoStock = $materialesBajoStock->pluck('nombre')->implode(', ');
            $totalProveedores = Proveedor::count();
            $mejoresProveedores = Proveedor::limit(3)->pluck('empresa')->implode(', '); // Simplificado
            $ultimosReportes = Reporte::with('user')->latest()->limit(3)->get()->map(function($r) {
                return $r->user->name . ' (' . $r->tipo . ')';
            })->implode(', ');

            $context = "Contexto actual del sistema UniStock:\n";
            $context .= "- Total de materias primas registradas: {$totalMateriales}.\n";
            if ($materialesBajoStock->count() > 0) {
                $context .= "- Materias primas con stock bajo o agotado: {$nombresBajoStock}.\n";
            } else {
                $context .= "- No hay materias primas con stock bajo.\n";
            }
            $context .= "- Total de proveedores registrados: {$totalProveedores} (Ej: {$mejoresProveedores}).\n";
            $context .= "- Últimos reportes generados por: {$ultimosReportes}.\n\n";
            $context .= "Eres un asistente experto de UniStock, un sistema de gestión de inventarios. Responde de manera concisa, profesional y basándote en el contexto proporcionado. Si el usuario pregunta qué materias primas hay, cuántos proveedores, o quién generó reportes, usa esta información. Puedes formatear tus respuestas usando markdown.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_MODEL', 'mixtral-8x7b-32768'),
                'messages' => [
                    ['role' => 'system', 'content' => $context],
                    ['role' => 'user', 'content' => $message]
                ],
                'max_tokens' => 300,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'No se pudo procesar la respuesta.';
                return response()->json(['reply' => $reply]);
            } else {
                // Log the error for debugging
                Log::error('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);
                // Return a friendly fallback reply
                return response()->json(['reply' => 'Lo siento, el asistente está temporalmente fuera de servicio. Por favor, intenta más tarde.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error conectando con Groq: ' . $e->getMessage()], 500);
        }
    }
}
