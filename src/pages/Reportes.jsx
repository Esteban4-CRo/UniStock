import { useState, useEffect } from 'react';
import api from '../api';
import * as XLSX from 'xlsx';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { PackageOpen, Download, Upload, AlertTriangle, Factory, FileSpreadsheet, FileText, PackageX, CheckCircle2 } from 'lucide-react';

const REPORT_TYPES = [
    { id: 'inventario',  label: 'Inventario Completo', icon: <PackageOpen size={24} />, desc: 'Stock actual de todas las materias primas' },
    { id: 'entradas',    label: 'Historial de Entradas', icon: <Download size={24} />, desc: 'Registro de todas las entradas al inventario' },
    { id: 'salidas',     label: 'Historial de Salidas',  icon: <Upload size={24} />, desc: 'Registro de todas las salidas del inventario' },
    { id: 'alertas',     label: 'Reporte de Alertas',   icon: <AlertTriangle size={24} />, desc: 'Materias con stock bajo o alertas activas' },
    { id: 'proveedores', label: 'Directorio de Proveedores', icon: <Factory size={24} />, desc: 'Lista completa de proveedores registrados' },
];

export default function Reportes() {
    const [selectedType, setSelectedType] = useState('inventario');
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [generating, setGenerating] = useState('');

    useEffect(() => {
        loadData(selectedType);
    }, [selectedType]);

    const loadData = async (type) => {
        setLoading(true);
        try {
            const endpointMap = {
                inventario:  'material-primas/',
                entradas:    'entradas/',
                salidas:     'salidas/',
                alertas:     'alertas/',
                proveedores: 'proveedores/',
            };
            const res = await api.get(endpointMap[type]);
            setData(res.data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const getTableConfig = () => {
        switch (selectedType) {
            case 'inventario':
                return {
                    columns: ['ID', 'Nombre', 'Unidad', 'Stock Actual', 'Stock Mínimo', 'Estado', 'Registrado Por', 'Fecha Creación'],
                    rows: data.map(m => [
                        m.id,
                        m.nombre,
                        m.unidad_medida,
                        m.cantidad,
                        m.stock_minimo,
                        parseFloat(m.cantidad) <= parseFloat(m.stock_minimo) ? 'STOCK BAJO' : 'NORMAL',
                        m.usuario_nombre || '—',
                        m.created_at ? new Date(m.created_at).toLocaleString('es-CO') : '—',
                    ]),
                };
            case 'entradas':
                return {
                    columns: ['ID', 'Material', 'Cantidad', 'Proveedor', 'Lote', 'Motivo', 'Fecha', 'Usuario', 'Estado'],
                    rows: data.map(e => [
                        e.id,
                        e.material_prima_nombre || `Mat. #${e.material_prima}`,
                        e.cantidad,
                        e.proveedor_nombre || '—',
                        e.lote || '—',
                        e.motivo || '—',
                        e.fecha_entrada ? new Date(e.fecha_entrada).toLocaleString('es-CO') : '—',
                        e.usuario_nombre || '—',
                        e.anulado ? 'ANULADA' : 'COMPLETADA',
                    ]),
                };
            case 'salidas':
                return {
                    columns: ['ID', 'Material', 'Destino', 'Cantidad', 'Lote', 'Motivo', 'Fecha', 'Usuario', 'Estado'],
                    rows: data.map(s => [
                        s.id,
                        s.material_prima_nombre || `Mat. #${s.material_prima}`,
                        s.destino || '—',
                        s.cantidad,
                        s.lote || '—',
                        s.motivo || '—',
                        s.fecha_salida ? new Date(s.fecha_salida).toLocaleString('es-CO') : '—',
                        s.usuario_nombre || '—',
                        s.anulado ? 'ANULADA' : 'COMPLETADA',
                    ]),
                };
            case 'alertas':
                return {
                    columns: ['ID', 'Tipo', 'Mensaje', 'Estado', 'Fecha'],
                    rows: data.map(a => [
                        a.id,
                        a.tipo || '—',
                        a.mensaje,
                        a.estado === 'activa' ? 'ACTIVA' : 'LEÍDA',
                        a.created_at ? new Date(a.created_at).toLocaleString('es-CO') : '—',
                    ]),
                };
            case 'proveedores':
                return {
                    columns: ['ID', 'Empresa', 'RUC/NIT', 'Teléfono', 'Ciudad', 'Dirección', 'Estado'],
                    rows: data.map(p => [
                        p.id,
                        p.empresa,
                        p.ruc || '—',
                        p.telefono || '—',
                        p.ciudad || '—',
                        p.direccion || '—',
                        p.estado_validacion === 'verificado' ? 'VERIFICADO' : 'PENDIENTE',
                    ]),
                };
            default:
                return { columns: [], rows: [] };
        }
    };

    const activeReport = REPORT_TYPES.find(r => r.id === selectedType);
    const reportLabel = activeReport?.label || 'Reporte';
    const { columns, rows } = getTableConfig();

    const exportExcel = () => {
        setGenerating('excel');
        setTimeout(() => {
            try {
                const wsData = [columns, ...rows];
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                ws['!cols'] = columns.map(() => ({ wch: 20 }));

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, reportLabel.substring(0, 31));

                const filename = `UniStock_${selectedType}_${new Date().toISOString().split('T')[0]}.xlsx`;
                XLSX.writeFile(wb, filename);
            } catch (err) {
                console.error('Error generando Excel:', err);
            } finally {
                setGenerating('');
            }
        }, 100);
    };

    const exportPDF = () => {
        setGenerating('pdf');
        setTimeout(() => {
            try {
                const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

                doc.setFillColor(11, 11, 11);
                doc.rect(0, 0, 297, 22, 'F');
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('UniStock — Sistema de Gestión de Inventario', 14, 10);
                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');
                doc.text(reportLabel, 14, 17);

                doc.setTextColor(255, 255, 255);
                doc.setFontSize(8);
                doc.text(`Generado: ${new Date().toLocaleString('es-CO')}`, 250, 17);

                autoTable(doc, {
                    startY: 28,
                    head: [columns],
                    body: rows,
                    styles: {
                        fontSize: 8,
                        cellPadding: 3,
                        textColor: [30, 30, 40],
                        lineColor: [220, 220, 235],
                        lineWidth: 0.3,
                    },
                    headStyles: {
                        fillColor: [15, 17, 26],
                        textColor: [255, 255, 255],
                        fontStyle: 'bold',
                        fontSize: 8,
                        halign: 'center',
                    },
                    alternateRowStyles: {
                        fillColor: [245, 246, 250],
                    },
                    rowPageBreak: 'auto',
                });

                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(7);
                    doc.setTextColor(150, 150, 160);
                    doc.text(`Página ${i} de ${pageCount}  |  UniStock © ${new Date().getFullYear()}`, 14, doc.internal.pageSize.height - 8);
                }

                const filename = `UniStock_${selectedType}_${new Date().toISOString().split('T')[0]}.pdf`;
                doc.save(filename);
            } catch (err) {
                console.error('Error generando PDF:', err);
            } finally {
                setGenerating('');
            }
        }, 100);
    };

    return (
        <div>
            <div className="page-header">
                <h1 className="page-title">Reportes</h1>
                <p className="page-subtitle">Genera y exporta reportes detallados en Excel o PDF</p>
            </div>

            <div className="report-type-grid">
                {REPORT_TYPES.map(type => (
                    <div
                        key={type.id}
                        onClick={() => setSelectedType(type.id)}
                        className={`report-type-card ${selectedType === type.id ? 'selected' : ''}`}
                    >
                        <div className="rt-icon" style={{ color: selectedType === type.id ? 'var(--accent)' : 'var(--text-muted)' }}>
                            {type.icon}
                        </div>
                        <div className="rt-label" style={{ color: selectedType === type.id ? 'var(--accent)' : 'var(--text-primary)' }}>
                            {type.label}
                        </div>
                        <div className="rt-desc">{type.desc}</div>
                    </div>
                ))}
            </div>

            <div className="card">
                <div className="card-header">
                    <span style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        {activeReport?.icon} {reportLabel}
                    </span>
                    <span className="badge badge-dark" style={{ marginLeft: '0.5rem' }}>{data.length} registros</span>
                    
                    <div style={{ marginLeft: 'auto', display: 'flex', gap: '0.5rem' }}>
                        <button className="btn btn-secondary btn-sm" onClick={exportExcel} disabled={!!generating || loading || data.length === 0}>
                            {generating === 'excel' ? <><span className="spinner dark" style={{ width: 14, height: 14, borderWidth: 2 }} /> Generando...</> : <><FileSpreadsheet size={16} /> Exportar Excel</>}
                        </button>
                        <button className="btn btn-primary btn-sm" onClick={exportPDF} disabled={!!generating || loading || data.length === 0}>
                            {generating === 'pdf' ? <><span className="spinner" style={{ width: 14, height: 14, borderWidth: 2 }} /> Generando...</> : <><FileText size={16} /> Exportar PDF</>}
                        </button>
                    </div>
                </div>
                
                <div className="card-body" style={{ padding: 0 }}>
                    {loading ? (
                        <div className="empty-state">
                            <div className="spinner dark" style={{ margin: '0 auto 1rem', width: 32, height: 32 }} />
                            <div className="empty-state-text">Cargando datos...</div>
                        </div>
                    ) : data.length === 0 ? (
                        <div className="empty-state">
                            <div className="empty-state-icon"><PackageX size={48} /></div>
                            <div className="empty-state-title">Sin resultados</div>
                            <div className="empty-state-text">No hay datos para este reporte</div>
                        </div>
                    ) : (
                        <div className="table-wrapper">
                            <table className="data-table">
                                <thead>
                                    <tr>{columns.map(col => <th key={col}>{col}</th>)}</tr>
                                </thead>
                                <tbody>
                                    {rows.slice(0, 50).map((row, i) => (
                                        <tr key={i}>
                                            {row.map((cell, j) => (
                                                <td key={j}>
                                                    {cell === 'STOCK BAJO' ? <span className="badge badge-danger" style={{ display: 'flex', gap: '0.2rem' }}><AlertTriangle size={12}/> STOCK BAJO</span>
                                                    : cell === 'ANULADA'   ? <span className="badge badge-danger">Anulada</span>
                                                    : cell === 'COMPLETADA' ? <span className="badge badge-success" style={{ display: 'flex', gap: '0.2rem' }}><CheckCircle2 size={12}/> Completada</span>
                                                    : cell === 'NORMAL'    ? <span className="badge badge-success">Normal</span>
                                                    : cell === 'SÍ' && columns[j] === 'Resuelta' ? <span className="badge badge-success">SÍ</span>
                                                    : cell === 'NO' && columns[j] === 'Resuelta' ? <span className="badge badge-warning">NO</span>
                                                    : String(cell)}
                                                </td>
                                            ))}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            {data.length > 50 && (
                                <div style={{ padding: '1rem', textAlign: 'center', color: 'var(--text-muted)', fontSize: '0.8rem', background: '#fafafa' }}>
                                    Mostrando 50 de {data.length} registros. El archivo exportado incluye todos.
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
