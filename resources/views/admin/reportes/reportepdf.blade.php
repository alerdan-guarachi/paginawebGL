<!DOCTYPE html>
<html>
<head>
    <title>Reporte PDF</title>
    <style>
        @page {
            size: 8.5in 11in;
            margin-right: 40px;
            margin-left: 40px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
            max-width: 120px;
            word-wrap: break-word;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .container {
            width: 100%;
            text-align: center;
            margin-bottom: 0px;
        }
        .report-title {
            font-size: 20px;
            margin-top: -10px;
            margin-bottom: -10px;
        }
        .report-meta {
            font-size: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="report-title">REPORTE DE {{ $nombresTablas[$tabla] }}</h2>
        <p class="report-meta">{{ $fechaInicial }} - {{ $fechaFinal }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID Reg.</th>
                @if ($tabla == 'proveedores')
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Celular</th>
                    <th>NIT</th>
                    <th>Ciudad</th>
                    <th>Nro. Cuenta</th>
                    <th>Estado</th>
                @elseif ($tabla == 'bateriasubclientesita')
                    <th>ID Cli.</th>
                    <th>Cliente</th>
                    <th>Fecha Batería</th>
                    <th>Proveedor</th>
                    <th>Est. / Esp.</th>
                    <th>Pr. Venta</th>
                    <th>Pr. Compra</th>
                    <th>Usuario Reg.</th>
                @elseif ($tabla == 'bateriasubclientesauditoria')
                    <th>ID Cli.</th>
                    <th>Cliente</th>
                    <th>Fecha Batería</th>
                    <th>Proveedor</th>
                    <th>Est. / Esp.</th>
                    <th>Pr. Venta</th>
                    <th>Pr. Compra</th>
                    <th>Usuario Reg.</th>
                @elseif ($tabla == 'bateriasubclientescomunes')
                    <th>ID Cli.</th>
                    <th>Cliente</th>
                    <th>Fecha Batería</th>
                    <th>Proveedor</th>
                    <th>Est. / Esp.</th>
                    <th>Pr. Venta</th>
                    <th>Pr. Compra</th>
                    <th>Usuario Reg.</th>
                @elseif ($tabla == 'bateriaproveedores')
                    <th>Proveedor</th>
                    <th>Tipo de Área</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Precio Compra</th>
                    <th>Precio Venta</th>
                    <th>Sucursal</th>
                    <th>Asociado</th>
                @elseif ($tabla == 'clientescomunes')
                    <th>Nombre Completo</th>
                    <th>CI</th>
                    <th>Edad</th>
                    <th>Estado civil</th>
                    <th>Ocupación/Profesión</th>
                    <th>Sucursal</th>
                    <th>Usuario Registro</th>
                @elseif ($tabla == 'clientesauditoria')
                    <th>Nombre Completo</th>
                    <th>CI</th>
                    <th>Edad</th>
                    <th>Estado civil</th>
                    <th>Ocupación/Profesión</th>
                    <th>Sucursal</th>
                    <th>Usuario Registro</th>
                @elseif ($tabla == 'clientes')
                    <th>Nombre Completo</th>
                    <th>CI</th>
                    <th>Edad</th>
                    <th>Estado civil</th>
                    <th>Ocupación/Profesión</th>
                    <th>Sucursal</th>
                    <th>Usuario Registro</th>
                @elseif ($tabla == 'programacionsubclientesita')
                    <th>ID Cli.</th>
                    <th>Cliente</th>
                    <th>Proveedor</th>
                    <th>Fecha Batería</th>
                    <th>Est. / Esp.</th>
                    <th>Fecha Prog.</th>
                    <th>Atención</th>
                    <th>Fecha Informe</th>
                    <th>Usuario Reg.</th>
                @elseif ($tabla == 'programacionsubclientescomunes')
                    <th>ID Cli.</th>
                    <th>Cliente</th>
                    <th>Proveedor</th>
                    <th>Fecha Batería</th>
                    <th>Est. / Esp.</th>
                    <th>Fecha Prog.</th>
                    <th>Atención</th>
                    <th>Usuario Reg.</th>
                @elseif ($tabla == 'programacionsubclientesauditoria')
                    <th>ID Cli.</th>
                    <th>Cliente</th>
                    <th>Proveedor</th>
                    <th>Fecha Batería</th>
                    <th>Est. / Esp.</th>
                    <th>Fecha Prog.</th>
                    <th>Atención</th>
                    <th>Fecha Informe</th>
                    <th>Usuario Reg.</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                @if ($tabla == 'proveedores')
                    <td>{{ $item->proveedor }}</td>
                    <td>{{ $item->direccion }}</td>
                    <td>{{ $item->celular }}</td>
                    <td>{{ $item->nit }}</td>
                    <td>{{ $item->ciudad }}</td>
                    <td>{{ $item->cuenta }}</td>
                    <td>{{ $item->estadoproveedor }}</td>
                @elseif ($tabla == 'bateriasubclientesita')
                    <td>{{ $item->clienteitaid }}</td>
                    <td>{{ $item->clienteitanombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->proveedorasignado }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->preciocompra }}</td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'bateriasubclientesauditoria')
                    <td>{{ $item->clienteauditoriaid }}</td>
                    <td>{{ $item->clienteauditorianombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->proveedorasignado }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->preciocompra }}</td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'bateriasubclientescomunes')
                    <td>{{ $item->clientecomunid }}</td>
                    <td>{{ $item->clientecomunnombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->proveedorasignado }}</td>
                    <td>
                        {{ $item->accionnombre }} 
                        @if(!empty($item->sesiones) && $item->sesiones > 0)
                            - {{ $item->sesiones }} SESIONES
                        @endif
                    </td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->preciocompra }}</td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'bateriaproveedores')
                    <td>{{ $item->proveedor }}</td>
                    <td>{{ $item->tipoarea }}</td>
                    <td>{{ $item->areanombre }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->preciocompra }}</td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->sucursal }}</td>
                    <td>{{ $item->asociado }}</td>
                @elseif ($tabla == 'clientescomunes')
                    <td>{{ $item->nombrecompleto }}</td>
                    <td>{{ $item->ci }}</td>
                    <td>{{ $item->edad }}</td>
                    <td>{{ $item->estadocivil }}</td>
                    <td>{{ $item->ocupacionprofesion }}</td>
                    <td>{{ $item->sucursal }}</td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'clientesauditoria')
                    <td>{{ $item->nombrecompleto }}</td>
                    <td>{{ $item->ci }}</td>
                    <td>{{ $item->edad }}</td>
                    <td>{{ $item->estadocivil }}</td>
                    <td>{{ $item->ocupacionprofesion }}</td>
                    <td>{{ $item->sucursal }}</td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'clientes')
                    <td>{{ $item->nombrecompleto }}</td>
                    <td>{{ $item->ci }}</td>
                    <td>{{ $item->edad }}</td>
                    <td>{{ $item->estadocivil }}</td>
                    <td>{{ $item->ocupacionprofesion }}</td>
                    <td>{{ $item->sucursal }}</td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'programacionsubclientesita')
                    <td>{{ $item->clienteitaid }}</td>
                    <td>{{ $item->clienteitanombre }}</td>
                    <td>{{ $item->proveedornombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->fechaasignada }}</td>
                    <td>
                        @if ($item->fechaatencionprogramacion !== "PENDIENTE" && $item->fechaatencionprogramacion !== null)
                            {{ $item->fechaatencionprogramacion }}
                        @else
                            PENDIENTE
                        @endif
                    </td>
                    <td>
                        @if ($item->created_at !== "PENDIENTE" && $item->created_at !== null)
                            {{ $item->created_at->format('Y-m-d') }}
                        @else
                            PENDIENTE
                        @endif
                    </td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'programacionsubclientescomunes')
                    <td>{{ $item->clientecomunid }}</td>
                    <td>{{ $item->clientecomunnombre }}</td>
                    <td>{{ $item->proveedornombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->fechaasignada }}</td>
                    <td>
                        @if ($item->fechaatencionprogramacion !== "PENDIENTE" && $item->fechaatencionprogramacion !== null)
                            {{ $item->fechaatencionprogramacion }}
                        @else
                            PENDIENTE
                        @endif
                    </td>
                    <td>{{ $item->usuarioregistro }}</td>
                @elseif ($tabla == 'programacionsubclientesauditoria')
                    <td>{{ $item->clienteauditoriaid }}</td>
                    <td>{{ $item->clienteauditorianombre }}</td>
                    <td>{{ $item->proveedornombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->fechaasignada }}</td>
                    <td>
                        @if ($item->fechaatencionprogramacion !== "PENDIENTE" && $item->fechaatencionprogramacion !== null)
                            {{ $item->fechaatencionprogramacion }}
                        @else
                            PENDIENTE
                        @endif
                    </td>
                    <td>
                        @if ($item->created_at !== "PENDIENTE" && $item->created_at !== null)
                            {{ $item->created_at->format('Y-m-d') }}
                        @else
                            PENDIENTE
                        @endif
                    </td>
                    <td>{{ $item->usuarioregistro }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
