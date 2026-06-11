import { useState } from 'react';
import api from '../api';
import { useNavigate } from 'react-router-dom';
import { UserCog, Truck, ArrowRight, CheckCircle2, AlertCircle } from 'lucide-react';

export default function RoleSelect({ onRoleSelected }) {
    const [role, setRole] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    const username = localStorage.getItem('user_name') || 'Usuario';

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!role) {
            setError('Por favor, selecciona un rol para continuar.');
            return;
        }

        const latitud = e.target.elements.latitud?.value;
        const longitud = e.target.elements.longitud?.value;

        setLoading(true);
        setError('');
        try {
            await api.post('role/update/', { role, latitud, longitud });
            localStorage.removeItem('needs_role');
            localStorage.setItem('user_role', role);
            onRoleSelected();
            navigate('/dashboard');
        } catch (err) {
            setError(err.response?.data?.error || 'Error al actualizar el rol.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="auth-page">
            <div className="auth-card" style={{ maxWidth: '500px' }}>
                <div className="auth-card-header">
                    <div className="auth-title">¡Bienvenido, {username}!</div>
                    <div className="auth-subtitle">Para finalizar, necesitamos saber cómo usarás UniStock.</div>
                </div>

                <div className="auth-body">
                    {error && (
                        <div className="alert alert-danger" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                            <AlertCircle size={18} /> {error}
                        </div>
                    )}
                    
                    <form onSubmit={handleSubmit}>
                        <div style={{ display: 'grid', gap: '1rem', marginBottom: '2rem' }}>
                            {/* Almacenista Option */}
                            <label 
                                style={{ 
                                    display: 'flex', gap: '1rem', padding: '1.25rem', 
                                    border: `2px solid ${role === 'almacenista' ? 'var(--accent)' : 'var(--border-color)'}`, 
                                    borderRadius: 'var(--radius-lg)', cursor: 'pointer',
                                    background: role === 'almacenista' ? 'var(--bg-hover)' : 'var(--bg-card)'
                                }}
                            >
                                <input 
                                    type="radio" 
                                    name="role" 
                                    value="almacenista" 
                                    checked={role === 'almacenista'} 
                                    onChange={() => setRole('almacenista')}
                                    style={{ marginTop: '0.2rem', display: 'none' }}
                                />
                                <div style={{ color: role === 'almacenista' ? 'var(--accent)' : 'var(--text-muted)' }}>
                                    {role === 'almacenista' ? <CheckCircle2 size={24} /> : <div style={{ width: 24, height: 24, borderRadius: '50%', border: '2px solid var(--border-dark)' }} />}
                                </div>
                                <div style={{ flex: 1 }}>
                                    <div style={{ fontWeight: 700, fontSize: '1.1rem', color: 'var(--text-primary)', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                                        <UserCog size={20} /> Almacenista
                                    </div>
                                    <div style={{ fontSize: '0.85rem', color: 'var(--text-secondary)', marginTop: '0.3rem', lineHeight: 1.4 }}>
                                        Gestionaré el inventario, registraré entradas y salidas, y supervisaré las alertas.
                                    </div>
                                </div>
                            </label>

                            {/* Proveedor Option */}
                            <label 
                                style={{ 
                                    display: 'flex', gap: '1rem', padding: '1.25rem', 
                                    border: `2px solid ${role === 'proveedor' ? 'var(--accent)' : 'var(--border-color)'}`, 
                                    borderRadius: 'var(--radius-lg)', cursor: 'pointer',
                                    background: role === 'proveedor' ? 'var(--bg-hover)' : 'var(--bg-card)'
                                }}
                            >
                                <input 
                                    type="radio" 
                                    name="role" 
                                    value="proveedor" 
                                    checked={role === 'proveedor'} 
                                    onChange={() => setRole('proveedor')}
                                    style={{ marginTop: '0.2rem', display: 'none' }}
                                />
                                <div style={{ color: role === 'proveedor' ? 'var(--accent)' : 'var(--text-muted)' }}>
                                    {role === 'proveedor' ? <CheckCircle2 size={24} /> : <div style={{ width: 24, height: 24, borderRadius: '50%', border: '2px solid var(--border-dark)' }} />}
                                </div>
                                <div>
                                    <div style={{ fontWeight: 700, fontSize: '1.1rem', color: 'var(--text-primary)', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                                        <Truck size={20} /> Proveedor externo
                                    </div>
                                    <div style={{ fontSize: '0.85rem', color: 'var(--text-secondary)', marginTop: '0.3rem', lineHeight: 1.4 }}>
                                        Solo necesito ver los despachos y pedidos asignados a mi empresa.
                                    </div>
                                </div>
                            </label>
                        </div>

                        {(role === 'proveedor' || role === 'almacenista') && (
                            <div style={{ marginBottom: '2rem' }}>
                                <h5>Datos de Verificación (Google Maps)</h5>
                                <div className="form-group mb-3">
                                    <label>Latitud</label>
                                    <input 
                                        type="number" step="any" className="form-control" 
                                        name="latitud" id="latitud" placeholder="Ej: 4.6097100" required
                                    />
                                </div>
                                <div className="form-group mb-3">
                                    <label>Longitud</label>
                                    <input 
                                        type="number" step="any" className="form-control" 
                                        name="longitud" id="longitud" placeholder="Ej: -74.0817500" required
                                    />
                                </div>
                                <p style={{ fontSize: '0.8rem', color: 'var(--text-muted)' }}>Estos datos serán verificados por el gerente para activar tu cuenta.</p>
                            </div>
                        )}

                        <button type="submit" className="btn btn-primary btn-block btn-lg" disabled={loading || !role}>
                            {loading ? <><span className="spinner" style={{ width:16, height:16, borderWidth:2 }} /> Guardando...</> : <span style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>Continuar al Dashboard <ArrowRight size={18} /></span>}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}
