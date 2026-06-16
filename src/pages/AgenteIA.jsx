import { useState, useEffect, useRef } from 'react';
import api from '../api';
import { Bot, Send, BarChart3, TrendingUp, TrendingDown, AlertTriangle, PackageOpen, RotateCw, Sparkles } from 'lucide-react';

const BACKEND_URL = import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000';
const GROQ_API_KEY = import.meta.env.VITE_GROQ_API_KEY || '';
const GROQ_MODEL = import.meta.env.VITE_GROQ_MODEL || 'llama-3.1-8b-instant';

const SUGGESTED_QUESTIONS = [
    'Haz una prediccion de consumo para los proximos 7 dias',
    'Que materias primas tienen stock critico?',
    'Dame estadisticas generales del inventario',
    'Que materiales debo reabastecer primero?',
    'Analiza las tendencias de entradas y salidas',
    'Genera un resumen ejecutivo del inventario',
];

export default function AgenteIA() {
    const [messages, setMessages] = useState([
        {
            role: 'assistant',
            content: 'Soy **Uni**, tu asistente inteligente de inventario. Tengo acceso en tiempo real a tus datos.\n\nPuedo ayudarte con:\n- **Predicciones** de consumo y reabastecimiento\n- **Estadisticas** del inventario y movimientos\n- **Alertas** y recomendaciones de stock\n- **Analisis** de tendencias\n\nPreguntame lo que necesites.',
        }
    ]);
    const [input, setInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [inventoryCtx, setInventoryCtx] = useState(null);
    const [stats, setStats] = useState(null);
    const messagesEndRef = useRef(null);

    useEffect(() => {
        loadInventoryContext();
    }, []);

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const loadInventoryContext = async () => {
        try {
            const [mRes, aRes, eRes, sRes] = await Promise.all([
                api.get('material-primas/'),
                api.get('alertas/'),
                api.get('entradas/'),
                api.get('salidas/'),
            ]);

            const materias = mRes.data;
            const alertas = aRes.data;
            const entradas = eRes.data;
            const salidas = sRes.data;

            const lowStock = materias.filter(m => parseFloat(m.cantidad) <= parseFloat(m.stock_minimo));
            const totalStock = materias.reduce((a, m) => a + parseFloat(m.cantidad || 0), 0);
            const volEntradas = entradas.filter(e => !e.anulado).reduce((a, e) => a + parseFloat(e.cantidad || 0), 0);
            const volSalidas = salidas.filter(s => !s.anulado).reduce((a, s) => a + parseFloat(s.cantidad || 0), 0);
            const avgStock = materias.length > 0 ? (totalStock / materias.length).toFixed(1) : 0;

            setStats({
                totalMaterias: materias.length,
                lowStockCount: lowStock.length,
                alertasActivas: alertas.length,
                entradas: entradas.filter(e => !e.anulado).length,
                salidas: salidas.filter(s => !s.anulado).length,
                volEntradas: volEntradas.toFixed(1),
                volSalidas: volSalidas.toFixed(1),
                avgStock,
                netFlow: (volEntradas - volSalidas).toFixed(1),
            });

            setInventoryCtx({
                materias,
                alertas,
                entradas,
                salidas,
                entradas_count: entradas.length,
                salidas_count: salidas.length,
            });
        } catch (err) {
            console.error('Error cargando contexto:', err);
        }
    };

    const buildSystemPrompt = () => {
        if (!inventoryCtx) return '';
        const { materias, alertas, entradas, salidas } = inventoryCtx;
        const lowStock = materias.filter(m => parseFloat(m.cantidad) <= parseFloat(m.stock_minimo));

        const entradasRecientes = entradas.filter(e => !e.anulado).slice(0, 20);
        const salidasRecientes = salidas.filter(s => !s.anulado).slice(0, 20);

        return `Eres Uni, el agente de IA de UniStock — sistema de gestion de inventario de materias primas.
Hablas en espanol, eres directo, conciso y profesional. Usas datos concretos y numeros.

CAPACIDADES PRINCIPALES:
1. PREDICCIONES: Calcula tendencias de consumo basandote en salidas historicas. Proyecta cuando se agotara cada material.
2. ESTADISTICAS: Proporciona metricas como rotacion, cobertura de stock, y eficiencia de flujo.
3. RECOMENDACIONES: Sugiere cantidades de reabastecimiento y prioridades.
4. ALERTAS: Identifica problemas actuales y potenciales.

DATOS EN TIEMPO REAL DEL INVENTARIO:
- Total materias primas: ${materias.length}
- Alertas activas: ${alertas.length}
- Total entradas (no anuladas): ${entradas.filter(e => !e.anulado).length}
- Total salidas (no anuladas): ${salidas.filter(s => !s.anulado).length}

MATERIAS CON STOCK BAJO (${lowStock.length}):
${lowStock.map(m => `- ${m.nombre}: ${m.cantidad} ${m.unidad_medida} (minimo: ${m.stock_minimo}) — DEFICIT: ${(parseFloat(m.stock_minimo) - parseFloat(m.cantidad)).toFixed(1)}`).join('\n') || 'Ninguna'}

INVENTARIO COMPLETO:
${materias.slice(0, 30).map(m => {
    const actual = parseFloat(m.cantidad);
    const minimo = parseFloat(m.stock_minimo);
    const cobertura = minimo > 0 ? ((actual / minimo) * 100).toFixed(0) : 'N/A';
    return `- ${m.nombre}: ${actual} ${m.unidad_medida} (min: ${minimo}) | Cobertura: ${cobertura}% | ${actual <= minimo ? 'CRITICO' : actual <= minimo * 1.5 ? 'ATENCION' : 'NORMAL'}`;
}).join('\n')}

ULTIMAS ENTRADAS:
${entradasRecientes.slice(0, 10).map(e => `- ${e.material_prima_nombre || 'Mat #' + e.material_prima}: +${e.cantidad} (${new Date(e.fecha_entrada).toLocaleDateString('es-CO')})`).join('\n') || 'Sin entradas recientes'}

ULTIMAS SALIDAS:
${salidasRecientes.slice(0, 10).map(s => `- ${s.material_prima_nombre || 'Mat #' + s.material_prima}: -${s.cantidad} a ${s.destino} (${new Date(s.fecha_salida).toLocaleDateString('es-CO')})`).join('\n') || 'Sin salidas recientes'}

${alertas.length > 0 ? `ALERTAS ACTIVAS:\n${alertas.slice(0, 10).map(a => `- ${a.mensaje}`).join('\n')}` : 'No hay alertas activas.'}

INSTRUCCIONES:
- Cuando te pidan predicciones, usa los datos historicos de entradas/salidas para calcular promedios y proyectar.
- Incluye numeros concretos y porcentajes siempre que sea posible.
- Estructura tus respuestas con encabezados (**negrita**) y listas para mayor claridad.
- Si no hay datos suficientes para una prediccion precisa, indicalo pero ofrece la mejor estimacion posible.
- Si te preguntan algo no relacionado con inventario, redirige amablemente.`;
    };

    const sendMessage = async (text) => {
        const userText = text || input.trim();
        if (!userText || loading) return;

        const userMsg = { role: 'user', content: userText };
        const newMessages = [...messages, userMsg];
        setMessages(newMessages);
        setInput('');
        setLoading(true);

        try {
            const groqMessages = newMessages.map(m => ({ role: m.role, content: m.content }));
            let responseText = '';

            try {
                const res = await api.post(
                    `${BACKEND_URL}/auth/ai/chat/`,
                    {
                        messages: groqMessages.slice(-10),
                        context: inventoryCtx ? {
                            materias: inventoryCtx.materias,
                            alertas: inventoryCtx.alertas,
                            entradas_count: inventoryCtx.entradas_count,
                            salidas_count: inventoryCtx.salidas_count,
                        } : {},
                    },
                    { baseURL: '' }
                );
                responseText = res.data.message;
            } catch (backendErr) {
                if (!GROQ_API_KEY) {
                    throw new Error('No hay API key de GROQ configurada');
                }

                const systemPrompt = buildSystemPrompt();
                const fetchRes = await fetch('https://api.groq.com/openai/v1/chat/completions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${GROQ_API_KEY}`,
                    },
                    body: JSON.stringify({
                        model: GROQ_MODEL,
                        messages: [
                            { role: 'system', content: systemPrompt },
                            ...groqMessages.slice(-8),
                        ],
                        temperature: 0.7,
                        max_tokens: 1500,
                    }),
                });

                if (!fetchRes.ok) {
                    const errData = await fetchRes.json();
                    throw new Error(errData.error?.message || 'Error de GROQ API');
                }

                const data = await fetchRes.json();
                responseText = data.choices[0].message.content;
            }

            setMessages(prev => [...prev, { role: 'assistant', content: responseText }]);
        } catch (err) {
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: `Error al conectar con la IA: ${err.message}. Verifica tu conexion y la API key de GROQ.`,
            }]);
        } finally {
            setLoading(false);
        }
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    };

    const renderContent = (text) => {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br/>');
    };

    return (
        <div>
            <div className="page-header">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
                    <div>
                        <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <Bot size={24} /> Agente IA
                        </h1>
                        <p className="page-subtitle">
                            Uni — Asistente inteligente con predicciones y estadisticas
                            {inventoryCtx && (
                                <span style={{ marginLeft: '1rem' }}>
                                    <span className="badge badge-success">Conectado</span>
                                    <span style={{ fontSize: '0.7rem', color: 'var(--text-muted)', marginLeft: '0.5rem' }}>
                                        {inventoryCtx.materias.length} materias | {inventoryCtx.alertas.length} alertas
                                    </span>
                                </span>
                            )}
                        </p>
                    </div>
                    <button className="btn btn-secondary btn-sm" onClick={loadInventoryContext} style={{ display: 'flex', alignItems: 'center', gap: '0.3rem' }}>
                        <RotateCw size={14} /> Actualizar datos
                    </button>
                </div>
            </div>

            {/* Stats Panel */}
            {stats && (
                <div className="grid-4" style={{ marginBottom: '1rem' }}>
                    <div className="kpi-card accent">
                        <div className="kpi-icon accent"><PackageOpen size={20} /></div>
                        <div className="kpi-value">{stats.totalMaterias}</div>
                        <div className="kpi-label">Materias Primas</div>
                        <div className="kpi-change" style={{ fontSize: '0.7rem' }}>Promedio: {stats.avgStock} uds</div>
                    </div>
                    <div className="kpi-card danger">
                        <div className="kpi-icon danger"><AlertTriangle size={20} /></div>
                        <div className="kpi-value">{stats.lowStockCount}</div>
                        <div className="kpi-label">Stock Critico</div>
                        <div className="kpi-change" style={{ color: stats.lowStockCount > 0 ? 'var(--danger)' : 'var(--success)', fontSize: '0.7rem' }}>
                            {stats.alertasActivas} alertas activas
                        </div>
                    </div>
                    <div className="kpi-card success">
                        <div className="kpi-icon success"><TrendingUp size={20} /></div>
                        <div className="kpi-value">+{stats.volEntradas}</div>
                        <div className="kpi-label">Vol. Entradas</div>
                        <div className="kpi-change up" style={{ fontSize: '0.7rem' }}>{stats.entradas} registros</div>
                    </div>
                    <div className="kpi-card warning">
                        <div className="kpi-icon warning"><TrendingDown size={20} /></div>
                        <div className="kpi-value">-{stats.volSalidas}</div>
                        <div className="kpi-label">Vol. Salidas</div>
                        <div className="kpi-change" style={{ color: parseFloat(stats.netFlow) >= 0 ? 'var(--success)' : 'var(--danger)', fontSize: '0.7rem' }}>
                            Flujo neto: {stats.netFlow}
                        </div>
                    </div>
                </div>
            )}

            {/* Suggested Questions */}
            <div style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap', marginBottom: '1rem' }}>
                {SUGGESTED_QUESTIONS.map((q, i) => (
                    <button
                        key={i}
                        className="btn btn-secondary btn-sm"
                        onClick={() => sendMessage(q)}
                        disabled={loading}
                        style={{ fontSize: '0.72rem', display: 'flex', alignItems: 'center', gap: '0.3rem' }}
                    >
                        <Sparkles size={12} /> {q}
                    </button>
                ))}
            </div>

            {/* Chat */}
            <div className="chat-container">
                <div className="chat-messages">
                    {messages.map((msg, i) => (
                        <div key={i} className={`chat-msg ${msg.role === 'user' ? 'user' : 'ai'}`}>
                            <div className={`chat-avatar ${msg.role === 'user' ? 'user' : 'ai'}`}>
                                {msg.role === 'user'
                                    ? (localStorage.getItem('user_name') || 'U').charAt(0).toUpperCase()
                                    : <Bot size={16} />}
                            </div>
                            <div
                                className="chat-bubble"
                                dangerouslySetInnerHTML={{ __html: renderContent(msg.content) }}
                            />
                        </div>
                    ))}

                    {loading && (
                        <div className="chat-msg ai">
                            <div className="chat-avatar ai"><Bot size={16} /></div>
                            <div className="chat-bubble" style={{ display: 'flex', gap: '4px', alignItems: 'center' }}>
                                <span style={{ animation: 'blink 1.4s infinite', animationDelay: '0s' }}>●</span>
                                <span style={{ animation: 'blink 1.4s infinite', animationDelay: '0.2s' }}>●</span>
                                <span style={{ animation: 'blink 1.4s infinite', animationDelay: '0.4s' }}>●</span>
                            </div>
                        </div>
                    )}

                    <div ref={messagesEndRef} />
                </div>

                <div className="chat-input-area">
                    <textarea
                        className="chat-input"
                        placeholder="Pregunta sobre tu inventario, pide predicciones o estadisticas... (Enter para enviar)"
                        value={input}
                        onChange={e => setInput(e.target.value)}
                        onKeyDown={handleKeyDown}
                        rows={1}
                        disabled={loading}
                    />
                    <button
                        className="btn btn-primary"
                        onClick={() => sendMessage()}
                        disabled={loading || !input.trim()}
                        style={{ flexShrink: 0, display: 'flex', alignItems: 'center', gap: '0.4rem' }}
                    >
                        {loading ? <span className="spinner" /> : <><Send size={16} /> Enviar</>}
                    </button>
                </div>
            </div>

            <style>{`
                @keyframes blink {
                    0%, 80%, 100% { opacity: 0.2; transform: scale(0.8); }
                    40% { opacity: 1; transform: scale(1); }
                }
            `}</style>
        </div>
    );
}
