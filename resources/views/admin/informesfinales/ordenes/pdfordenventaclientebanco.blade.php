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
         }
         .table th, .table td {
             padding: 8px;
             text-align: center;
             font-size: 10px;
         }
         .table th {
             background-color: green;
             color: white;
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
    justify-content: space-between; /* Espacia los elementos entre el inicio y el final del contenedor */
    margin-top: 10px; /* Ajusta el margen según sea necesario */
}

.company-info {
    text-align: left; /* Asegura que el texto esté alineado a la izquierda */
    font-size: 10px;
}

.header-right {
    text-align: right; /* Asegura que el texto esté alineado a la derecha */
    font-size: 10px; /* Mantiene el mismo tamaño de fuente que tenías */
    margin-top: -100px;
}

     </style>
 </head>
 <body>
     <div class="container">
         <header  style="margin-top: -25px;">
             <img src="{{ public_path('membrete/logogl.png') }}" alt="Logo">
             <h1 style="margin-top: -40px;">ORDEN DE VENTA</h1>
         </header>
 
         <div class="info-container">
            <div class="company-info"> 
                <p>SANTA CRUZ: AV. RENE MORENO NRO 484 ESQ. ANA BARBA</p>
                <p>COCHABAMBA: CALLE LANZA NRO 940 ENTRE AV. RAMON RIVERO Y ORURO</p>
                <p>TELÉFONO: 65045401 - 4507269 - 3259385</p>
            </div>
            
            <div class="header-right">
                <p>FECHA: 2024/10/08</p>
                <p>ID ORDEN DE SALIDA NRO: 4955</p>
                <p>COMPROBANTE: RECIBO</p>
            </div>
        </div>
        
 
         <div class="client-personal">
             <span>CLIENTE/PERSONAL</span>
             <span>ALIANZA SEGUROS - {{$clientebanconombre}}</span>
         </div>
 
         <div class="company-info">
             <p>ALIANZA SEGUROS</p>
             <p>Teléfono: 72202446</p>
             <p>Ciudad: Santa Cruz</p>
             <p>Dirección: Cualquier direccion</p>
             <p>Servicio: Cualquier servicio</p>
             <p>Num Cuenta: Cualquier numero</p>
             <p>Tipo de Transacción: Banco</p>
         </div>
 
         <div class="header-right" style="margin-top: -300px;">
             <p>VANESSA MAMANI HUANACO</p>
             <p>0</p>
             <p>SANTA CRUZ</p>
             <p>Cualquier banco</p>
             <p>NUMERO DE CUENTA: Cualquier nunmero</p>
             <p>TITULAR: FABRICIO PRADO PARRADO</p>
         </div>
 
         <table class="table">
             <thead>
                 <tr>
                     <th>DETALLE</th>
                     <th>SALIDA</th>
                     <th>DESTINO</th>
                     <th>PRECIO</th>
                     <th>TIPO TRANSACCION</th>
                     <th>FECHA DE PAGO</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach ($bateriasubclientes as $bateriasubcliente)
                     <tr>
                         <td>{{$bateriasubcliente->accionnombre}}</td>
                         <td>Banco</td>
                         <td>Banco</td>
                         <td class="precio">{{$bateriasubcliente->precio}}</td>
                         <td>Transferencia bancaria</td>
                         <td>22/10/2024</td>
                     </tr>
                 @endforeach
             </tbody>
         </table>
 
         <div class="totals">
             <p><strong>SUB TOTAL:</strong> {{$total}}</p>
             <p><strong>DESCUENTOS:</strong> 0</p>
             <p><strong>TOTAL:</strong> {{$total}}</p>
         </div>
 
         <p><strong>Encargado:</strong> VANESSA MAMANI HUANACO</p>
         <p>Tipo de transacción entregada: Transferencias Bancarias</p>
     </div>
 
 </body>
 </html>
 