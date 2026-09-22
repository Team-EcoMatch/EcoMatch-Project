<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte EcoMatch</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111;
            padding: 20px;
        }

        .header {
            border-bottom: 3px solid #10b981;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 20px;
            color: #065f46;
            margin-bottom: 4px;
        }

        .header p {
            color: #666;
            font-size: 10px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 9px;
            color: #555;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h2 {
            font-size: 13px;
            color: #065f46;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th,
        td {
            padding: 5px 7px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }

        th {
            background: #f0fdf4;
            font-weight: bold;
            color: #065f46;
        }

        .stats {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .stat-box {
            border: 1px solid #d1d5db;
            padding: 8px;
            flex: 1;
        }

        .stat-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
            margin-top: 2px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>EcoMatch - Reporte de Actividad</h1>
        <p>{{ $titulo }}</p>
    </div>

    <div class="meta">
        <span><strong>Periodo:</strong> {{ $desde }} al {{ $hasta }}</span>
        <span><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</span>
    </div>

    <div class="section">
        <h2>Indicadores Generales</h2>
        <div class="stats">
            <div class="stat-box">
                <div class="stat-label">Publicaciones</div>
                <div class="stat-value">{{ $indicadores['totalPublicaciones'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Solicitudes</div>
                <div class="stat-value">{{ $indicadores['totalSolicitudes'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Tasa Aceptación</div>
                <div class="stat-value">{{ $indicadores['tasaAceptacion'] }}%</div>
            </div>
        </div>
    </div>

    @if(count($materialesIntercambiados) > 0)
    <div class="section">
        <h2>Impacto Ambiental - Materiales Intercambiados</h2>
        <table>
            <thead>
                <tr>
                    <th>Unidad</th>
                    <th>Cantidad Total</th>
                    <th>N° Intercambios</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materialesIntercambiados as $mat)
                <tr>
                    <td>{{ $mat->unidadMedida }}</td>
                    <td>{{ number_format($mat->total_cantidad, 2) }}</td>
                    <td>{{ $mat->total_intercambios }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <h2>Publicaciones por Estado</h2>
        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($publicacionesPorEstado as $estado => $total)
                <tr>
                    <td>{{ $estado }}</td>
                    <td>{{ $total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Solicitudes por Estado</h2>
        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudesPorEstado as $estado => $total)
                <tr>
                    <td>{{ $estado }}</td>
                    <td>{{ $total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(count($topMateriales) > 0)
    <div class="section">
        <h2>Top Materiales más Publicados</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Material</th>
                    <th>Publicaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topMateriales as $i => $mat)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $mat->nombre }}</td>
                    <td>{{ $mat->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(count($topEmpresas) > 0)
    <div class="section">
        <h2>Top Empresas con más Publicaciones</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>Publicaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topEmpresas as $i => $emp)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $emp->nombreEmpresa }}</td>
                    <td>{{ $emp->publicaciones_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(count($porCategoria) > 0)
    <div class="section">
        <h2>Publicaciones por Categoría</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($porCategoria as $cat)
                <tr>
                    <td>{{ $cat->nombre }}</td>
                    <td>{{ $cat->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        EcoMatch - Economía Circular | Reporte generado automáticamente
    </div>
</body>

</html>