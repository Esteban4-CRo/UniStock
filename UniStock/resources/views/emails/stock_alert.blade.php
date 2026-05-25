<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alerta Crítica de Inventario</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #1a1a2e; padding: 30px 20px; text-align: center; }
        .header img { max-width: 150px; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .content h2 { color: #1a1a2e; margin-top: 0; }
        .alert-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px 20px; margin: 25px 0; border-radius: 4px; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 12px 15px; border-bottom: 1px solid #eeeeee; text-align: left; }
        .table th { background-color: #f8f9fa; color: #666666; font-weight: 600; font-size: 0.9em; text-transform: uppercase; }
        .table tr:last-child td { border-bottom: none; }
        .footer { background-color: #f8f9fa; padding: 25px 30px; text-align: center; color: #777777; font-size: 0.9em; border-top: 1px solid #eeeeee; }
        .ceo-signature { margin-top: 40px; }
        .ceo-signature p { margin: 5px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0f62fe; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo en vez del texto si existiera una URL pública. Como es local, lo simulamos o usamos CID si lo adjuntáramos, pero aquí usaremos texto/logo estilizado -->
        <div class="header">
            <!-- Si tienes la app publicada, aquí iría la URL absoluta del logo: src="{{ asset('images/logo.png') }}" no funciona bien en emails si no está en internet -->
            <h1 style="color: white; margin: 0; font-size: 28px; letter-spacing: 1px;">UniStock <span style="color: #ffc107;">Alerts</span></h1>
        </div>
        
        <div class="content">
            <h2>Notificación Automática de Abastecimiento</h2>
            
            <p>Estimado equipo,</p>
            
            <p>El sistema inteligente de UniStock ha detectado una reducción crítica en los niveles de inventario de uno o más insumos clave. Esta es una alerta prioritaria para garantizar la continuidad de nuestras operaciones.</p>
            
            <div class="alert-box">
                <strong>Motivo de la Alerta:</strong> Los siguientes materiales han alcanzado o perforado el umbral de stock mínimo de seguridad establecido.
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Material Prima</th>
                        <th>Stock Actual</th>
                        <th>Nivel Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts as $alert)
                    <tr>
                        <td><strong>{{ $alert['codigo'] }}</strong></td>
                        <td>{{ $alert['nombre'] }}</td>
                        <td style="color: #dc3545; font-weight: bold;">{{ $alert['cantidad'] }}</td>
                        <td style="color: #6c757d;">{{ $alert['stock_minimo'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Se recomienda generar inmediatamente las órdenes de compra correspondientes para reabastecer el inventario a la brevedad.</p>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/home" class="btn">Acceder al Panel de Control</a>
            </div>

            <div class="ceo-signature">
                <p>Atentamente,</p>
                <p><img src="https://upload.wikimedia.org/wikipedia/commons/d/d4/Firma_de_Juan_Guaid%C3%B3.svg" style="height: 40px; margin-bottom: 5px; opacity: 0.8;" alt="Firma"></p>
                <p><strong>Juan Campino</strong><br>
                <span style="color: #666; font-size: 0.9em;">CEO & Founder, UniStock Inc.</span></p>
                <p style="font-size: 0.85em; color: #888; margin-top: 15px;">Fecha del reporte: {{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>
        
        <div class="footer">
            Este es un correo generado automáticamente por el motor de inteligencia y monitoreo de UniStock. Por favor no respondas a este mensaje.
            <br><br>
            &copy; {{ date('Y') }} UniStock Systems. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
