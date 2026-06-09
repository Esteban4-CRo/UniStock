import { useState, useEffect } from 'react';
import api from '../api';

export default function MateriasPrimas() {
    const [materias, setMaterias] = useState([]);
    const [codigo, setCodigo] = useState('');
    const [nombre, setNombre] = useState('');
    const [unidad, setUnidad] = useState('');
    const [minimo, setMinimo] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const res = await api.get('material-primas/');
            setMaterias(res.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await api.post('material-primas/', { codigo, nombre, unidad_medida: unidad, stock_minimo: minimo, stock_actual: 0 });
            loadData();
            setCodigo(''); setNombre(''); setUnidad(''); setMinimo('');
        } catch (err) { console.error(err); }
        finally { setSaving(false); }
    };

    const lowCount = materias.filter(m => parseFloat(m.stock_actual) <= parseFloat(m.stock_minimo)).length;

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title">Inventario</h1>
                <p className="page-subtitle">Gestión de materias primas · {materias.length} registros
                    {lowCount > 0 && <span className="badge badge-danger" style={{ marginLeft: '0.75rem' }}>⚠ {lowCount} con stock bajo</span>}
                </p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '320px 1fr', gap: '1rem', alignItems: 'start' }}>
                {/* Form */}
                <div className="card">
                    <div className="card-header">➕ Nueva Materia Prima</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Código</label>
                                <input type="text" className="form-control" placeholder="Ej: MAT-01" value={codigo} onChange={e => setCodigo(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Nombre</label>
                                <input type="text" className="form-control" placeholder="Ej: Cemento Portland" value={nombre} onChange={e => setNombre(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Unidad de Medida</label>
                                <input type="text" className="form-control" placeholder="Ej: kg, L, m³" value={unidad} onChange={e => setUnidad(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Stock Mínimo</label>
                                <input type="number" step="0.01" min="0" className="form-control" placeholder="0.00" value={minimo} onChange={e => setMinimo(e.target.value)} required />
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : 'Guardar'}
                            </button>
                        </form>
                    </div>
                </div>

                {/* Table */}
                <div className="card">
                    <div className="card-header">
                        Inventario
                        <span className="badge badge-accent" style={{ marginLeft: 'auto' }}>{materias.length}</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : materias.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon">📦</div>
                                <div className="empty-state-text">Sin registros aún</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Unidad</th>
                                            <th>Stock Actual</th>
                                            <th>Stock Mín.</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {materias.map(m => {
                                            const isLow = parseFloat(m.stock_actual) <= parseFloat(m.stock_minimo);
                                            return (
                                                <tr key={m.id}>
                                                    <td style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>{m.codigo || `#${m.id}`}</td>
                                                    <td style={{ fontWeight: 600 }}>{m.nombre}</td>
                                                    <td style={{ color: 'var(--text-secondary)' }}>{m.unidad_medida}</td>
                                                    <td>
                                                        <span style={{ color: isLow ? 'var(--danger)' : 'var(--success)', fontWeight: 700 }}>
                                                            {m.stock_actual}
                                                        </span>
                                                    </td>
                                                    <td style={{ color: 'var(--text-secondary)' }}>{m.stock_minimo}</td>
                                                    <td>
                                                        {isLow
                                                            ? <span className="badge badge-danger">⚠ Stock bajo</span>
                                                            : <span className="badge badge-success">✓ Normal</span>
                                                        }
                                                    </td>
                                                </tr>
                                            );
                                        })}
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
