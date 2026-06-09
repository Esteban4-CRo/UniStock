import { useState, useEffect } from 'react';
import api from '../api';
import { MapPin, Search, Filter, Plus } from 'lucide-react';

export default function Ubicaciones() {
    const [ubicaciones, setUbicaciones] = useState([]);
    const [nombre, setNombre] = useState('');
    const [descripcion, setDescripcion] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const res = await api.get('ubicaciones/');
            setUbicaciones(res.data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await api.post('ubicaciones/', { nombre, descripcion });
            loadData();
            setNombre(''); setDescripcion('');
        } catch (err) {
            console.error(err);
        } finally {
            setSaving(false);
        }
    };

    return (
        <div>
            <div className="page-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <div>
                    <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><MapPin size={28} /> Ubicaciones</h1>
                    <p className="page-subtitle">Zonas y estantes físicos dentro del almacén</p>
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '320px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Plus size={16} /> Nueva Ubicación</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Nombre (Ej: Pasillo A)</label>
                                <input type="text" className="form-control" value={nombre} onChange={e=>setNombre(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Descripción</label>
                                <textarea className="form-control" rows={3} value={descripcion} onChange={e=>setDescripcion(e.target.value)}></textarea>
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Guardar'}
                            </button>
                        </form>
                    </div>
                </div>
                
                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                            <div className="form-control" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '0.4rem 0.8rem', width: '250px' }}>
                                <Search size={14} color="var(--text-muted)" />
                                <input type="text" placeholder="Buscar ubicación..." style={{ border: 'none', outline: 'none', background: 'transparent', width: '100%', fontSize: '0.85rem' }} />
                            </div>
                            <button className="btn btn-secondary btn-sm" style={{ display: 'flex', alignItems: 'center', gap: '0.4rem' }}><Filter size={14} /> Filtros</button>
                        </div>
                        <span className="badge badge-neutral">{ubicaciones.length} registros</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state">
                                <span className="spinner dark" style={{ width: 24, height: 24, borderWidth: 2 }} />
                            </div>
                        ) : ubicaciones.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><MapPin size={48} /></div>
                                <div className="empty-state-title">Sin ubicaciones</div>
                                <div className="empty-state-text">No hay ubicaciones registradas en el sistema</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {ubicaciones.map(u => (
                                            <tr key={u.id}>
                                                <td style={{ color: 'var(--text-muted)' }}>#{u.id}</td>
                                                <td style={{ fontWeight: 600 }}>{u.nombre}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{u.descripcion || '—'}</td>
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
