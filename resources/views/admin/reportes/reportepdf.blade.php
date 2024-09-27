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
                <th>ID</th>
                @if ($tabla == 'proveedores')
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Celular</th>
                    <th>NIT</th>
                    <th>Ciudad</th>
                    <th>Nro. Cuenta</th>
                    <th>Estado</th>
                @elseif ($tabla == 'bateriasubclientes')
                    <th>ID Cliente</th>
                    <th>Cliente</th>
                    <th>Tipo Cliente</th>
                    <th>Tipo de Área</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Precio</th>
                    <th>Fecha de Batería</th>
                @elseif ($tabla == 'bateriaproveedores')
                    <th>Proveedor</th>
                    <th>Tipo de Área</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Precio Compra</th>
                    <th>Precio Venta</th>
                    <th>Sucursal</th>
                    <th>Asociado</th>
                @elseif ($tabla == 'areaacciones')
                    <th>Tipo de Área</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Precio Compra</th>
                    <th>Precio Venta</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
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
                @elseif ($tabla == 'clientesbancos')
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
                    <th>Proveedor</th>
                    <th>Cliente</th>
                    <th>Fecha de Batería</th>
                    <th>Fecha asignada</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Fecha de atención</th>
                    <th>Documentación</th>
                @elseif ($tabla == 'programacionsubclientescomunes')
                    <th>Proveedor</th>
                    <th>Cliente</th>
                    <th>Fecha de Batería</th>
                    <th>Fecha asignada</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Fecha de atención</th>
                    <th>Documentación</th>
                @elseif ($tabla == 'programacionsubclientesauditoria')
                    <th>Proveedor</th>
                    <th>Cliente</th>
                    <th>Fecha de Batería</th>
                    <th>Fecha asignada</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Fecha de atención</th>
                    <th>Documentación</th>
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
                @elseif ($tabla == 'bateriasubclientes')
                    <td>{{ $item->clienteid }}{{ $item->clientecomunid }}{{ $item->clienteitaid }}{{ $item->clienteauditoriaid }}</td>
                    <td>
                        @if (!empty($item->clientenombre))
                            {{ $item->clientenombre }}
                        @elseif (!empty($item->clientecomunnombre))
                            {{ $item->clientecomunnombre }}
                            @elseif (!empty($item->clienteitanombre))
                            {{ $item->clienteitanombre }}
                            @elseif (!empty($item->clienteauditorianombre))
                            {{ $item->clienteauditorianombre }}
                        @endif
                    </td>
                    <td>
                        @if (!empty($item->clientenombre))
                            CLIENTE BANCO
                        @elseif (!empty($item->clientecomunnombre))
                            CLIENTE COMÚN
                            @elseif (!empty($item->clienteitanombre))
                            CLIENTE ITA
                            @elseif (!empty($item->clienteauditorianombre))
                            CLIENTE AUDITORÍA
                        @endif
                    </td>
                    <td>{{ $item->tipoarea }}</td>
                    <td>{{ $item->areanombre }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->fechabateria }}</td>
                @elseif ($tabla == 'bateriaproveedores')
                    <td>{{ $item->proveedor }}</td>
                    <td>{{ $item->tipoarea }}</td>
                    <td>{{ $item->areanombre }}</td>
                    <td>{{ $item->accionnombre }}</td>
                    <td>{{ $item->preciocompra }}</td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->sucursal }}</td>
                    <td>{{ $item->asociado }}</td>
                @elseif ($tabla == 'areaacciones')
                    <td>{{ $item->tiponombre }}</td>
                    <td>{{ $item->area }}</td>
                    <td>{{ $item->accion }}</td>
                    <td>{{ $item->preciocompra }}</td>
                    <td>{{ $item->precio }}</td>
                    <td>{{ $item->sucursal }}</td>
                    <td>{{ $item->estado }}</td>
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
                @elseif ($tabla == 'clientesbancos')
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
                    <td>{{ $item->proveedornombre }}</td>
                    <td>{{ $item->clienteitanombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->fechaasignada }}</td>
                    <td>{{ $item->areanombre }}</td>
                    <td>{{ $item->accionnombre }}</td>
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
                @elseif ($tabla == 'programacionsubclientescomunes')
                    <td>{{ $item->proveedornombre }}</td>
                    <td>{{ $item->clientecomunnombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->fechaasignada }}</td>
                    <td>{{ $item->areanombre }}</td>
                    <td>{{ $item->accionnombre }}</td>
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
                @elseif ($tabla == 'programacionsubclientesauditoria')
                    <td>{{ $item->proveedornombre }}</td>
                    <td>{{ $item->clienteauditorianombre }}</td>
                    <td>{{ $item->fechabateria }}</td>
                    <td>{{ $item->fechaasignada }}</td>
                    <td>{{ $item->areanombre }}</td>
                    <td>{{ $item->accionnombre }}</td>
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
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
