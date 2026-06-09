import { Link } from 'react-router-dom';
import { PackageOpen, Bot, FileBarChart, ArrowRight } from 'lucide-react';

export default function Landing() {
    return (
        <div style={{ minHeight: '100vh', background: 'var(--bg-root)', display: 'flex', flexDirection: 'column' }}>
            {/* Navbar */}
            <nav style={{ padding: '1rem 2.5rem', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'var(--bg-sidebar)' }}>
                <div style={{ color: '#fff', fontSize: '1.5rem', fontWeight: 800, letterSpacing: '-0.03em' }}>UniStock</div>
                <div style={{ display: 'flex', gap: '1rem' }}>
                    <Link to="/login" className="btn btn-ghost" style={{ color: '#fff' }}>Iniciar Sesión</Link>
                </div>
            </nav>

            {/* Hero Section */}
            <main style={{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '4rem 2rem', textAlign: 'center' }}>
                <h1 style={{ fontSize: '3.5rem', fontWeight: 900, color: 'var(--text-primary)', marginBottom: '1rem', maxWidth: '800px', lineHeight: 1.1, letterSpacing: '-0.04em' }}>
                    Gestión de Inventario Industrial, <span style={{ color: 'var(--text-muted)' }}>Simplificada.</span>
                </h1>
                <p style={{ fontSize: '1.1rem', color: 'var(--text-secondary)', marginBottom: '2.5rem', maxWidth: '600px', lineHeight: 1.6 }}>
                    Controla tus materias primas, monitorea entradas y salidas en tiempo real, y prevén el desabastecimiento con nuestro Agente IA y sistema de alertas automáticas.
                </p>
                <div style={{ display: 'flex', gap: '1rem' }}>
                    <Link to="/login" className="btn btn-primary btn-lg" style={{ padding: '0.8rem 2rem', fontSize: '1.1rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        Comenzar Ahora <ArrowRight size={20} />
                    </Link>
                </div>
            </main>

            {/* Features Section */}
            <section id="features" style={{ padding: '4rem 2rem', background: '#fff', borderTop: '1px solid var(--border-color)' }}>
                <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                    <h2 style={{ fontSize: '2rem', fontWeight: 800, textAlign: 'center', marginBottom: '3rem', letterSpacing: '-0.03em' }}>Todo lo que necesitas en un solo lugar</h2>
                    <div className="grid-3" style={{ gap: '2rem' }}>
                        <div style={{ padding: '2rem', background: 'var(--bg-root)', borderRadius: 'var(--radius-lg)' }}>
                            <div style={{ color: 'var(--accent)', marginBottom: '1rem' }}><PackageOpen size={48} /></div>
                            <h3 style={{ fontSize: '1.2rem', fontWeight: 700, marginBottom: '0.5rem' }}>Control en Tiempo Real</h3>
                            <p style={{ color: 'var(--text-secondary)', fontSize: '0.9rem', lineHeight: 1.5 }}>Visualiza el stock actual de todas tus materias primas y recibe alertas automáticas cuando los niveles estén por debajo del mínimo requerido.</p>
                        </div>
                        <div style={{ padding: '2rem', background: 'var(--bg-root)', borderRadius: 'var(--radius-lg)' }}>
                            <div style={{ color: 'var(--accent)', marginBottom: '1rem' }}><Bot size={48} /></div>
                            <h3 style={{ fontSize: '1.2rem', fontWeight: 700, marginBottom: '0.5rem' }}>Agente IA Integrado</h3>
                            <p style={{ color: 'var(--text-secondary)', fontSize: '0.9rem', lineHeight: 1.5 }}>Interactúa con Uni, nuestro asistente inteligente potenciado por Llama 3.1, que te ayudará a analizar datos y predecir necesidades de reabastecimiento.</p>
                        </div>
                        <div style={{ padding: '2rem', background: 'var(--bg-root)', borderRadius: 'var(--radius-lg)' }}>
                            <div style={{ color: 'var(--accent)', marginBottom: '1rem' }}><FileBarChart size={48} /></div>
                            <h3 style={{ fontSize: '1.2rem', fontWeight: 700, marginBottom: '0.5rem' }}>Reportes Detallados</h3>
                            <p style={{ color: 'var(--text-secondary)', fontSize: '0.9rem', lineHeight: 1.5 }}>Genera exportaciones en Excel y PDF con un solo clic. Mantén un registro histórico impecable de todas las entradas, salidas y proveedores.</p>
                        </div>
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer style={{ padding: '2rem', textAlign: 'center', background: 'var(--bg-sidebar)', color: 'rgba(255,255,255,0.6)', fontSize: '0.8rem' }}>
                UniStock © {new Date().getFullYear()}. Todos los derechos reservados.
            </footer>
        </div>
    );
}
