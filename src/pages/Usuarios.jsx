import { useState, useEffect } from 'react';
import api from '../api';
import { Users, Search, Filter, UserPlus } from 'lucide-react';

export default function Usuarios() {
    const [usuarios, setUsuarios] = useState([]);
    const [username, setUsername] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const res = await api.get('users/');
            setUsuarios(res.data);
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
            await api.post('users/', { username, email, password });
            loadData();
            setUsername(''); setEmail(''); setPassword('');
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
                    <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Users size={28} /> Usuarios</h1>
                    <p className="page-subtitle">Gestión de cuentas y accesos al sistema</p>
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '320px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><UserPlus size={16} /> Nuevo Usuario</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Username</label>
                                <input type="text" className="form-control" placeholder="juanperez" value={username} onChange={e=>setUsername(e.target.value.toLowerCase().replace(/\s/g,''))} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Email</label>
                                <input type="email" className="form-control" placeholder="juan@empresa.com" value={email} onChange={e=>setEmail(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Password</label>
                                <input type="password" className="form-control" placeholder="Mínimo 6 caracteres" value={password} onChange={e=>setPassword(e.target.value)} required />
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Creando...</> : 'Crear Usuario'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                            <div className="form-control" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '0.4rem 0.8rem', width: '250px' }}>
                                <Search size={14} color="var(--text-muted)" />
                                <input type="text" placeholder="Buscar usuario..." style={{ border: 'none', outline: 'none', background: 'transparent', width: '100%', fontSize: '0.85rem' }} />
                            </div>
                            <button className="btn btn-secondary btn-sm" style={{ display: 'flex', alignItems: 'center', gap: '0.4rem' }}><Filter size={14} /> Filtros</button>
                        </div>
                        <span className="badge badge-neutral">{usuarios.length} cuentas</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state">
                                <span className="spinner dark" style={{ width: 24, height: 24, borderWidth: 2 }} />
                            </div>
                        ) : usuarios.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Users size={48} /></div>
                                <div className="empty-state-title">Sin usuarios</div>
                                <div className="empty-state-text">No hay usuarios registrados en el sistema</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {usuarios.map(u => (
                                            <tr key={u.id}>
                                                <td style={{ color: 'var(--text-muted)' }}>#{u.id}</td>
                                                <td style={{ fontWeight: 600 }}>{u.username}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{u.email}</td>
                                                <td>
                                                    {u.role === 'gerente' ? <span className="badge badge-accent">Gerente</span> 
                                                    : u.role === 'proveedor' ? <span className="badge badge-warning">Proveedor</span>
                                                    : <span className="badge badge-neutral">Usuario</span>}
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
