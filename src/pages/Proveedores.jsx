import { useState, useEffect } from 'react';
import api from '../api';
import { Building2, Edit2, Trash2 } from 'lucide-react';

export default function Proveedores() {
    const [proveedores, setProveedores] = useState([]);
    const [empresa, setEmpresa] = useState('');
    const [ruc, setRuc] = useState('');
    const [telefono, setTelefono] = useState('');
    const [direccion, setDireccion] = useState('');
    const [ciudad, setCiudad] = useState('');
    const [pais, setPais] = useState('Colombia');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [editId, setEditId] = useState(null);

    useEffect(() => { loadData(); }, []);

    const loadData = async () => {
        try {
            const res = await api.get('proveedores/');
            setProveedores(res.data);
        } catch (err) { console.error(err); }
        finally { setLoading(false); }
    };

    const resetForm = () => {
        setEmpresa(''); setRuc(''); setTelefono(''); setDireccion(''); setCiudad(''); setPais('Colombia');
        setEditId(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            const payload = { empresa, ruc, telefono, direccion, ciudad, pais };
            if (editId) {
                await api.put(`proveedores/${editId}/`, payload);
            } else {
                await api.post('proveedores/', payload);
            }
            loadData();
            resetForm();
        } catch (err) { console.error(err); alert('Error: Verifique que el RUC no este duplicado.'); }
        finally { setSaving(false); }
    };

    const handleEdit = (p) => {
        setEditId(p.id);
        setEmpresa(p.empresa);
        setRuc(p.ruc);
        setTelefono(p.telefono);
        setDireccion(p.direccion);
        setCiudad(p.ciudad || '');
        setPais(p.pais || 'Colombia');
    };

    const handleDelete = async (id) => {
        if (!confirm('Eliminar este proveedor?')) return;
        try { await api.delete(`proveedores/${id}/`); loadData(); }
        catch (err) { console.error(err); }
    };

    const validados = proveedores.filter(p => p.estado_validacion === 'validado').length;

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><Building2 size={28} /> Proveedores</h1>
                <p className="page-subtitle">Directorio de proveedores · {proveedores.length} registrados · {validados} validados</p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '340px 1fr', gap: '1rem', alignItems: 'start' }}>
                <div className="card">
                    <div className="card-header">{editId ? 'Editar Proveedor' : 'Nuevo Proveedor'}</div>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="form-group">
                                <label className="form-label">Empresa</label>
                                <input type="text" className="form-control" placeholder="Empresa S.A.S" value={empresa} onChange={e => setEmpresa(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">RUC / NIT</label>
                                <input type="text" className="form-control" placeholder="900123456-7" value={ruc} onChange={e => setRuc(e.target.value)} required disabled={!!editId} />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Telefono</label>
                                <input type="text" className="form-control" placeholder="+57 300 000 0000" value={telefono} onChange={e => setTelefono(e.target.value)} required />
                            </div>
                            <div className="form-group">
                                <label className="form-label">Direccion</label>
                                <input type="text" className="form-control" placeholder="Calle 123 #45-67" value={direccion} onChange={e => setDireccion(e.target.value)} required />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                                <div className="form-group">
                                    <label className="form-label">Ciudad</label>
                                    <input type="text" className="form-control" placeholder="Bogota" value={ciudad} onChange={e => setCiudad(e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Pais</label>
                                    <input type="text" className="form-control" value={pais} onChange={e => setPais(e.target.value)} />
                                </div>
                            </div>
                            <button type="submit" className="btn btn-primary btn-block" disabled={saving}>
                                {saving ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Guardando...</> : (editId ? 'Actualizar' : 'Guardar')}
                            </button>
                            {editId && <button type="button" className="btn btn-secondary btn-block" style={{ marginTop: '0.5rem' }} onClick={resetForm}>Cancelar</button>}
                        </form>
                    </div>
                </div>

                <div className="card">
                    <div className="card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        Lista de Proveedores
                        <span className="badge badge-accent">{proveedores.length}</span>
                    </div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {loading ? (
                            <div className="empty-state"><div className="spinner" style={{ margin: '0 auto' }} /></div>
                        ) : proveedores.length === 0 ? (
                            <div className="empty-state">
                                <div className="empty-state-icon"><Building2 size={48} /></div>
                                <div className="empty-state-text">Sin proveedores registrados</div>
                            </div>
                        ) : (
                            <div className="table-wrapper">
                                <table className="data-table">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>RUC</th>
                                            <th>Telefono</th>
                                            <th>Ciudad</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {proveedores.map(p => (
                                            <tr key={p.id}>
                                                <td style={{ fontWeight: 600 }}>{p.empresa}</td>
                                                <td style={{ color: 'var(--text-muted)', fontSize: '0.85rem' }}>{p.ruc}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{p.telefono}</td>
                                                <td style={{ color: 'var(--text-secondary)' }}>{p.ciudad || '--'}</td>
                                                <td>
                                                    {p.estado_validacion === 'validado'
                                                        ? <span className="badge badge-success">Validado</span>
                                                        : p.estado_validacion === 'rechazado'
                                                        ? <span className="badge badge-danger">Rechazado</span>
                                                        : <span className="badge badge-warning">Pendiente</span>
                                                    }
                                                </td>
                                                <td>
                                                    <div style={{ display: 'flex', gap: '0.4rem' }}>
                                                        <button className="btn btn-secondary btn-sm" onClick={() => handleEdit(p)}><Edit2 size={14} /></button>
                                                        <button className="btn btn-secondary btn-sm" onClick={() => handleDelete(p.id)} style={{ color: 'var(--danger)' }}><Trash2 size={14} /></button>
                                                    </div>
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
