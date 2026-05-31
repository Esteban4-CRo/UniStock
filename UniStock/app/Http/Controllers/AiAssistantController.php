<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\MaterialPrima;
use App\Models\Proveedor;
use App\Models\Reporte;
use App\Models\Entrada;

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
            return response()->json(['error' => 'API Key de Groq no configurada en Render'], 500);
        }

        try {
            // Recopilar contexto de la base de datos
            $totalMateriales = MaterialPrima::count();
            $materialesBajoStock = MaterialPrima::whereColumn('cantidad', '<=', 'stock_minimo')->get();
            $nombresBajoStock = $materialesBajoStock->pluck('nombre')->implode(', ');
            $totalProveedores = Proveedor::count();

            $context = "Eres 'UniStock AI', un asistente experto EXCLUSIVAMENTE para el sistema de gestión de inventarios UniStock.\n";
            $context .= "REGLA CRÍTICA 1: Bájate de cualquier otro tema. Si el usuario te pregunta cosas generales, chistes, código, historia, o cualquier cosa fuera de UniStock, DEBES negarte a responder cortésmente y decir que solo hablas de inventarios y materias primas.\n";
            $context .= "REGLA CRÍTICA 2: Puedes ejecutar tareas en el sistema si el usuario te lo pide, usando las herramientas que se te proporcionan. Por ejemplo, registrar una materia prima o una entrada.\n\n";
            $context .= "Contexto de la BD actual:\n";
            $context .= "- Total de materias primas: {$totalMateriales}.\n";
            $context .= $materialesBajoStock->count() > 0 ? "- Alertas de stock bajo: {$nombresBajoStock}.\n" : "- No hay alertas de stock bajo.\n";
            $context .= "- Total de proveedores: {$totalProveedores}.\n";

            $tools = [
                [
                    "type" => "function",
                    "function" => [
                        "name" => "registrar_materia_prima",
                        "description" => "Registra una nueva materia prima en el inventario.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "codigo" => ["type" => "string", "description" => "Código único alfanumérico (ej: MP-001)"],
                                "nombre" => ["type" => "string", "description" => "Nombre del material"],
                                "cantidad" => ["type" => "number", "description" => "Cantidad inicial"],
                                "unidad_medida" => ["type" => "string", "description" => "kg, litros, unidades, gramos, etc"],
                                "stock_minimo" => ["type" => "number", "description" => "Nivel crítico de inventario"],
                                "precio" => ["type" => "number", "description" => "Precio por unidad"]
                            ],
                            "required" => ["codigo", "nombre", "cantidad", "unidad_medida", "stock_minimo", "precio"]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_MODEL', 'llama3-70b-8192'),
                'messages' => [
                    ['role' => 'system', 'content' => $context],
                    ['role' => 'user', 'content' => $message]
                ],
                'tools' => $tools,
                'tool_choice' => 'auto',
                'max_tokens' => 500,
                'temperature' => 0.2 // Baja temperatura para ser más preciso
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $responseMessage = $data['choices'][0]['message'];

                // Si la IA decide llamar a una función
                if (isset($responseMessage['tool_calls'])) {
                    foreach ($responseMessage['tool_calls'] as $toolCall) {
                        if ($toolCall['function']['name'] === 'registrar_materia_prima') {
                            $args = json_decode($toolCall['function']['arguments'], true);
                            
                            // Verificar que no exista el código
                            if (MaterialPrima::where('codigo', $args['codigo'])->exists()) {
                                return response()->json(['reply' => "❌ No pude registrarla porque ya existe una materia prima con el código **{$args['codigo']}**."]);
                            }

                            MaterialPrima::create([
                                'codigo' => $args['codigo'],
                                'nombre' => $args['codigo'] . ' - ' . $args['nombre'], // Prefix just to ensure uniqueness conceptually or just use name
                                'nombre' => $args['nombre'],
                                'cantidad' => $args['cantidad'],
                                'unidad_medida' => $args['unidad_medida'],
                                'stock_minimo' => $args['stock_minimo'],
                                'precio' => $args['precio'],
                                'activo' => true
                            ]);

                            return response()->json(['reply' => "✅ He registrado la nueva materia prima **{$args['nombre']}** ({$args['codigo']}) con éxito en el sistema con un stock de {$args['cantidad']} {$args['unidad_medida']}."]);
                        }
                    }
                }

                $reply = $responseMessage['content'] ?? 'Entendido.';
                return response()->json(['reply' => $reply]);

            } else {
                Log::error('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['reply' => 'Lo siento, hay un problema de conexión con el cerebro de la IA.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
