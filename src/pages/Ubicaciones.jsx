import { useState, useEffect } from 'react';
import api from '../api';
import { MapPin, Search, Filter, Plus, Edit2, Trash2 } from 'lucide-react';

export default function Ubicaciones() {
    const [ubicaciones, setUbicaciones] = useState([]);
    const [pasillo, setPasillo] = useState('');
    const [estante, setEstante] = useState('');
    const [casillero, setCasillero] = useState('');
    const [descripcion, setDescripcion] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [editId, setEditId] = useState(null);
    const [search, setSearch] = useState('');

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const res = await api.get('ubicaciones/');
            setUbicaciones(res.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const resetForm = () => {
        setPasillo(''); setEstante(''); setCasillero(''); setDescripcion('');
        setEditId(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            const payload = { pasillo, estante, casillero, descripcion };
            if (editId) {
                await api.put(`ubicaciones/${editId}/`, payload);
            } else {
                await api.post('ubicaciones/', payload);
            }
            loadData();
            resetForm();
        } catch (err) { console.error(err); }
        finally { setSaving(false); }
    };

    const handleEdit = (u) => {
        setEditId(u.id);
        setPasillo(u.pasillo);
        setEstante(u.estante);
        setCasillero(u.casillero);
        setDescripcion(u.descripcion || '');
    };

    const handleDelete = async (id) => {
        if (!confirm('Eliminar esta ubicacion?')) return;
        try { await api.delete(`ubicaciones/${id}/`); loadData(); }
        catch (err) { console.error(err); }
    };

    const filtered = ubicaciones.filter(u =>
        !search || [u.pasillo, u.estante, u.casillero, u.descripcion].join(' ').toLowerCase().includes(search.toLowerCase())
    );

    return (
        <div>
            <div className="page-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <div>
                    <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><MapPin size={28} /> Ubicaciones</h1>
                    <p className="page-subtitle">Zonas y estantes fisicos del almacen · {ubicaciones.length} registros</p>
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '340px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        <Plus size={16} /> {editId ? 'Editar Ubicacion' : 'Nueva Ubicacion'}
                    </div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Pasillo</label>
                                <input type="text" className="form-control" placeholder="Ej: A" value={pasillo} onChange={e => setPasillo(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Estante</label>
                                <input type="text" className="form-control" placeholder="Ej: 1" value={estante} onChange={e => setEstante(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Casillero</label>
                                <input type="text" className="form-control" placeholder="Ej: 01" value={casillero} onChange={e => setCasillero(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Descripcion (opcional)</label>
                                <textarea className="form-control" rows={2} value={descripcion} onChange={e => setDescripcion(e.target.value)}></textarea>
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
                        <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                            <div className="form-control" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '0.4rem 0.8rem', width: '250px' }}>
                                <Search size={14} color="var(--text-muted)" />
                                <input type="text" placeholder="Buscar ubicacion..." value={search} onChange={e => setSearch(e.target.value)} style={{ border: 'none', outline: 'none', background: 'transparent', width: '100%', fontSize: '0.85rem' }} />
                            </div>
                        </div>
                        <span className="badge badge-neutral">{filtered.length} registros</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><span className="spinner dark" style={{ width: 24, height: 24, borderWidth: 2 }} /></div>
                        ) : filtered.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><MapPin size={48} /></div>
                                <div className="empty-state-title">Sin ubicaciones</div>
                                <div className="empty-state-text">No hay ubicaciones registradas</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>Pasillo</th>
                                            <th>Estante</th>
                                            <th>Casillero</th>
                                            <th>Descripcion</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filtered.map(u => (
                                            <tr key={u.id}>
                                                <td style={{ fontWeight: 600 }}>{u.pasillo}</td>
                                                <td>{u.estante}</td>
                                                <td>{u.casillero}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{u.descripcion || '--'}</td>
                                                <td>{u.activo !== false ? <span className="badge badge-success">Activo</span> : <span className="badge badge-danger">Inactivo</span>}</td>
                                                <td>
                                                    <div style={{ display: 'flex', gap: '0.4rem' }}>
                                                        <button className="btn btn-secondary btn-sm" onClick={() => handleEdit(u)} title="Editar"><Edit2 size={14} /></button>
                                                        <button className="btn btn-secondary btn-sm" onClick={() => handleDelete(u.id)} title="Eliminar" style={{ color: 'var(--danger)' }}><Trash2 size={14} /></button>
                                                    </div>
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
