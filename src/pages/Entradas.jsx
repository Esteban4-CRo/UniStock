import { useState, useEffect } from 'react';
import api from '../api';

export default function Entradas() {
    const [entradas, setEntradas] = useState([]);
    const [materias, setMaterias] = useState([]);
    const [proveedores, setProveedores] = useState([]);
    const [materialId, setMaterialId] = useState('');
    const [proveedorId, setProveedorId] = useState('');
    const [cantidad, setCantidad] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const [eRes, mRes, pRes] = await Promise.all([
                api.get('entradas/'),
                api.get('material-primas/'),
                api.get('proveedores/'),
            ]);
            setEntradas(eRes.data);
            setMaterias(mRes.data);
            setProveedores(pRes.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await api.post('entradas/', { material_prima: materialId, proveedor: proveedorId || null, cantidad });
            loadData();
            setCantidad(''); setMaterialId(''); setProveedorId('');
        } catch (err) { console.error(err); }
        finally { setSaving(false); }
    };

    const totalVolumen = entradas.filter(e => !e.anulado).reduce((acc, e) => acc + parseFloat(e.cantidad || 0), 0);

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title">Entradas</h1>
                <p className="page-subtitle">Registro de entradas al inventario · Volumen total: <strong style={{ color: 'var(--success)' }}>+{totalVolumen.toFixed(2)}</strong></p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '320px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header">📥 Registrar Entrada</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Materia Prima</label>
                                <select className="form-select" value={materialId} onChange={e => setMaterialId(e.target.value)} required>
                                    <option value="">Seleccione...</option>
                                    {materias.map(m => <option key={m.id} value={m.id}>{m.nombre}</option>)}
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Proveedor (opcional)</label>
                                <select className="form-select" value={proveedorId} onChange={e => setProveedorId(e.target.value)}>
                                    <option value="">Ninguno</option>
                                    {proveedores.map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Cantidad</label>
                                <input type="number" step="0.01" min="0.01" className="form-control" placeholder="0.00" value={cantidad} onChange={e => setCantidad(e.target.value)} required />
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Registrar Entrada'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header">
                        Historial de Entradas
                        <span className="badge badge-success" style={{ marginLeft: 'auto' }}>{entradas.filter(e => !e.anulado).length} activas</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : entradas.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon">📥</div>
                                <div className="empty-state-text">Sin entradas registradas</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Materia Prima</th>
                                            <th>Cantidad</th>
                                            <th>Proveedor</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {entradas.map(e => (
                                            <tr key={e.id}>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>#{e.id}</td>
                                                <td style={{ fontWeight: 500 }}>{e.material_prima_nombre || `Mat. #${e.material_prima}`}</td>
                                                <td style={{ color: 'var(--success)', fontWeight: 700 }}>+{e.cantidad}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{e.proveedor_nombre || '—'}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{new Date(e.fecha_entrada).toLocaleString('es-CO')}</td>
                                                <td>{e.anulado ? <span className="badge badge-danger">Anulada</span> : <span className="badge badge-success">✓ Completada</span>}</td>
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
