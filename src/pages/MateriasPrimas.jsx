import { useState, useEffect } from 'react';
import api from '../api';
import { Package, Edit2, Trash2 } from 'lucide-react';

export default function MateriasPrimas() {
    const [materias, setMaterias] = useState([]);
    const [ubicaciones, setUbicaciones] = useState([]);
    const [codigo, setCodigo] = useState('');
    const [nombre, setNombre] = useState('');
    const [descripcion, setDescripcion] = useState('');
    const [unidad, setUnidad] = useState('unidad');
    const [minimo, setMinimo] = useState('0');
    const [precio, setPrecio] = useState('0');
    const [lote, setLote] = useState('');
    const [fechaCaducidad, setFechaCaducidad] = useState('');
    const [ubicacionId, setUbicacionId] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [editId, setEditId] = useState(null);

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const [mRes, uRes] = await Promise.all([
                api.get('material-primas/'),
                api.get('ubicaciones/'),
            ]);
            setMaterias(mRes.data);
            setUbicaciones(uRes.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const resetForm = () => {
        setCodigo(''); setNombre(''); setDescripcion(''); setUnidad('unidad');
        setMinimo('0'); setPrecio('0'); setLote(''); setFechaCaducidad(''); setUbicacionId('');
        setEditId(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            const payload = {
                codigo, nombre, descripcion: descripcion || null,
                unidad_medida: unidad, stock_minimo: parseInt(minimo) || 0,
                precio: parseFloat(precio) || 0, lote: lote || null,
                fecha_caducidad: fechaCaducidad || null,
                ubicacion: ubicacionId || null,
            };
            if (editId) {
                await api.put(`material-primas/${editId}/`, payload);
            } else {
                payload.cantidad = 0;
                await api.post('material-primas/', payload);
            }
            loadData();
            resetForm();
        } catch (err) { 
            console.error(err); 
            const errorMsg = err.response?.data ? JSON.stringify(err.response.data) : err.message;
            alert(`Error: ${errorMsg}`); 
        }
        finally { setSaving(false); }
    };

    const handleEdit = (m) => {
        setEditId(m.id);
        setCodigo(m.codigo);
        setNombre(m.nombre);
        setDescripcion(m.descripcion || '');
        setUnidad(m.unidad_medida || 'unidad');
        setMinimo(String(m.stock_minimo));
        setPrecio(String(m.precio));
        setLote(m.lote || '');
        setFechaCaducidad(m.fecha_caducidad || '');
        setUbicacionId(m.ubicacion || '');
    };

    const handleDelete = async (id) => {
        if (!confirm('Eliminar esta materia prima?')) return;
        try { await api.delete(`material-primas/${id}/`); loadData(); }
        catch (err) { console.error(err); }
    };

    const lowCount = materias.filter(m => m.cantidad <= m.stock_minimo).length;

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Package size={28} /> Inventario</h1>
                <p className="page-subtitle">Gestion de materias primas · {materias.length} registros
                    {lowCount > 0 && <span className="badge badge-danger" style={{ marginLeft: '0.75rem' }}>⚠ {lowCount} con stock bajo</span>}
                </p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '340px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header">{editId ? 'Editar Materia Prima' : 'Nueva Materia Prima'}</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Codigo</label>
                                <input type="text" className="form-control" placeholder="Ej: MAT-01" value={codigo} onChange={e => setCodigo(e.target.value)} required disabled={!!editId} />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Nombre</label>
                                <input type="text" className="form-control" placeholder="Ej: Cemento Portland" value={nombre} onChange={e => setNombre(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Descripcion</label>
                                <textarea className="form-control" rows={2} placeholder="Descripcion del material..." value={descripcion} onChange={e => setDescripcion(e.target.value)} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                                <div className="form-group">
                                    <label className="form-label">Unidad Medida</label>
                                    <select className="form-select" value={unidad} onChange={e => setUnidad(e.target.value)}>
                                        <option value="unidad">Unidad</option>
                                        <option value="kg">Kilogramo</option>
                                        <option value="litro">Litro</option>
                                        <option value="metro">Metro</option>
                                        <option value="galón">Galon</option>
                                        <option value="caja">Caja</option>
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Stock Minimo</label>
                                    <input type="number" min="0" className="form-control" value={minimo} onChange={e => setMinimo(e.target.value)} />
                                </div>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                                <div className="form-group">
                                    <label className="form-label">Precio</label>
                                    <input type="number" step="0.01" min="0" className="form-control" value={precio} onChange={e => setPrecio(e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Lote</label>
                                    <input type="text" className="form-control" placeholder="Ej: L-2024-01" value={lote} onChange={e => setLote(e.target.value)} />
                                </div>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                                <div className="form-group">
                                    <label className="form-label">Fecha Caducidad</label>
                                    <input type="date" className="form-control" value={fechaCaducidad} onChange={e => setFechaCaducidad(e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Ubicacion</label>
                                    <select className="form-select" value={ubicacionId} onChange={e => setUbicacionId(e.target.value)}>
                                        <option value="">Sin ubicacion</option>
                                        {ubicaciones.map(u => <option key={u.id} value={u.id}>{u.pasillo}-{u.estante}-{u.casillero}</option>)}
                                    </select>
                                </div>
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : (editId ? 'Actualizar' : 'Guardar')}
                            </button>
                            {editId && <button type="button" className="btn btn-secondary btn-block" style={{ marginTop: '0.5rem' }} onClick={resetForm}>Cancelar</button>}
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        Lista de Materias Primas
                        <span className="badge badge-accent">{materias.length}</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : materias.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Package size={48} /></div>
                                <div className="empty-state-text">Sin materias primas registradas</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Cantidad</th>
                                            <th>Min.</th>
                                            <th>Precio</th>
                                            <th>Ubicacion</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {materias.map(m => {
                                            const isLow = m.cantidad <= m.stock_minimo;
                                            return (
                                                <tr key={m.id}>
                                                    <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem', fontFamily: 'monospace' }}>{m.codigo}</td>
                                                    <td style={{ fontWeight: 600 }}>{m.nombre}</td>
                                                    <td style={{ fontWeight: 700 }}>{m.cantidad} <span style={{ color: 'var(--text-muted)', fontSize: '0.75rem' }}>{m.unidad_medida}</span></td>
                                                    <td style={{ color: 'var(--text-muted)' }}>{m.stock_minimo}</td>
                                                    <td style={{ color: 'var(--text-secondary)' }}>${Number(m.precio).toLocaleString()}</td>
                                                    <td style={{ color: 'var(--text-secondary)', fontSize: '0.8rem' }}>{m.ubicacion_nombre || '--'}</td>
                                                    <td>{isLow ? <span className="badge badge-danger">Bajo</span> : <span className="badge badge-success">OK</span>}</td>
                                                    <td>
                                                        <div style={{ display: 'flex', gap: '0.4rem' }}>
                                                            <button className="btn btn-secondary btn-sm" onClick={() => handleEdit(m)}><Edit2 size={14} /></button>
                                                            <button className="btn btn-secondary btn-sm" onClick={() => handleDelete(m.id)} style={{ color: 'var(--danger)' }}><Trash2 size={14} /></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
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
