import { useState, useEffect } from 'react';
import api from '../api';
import { Upload } from 'lucide-react';

export default function Salidas() {
    const [salidas, setSalidas] = useState([]);
    const [materias, setMaterias] = useState([]);
    const [materialId, setMaterialId] = useState('');
    const [destino, setDestino] = useState('');
    const [cantidad, setCantidad] = useState('');
    const [lote, setLote] = useState('');
    const [motivo, setMotivo] = useState('');
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
            await api.post('salidas/', {
                material_prima: materialId,
                destino: destino || null,
                cantidad: parseInt(cantidad),
                lote: lote || null,
                motivo: motivo || null,
            });
            loadData();
            setCantidad(''); setDestino(''); setMaterialId(''); setLote(''); setMotivo('');
        } catch (err) { console.error(err); alert('Error al registrar salida.'); }
        finally { setSaving(false); }
    };

    const handleAnular = async (id) => {
        if (!confirm('Anular esta salida? El stock se revertira.')) return;
        try { await api.post(`salidas/${id}/anular/`); loadData(); }
        catch (err) { console.error(err); }
    };

    const totalVolumen = salidas.filter(s => !s.anulado).reduce((acc, s) => acc + parseFloat(s.cantidad || 0), 0);

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Upload size={28} /> Salidas y Despachos</h1>
                <p className="page-subtitle">Registro de consumo y envio a produccion · Volumen total: <strong style={{ color: 'var(--danger)' }}>-{totalVolumen}</strong></p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '340px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header">Registrar Salida</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Materia Prima</label>
                                <select className="form-select" value={materialId} onChange={e => setMaterialId(e.target.value)} required>
                                    <option value="">Seleccione...</option>
                                    {materias.map(m => (
                                        <option key={m.id} value={m.id}>
                                            {m.codigo} - {m.nombre} (stock: {m.cantidad} {m.unidad_medida})
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Destino</label>
                                <input type="text" className="form-control" placeholder="Ej: Produccion linea A" value={destino} onChange={e => setDestino(e.target.value)} />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Cantidad</label>
                                <input type="number" min="1" className="form-control" placeholder="0" value={cantidad} onChange={e => setCantidad(e.target.value)} required />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                                <div className="form-group">
                                    <label className="form-label">Lote (opcional)</label>
                                    <input type="text" className="form-control" placeholder="L-001" value={lote} onChange={e => setLote(e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Motivo</label>
                                    <input type="text" className="form-control" placeholder="Consumo" value={motivo} onChange={e => setMotivo(e.target.value)} />
                                </div>
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Registrar Salida'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        Historial de Salidas
                        <span className="badge badge-warning">{salidas.filter(s => !s.anulado).length} activas</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : salidas.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Upload size={48} /></div>
                                <div className="empty-state-text">Sin salidas registradas</div>
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
                                            <th>Lote</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {salidas.map(s => (
                                            <tr key={s.id} style={s.anulado ? { opacity: 0.5 } : {}}>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>#{s.id}</td>
                                                <td style={{ fontWeight: 500 }}>{s.material_prima_nombre || `Mat. #${s.material_prima}`}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{s.destino || '--'}</td>
                                                <td style={{ color: 'var(--danger)', fontWeight: 700 }}>-{s.cantidad}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{s.lote || '--'}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{s.fecha_salida ? new Date(s.fecha_salida).toLocaleString('es-CO') : '--'}</td>
                                                <td>{s.anulado ? <span className="badge badge-danger">Anulada</span> : <span className="badge badge-success">Completada</span>}</td>
                                                <td>
                                                    {!s.anulado && (
                                                        <button className="btn btn-secondary btn-sm" onClick={() => handleAnular(s.id)} style={{ color: 'var(--danger)', fontSize: '0.75rem' }}>Anular</button>
                                                    )}
                                                </td>
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
