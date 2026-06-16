import { useState } from 'react';
import api from '../api';
import { useNavigate } from 'react-router-dom';
import { UserCog, Truck, ArrowRight, CheckCircle2, AlertCircle } from 'lucide-react';
import { MapContainer, TileLayer, Marker, useMapEvents } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

let DefaultIcon = L.icon({
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41]
});
L.Marker.prototype.options.icon = DefaultIcon;

function LocationMarker({ position, setPosition }) {
    useMapEvents({
        click(e) {
            setPosition(e.latlng);
        },
    });
    return position === null ? null : <Marker position={position}></Marker>;
}

export default function RoleSelect({ onRoleSelected }) {
    const [role, setRole] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [position, setPosition] = useState({ lat: 4.5709, lng: -74.2973 }); // Default Colombia
    const navigate = useNavigate();

    const username = localStorage.getItem('user_name') || 'Usuario';

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!role) {
            setError('Por favor, selecciona un rol para continuar.');
            return;
        }

        setLoading(true);
        setError('');
        try {
            await api.post('role/update/', { 
                role: role,
                latitud: role === 'proveedor' ? position.lat : null,
                longitud: role === 'proveedor' ? position.lng : null
            });
            
            // Si eligen rol por primera vez, pasan a estado pendiente
            localStorage.clear();
            alert("Tu cuenta ha sido enviada para aprobación. Un administrador debe aceptarte antes de poder ingresar al sistema.");
            window.location.href = '/login';
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
                                <h5>Ubicación de su Empresa</h5>
                                <p style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '1rem' }}>
                                    Haga clic en el mapa para marcar la ubicación exacta de su bodega o sede.
                                </p>
                                <div style={{ height: '300px', borderRadius: 'var(--radius-lg)', overflow: 'hidden', border: '1px solid var(--border-color)', marginBottom: '1rem' }}>
                                    <MapContainer center={[4.5709, -74.2973]} zoom={5} style={{ height: '100%', width: '100%' }}>
                                        <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
                                        <LocationMarker position={position} setPosition={setPosition} />
                                    </MapContainer>
                                </div>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
                                    <div className="form-group mb-3">
                                        <label>Latitud</label>
                                        <input type="text" className="form-control" value={position.lat.toFixed(6)} readOnly style={{ background: 'var(--bg-hover)' }} />
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Longitud</label>
                                        <input type="text" className="form-control" value={position.lng.toFixed(6)} readOnly style={{ background: 'var(--bg-hover)' }} />
                                    </div>
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
