import { useState, useEffect } from 'react';
import api from '../api';

export default function Proveedores() {
    const [proveedores, setProveedores] = useState([]);
    const [nombre, setNombre] = useState('');
    const [contacto, setContacto] = useState('');
    const [email, setEmail] = useState('');
    const [telefono, setTelefono] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const res = await api.get('proveedores/');
            setProveedores(res.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await api.post('proveedores/', { nombre, contacto, email, telefono });
            loadData();
            setNombre(''); setContacto(''); setEmail(''); setTelefono('');
        } catch (err) { console.error(err); }
        finally { setSaving(false); }
    };

    const validados = proveedores.filter(p => p.estado_validacion).length;

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title">Proveedores</h1>
                <p className="page-subtitle">Directorio de proveedores · {proveedores.length} registrados · {validados} validados</p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '320px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header">🏭 Nuevo Proveedor</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Razón Social</label>
                                <input type="text" className="form-control" placeholder="Empresa S.A.S" value={nombre} onChange={e => setNombre(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Persona de Contacto</label>
                                <input type="text" className="form-control" placeholder="Nombre del contacto" value={contacto} onChange={e => setContacto(e.target.value)} />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Teléfono</label>
                                <input type="text" className="form-control" placeholder="+57 300 000 0000" value={telefono} onChange={e => setTelefono(e.target.value)} />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Email</label>
                                <input type="email" className="form-control" placeholder="contacto@empresa.com" value={email} onChange={e => setEmail(e.target.value)} />
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Guardar'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        Lista de Proveedores
                        <span className="badge badge-accent" style={{ marginLeft: 'auto' }}>{proveedores.length}</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : proveedores.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Factory size={48} /></div>
                                <div className="empty-state-text">Sin proveedores registrados</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Empresa</th>
                                            <th>Contacto</th>
                                            <th>Teléfono</th>
                                            <th>Email</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {proveedores.map(p => (
                                            <tr key={p.id}>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>#{p.id}</td>
                                                <td style={{ fontWeight: 600 }}>{p.nombre}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{p.contacto || '—'}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{p.telefono || '—'}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{p.email || '—'}</td>
                                                <td>
                                                    {p.estado_validacion
                                                        ? <span className="badge badge-success">✓ Validado</span>
                                                        : <span className="badge badge-warning">Pendiente</span>
                                                    }
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
