import { useState, useEffect } from 'react';
import api from '../api';
import { Users, Search, Filter, UserPlus, Edit2, Trash2 } from 'lucide-react';
import { MapContainer, TileLayer, Marker } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';

export default function Usuarios() {
    const [usuarios, setUsuarios] = useState([]);
    const [username, setUsername] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [role, setRole] = useState('usuario');
    const [filterPending, setFilterPending] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [editingId, setEditingId] = useState(null);

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
            if (editingId) {
                const payload = { username, email, role };
                if (password) payload.password = password;
                await api.patch(`users/${editingId}/`, payload);
            } else {
                await api.post('users/', { username, email, password, role });
            }
            loadData();
            cancelEdit();
        } catch (err) {
            console.error(err);
        } finally {
            setSaving(false);
        }
    };

    const handleEdit = (u) => {
        setEditingId(u.id);
        setUsername(u.username);
        setEmail(u.email);
        setPassword('');
        setRole(u.role);
    };

    const cancelEdit = () => {
        setEditingId(null);
        setUsername('');
        setEmail('');
        setPassword('');
        setRole('usuario');
    };

    const handleDelete = async (id) => {
        if (confirm('¿Seguro que deseas eliminar este usuario del sistema?')) {
            try {
                await api.delete(`users/${id}/`);
                loadData();
            } catch (err) {
                console.error(err);
            }
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
                    <div className="card-header" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        {editingId ? <Edit2 size={16} /> : <UserPlus size={16} />} 
                        {editingId ? 'Editar Usuario' : 'Nuevo Usuario'}
                    </div>
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
                                <label className="form-label">Password {editingId && <span style={{ fontSize: '0.7rem', color: 'var(--text-muted)' }}>(Opcional)</span>}</label>
                                <input type="password" className="form-control" placeholder={editingId ? "Dejar en blanco para no cambiar" : "Mínimo 6 caracteres"} value={password} onChange={e=>setPassword(e.target.value)} required={!editingId} />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Rol del Usuario</label>
                                <select className="form-select" value={role} onChange={e=>setRole(e.target.value)}>
                                    <option value="usuario">Usuario Estándar</option>
                                    <option value="gerente">Gerente</option>
                                    <option value="almacenista">Almacenista</option>
                                    <option value="proveedor">Proveedor</option>
                                </select>
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> {editingId ? 'Guardando...' : 'Creando...'}</> : (editingId ? 'Guardar Cambios' : 'Crear Usuario')}
                            </button>
                            {editingId && (
                                <button type="button" className="btn btn-secondary btn-block" style={{ marginTop: '0.5rem' }} onClick={cancelEdit}>
                                    Cancelar
                                </button>
                            )}
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                            <div className="form-control" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '0.4rem 0.8rem', width: '250px' }}>
                                <Search size={14} color="var(--text-muted)" />
                                <input type="text" placeholder="Buscar usuario..." value={searchTerm} onChange={e=>setSearchTerm(e.target.value)} style={{ border: 'none', outline: 'none', background: 'transparent', width: '100%', fontSize: '0.85rem' }} />
                            </div>
                            
                            <button 
                                className={`btn btn-sm ${filterPending ? 'btn-primary' : 'btn-secondary'}`} 
                                style={{ display: 'flex', alignItems: 'center', gap: '0.4rem' }}
                                onClick={() => setFilterPending(!filterPending)}
                            >
                                <Filter size={14} /> {filterPending ? 'Ver Todos' : 'Confirmar Proveedores'}
                                {usuarios.filter(u => u.profile?.estado_validacion === 'pendiente').length > 0 && (
                                    <span style={{ background: 'var(--danger)', color: 'white', padding: '0.1rem 0.4rem', borderRadius: '99px', fontSize: '0.6rem' }}>
                                        {usuarios.filter(u => u.profile?.estado_validacion === 'pendiente').length}
                                    </span>
                                )}
                            </button>
                        </div>
                        <span className="badge badge-neutral">{filterPending ? usuarios.filter(u => u.profile?.estado_validacion === 'pendiente').length : usuarios.length} cuentas</span>
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
                                            <th>Foto</th>
                                            <th>Usuario</th>
                                            <th>Nombres</th>
                                            <th>Correo</th>
                                            <th>Rol</th>
                                            <th>Ubicación / Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {usuarios
                                            .filter(u => !filterPending || u.profile?.estado_validacion === 'pendiente')
                                            .filter(u => !searchTerm || u.username.toLowerCase().includes(searchTerm.toLowerCase()) || u.email.toLowerCase().includes(searchTerm.toLowerCase()) || (u.first_name + ' ' + u.last_name).toLowerCase().includes(searchTerm.toLowerCase()))
                                            .map(u => (
                                            <tr key={u.id}>
                                                <td>
                                                    <div style={{ width: '40px', height: '40px', borderRadius: '50%', background: '#eee', overflow: 'hidden', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                                        {u.photo ? (
                                                            <img src={u.photo} alt="Avatar" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                                        ) : (
                                                            <Users size={20} color="#aaa" />
                                                        )}
                                                    </div>
                                                </td>
                                                <td style={{ fontWeight: 600 }}>@{u.username}</td>
                                                <td>{u.first_name} {u.last_name}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{u.email}</td>
                                                <td>
                                                    {u.role === 'gerente' ? <span className="badge badge-accent">Gerente</span> 
                                                    : u.role === 'proveedor' ? <span className="badge badge-warning">Proveedor</span>
                                                    : u.role === 'almacenista' ? <span className="badge badge-primary" style={{ background: 'var(--info-bg)', color: 'var(--info)', border: '1px solid var(--info-border)' }}>Almacenista</span>
                                                    : <span className="badge badge-neutral">Usuario</span>}
                                                </td>
                                                <td>
                                                    {u.profile && u.profile.latitud && u.profile.longitud && (
                                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem', width: '200px' }}>
                                                            {u.role === 'proveedor' ? (
                                                                <div style={{ height: '120px', width: '100%', borderRadius: 'var(--radius-sm)', overflow: 'hidden', border: '1px solid var(--border-color)' }}>
                                                                    <MapContainer center={[u.profile.latitud, u.profile.longitud]} zoom={13} style={{ height: '100%', width: '100%' }} zoomControl={false}>
                                                                        <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" attribution="&copy; OpenStreetMap" />
                                                                        <Marker position={[u.profile.latitud, u.profile.longitud]} />
                                                                    </MapContainer>
                                                                </div>
                                                            ) : (
                                                                <a href={`https://www.google.com/maps?q=${u.profile.latitud},${u.profile.longitud}`} target="_blank" rel="noreferrer" style={{ fontSize: '0.8rem', color: 'var(--info)', fontWeight: 600, textDecoration: 'none' }}>
                                                                    📍 Ver en Maps
                                                                </a>
                                                            )}
                                                            
                                                            {u.profile.estado_validacion === 'pendiente' ? (
                                                                <div style={{ display: 'flex', gap: '0.4rem' }}>
                                                                    <button className="btn btn-sm" style={{ padding: '0.25rem 0.6rem', fontSize: '0.7rem', background: 'var(--success)', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', flex: 1 }}
                                                                        onClick={async () => {
                                                                            await api.post(`users/${u.id}/verificar/`);
                                                                            loadData();
                                                                        }}
                                                                    >
                                                                        Aprobar
                                                                    </button>
                                                                    <button className="btn btn-sm" style={{ padding: '0.25rem 0.6rem', fontSize: '0.7rem', background: 'var(--danger)', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', flex: 1 }}
                                                                        onClick={async () => {
                                                                            if(confirm('¿Seguro que deseas rechazar y eliminar a este proveedor?')) {
                                                                                await api.delete(`users/${u.id}/`);
                                                                                loadData();
                                                                            }
                                                                        }}
                                                                    >
                                                                        Denegar
                                                                    </button>
                                                                </div>
                                                            ) : (
                                                                <span className="badge badge-success" style={{ fontSize: '0.7rem' }}>Verificado</span>
                                                            )}
                                                        </div>
                                                    )}
                                                    {(!u.profile || u.profile.estado_validacion !== 'pendiente') && (
                                                        <div style={{ display: 'flex', gap: '0.4rem', marginTop: u.profile && u.profile.latitud ? '0.5rem' : '0' }}>
                                                            <button className="btn btn-sm btn-secondary" title="Editar" onClick={() => handleEdit(u)} style={{ padding: '0.3rem' }}><Edit2 size={14} /></button>
                                                            <button className="btn btn-sm" title="Eliminar" onClick={() => handleDelete(u.id)} style={{ padding: '0.3rem', background: 'var(--danger)', color: 'white', border: 'none' }}><Trash2 size={14} /></button>
                                                        </div>
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
