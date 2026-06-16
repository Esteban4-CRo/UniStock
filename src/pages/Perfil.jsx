import React, { useState, useEffect } from 'react';
import api from '../api';
import { User, Mail, Phone, MapPin, Camera, Lock, CheckCircle2, AlertCircle } from 'lucide-react';

export default function Perfil() {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [message, setMessage] = useState({ type: '', text: '' });
    
    // Formularios
    const [formData, setFormData] = useState({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
    });
    
    // Contraseña 2FA
    const [pwdTab, setPwdTab] = useState(false);
    const [pwdData, setPwdData] = useState({
        new_password: '',
        confirm_password: '',
        otp: ''
    });
    const [otpSent, setOtpSent] = useState(false);
    const [pwdLoading, setPwdLoading] = useState(false);
    const [pwdMsg, setPwdMsg] = useState({ type: '', text: '' });

    useEffect(() => {
        loadProfile();
    }, []);

    const loadProfile = async () => {
        try {
            const res = await api.get('users/me/');
            setUser(res.data);
            setFormData({
                first_name: res.data.first_name || '',
                last_name: res.data.last_name || '',
                email: res.data.email || '',
                phone: res.data.profile?.phone || '',
                address: res.data.profile?.address || '',
            });
        } catch (err) {
            console.error("Error cargando perfil", err);
        } finally {
            setLoading(false);
        }
    };

    const handleSaveProfile = async (e) => {
        e.preventDefault();
        setSaving(true);
        setMessage({ type: '', text: '' });
        try {
            await api.patch('users/me/', formData);
            setMessage({ type: 'success', text: 'Perfil actualizado exitosamente.' });
            loadProfile();
        } catch (err) {
            setMessage({ type: 'error', text: 'Error al actualizar perfil.' });
        } finally {
            setSaving(false);
        }
    };

    const handleRequestOTP = async () => {
        setPwdLoading(true);
        setPwdMsg({ type: '', text: '' });
        try {
            await api.post('users/request_password_reset/');
            setOtpSent(true);
            setPwdMsg({ type: 'success', text: 'Código enviado a tu correo.' });
        } catch (err) {
            setPwdMsg({ type: 'error', text: 'Error al solicitar el código.' });
        } finally {
            setPwdLoading(false);
        }
    };

    const handleVerifyPassword = async (e) => {
        e.preventDefault();
        setPwdMsg({ type: '', text: '' });
        if (pwdData.new_password !== pwdData.confirm_password) {
            setPwdMsg({ type: 'error', text: 'Las contraseñas no coinciden.' });
            return;
        }
        if (pwdData.new_password.length < 6) {
            setPwdMsg({ type: 'error', text: 'La contraseña debe tener al menos 6 caracteres.' });
            return;
        }
        if (!pwdData.otp) {
            setPwdMsg({ type: 'error', text: 'Ingresa el código OTP.' });
            return;
        }

        setPwdLoading(true);
        try {
            await api.post('users/verify_password_reset/', {
                otp: pwdData.otp,
                new_password: pwdData.new_password
            });
            setPwdMsg({ type: 'success', text: 'Contraseña cambiada exitosamente.' });
            setOtpSent(false);
            setPwdData({ new_password: '', confirm_password: '', otp: '' });
            setPwdTab(false);
        } catch (err) {
            const errorMsg = err.response?.data?.error || 'Error al cambiar contraseña.';
            setPwdMsg({ type: 'error', text: errorMsg });
        } finally {
            setPwdLoading(false);
        }
    };

    const handlePhotoUpload = async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('photo', file);
        try {
            const res = await api.patch('users/me/', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            if (res.data && res.data.photo) {
                let photoUrl = res.data.photo;
                if (photoUrl.startsWith('/')) photoUrl = `${import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000'}${photoUrl}`;
                localStorage.setItem('user_photo', photoUrl);
                window.dispatchEvent(new Event('storage'));
            }
            setMessage({ type: 'success', text: 'Foto actualizada.' });
            loadProfile();
        } catch (err) {
            setMessage({ type: 'error', text: 'Error al subir foto.' });
        }
    };

    if (loading) return (
        <div className="empty-state">
            <span className="spinner dark" style={{ width: 36, height: 36, borderWidth: 3 }}></span>
        </div>
    );
    if (!user) return <div className="empty-state">No se pudo cargar el perfil.</div>;

    return (
        <div>
            <div className="page-header">
                <div>
                    <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><User size={28} /> Mi Perfil</h1>
                    <p className="page-subtitle">Administra tu información personal y opciones de seguridad</p>
                </div>
            </div>

            <div className="grid-3" style={{ alignItems: 'start' }}>
                
                {/* Columna Izquierda: Foto y Resumen */}
                <div className="card" style={{ gridColumn: 'span 1' }}>
                    <div className="card-header">
                        <User size={16} /> Foto de Perfil
                    </div>
                    <div className="card-body" style={{ textAlign: 'center' }}>
                        <div style={{ position: 'relative', width: '120px', height: '120px', margin: '0 auto 1.5rem', borderRadius: '50%', background: 'var(--bg-hover)', border: '2px dashed var(--border-color)', display: 'flex', alignItems: 'center', justifyContent: 'center', overflow: 'hidden' }}>
                            {user.photo ? (
                                <img src={user.photo.startsWith('/') ? `${import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000'}${user.photo}` : user.photo} alt="Perfil" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                            ) : (
                                <User size={48} color="var(--text-muted)" />
                            )}
                            <label style={{ position: 'absolute', bottom: 0, left: 0, width: '100%', background: 'rgba(0,0,0,0.6)', color: 'white', padding: '0.2rem', cursor: 'pointer', fontSize: '0.75rem', fontWeight: 600 }}>
                                <Camera size={14} style={{ verticalAlign: 'middle', marginRight: '4px' }} /> Cambiar
                                <input type="file" accept="image/*" style={{ display: 'none' }} onChange={handlePhotoUpload} />
                            </label>
                        </div>
                        <h3 className="fw-800" style={{ fontSize: '1.25rem' }}>{user.first_name} {user.last_name}</h3>
                        <p className="text-muted" style={{ fontSize: '0.85rem', marginBottom: '1rem' }}>@{user.username}</p>
                        
                        <span className="badge badge-dark" style={{ textTransform: 'capitalize' }}>Rol: {user.role || 'Usuario'}</span>
                        {user.profile?.estado_validacion === 'verificado' && (
                            <span className="badge badge-success mt-2" style={{ display: 'block', width: 'fit-content', margin: '0.5rem auto 0' }}>Verificado</span>
                        )}
                    </div>
                </div>

                {/* Columna Derecha: Formulario y Seguridad */}
                <div style={{ gridColumn: 'span 2', display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                    
                    {/* Tarjeta de Datos Personales */}
                    <div className="card">
                        <div className="card-header">
                            Datos Personales
                        </div>
                        <div className="card-body">
                            {message.text && (
                                <div className={`alert alert-${message.type === 'success' ? 'success' : 'danger'}`}>
                                    {message.type === 'success' ? <CheckCircle2 size={18} /> : <AlertCircle size={18} />} {message.text}
                                </div>
                            )}
                            
                            <form onSubmit={handleSaveProfile}>
                                <div className="grid-2">
                                    <div className="form-group">
                                        <label className="form-label">Nombre</label>
                                        <input type="text" className="form-control" value={formData.first_name} onChange={e => setFormData({...formData, first_name: e.target.value})} required />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Apellido</label>
                                        <input type="text" className="form-control" value={formData.last_name} onChange={e => setFormData({...formData, last_name: e.target.value})} />
                                    </div>
                                </div>
                                
                                <div className="form-group">
                                    <label className="form-label"><Mail size={14} style={{ verticalAlign: 'middle', marginRight: '4px' }}/> Correo Electrónico</label>
                                    <input type="email" className="form-control" value={formData.email} onChange={e => setFormData({...formData, email: e.target.value})} required />
                                </div>

                                <div className="form-group">
                                    <label className="form-label"><Phone size={14} style={{ verticalAlign: 'middle', marginRight: '4px' }}/> Teléfono</label>
                                    <input type="text" className="form-control" value={formData.phone} onChange={e => setFormData({...formData, phone: e.target.value})} />
                                </div>

                                <div className="form-group mb-4">
                                    <label className="form-label"><MapPin size={14} style={{ verticalAlign: 'middle', marginRight: '4px' }}/> Dirección</label>
                                    <textarea className="form-control" rows="2" value={formData.address} onChange={e => setFormData({...formData, address: e.target.value})}></textarea>
                                </div>

                                <button type="submit" className="btn btn-primary" disabled={saving}>
                                    {saving ? <><span className="spinner" style={{ width:16, height:16, borderWidth:2 }} /> Guardando...</> : 'Guardar Cambios'}
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Tarjeta de Seguridad */}
                    <div className="card">
                        <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                                <Lock size={16} /> Seguridad y Contraseña
                            </div>
                            {!pwdTab && (
                                <button className="btn btn-secondary btn-sm" onClick={() => setPwdTab(true)}>Actualizar Contraseña</button>
                            )}
                        </div>
                        
                        {pwdTab && (
                            <div className="card-body">
                                {pwdMsg.text && (
                                    <div className={`alert alert-${pwdMsg.type === 'success' ? 'success' : 'danger'}`}>
                                        {pwdMsg.type === 'success' ? <CheckCircle2 size={18} /> : <AlertCircle size={18} />} {pwdMsg.text}
                                    </div>
                                )}

                                {!otpSent ? (
                                    <div>
                                        <p className="text-muted" style={{ fontSize: '0.85rem', marginBottom: '1rem' }}>
                                            Para cambiar tu contraseña, enviaremos un código de seguridad de 6 dígitos a tu correo electrónico registrado ({user.email}).
                                        </p>
                                        <div style={{ display: 'flex', gap: '0.75rem' }}>
                                            <button className="btn btn-primary" onClick={handleRequestOTP} disabled={pwdLoading}>
                                                {pwdLoading ? <><span className="spinner" style={{ width:16, height:16, borderWidth:2 }} /> Enviando...</> : 'Enviar código por correo'}
                                            </button>
                                            <button className="btn btn-ghost" onClick={() => {setPwdTab(false); setPwdMsg({type:'', text:''})}}>Cancelar</button>
                                        </div>
                                    </div>
                                ) : (
                                    <form onSubmit={handleVerifyPassword}>
                                        <p className="text-muted" style={{ fontSize: '0.85rem', marginBottom: '1rem' }}>
                                            Ingresa el código que hemos enviado a tu correo junto con tu nueva contraseña.
                                        </p>
                                        
                                        <div className="grid-2">
                                            <div className="form-group">
                                                <label className="form-label">Código OTP (6 dígitos)</label>
                                                <input type="text" className="form-control" placeholder="123456" maxLength="6" value={pwdData.otp} onChange={e => setPwdData({...pwdData, otp: e.target.value})} required autoFocus />
                                            </div>
                                            <div></div> {/* Empty column */}
                                        </div>

                                        <div className="grid-2">
                                            <div className="form-group">
                                                <label className="form-label">Nueva Contraseña</label>
                                                <input type="password" className="form-control" placeholder="Mínimo 6 caracteres" value={pwdData.new_password} onChange={e => setPwdData({...pwdData, new_password: e.target.value})} required />
                                            </div>

                                            <div className="form-group mb-4">
                                                <label className="form-label">Confirmar Contraseña</label>
                                                <input type="password" className="form-control" placeholder="Repite la contraseña" value={pwdData.confirm_password} onChange={e => setPwdData({...pwdData, confirm_password: e.target.value})} required />
                                            </div>
                                        </div>

                                        <div style={{ display: 'flex', gap: '0.75rem' }}>
                                            <button type="submit" className="btn btn-success" disabled={pwdLoading}>
                                                {pwdLoading ? <><span className="spinner" style={{ width:16, height:16, borderWidth:2 }} /> Verificando...</> : 'Verificar y Cambiar Contraseña'}
                                            </button>
                                            <button type="button" className="btn btn-ghost" onClick={() => {setOtpSent(false); setPwdMsg({type:'', text:''})}}>Volver</button>
                                        </div>
                                    </form>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
