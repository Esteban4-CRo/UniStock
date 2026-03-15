<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Historial - {{ $product->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
        }

        /* Header */
        .header {
            background: #0b0b0b;
            color: #ffffff;
            padding: 20px 24px;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 22px; font-weight: 800; letter-spacing: 1px; }
        .header .subtitle { font-size: 13px; color: #aaa; margin-top: 4px; }

        /* Meta info */
        .meta-box {
            background: #f7f7f7;
            border-left: 4px solid #0b0b0b;
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 4px;
        }
        .meta-box table { width: 100%; border-collapse: collapse; }
        .meta-box td { padding: 3px 8px; }
        .meta-box td:first-child { font-weight: 700; color: #555; width: 180px; }

        /* Section title */
        .section-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0b0b0b;
            border-bottom: 2px solid #0b0b0b;
            padding-bottom: 4px;
            margin: 20px 0 10px 0;
        }
        .section-title.entradas { color: #166534; border-color: #166534; }
        .section-title.salidas  { color: #991b1b; border-color: #991b1b; }

        /* Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table thead th {
            background: #1a1a1a;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data-table.entradas thead th { background: #166534; }
        table.data-table.salidas  thead th { background: #991b1b; }

        table.data-table tbody tr:nth-child(even) { background: #f9f9f9; }
        table.data-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
        }

        .no-records {
            text-align: center;
            color: #999;
            padding: 16px;
            font-style: italic;
            border: 1px dashed #ddd;
            background: #fafafa;
            border-radius: 4px;
        }

        /* Summary box */
        .summary {
            margin-top: 24px;
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 33%;
            text-align: center;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            margin: 4px;
        }
        .summary-number { font-size: 24px; font-weight: 900; color: #0b0b0b; }
        .summary-label  { font-size: 10px; color: #666; text-transform: uppercase; margin-top: 4px; }

        /* Footer */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #999;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>UniStock &mdash; Reporte de Historial</h1>
        <div class="subtitle">Informe completo de movimientos del producto</div>
    </div>

    <!-- Product Info -->
    <div class="meta-box">
        <table>
            <tr>
                <td>Producto</td>
                <td><strong>{{ $product->nombre }}</strong></td>
                <td>Código</td>
                <td><strong>{{ $product->codigo ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td>Stock Actual</td>
                <td><strong>{{ $product->stock_actual }} unidades</strong></td>
                <td>Estado</td>
                <td><strong>{{ ucfirst($product->estado ?? 'activo') }}</strong></td>
            </tr>
            <tr>
                <td>Generado por</td>
                <td><strong>{{ $generated_by }}</strong></td>
                <td>Fecha de Generación</td>
                <td><strong>{{ $generated_at }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Summary -->
    <table style="width:100%; border-collapse:separate; border-spacing:6px; margin-bottom:16px;">
        <tr>
            <td style="width:33%; text-align:center; background:#dcfce7; border:1px solid #86efac; border-radius:6px; padding:12px;">
                <div style="font-size:24px; font-weight:900; color:#166534;">{{ $entries->count() }}</div>
                <div style="font-size:10px; color:#166534; text-transform:uppercase; margin-top:4px;">Entradas Totales</div>
            </td>
            <td style="width:33%; text-align:center; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; padding:12px;">
                <div style="font-size:24px; font-weight:900; color:#991b1b;">{{ $exits->count() }}</div>
                <div style="font-size:10px; color:#991b1b; text-transform:uppercase; margin-top:4px;">Salidas Totales</div>
            </td>
            <td style="width:33%; text-align:center; background:#dbeafe; border:1px solid #93c5fd; border-radius:6px; padding:12px;">
                <div style="font-size:24px; font-weight:900; color:#1d4ed8;">{{ $entries->sum('cantidad') - $exits->sum('cantidad') }}</div>
                <div style="font-size:10px; color:#1d4ed8; text-transform:uppercase; margin-top:4px;">Neto (Entradas &minus; Salidas)</div>
            </td>
        </tr>
    </table>

    <!-- Entries -->
    <div class="section-title entradas">Entradas</div>
    @if($entries->count() > 0)
        <table class="data-table entradas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $index => $entry)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>+{{ $entry->cantidad }}</strong></td>
                        <td>{{ $entry->motivo ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-records">No hay entradas registradas para este producto.</div>
    @endif

    <!-- Exits -->
    <div class="section-title salidas">Salidas</div>
    @if($exits->count() > 0)
        <table class="data-table salidas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exits as $index => $exit)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $exit->created_at->format('d/m/Y H:i') }}</td>
                        <td style="color:#991b1b;"><strong>-{{ $exit->cantidad }}</strong></td>
                        <td>{{ $exit->motivo ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-records">No hay salidas registradas para este producto.</div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Reporte generado automaticamente por UniStock &bull; {{ $generated_at }} &bull; Usuario: {{ $generated_by }}
    </div>

</body>
</html>