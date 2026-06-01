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
        $history = $request->input('history', []);

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
            $context .= "REGLA CRÍTICA 2: Puedes ejecutar tareas en el sistema usando las herramientas. Puedes registrar: materias primas, proveedores, entradas de inventario y salidas de inventario.\n";
            $context .= "REGLA CRÍTICA 3: NUNCA ejecutes una herramienta sin tener TODOS los datos necesarios. Si el usuario quiere registrar algo, PRIMERO pregúntale los campos obligatorios. Para materia prima: nombre, código, cantidad, unidad de medida, stock mínimo, precio. Para proveedor: nombre de la empresa y RUC/NIT. Para entrada: nombre de la materia prima, nombre del proveedor, cantidad y motivo. Para salida: nombre de la materia prima, cantidad, motivo y destino.\n";
            $context .= "REGLA CRÍTICA 4: Responde siempre en español, de forma breve y amigable.\n\n";
            $context .= "Contexto de la BD actual:\n";
            $context .= "- Total de materias primas: {$totalMateriales}.\n";
            $context .= $materialesBajoStock->count() > 0 ? "- Alertas de stock bajo: {$nombresBajoStock}.\n" : "- No hay alertas de stock bajo.\n";
            $context .= "- Total de proveedores: {$totalProveedores}.\n";

            $tools = [
                [
                    "type" => "function",
                    "function" => [
                        "name" => "registrar_materia_prima",
                        "description" => "Registra una nueva materia prima en el inventario. SOLO llamar cuando el usuario haya proporcionado TODOS los campos requeridos.",
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
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "registrar_proveedor",
                        "description" => "Registra un nuevo proveedor.",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "empresa" => ["type" => "string", "description" => "Nombre de la empresa proveedora"],
                                "ruc" => ["type" => "string", "description" => "RUC, NIT o documento de identificación de la empresa"],
                                "telefono" => ["type" => "string", "description" => "Teléfono de contacto (opcional)"],
                                "direccion" => ["type" => "string", "description" => "Dirección física (opcional)"]
                            ],
                            "required" => ["empresa", "ruc"]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "registrar_entrada",
                        "description" => "Registra una entrada de inventario (ingreso de materia prima al almacén).",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "nombre_material" => ["type" => "string", "description" => "Nombre o código de la materia prima"],
                                "nombre_proveedor" => ["type" => "string", "description" => "Nombre del proveedor que entrega el material"],
                                "cantidad" => ["type" => "number", "description" => "Cantidad que ingresa"],
                                "motivo" => ["type" => "string", "description" => "Motivo del ingreso (ej: Compra, Devolución)"]
                            ],
                            "required" => ["nombre_material", "nombre_proveedor", "cantidad", "motivo"]
                        ]
                    ]
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "registrar_salida",
                        "description" => "Registra una salida de inventario (retiro de materia prima del almacén).",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "nombre_material" => ["type" => "string", "description" => "Nombre o código de la materia prima"],
                                "cantidad" => ["type" => "number", "description" => "Cantidad que sale"],
                                "motivo" => ["type" => "string", "description" => "Motivo de la salida (ej: Producción, Merma)"],
                                "destino" => ["type" => "string", "description" => "Área de destino (ej: Planta 1, Taller)"]
                            ],
                            "required" => ["nombre_material", "cantidad", "motivo", "destino"]
                        ]
                    ]
                ]
            ];

            // Construir mensajes con historial
            $messages = [['role' => 'system', 'content' => $context]];
            
            // Agregar historial previo (máximo 20 mensajes para no exceder tokens)
            if (is_array($history)) {
                $history = array_slice($history, -20);
                foreach ($history as $msg) {
                    if (isset($msg['role']) && isset($msg['content'])) {
                        $messages[] = [
                            'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                            'content' => $msg['content']
                        ];
                    }
                }
            }
            
            // Agregar el mensaje actual
            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                'messages' => $messages,
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
                            
                            try {
                                if (MaterialPrima::where('codigo', $args['codigo'])->exists()) {
                                    return response()->json(['reply' => "❌ No pude registrarla porque ya existe una materia prima con el código **{$args['codigo']}**."]);
                                }

                                MaterialPrima::create([
                                    'codigo' => $args['codigo'],
                                    'nombre' => $args['nombre'],
                                    'cantidad' => $args['cantidad'],
                                    'unidad_medida' => $args['unidad_medida'],
                                    'stock_minimo' => $args['stock_minimo'],
                                    'precio' => $args['precio'],
                                    'activo' => true,
                                    'user_id' => Auth::id(),
                                ]);

                                return response()->json(['reply' => "✅ He registrado la nueva materia prima **{$args['nombre']}** ({$args['codigo']}) con un stock de {$args['cantidad']} {$args['unidad_medida']}."]);
                            } catch (\Exception $e) {
                                Log::error('Error al registrar materia prima via IA', ['error' => $e->getMessage()]);
                                return response()->json(['reply' => "❌ Hubo un error al registrar la materia prima."]);
                            }
                        }

                        if ($toolCall['function']['name'] === 'registrar_proveedor') {
                            $args = json_decode($toolCall['function']['arguments'], true);
                            try {
                                if (Proveedor::where('ruc', $args['ruc'])->exists()) {
                                    return response()->json(['reply' => "❌ El proveedor con RUC **{$args['ruc']}** ya existe."]);
                                }
                                Proveedor::create([
                                    'empresa' => $args['empresa'],
                                    'ruc' => $args['ruc'],
                                    'telefono' => $args['telefono'] ?? null,
                                    'direccion' => $args['direccion'] ?? null,
                                    'activo' => true,
                                    'user_id' => Auth::id(),
                                ]);
                                return response()->json(['reply' => "✅ He registrado al proveedor **{$args['empresa']}** exitosamente."]);
                            } catch (\Exception $e) {
                                Log::error('Error al registrar proveedor via IA', ['error' => $e->getMessage()]);
                                return response()->json(['reply' => "❌ Hubo un error al registrar el proveedor."]);
                            }
                        }

                        if ($toolCall['function']['name'] === 'registrar_entrada') {
                            $args = json_decode($toolCall['function']['arguments'], true);
                            try {
                                $material = MaterialPrima::where('nombre', 'ilike', '%' . $args['nombre_material'] . '%')->orWhere('codigo', $args['nombre_material'])->first();
                                $proveedor = Proveedor::where('empresa', 'ilike', '%' . $args['nombre_proveedor'] . '%')->first();

                                if (!$material) return response()->json(['reply' => "❌ No encontré ninguna materia prima llamada **{$args['nombre_material']}**."]);
                                if (!$proveedor) return response()->json(['reply' => "❌ No encontré ningún proveedor llamado **{$args['nombre_proveedor']}**."]);

                                Entrada::create([
                                    'material_prima_id' => $material->id,
                                    'proveedor_id' => $proveedor->id,
                                    'cantidad' => $args['cantidad'],
                                    'motivo' => $args['motivo'],
                                    'user_id' => Auth::id(),
                                    'anulado' => false
                                ]);
                                
                                $material->increment('cantidad', $args['cantidad']);
                                return response()->json(['reply' => "✅ Entrada registrada: ingresaron {$args['cantidad']} de **{$material->nombre}** desde {$proveedor->empresa}."]);
                            } catch (\Exception $e) {
                                Log::error('Error al registrar entrada via IA', ['error' => $e->getMessage()]);
                                return response()->json(['reply' => "❌ Hubo un error al registrar la entrada."]);
                            }
                        }

                        if ($toolCall['function']['name'] === 'registrar_salida') {
                            $args = json_decode($toolCall['function']['arguments'], true);
                            try {
                                $material = MaterialPrima::where('nombre', 'ilike', '%' . $args['nombre_material'] . '%')->orWhere('codigo', $args['nombre_material'])->first();
                                if (!$material) return response()->json(['reply' => "❌ No encontré la materia prima **{$args['nombre_material']}**."]);

                                if ($material->cantidad < $args['cantidad']) {
                                    return response()->json(['reply' => "❌ Stock insuficiente. Quieres sacar {$args['cantidad']} pero solo hay {$material->cantidad} de {$material->nombre}."]);
                                }

                                \App\Models\Salida::create([
                                    'material_prima_id' => $material->id,
                                    'cantidad' => $args['cantidad'],
                                    'motivo' => $args['motivo'],
                                    'destino' => $args['destino'],
                                    'user_id' => Auth::id(),
                                    'anulado' => false
                                ]);
                                
                                $material->decrement('cantidad', $args['cantidad']);
                                return response()->json(['reply' => "✅ Salida registrada: se retiraron {$args['cantidad']} de **{$material->nombre}** para {$args['destino']}."]);
                            } catch (\Exception $e) {
                                Log::error('Error al registrar salida via IA', ['error' => $e->getMessage()]);
                                return response()->json(['reply' => "❌ Hubo un error al registrar la salida."]);
                            }
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
            Log::error('AI Assistant error', ['error' => $e->getMessage()]);
            return response()->json(['reply' => '❌ Error interno: ' . $e->getMessage()]);
        }
    }
}
