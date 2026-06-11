import { useState, useEffect } from 'react';
import api from '../api';
import { Download } from 'lucide-react';

export default function Entradas() {
    const [entradas, setEntradas] = useState([]);
    const [materias, setMaterias] = useState([]);
    const [proveedores, setProveedores] = useState([]);
    const [materialId, setMaterialId] = useState('');
    const [proveedorId, setProveedorId] = useState('');
    const [cantidad, setCantidad] = useState('');
    const [lote, setLote] = useState('');
    const [fechaCaducidad, setFechaCaducidad] = useState('');
    const [motivo, setMotivo] = useState('');
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
            await api.post('entradas/', {
                material_prima: materialId,
                proveedor: proveedorId,
                cantidad: parseInt(cantidad),
                lote: lote || null,
                fecha_caducidad: fechaCaducidad || null,
                motivo: motivo || null,
            });
            loadData();
            setCantidad(''); setMaterialId(''); setProveedorId(''); setLote(''); setFechaCaducidad(''); setMotivo('');
        } catch (err) { console.error(err); alert('Error al registrar entrada.'); }
        finally { setSaving(false); }
    };

    const handleAnular = async (id) => {
        if (!confirm('Anular esta entrada? El stock se revertira.')) return;
        try { await api.post(`entradas/${id}/anular/`); loadData(); }
        catch (err) { console.error(err); }
    };

    const totalVolumen = entradas.filter(e => !e.anulado).reduce((acc, e) => acc + parseFloat(e.cantidad || 0), 0);

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Download size={28} /> Entradas</h1>
                <p className="page-subtitle">Registro de entradas al inventario · Volumen total: <strong style={{ color: 'var(--success)' }}>+{totalVolumen}</strong></p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '340px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header">Registrar Entrada</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Materia Prima</label>
                                <select className="form-select" value={materialId} onChange={e => setMaterialId(e.target.value)} required>
                                    <option value="">Seleccione...</option>
                                    {materias.map(m => <option key={m.id} value={m.id}>{m.codigo} - {m.nombre}</option>)}
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Proveedor</label>
                                <select className="form-select" value={proveedorId} onChange={e => setProveedorId(e.target.value)} required>
                                    <option value="">Seleccione...</option>
                                    {proveedores.map(p => <option key={p.id} value={p.id}>{p.empresa}</option>)}
                                </select>
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
                                    <label className="form-label">Caducidad</label>
                                    <input type="date" className="form-control" value={fechaCaducidad} onChange={e => setFechaCaducidad(e.target.value)} />
                                </div>
                            </div>
                            <div className="form-group">
                                <label className="form-label">Motivo (opcional)</label>
                                <textarea className="form-control" rows={2} placeholder="Reabastecimiento, devolucion..." value={motivo} onChange={e => setMotivo(e.target.value)} />
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Registrar Entrada'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        Historial de Entradas
                        <span className="badge badge-success">{entradas.filter(e => !e.anulado).length} activas</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : entradas.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Download size={48} /></div>
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
                                            <th>Lote</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {entradas.map(e => (
                                            <tr key={e.id} style={e.anulado ? { opacity: 0.5 } : {}}>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>#{e.id}</td>
                                                <td style={{ fontWeight: 500 }}>{e.material_prima_nombre || `Mat. #${e.material_prima}`}</td>
                                                <td style={{ color: 'var(--success)', fontWeight: 700 }}>+{e.cantidad}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{e.proveedor_nombre || '--'}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{e.lote || '--'}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{e.fecha_entrada ? new Date(e.fecha_entrada).toLocaleString('es-CO') : '--'}</td>
                                                <td>{e.anulado ? <span className="badge badge-danger">Anulada</span> : <span className="badge badge-success">Completada</span>}</td>
                                                <td>
                                                    {!e.anulado && (
                                                        <button className="btn btn-secondary btn-sm" onClick={() => handleAnular(e.id)} style={{ color: 'var(--danger)', fontSize: '0.75rem' }}>Anular</button>
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
