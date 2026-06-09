import { useState, useEffect } from 'react';
import api from '../api';
import { Upload, Search, Filter, Plus } from 'lucide-react';

export default function Salidas() {
    const [salidas, setSalidas] = useState([]);
    const [materias, setMaterias] = useState([]);
    const [materialId, setMaterialId] = useState('');
    const [destino, setDestino] = useState('');
    const [cantidad, setCantidad] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const [sRes, mRes] = await Promise.all([
                api.get('salidas/'),
                api.get('material-primas/'),
            ]);
            setSalidas(sRes.data);
            setMaterias(mRes.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await api.post('salidas/', { material_prima: materialId, destino, cantidad });
            loadData();
            setCantidad(''); setDestino(''); setMaterialId('');
        } catch (err) { console.error(err); }
        finally { setSaving(false); }
    };

    const totalVolumen = salidas.filter(s => !s.anulado).reduce((acc, s) => acc + parseFloat(s.cantidad || 0), 0);

    return (
        <div>
            <div className="page-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <div>
                    <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Upload size={28} /> Salidas y Despachos</h1>
                    <p className="page-subtitle">Registro de consumo y envío a producción · Volumen total: <strong style={{ color: 'var(--danger)' }}>-{totalVolumen.toFixed(2)}</strong></p>
                </div>
            </div>

            <div style={{ display: 'flex', flexWrap: 'wrap', gap: '1rem', alignItems: 'flex-start' }}>
                <div className="card" style={{ flex: '1 1 320px', minWidth: '300px' }}>
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Plus size={16} /> Registrar Salida</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Materia Prima</label>
                                <select className="form-select" value={materialId} onChange={e => setMaterialId(e.target.value)} required>
                                    <option value="">Seleccione...</option>
                                    {materias.map(m => (
                                        <option key={m.id} value={m.id}>
                                            {m.nombre} (stock: {m.stock_actual} {m.unidad_medida})
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Destino</label>
                                <input type="text" className="form-control" placeholder="Ej: Producción línea A" value={destino} onChange={e => setDestino(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Cantidad</label>
                                <input type="number" step="0.01" min="0.01" className="form-control" placeholder="0.00" value={cantidad} onChange={e => setCantidad(e.target.value)} required />
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Registrar Salida'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="card" style={{ flex: '2 1 600px', minWidth: '300px' }}>
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                            <div className="form-control" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '0.4rem 0.8rem', width: '250px' }}>
                                <Search size={14} color="var(--text-muted)" />
                                <input type="text" placeholder="Buscar salida..." style={{ border: 'none', outline: 'none', background: 'transparent', width: '100%', fontSize: '0.85rem' }} />
                            </div>
                            <button className="btn btn-secondary btn-sm" style={{ display: 'flex', alignItems: 'center', gap: '0.4rem' }}><Filter size={14} /> Filtros</button>
                        </div>
                        <span className="badge badge-warning" style={{ marginLeft: 'auto' }}>{salidas.filter(s => !s.anulado).length} activas</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state">
                                <span className="spinner dark" style={{ width: 24, height: 24, borderWidth: 2 }} />
                            </div>
                        ) : salidas.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Upload size={48} /></div>
                                <div className="empty-state-title">Sin salidas</div>
                                <div className="empty-state-text">No hay salidas registradas en el sistema</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Materia Prima</th>
                                            <th>Destino</th>
                                            <th>Cantidad</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {salidas.map(s => (
                                            <tr key={s.id}>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>#{s.id}</td>
                                                <td style={{ fontWeight: 500 }}>{s.material_prima_nombre || `Mat. #${s.material_prima}`}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{s.destino}</td>
                                                <td style={{ color: 'var(--danger)', fontWeight: 700 }}>-{s.cantidad}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{new Date(s.fecha_salida).toLocaleString('es-CO')}</td>
                                                <td>{s.anulado ? <span className="badge badge-danger">Anulada</span> : <span className="badge badge-success">✓ Completada</span>}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
