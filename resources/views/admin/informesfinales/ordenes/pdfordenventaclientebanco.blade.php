{{-- <!DOCTYPE html>
<html>
    <head>
        <style>
            @page {
                size: 8.5in 11in;
                margin: 0;
            }
            body {
                margin: 0cm 1.2cm 3.5cm 1.2cm;
                font-family: "Segoe UI";
                background: transparent;
            }
            .page-background {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                /* background-image: url('{{ public_path('membrete/ordendeventa.png') }}');  */
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                z-index: -1;
            }
            main {
                text-align: center;
                padding: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            h1 {
                color: #000000; 
                font-weight: 900;
                font-size: 25px;
                margin-bottom: 0px;
            }
            h2 {
                color: #94c93b; 
                font-weight: 900;
                font-size:25px;
                margin-top: 5px;
            }
            p {
                margin-top: 20px;
                font-size: 18px;
            }
        </style>
    </head>
<body>
    <div class="page-background"></div>
    <main>
        <div style="position: relative;">
            <img src="{{ public_path('membrete/logogl.png') }}" alt="Membrete" style="position: absolute; top: 0; left: 0; width: 150px; height: auto;">
            
            <div style="text-align: center; margin-top: 60px;">
                <h1>ORDEN DE VENTA</h1>
                <h2>{{$clientebanco->nombrecompleto}}</h2>
            </div>
    
            <table>
                <thead>
                    <tr>
                        <th>Tipo área</th>
                        <th>Área</th>
                        <th>Acción</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bateriasubclientes as $bateriasubcliente)
                        <tr>
                            <td>{{$bateriasubcliente->tipoarea}}</td>
                            <td>{{$bateriasubcliente->areanombre}}</td>
                            <td>{{$bateriasubcliente->accionnombre}}</td>
                            <td class="precio">{{$bateriasubcliente->precio}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    
            <p>Total: {{$total}}</p>
        </div>
    </main>
    
</body>
</html>
 --}}
 <!DOCTYPE html>
 <html lang="es">
 
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Orden de Salida</title>
     <style>
         @page {
             size: 8.5in 11in;
             margin: 0;
         }
 
         body {
             font-family: Arial, sans-serif;
             margin: 1cm 0.2cm 3.5cm 0.2cm;
             padding: 0;
             color: #000;
         }
 
         .container {
             width: 90%;
             margin: 0 auto;
             padding: 20px;
             font-size: 12px;
         }
 
         header {
             display: flex;
             justify-content: space-between;
             align-items: center;
         }
 
         header img {
             width: 150px;
         }
 
         /* .header-right {
              text-align: right;
              font-size: 10px;
          } */
         h1 {
             text-align: center;
             font-size: 26px;
             margin-top: 0;
         }
 
         /* .company-info {
              text-align: left;
              font-size: 10px;
              margin-top: 10px;
          } */
         .client-personal {
             background-color: orange;
             color: white;
             padding: 5px;
             display: flex;
             justify-content: space-between;
             font-weight: bold;
         }
 
         .details {
             margin-top: 10px;
         }
 
         .details-header {
             background-color: green;
             color: white;
             text-align: center;
             padding: 5px;
             font-weight: bold;
             margin-top: 10px;
         }
 
         .table {
             width: 100%;
             border-collapse: collapse;
             margin-top: 10px;
             font-size: 10px;
             border: 1px solid black;
             /* Borde grueso para toda la tabla */
         }
 
         .table th,
         .table td {
             padding: 5px 8px; /* Espaciado entre filas similar a un margen inferior de 5px */
             text-align: center;
             /* Centrar el texto en cada celda */
             font-size: 10px;
         }
 
         .table th {
             background-color: #94c93b;
             color: white;
             border: 1px solid black;
             text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
             /* Sombra ligera */
         }
 
         .table td {
             border-left: 1px solid black;
             /* Bordes laterales gruesos entre columnas */
             border-right: 1px solid black;
             border-bottom: 1px solid lightgray;
             /* Bordes finos entre filas */
         }
 
         .table tr:last-child td {
             border-bottom: 1px solid black;
             /* Borde inferior grueso en la última fila */
         }
 
         .totals {
             text-align: right;
             margin-top: 10px;
             font-size: 14px;
         }
 
         .totals p {
             margin: 0;
             font-size: 12px;
         }
 
         .info-container {
             display: flex;
             justify-content: space-between;
             /* Espacia los elementos entre el inicio y el final del contenedor */
             margin-top: 10px;
             /* Ajusta el margen según sea necesario */
         }
 
         .company-info {
             text-align: left;
             /* Asegura que el texto esté alineado a la izquierda */
             font-size: 10px;
         }
 
         .header-right {
             text-align: right;
             /* Asegura que el texto esté alineado a la derecha */
             font-size: 10px;
             /* Mantiene el mismo tamaño de fuente que tenías */
             margin-top: -100px;
         }
 
         .custom-info-container {
             display: flex;
             justify-content: space-between;
             align-items: flex-start;
             margin-top: 12px; /* Alinea los elementos en la parte superior */
             margin-bottom: 0px; /* Quita el margen inferior */
             padding-bottom: 35px; /* Mantiene el espaciado en la parte inferior */
         }
 
         .custom-company-info {
             font-size: 10px;
             text-align: left;
             flex: 1;
             padding-right: 20px;
         }
 
         .custom-header-right {
             font-size: 10px;
             text-align: right;
             align-self: flex-start;
             margin-top: -180px; /* Alinea este bloque en la parte superior */
             margin-bottom: 35px;
             /* Agrega el mismo margen inferior para igualar el espaciado */
         }
 
         .custom-company-info p,
         .custom-header-right p {
             margin: 0 0 5px 0;
         }
 
         th:last-child,
         td.precio {
             text-align: right;
         }
     </style>
 </head>
 
 <body>
     <div class="container">
         <header style="margin-top: -25px;">
             <img src="{{ public_path('membrete/logogl.png') }}" alt="Logo">
             <h1 style="margin-top: -40px; text-align: right;">ORDEN DE VENTA</h1>
         </header>
 
         <div class="info-container"
             style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; margin-top: 25px;">
             <div class="company-info" style="flex: 1; margin-right: 20px;">
                 <p style="margin: 0 0 5px 0;">NIT: 310634022</p>
                 <p style="margin: 0 0 5px 0;">SANTA CRUZ: AV. RENE MORENO NRO 484 ESQ. ANA BARBA</p>
                 <p style="margin: 0 0 5px 0;">COCHABAMBA: CALLE LANZA NRO 940 ENTRE AV. RAMON RIVERO Y ORURO</p>
                 <p style="margin: 0;">TELÉFONO: 65045401 - 4507269 - 3259385</p>
             </div>
 
             <div class="header-right" style="text-align: right; flex: 1;">
                 <p style="margin: 0; padding-bottom: 0;">MODALIDAD DE PAGO:<span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">CRÉDITO
                         CONTADO</span>
                 </p>
                 <p style="margin: 0; padding-bottom: 0;">NRO ORDEN DE VENTA:<span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">4955</span>
                 </p>
                 <p style="margin: 0; padding-bottom: 0;">FORMA DE PAGO:<span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">RECIBO</span>
                 </p>
                 <p style="margin: 0; padding-bottom: 0;">FECHA DE PAGO:<span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">2024/10/08</span>
                 </p>
             </div>
         </div>
 
         <div class="client-personal">
             <span>SEÑORES:</span>
             {{-- <span>{{ $asociado->asociado }} - {{ $clientebanconombre }}</span> --}}
         </div>
 
         <div class="custom-info-container">
             <!-- Bloque de la izquierda -->
             <div class="custom-company-info">
                 <p>CLIENTE: {{ $asociado->asociado ?? 'No disponible' }}</p>
                 <p>PERSONAL: {{ $clientebanconombre ?? 'No disponible' }}</p>
                 <p>TELÉFONO: {{ $asociado->telefono ?? 'No disponible' }}</p>
                 <p>CIUDAD: {{ $asociado->ciudad ?? 'No disponible' }}</p>
                 <p>DIRECCIÓN: {{ $asociado->direccion ?? 'No disponible' }}</p>
                 {{-- <p style="margin: 0 0 5px 0;">Servicio: Cualquier servicio</p> --}}
                 <p>NRO CUENTA: {{ $asociado->cuenta ?? 'No disponible' }}</p>
                 <p>TIPO DE CUENTA: {{ $asociado->tipocuenta ?? 'No disponible' }}</p>
             </div>
 
             <!-- Bloque de la derecha -->
             <div class="custom-header-right">
                 {{-- <p>{{ $personal->nombrecompleto ?? 'N/A' }}</p> <!-- Nombre completo del personal -->
                 <p>{{ $personal->celular ?? 'N/A' }}</p> <!-- Celular del personal -->
                 <p>{{ $personal->sucursal ?? 'N/A' }}</p> <!-- Sucursal del personal --> --}}
                 <p>ENTIDAD FINANCIERA: BANCO NACIONAL DE BOLIVIA</p>
                 <p>NRO DE CUENTA: 3000-189269</p>
                 <p>TITULAR: FABRICIO PRADO PARRADO</p>
             </div>
         </div>
 
         {{-- <div class="company-info">
             <p>ALIANZA SEGUROS</p>
             <p>Teléfono: 72202446</p>
             <p>Ciudad: Santa Cruz</p>
             <p>Dirección: Cualquier direccion</p>
             <p>Servicio: Cualquier servicio</p>
             <p>Num Cuenta: Cualquier número</p>
             <p>Tipo de Transacción: Banco</p>
         </div>
 
         <div class="header-right" style="margin-top: -300px;">
             <p>VANESSA MAMANI HUANACO</p>
             <p>0</p>
             <p>SANTA CRUZ</p>
             <p>Cualquier banco</p>
             <p>NUMERO DE CUENTA: Cualquier número</p>
             <p>TITULAR: FABRICIO PRADO PARRADO</p>
         </div> --}}
 
         <table class="table">
             <thead>
                 <tr>
                     <th>DETALLE</th>
                     <th>SALIDA</th>
                     <th>DESTINO</th>
                     <th>TIPO TRANSACCION</th>
                     {{-- <th>FECHA</th> --}}
                     <th>PRECIO P/U</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach ($bateriasubclientes as $bateriasubcliente)
                     <tr>
                         <td>{{ $bateriasubcliente->accionnombre }}</td>
                         <td>Banco</td>
                         <td>Banco</td>
                         <td>Transferencia bancaria</td>
                         {{-- <td>22/10/2024</td> --}}
                         <td class="precio">{{ $bateriasubcliente->precio }}</td>
                     </tr>
                 @endforeach
             </tbody>
         </table>
 
         <div class="totals-container"
             style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px; page-break-inside: avoid;">
             <!-- Cuadro a la izquierda -->
             <div class="company-info" style="width: 42.7%; padding: 5px; border: 2px solid #696969;">
                 <!-- Título con borde y alineado a la derecha -->
                 <div
                     style="border-bottom: 2px solid #696969; padding: 5px; background-color: #d3d3d3; text-align: left;">
                     <strong>OBSERVACIONES</strong>
                 </div>
                 <!-- Espacio en blanco para contenido adicional -->
                 <div style="height: 80px; background-color: white;"></div>
             </div>
 
             <!-- Totales a la derecha -->
             <div class="totals" style="text-align: right; flex-grow: 1; margin-left: 20px;" style="margin-top: -300px;">
                 <p style="margin: 0; padding-bottom: 0;"><strong>NETO:</strong><span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $total }}</span>
                 </p>
                 <p style="margin: 0; padding-bottom: 0;"><strong>SUB TOTAL:</strong><span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $total }}</span>
                 </p>
                 <p style="margin: 0; padding-bottom: 0;"><strong>DESCUENTOS:</strong><span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid black; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">0</span>
                 </p>
                 <p style="margin: 0; padding-bottom: 0;"><strong>TOTAL:</strong><span
                         style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $total }}</span>
                 </p>
             </div>
         </div>
 
         {{--  <p style="margin-top: 40px;"><strong>Encargado:</strong> {{ $usuario->name }}</p> --}}
         {{-- <p style="margin-top: 5px;">Tipo de transacción entregada: Transferencias Bancarias</p> --}}
 
     </div>
 
 </body>
 
 </html>
 