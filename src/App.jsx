import { BrowserRouter as Router, Routes, Route, Navigate, NavLink, Outlet, useNavigate, useLocation } from 'react-router-dom';
import { useState, useEffect } from 'react';
import api from './api';
import Landing from './pages/Landing';
import Dashboard from './pages/Dashboard';
import Reportes from './pages/Reportes';
import MateriasPrimas from './pages/MateriasPrimas';
import Entradas from './pages/Entradas';
import Salidas from './pages/Salidas';
import Usuarios from './pages/Usuarios';
import Proveedores from './pages/Proveedores';
import Ubicaciones from './pages/Ubicaciones';
import AgenteIA from './pages/AgenteIA';
import RoleSelect from './pages/RoleSelect';
import { LayoutDashboard, PackageOpen, Download, Upload, Factory, MapPin, Users, FileBarChart, Bot, Menu, X, LogOut } from 'lucide-react';

// ─── Sidebar ──────────────────────────────────────────────────────────────────
function Sidebar({ collapsed, onToggle, onLogout, alertCount, mobileOpen, setMobileOpen }) {
    const userRole = localStorage.getItem('user_role') || '';

    // Filtro de rutas según el rol
    const allNavItems = [
        { to: '/dashboard',       icon: <LayoutDashboard size={18} />, label: 'Dashboard',     section: 'Principal', roles: ['gerente', 'admin', 'almacenista', 'proveedor'] },
        { to: '/materias-primas', icon: <PackageOpen size={18} />,     label: 'Inventario',    section: 'Gestión', roles: ['gerente', 'admin', 'almacenista'] },
        { to: '/entradas',        icon: <Download size={18} />,        label: 'Entradas',      roles: ['gerente', 'admin', 'almacenista'] },
        { to: '/salidas',         icon: <Upload size={18} />,          label: 'Despachos',     roles: ['gerente', 'admin', 'almacenista', 'proveedor'] },
        { to: '/proveedores',     icon: <Factory size={18} />,         label: 'Proveedores',   section: 'Catálogos', roles: ['gerente', 'admin', 'almacenista'] },
        { to: '/ubicaciones',     icon: <MapPin size={18} />,          label: 'Ubicaciones',   roles: ['gerente', 'admin', 'almacenista'] },
        { to: '/usuarios',        icon: <Users size={18} />,           label: 'Usuarios',      roles: ['gerente', 'admin'] },
        { to: '/reportes',        icon: <FileBarChart size={18} />,    label: 'Reportes',      section: 'Analytics', roles: ['gerente', 'admin', 'almacenista', 'proveedor'] },
        { to: '/agente-ia',       icon: <Bot size={18} />,             label: 'Agente IA',     badge: 'IA', roles: ['gerente', 'admin', 'almacenista', 'proveedor'] },
    ];

    const navItems = allNavItems.filter(item => item.roles.includes(userRole) || userRole === '');

    const username = localStorage.getItem('user_name') || 'Usuario';
    const userInitial = username.charAt(0).toUpperCase();

    let lastSection = '';

    return (
        <>
            {mobileOpen && (
                <div 
                    className="sidebar-overlay" 
                    onClick={() => setMobileOpen(false)}
                    style={{ position: 'fixed', top:0, left:0, right:0, bottom:0, background: 'rgba(0,0,0,0.5)', zIndex: 999 }}
                />
            )}
            
            <aside className={`sidebar ${collapsed ? 'collapsed' : ''} ${mobileOpen ? 'mobile-open' : ''}`}>
                <div className="sidebar-header">
                    <span className="sidebar-brand">UniStock</span>
                    <button className="sidebar-toggle" onClick={onToggle} title="Colapsar" style={{ display: window.innerWidth <= 768 ? 'none' : 'flex' }}>
                        {collapsed ? '»' : '«'}
                    </button>
                    <button className="sidebar-toggle" onClick={() => setMobileOpen(false)} style={{ display: window.innerWidth > 768 ? 'none' : 'flex' }}>
                        <X size={16} />
                    </button>
                </div>

                <nav className="sidebar-nav">
                    {navItems.map(item => {
                        const showSection = item.section && item.section !== lastSection;
                        if (item.section) lastSection = item.section;

                        return (
                            <div key={item.to}>
                                {showSection && (
                                    <div className="nav-section-label" style={{ marginTop: '0.75rem' }}>
                                        {item.section}
                                    </div>
                                )}
                                <NavLink
                                    to={item.to}
                                    className={({ isActive }) => `nav-item ${isActive ? 'active' : ''}`}
                                    title={collapsed ? item.label : ''}
                                    onClick={() => setMobileOpen(false)}
                                >
                                    <span className="nav-icon" style={{ display: 'flex', alignItems: 'center' }}>{item.icon}</span>
                                    <span className="nav-label">{item.label}</span>
                                    {item.badge && (
                                        <span className="nav-badge" style={{ background: '#2563eb' }}>{item.badge}</span>
                                    )}
                                    {item.to === '/dashboard' && alertCount > 0 && ['gerente', 'admin', 'almacenista'].includes(userRole) && (
                                        <span className="nav-badge">{alertCount}</span>
                                    )}
                                </NavLink>
                            </div>
                        );
                    })}
                </nav>

                <div className="sidebar-footer">
                    <div className="sidebar-user" onClick={onLogout} title="Cerrar sesión">
                        <div className="user-avatar">{userInitial}</div>
                        <div className="user-info">
                            <div className="user-name">{username}</div>
                            <div className="user-role" style={{ textTransform: 'capitalize', display: 'flex', alignItems: 'center', gap: '0.2rem' }}>
                                {userRole || 'Cerrar sesión'} <LogOut size={12} />
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </>
    );
}

// ─── Layout ───────────────────────────────────────────────────────────────────
function Layout({ setAuth }) {
    const [collapsed, setCollapsed] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);
    const [alertCount, setAlertCount] = useState(0);
    const navigate = useNavigate();

    const needsRole = localStorage.getItem('needs_role') === 'true';

    useEffect(() => {
        if (!needsRole) {
            api.get('alertas/').then(r => setAlertCount(r.data.length)).catch(() => {});
        }
    }, [needsRole]);

    const handleLogout = () => {
        localStorage.clear();
        setAuth(false);
        navigate('/');
    };

    if (needsRole) {
        return <RoleSelect onRoleSelected={() => setAuth(true)} />;
    }

    return (
        <div className="app-layout">
            <Sidebar
                collapsed={collapsed}
                onToggle={() => setCollapsed(c => !c)}
                onLogout={handleLogout}
                alertCount={alertCount}
                mobileOpen={mobileOpen}
                setMobileOpen={setMobileOpen}
            />
            <main className={`main-content ${collapsed ? 'sidebar-collapsed' : ''}`}>
                <div className="mobile-header" style={{ display: 'none', alignItems: 'center', gap: '1rem', marginBottom: '1rem' }}>
                    <button className="btn btn-ghost" onClick={() => setMobileOpen(true)} style={{ padding: '0.4rem 0.6rem', fontSize: '1.2rem' }}>
                        <Menu size={20} />
                    </button>
                    <span style={{ fontWeight: 800, fontSize: '1.2rem' }}>UniStock</span>
                </div>
                
                <Outlet />
            </main>
            
            <style>{`
                @media (max-width: 768px) {
                    .mobile-header { display: flex !important; }
                    .main-content { padding-top: 1rem !important; }
                }
            `}</style>
        </div>
    );
}

// ─── Auth (Login + Registro en tabs) ─────────────────────────────────────────
// (Código de Auth sin cambios importantes visuales pero quitamos emojis y usamos iconos si hubiera)
// (Por brevedad incluyo el componente Auth completo)
import { AlertCircle, CheckCircle2 } from 'lucide-react';

function Auth({ setAuth }) {
    const [tab, setTab] = useState('login');
    const location = useLocation();
    const navigate = useNavigate();

    const [urlError, setUrlError] = useState('');
    useEffect(() => {
        const params = new URLSearchParams(location.search);
        if (params.get('error')) {
            setUrlError('Ocurrió un error al autenticar con Google. Intenta nuevamente.');
        }
    }, [location.search]);

    const [loginUser, setLoginUser] = useState('');
    const [loginPass, setLoginPass] = useState('');
    const [loginLoading, setLoginLoading] = useState(false);
    const [loginError, setLoginError] = useState('');

    const [regName, setRegName]     = useState('');
    const [regUser, setRegUser]     = useState('');
    const [regEmail, setRegEmail]   = useState('');
    const [regPass, setRegPass]     = useState('');
    const [regPass2, setRegPass2]   = useState('');
    const [regLoading, setRegLoading] = useState(false);
    const [regError, setRegError]   = useState('');
    const [regSuccess, setRegSuccess] = useState('');

    const handleLogin = async (e) => {
        e.preventDefault();
        setLoginLoading(true);
        setLoginError('');
        setUrlError('');
        try {
            const res = await api.post('token/', { username: loginUser, password: loginPass });
            localStorage.setItem('access_token', res.data.access);
            localStorage.setItem('refresh_token', res.data.refresh);
            localStorage.setItem('user_name', loginUser);
            
            // Fetch user profile to get actual role
            try {
                const meRes = await api.get('users/', {
                    headers: { Authorization: `Bearer ${res.data.access}` }
                });
                const me = meRes.data.find(u => u.username === loginUser);
                if (me) {
                    const role = me.role || '';
                    if (['gerente', 'admin', 'almacenista', 'proveedor'].includes(role)) {
                        localStorage.setItem('user_role', role);
                        localStorage.removeItem('needs_role');
                    } else {
                        localStorage.setItem('needs_role', 'true');
                    }
                } else {
                    localStorage.setItem('needs_role', 'true');
                }
            } catch {
                localStorage.setItem('needs_role', 'true');
            }

            setAuth(true);
            navigate('/dashboard');
        } catch {
            setLoginError('Usuario o contraseña incorrectos. Verifica e intenta de nuevo.');
        } finally {
            setLoginLoading(false);
        }
    };

    const handleRegister = async (e) => {
        e.preventDefault();
        setRegError('');
        setRegSuccess('');
        setUrlError('');
        if (regPass !== regPass2) { setRegError('Las contraseñas no coinciden.'); return; }
        if (regPass.length < 6)   { setRegError('La contraseña debe tener al menos 6 caracteres.'); return; }

        setRegLoading(true);
        try {
            await api.post('users/', {
                username: regUser,
                email: regEmail,
                password: regPass,
                first_name: regName.split(' ')[0] || regName,
                last_name: regName.split(' ').slice(1).join(' ') || '',
            });
            setRegSuccess('¡Cuenta creada! Inicia sesión con tus credenciales.');
            setTab('login');
            setLoginUser(regUser);
            setRegName(''); setRegUser(''); setRegEmail(''); setRegPass(''); setRegPass2('');
        } catch (err) {
            const detail = err?.response?.data;
            if (detail?.username) setRegError(`Usuario: ${detail.username[0]}`);
            else if (detail?.email) setRegError(`Email: ${detail.email[0]}`);
            else if (detail?.password) setRegError(`Contraseña: ${detail.password[0]}`);
            else setRegError('Error al crear la cuenta. Verifica los datos.');
        } finally {
            setRegLoading(false);
        }
    };

    const handleGoogle = () => {
        const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000';
        window.location.href = `${backendUrl}/auth/google/`;
    };

    return (
        <div className="auth-page">
            <div className="auth-card">
                <div className="auth-card-header" style={{ position: 'relative' }}>
                    <button 
                        onClick={() => navigate('/')} 
                        className="btn btn-secondary" 
                        style={{ position: 'absolute', top: '-1.5rem', left: '-1.5rem', display: 'flex', alignItems: 'center', gap: '0.4rem', fontSize: '0.85rem', padding: '0.4rem 0.8rem', borderRadius: 'var(--radius-md)', boxShadow: '0 2px 4px rgba(0,0,0,0.1)' }}
                    >
                        &larr; Volver
                    </button>
                    <div className="auth-title">UniStock</div>
                    <div className="auth-subtitle">Sistema de gestión de inventario</div>
                </div>

                <div className="auth-tabs">
                    <button className={`auth-tab ${tab === 'login' ? 'active' : ''}`} onClick={() => { setTab('login'); setLoginError(''); setRegError(''); setRegSuccess(''); }}>Iniciar Sesión</button>
                    <button className={`auth-tab ${tab === 'register' ? 'active' : ''}`} onClick={() => { setTab('register'); setLoginError(''); setRegError(''); setRegSuccess(''); }}>Registrarse</button>
                </div>

                <div className="auth-body">
                    {urlError && <div className="alert alert-danger" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><AlertCircle size={18} /> {urlError}</div>}
                    
                    {tab === 'login' && (
                        <>
                            {regSuccess && <div className="alert alert-success" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><CheckCircle2 size={18} /> {regSuccess}</div>}
                            {loginError && <div className="alert alert-danger" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><AlertCircle size={18} /> {loginError}</div>}
                            <form onSubmit={handleLogin}>
                                <div className="form-group">
                                    <label className="form-label">Usuario</label>
                                    <input type="text" className="form-control" placeholder="Tu nombre de usuario" value={loginUser} onChange={e => setLoginUser(e.target.value)} required autoFocus />
                                </div>
                                <div className="form-group" style={{ marginBottom: '1.25rem' }}>
                                    <label className="form-label">Contraseña</label>
                                    <input type="password" className="form-control" placeholder="••••••••" value={loginPass} onChange={e => setLoginPass(e.target.value)} required />
                                </div>
                                <button type="submit" className="btn btn-primary btn-block btn-lg" disabled={loginLoading}>
                                    {loginLoading ? <><span className="spinner" style={{ width:16, height:16, borderWidth:2 }} /> Ingresando...</> : 'Ingresar'}
                                </button>
                            </form>
                            <div className="auth-divider">o continúa con</div>
                            <button className="btn btn-google btn-block btn-lg" onClick={handleGoogle}>
                                <svg width="16" height="16" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Continuar con Google
                            </button>
                        </>
                    )}

                    {tab === 'register' && (
                        <>
                            {regError && <div className="alert alert-danger" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><AlertCircle size={18} /> {regError}</div>}
                            <form onSubmit={handleRegister}>
                                <div className="form-group"><label className="form-label">Nombre Completo</label><input type="text" className="form-control" placeholder="Juan Pérez" value={regName} onChange={e => setRegName(e.target.value)} required autoFocus /></div>
                                <div className="form-group"><label className="form-label">Usuario</label><input type="text" className="form-control" placeholder="juanperez" value={regUser} onChange={e => setRegUser(e.target.value.toLowerCase().replace(/\s/g,''))} required /></div>
                                <div className="form-group"><label className="form-label">Email</label><input type="email" className="form-control" placeholder="juan@empresa.com" value={regEmail} onChange={e => setRegEmail(e.target.value)} required /></div>
                                <div className="form-group"><label className="form-label">Contraseña</label><input type="password" className="form-control" placeholder="Mínimo 6 caracteres" value={regPass} onChange={e => setRegPass(e.target.value)} required /></div>
                                <div className="form-group" style={{ marginBottom: '1.25rem' }}><label className="form-label">Confirmar Contraseña</label><input type="password" className="form-control" placeholder="Repite la contraseña" value={regPass2} onChange={e => setRegPass2(e.target.value)} required /></div>
                                <button type="submit" className="btn btn-primary btn-block btn-lg" disabled={regLoading}>
                                    {regLoading ? <><span className="spinner" style={{ width:16, height:16, borderWidth:2 }} /> Creando cuenta...</> : 'Crear Cuenta'}
                                </button>
                            </form>
                            <div className="auth-divider">o</div>
                            <button className="btn btn-google btn-block btn-lg" onClick={handleGoogle}>
                                <svg width="16" height="16" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Registrarse con Google
                            </button>
                        </>
                    )}
                    <p style={{ textAlign:'center', marginTop:'1.25rem', fontSize:'0.72rem', color:'var(--text-muted)' }}>
                        UniStock © {new Date().getFullYear()} · Gestión de Inventario Industrial
                    </p>
                </div>
            </div>
        </div>
    );
}

// ─── Auth Callback ────────────────────────────────────────────────────────────
function AuthCallback({ setAuth }) {
    const location = useLocation();
    const navigate = useNavigate();

    useEffect(() => {
        const params = new URLSearchParams(location.search);
        const access     = params.get('access');
        const refresh    = params.get('refresh');
        const name       = params.get('name');
        const email      = params.get('email');
        const role       = params.get('role') || '';
        const needsRole  = params.get('needs_role') === 'true';

        if (access && refresh) {
            localStorage.setItem('access_token', access);
            localStorage.setItem('refresh_token', refresh);
            if (name)  localStorage.setItem('user_name', name);
            if (email) localStorage.setItem('user_email', email);
            
            if (needsRole) {
                localStorage.setItem('needs_role', 'true');
            } else {
                localStorage.removeItem('needs_role');
                if (role && ['gerente', 'admin', 'almacenista', 'proveedor'].includes(role)) {
                    localStorage.setItem('user_role', role);
                }
            }
            
            setAuth(true);
            navigate('/dashboard');
        } else {
            navigate('/login');
        }
    }, []);

    return (
        <div style={{ display:'flex', alignItems:'center', justifyContent:'center', height:'100vh', background:'#f5f5f5' }}>
            <div style={{ textAlign:'center' }}>
                <div className="spinner dark" style={{ width:36, height:36, borderWidth:3, margin:'0 auto 1rem' }} />
                <p style={{ color:'var(--text-muted)', fontSize:'0.875rem' }}>Conectando con Google...</p>
            </div>
        </div>
    );
}

// ─── App ──────────────────────────────────────────────────────────────────────
export default function App() {
    const [auth, setAuth] = useState(!!localStorage.getItem('access_token'));

    return (
        <Router>
            <Routes>
                <Route path="/" element={!auth ? <Landing /> : <Navigate to="/dashboard" />} />
                <Route path="/login"         element={auth ? <Navigate to="/dashboard" /> : <Auth setAuth={setAuth} />} />
                <Route path="/auth/callback" element={<AuthCallback setAuth={setAuth} />} />
                
                <Route element={auth ? <Layout setAuth={setAuth} /> : <Navigate to="/login" />}>
                    <Route path="/dashboard"      element={<Dashboard />} />
                    <Route path="/materias-primas" element={<MateriasPrimas />} />
                    <Route path="/entradas"        element={<Entradas />} />
                    <Route path="/salidas"         element={<Salidas />} />
                    <Route path="/proveedores"     element={<Proveedores />} />
                    <Route path="/ubicaciones"     element={<Ubicaciones />} />
                    <Route path="/usuarios"        element={<Usuarios />} />
                    <Route path="/reportes"        element={<Reportes />} />
                    <Route path="/agente-ia"       element={<AgenteIA />} />
                </Route>
            </Routes>
        </Router>
    );
}
