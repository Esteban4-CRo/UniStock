import { useState, useEffect } from 'react';
import api from '../api';
import { PackageOpen, AlertTriangle, Download, Upload, CheckCircle2, RotateCw, Factory, CheckCircle } from 'lucide-react';

export default function Dashboard() {
    const [materias, setMaterias]   = useState([]);
    const [alertas, setAlertas]     = useState([]);
    const [entradas, setEntradas]   = useState([]);
    const [salidas, setSalidas]     = useState([]);
    const [loading, setLoading]     = useState(true);
    
    const userRole = localStorage.getItem('user_role') || '';

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            if (userRole === 'proveedor') {
                // Proveedores solo ven sus salidas (despachos)
                const sRes = await api.get('salidas/');
                setSalidas(sRes.data);
            } else {
                // gerente, admin, almacenista, o cualquier otro rol: cargar todo
                const [mRes, aRes, eRes, sRes] = await Promise.all([
                    api.get('material-primas/'),
                    api.get('alertas/'),
                    api.get('entradas/'),
                    api.get('salidas/'),
                ]);
                setMaterias(mRes.data);
                setAlertas(aRes.data);
                setEntradas(eRes.data);
                setSalidas(sRes.data);
            }
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const lowStock       = materias.filter(m => parseFloat(m.cantidad) <= parseFloat(m.stock_minimo));
    const entradasMes    = entradas.filter(e => !e.anulado);
    const salidasMes     = salidas.filter(s => !s.anulado);
    const volEntradas    = entradasMes.reduce((a, e) => a + parseFloat(e.cantidad||0), 0);
    const volSalidas     = salidasMes.reduce((a, s) => a + parseFloat(s.cantidad||0), 0);

    if (loading) return (
        <div style={{ display:'flex', alignItems:'center', justifyContent:'center', height:'60vh' }}>
            <div style={{ textAlign:'center' }}>
                <div className="spinner dark" style={{ width:36, height:36, borderWidth:3, margin:'0 auto 0.875rem' }} />
                <p style={{ color:'var(--text-muted)', fontSize:'0.85rem' }}>Cargando datos...</p>
            </div>
        </div>
    );

    return (
        <div>
            {/* Header */}
            <div className="page-header">
                <div style={{ display:'flex', justifyContent:'space-between', alignItems:'flex-end' }}>
                    <div>
                        <h1 className="page-title">Dashboard</h1>
                        <p className="page-subtitle">
                            {new Date().toLocaleDateString('es-CO', { weekday:'long', year:'numeric', month:'long', day:'numeric' })}
                        </p>
                    </div>
                    <button className="btn btn-primary btn-sm" onClick={loadData} style={{ display: 'flex', alignItems: 'center', gap: '0.4rem' }}>
                        <RotateCw size={14} /> Actualizar
                    </button>
                </div>
            </div>

            {/* KPIs */}
            <div className="grid-4" style={{ marginBottom:'1.5rem' }}>
                {userRole !== 'proveedor' && (
                    <>
                        <div className="kpi-card accent">
                            <div className="kpi-icon accent"><PackageOpen size={24} /></div>
                            <div className="kpi-value">{materias.length}</div>
                            <div className="kpi-label">Materias Primas</div>
                            <div className="kpi-change up">Registradas en sistema</div>
                        </div>
                        <div className="kpi-card danger">
                            <div className="kpi-icon danger"><AlertTriangle size={24} /></div>
                            <div className="kpi-value">{lowStock.length}</div>
                            <div className="kpi-label">Stock Crítico</div>
                            <div className="kpi-change" style={{ color: lowStock.length > 0 ? 'var(--danger)' : 'var(--success)' }}>
                                {lowStock.length > 0 ? `${lowStock.length} requieren atención` : 'Todo en niveles normales'}
                            </div>
                        </div>
                        <div className="kpi-card success">
                            <div className="kpi-icon success"><Download size={24} /></div>
                            <div className="kpi-value">{entradasMes.length}</div>
                            <div className="kpi-label">Entradas</div>
                            <div className="kpi-change up">+{volEntradas.toFixed(1)} unidades totales</div>
                        </div>
                    </>
                )}
                
                <div className="kpi-card warning">
                    <div className="kpi-icon warning"><Upload size={24} /></div>
                    <div className="kpi-value">{salidasMes.length}</div>
                    <div className="kpi-label">Despachos (Salidas)</div>
                    <div className="kpi-change down">-{volSalidas.toFixed(1)} unidades totales</div>
                </div>
            </div>

            {userRole !== 'proveedor' && (
                <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:'1rem', marginBottom:'1rem' }}>
                    {/* Alertas activas */}
                    <div className="card">
                        <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <AlertTriangle size={18} /> Alertas Activas
                            {alertas.length > 0
                                ? <span className="badge badge-danger" style={{ marginLeft:'auto' }}>{alertas.length} pendientes</span>
                                : <span className="badge badge-success" style={{ marginLeft:'auto' }}>Sin alertas</span>
                            }
                        </div>
                        <div className="card-body" style={{ padding:0 }}>
                            {alertas.length === 0 ? (
                                <div className="empty-state">
                                    <div className="empty-state-icon" style={{ color: 'var(--success)' }}><CheckCircle2 size={48} /></div>
                                    <div className="empty-state-title">Todo en orden</div>
                                    <div className="empty-state-text">No hay alertas de stock bajo</div>
                                </div>
                            ) : (
                                <div style={{ maxHeight:300, overflowY:'auto' }}>
                                    {alertas.map(a => (
                                        <div key={a.id} style={{
                                            padding:'0.8rem 1.1rem',
                                            borderBottom:'1px solid #f5f5f5',
                                            display:'flex', gap:'0.75rem', alignItems:'flex-start',
                                        }}>
                                            <span style={{ flexShrink:0, color: 'var(--danger)' }}><AlertTriangle size={16} /></span>
                                            <div>
                                                <div style={{ fontSize:'0.825rem', fontWeight:600, color:'var(--text-primary)' }}>{a.mensaje}</div>
                                                <div style={{ fontSize:'0.7rem', color:'var(--text-muted)', marginTop:'0.15rem' }}>
                                                    {a.created_at ? new Date(a.created_at).toLocaleString('es-CO') : ''}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Niveles de inventario */}
                    <div className="card">
                        <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <PackageOpen size={18} /> Niveles de Inventario
                            <span className="badge badge-neutral" style={{ marginLeft:'auto' }}>{materias.length} ítems</span>
                        </div>
                        <div className="card-body" style={{ padding:0 }}>
                            {materias.length === 0 ? (
                                <div className="empty-state">
                                    <div className="empty-state-icon"><PackageOpen size={48} /></div>
                                    <div className="empty-state-title">Sin materias primas</div>
                                    <div className="empty-state-text">Agrega tu primera materia prima</div>
                                </div>
                            ) : (
                                <div style={{ maxHeight:300, overflowY:'auto' }}>
                                    {materias.map(m => {
                                        const actual  = parseFloat(m.cantidad);
                                        const minimo  = parseFloat(m.stock_minimo);
                                        const isLow   = actual <= minimo;
                                        const pct     = minimo > 0 ? Math.min(100, (actual / (minimo * 2)) * 100) : (actual > 0 ? 80 : 0);
                                        return (
                                            <div key={m.id} style={{ padding:'0.8rem 1.1rem', borderBottom:'1px solid #f5f5f5' }}>
                                                <div style={{ display:'flex', justifyContent:'space-between', marginBottom:'0.3rem', alignItems:'center' }}>
                                                    <span style={{ fontSize:'0.825rem', fontWeight:600 }}>{m.nombre}</span>
                                                    <span style={{ fontSize:'0.75rem', fontWeight:700, color: isLow ? 'var(--danger)' : 'var(--success)' }}>
                                                        {m.cantidad} {m.unidad_medida}
                                                    </span>
                                                </div>
                                                <div className="progress">
                                                    <div
                                                        className={`progress-bar ${isLow ? 'danger' : 'success'}`}
                                                        style={{ width:`${pct}%` }}
                                                    />
                                                </div>
                                                <div style={{ display:'flex', justifyContent:'space-between', marginTop:'0.2rem' }}>
                                                    <span style={{ fontSize:'0.65rem', color:'var(--text-muted)' }}>Mín: {m.stock_minimo}</span>
                                                    {isLow && <span style={{ fontSize:'0.65rem', color:'var(--danger)', fontWeight:700, display: 'flex', alignItems: 'center', gap: '0.2rem' }}><AlertTriangle size={10} /> BAJO</span>}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {/* Últimas entradas y salidas */}
            <div style={{ display:'grid', gridTemplateColumns: userRole !== 'proveedor' ? '1fr 1fr' : '1fr', gap:'1rem' }}>
                {userRole !== 'proveedor' && (
                    <div className="card">
                        <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <Download size={18} /> Últimas Entradas
                            <span className="badge badge-success" style={{ marginLeft:'auto' }}>{entradasMes.length} registros</span>
                        </div>
                        <div className="card-body" style={{ padding:0 }}>
                            {entradasMes.length === 0 ? (
                                <div className="empty-state"><div className="empty-state-text">Sin entradas registradas</div></div>
                            ) : (
                                <table className="data-table">
                                    <thead><tr><th>Material</th><th>Cantidad</th><th>Fecha</th></tr></thead>
                                    <tbody>
                                        {entradasMes.slice(0,5).map(e => (
                                            <tr key={e.id}>
                                                <td style={{ fontWeight:500 }}>{e.material_prima_nombre || `#${e.material_prima}`}</td>
                                                <td className="text-success fw-700">+{e.cantidad}</td>
                                                <td style={{ color:'var(--text-muted)', fontSize:'0.78rem' }}>{new Date(e.fecha_entrada).toLocaleDateString('es-CO')}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </div>
                    </div>
                )}

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        <Upload size={18} /> Últimos Despachos
                        <span className="badge badge-warning" style={{ marginLeft:'auto' }}>{salidasMes.length} registros</span>
                    </div>
                    <div className="card-body" style={{ padding:0 }}>
                        {salidasMes.length === 0 ? (
                            <div className="empty-state"><div className="empty-state-text">Sin despachos registrados</div></div>
                        ) : (
                            <table className="data-table">
                                <thead><tr><th>Material</th><th>Destino</th><th>Cantidad</th></tr></thead>
                                <tbody>
                                    {salidasMes.slice(0,5).map(s => (
                                        <tr key={s.id}>
                                            <td style={{ fontWeight:500 }}>{s.material_prima_nombre || `#${s.material_prima}`}</td>
                                            <td style={{ color:'var(--text-secondary)', fontSize:'0.8rem' }}>{s.destino}</td>
                                            <td className="text-danger fw-700">-{s.cantidad}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

