@extends('adminlte::page')

@section('content_header')
<a class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#modalHistorial">
    VER HISTORIAL DE TRASPASOS
</a>
<div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><strong>HISTORIAL DE TRASPASOS</strong></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID Reg.</th>
                                <th>Código Origen</th>
                                <th>Código Destino</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Usuario Traspaso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historial as $r)
                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td>{{ $r->codigoproducto }}</td>
                                    <td>{{ $r->codprodtraspaso }}</td>
                                    <td>{{ $r->cantidad }}</td>
                                    <td>{{ $r->fechamovimiento }}</td>
                                    <td>{{ $r->usuarioregistronombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<h1>TRASPASO DE INVENTARIO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .table td {
        padding: 5px 10px;
    }
    .btn-registrar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-registrar:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-actualizar {
        background-color:  #ffffff;
        color: #e8932b;
        border-color: #e8932b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-actualizar:hover {
        background-color: #e8932b;
        color: #ffffff;
        }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .btn-verregistros {
        background-color:  #ffffff;
        color: #787878;
        border-color: #787878;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verregistros:hover {
        background-color: #787878;
        color: #ffffff;
        }
    .btn-custom2 {
        background-color:  #ffffff;
        color: #9d9d9d;
        border-color: #9d9d9d;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom2:hover {
        background-color: #9d9d9d;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom2:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
    }
    .circle {
        display: inline-block;
        width: 30px;
        height: 20px;
        line-height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-left: 8px;
    }
    .nav-link.active .circle {
        background-color: #6e6e6e;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #6e6e6e;
        color: #fff;
    }
</style>
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif
<div class="card p-3">
    <div class="row">
        <div class="col-lg-4 col-12">
            <label><strong>Buscar producto</strong></label>
            <input type="text" id="buscador" class="form-control" placeholder="BUSCAR PRODUCTO...">
        </div>
        <div class="col-lg-3 col-6">
            <label><strong>Tipo Traspaso</strong></label>
            <select id="tipoTraspaso" class="form-control">
                <option value="existente" selected>TRASPASAR A ITEM EXISTENTE</option>
                <option value="nuevo">CREAR ITEM</option>
            </select>
        </div>
        <div class="col-lg-2 col-6">
            <label><strong>Cantidad Traspaso</strong></label>
            <input type="number" id="cantidad" class="form-control" placeholder="CANTIDAD">
        </div>
        <div class="col-lg-2 col-6">
            <label><strong>Sucursal Traspaso</strong></label>
            <select id="sucursalTraspaso" class="form-control">
                <option value="SANTA CRUZ">SANTA CRUZ</option>
                <option value="COCHABAMBA">COCHABAMBA</option>
            </select>
        </div>
        <div class="col-lg-1 col-6 d-flex align-items-end">
            <button class="btn btn-secondary w-100" id="btnTraspasar" title="TRASPASAR">
                <i class="fas fa-share"></i>
            </button>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-body">
            
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="tablaOrigen">
                    <h5><strong>TRASPASAR DE:</strong></h5>
                    <thead class="table-secondary">
                        <tr>
                            <th>Selec.</th>
                            <th>Código_Prod.</th>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th>Especificación</th>
                            <th>Materia_Prima</th>
                            <th>Color</th>
                            <th>Marca</th>
                            <th>Stock</th>
                            <th>Ciudad</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- <h5><strong>TRASPASAR A:</strong></h5> --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="tablaDestino">
                    <h5><strong>TRASPASAR A:</strong></h5>
                    <thead class="table-secondary">
                        <tr>
                            <th>Selec.</th>
                            <th>Código_Prod.</th>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th>Especificación</th>
                            <th>Materia_Prima</th>
                            <th>Color</th>
                            <th>Marca</th>
                            <th>Stock</th>
                            <th>Ciudad</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let origenSeleccionado = null;
let destinoSeleccionado = null;

$('#buscador').on('keyup', function() {
    let buscar = $(this).val();

    $.get('{{ url("buscar-productos") }}', {buscar: buscar}, function(data) {

        let filas = '';

        data.forEach(p => {
            filas += `
                <tr>
                    <td><input type="checkbox" class="checkOrigen" value="${p.codigo}"></td>
                    <td>${p.codigo}</td>
                    <td>${p.tipoinventario}</td>
                    <td>${p.nombreproducto}</td>
                    <td>${p.especificacionmedida}</td>
                    <td>${p.materiaprima}</td>
                    <td>${p.color}</td>
                    <td>${p.marca}</td>
                    <td>${p.stockactual}</td>
                    <td>${p.ciudad}</td>
                </tr>
            `;
        });

        $('#tablaOrigen tbody').html(filas);

        let filas2 = filas.replaceAll('checkOrigen', 'checkDestino');
        $('#tablaDestino tbody').html(filas2);
    });
});





// 🔹 SELECCIÓN DESTINO
$(document).on('change', '.checkDestino', function() {

    if ($(this).is(':checked')) {
        destinoSeleccionado = $(this).val();

        $('.checkDestino').not(this).closest('tr').hide();
    } else {
        destinoSeleccionado = null;
        $('#tablaDestino tbody tr').show();
    }
});


// 🔹 TRASPASO
$('#btnTraspasar').click(function() {

    let cantidad = parseFloat($('#cantidad').val());
    let tipo = $('#tipoTraspaso').val();
    let sucursal = $('#sucursalTraspaso').val();

    if (!origenSeleccionado) {
    alert('Debes seleccionar origen');
    return;
}

    if (!cantidad || cantidad <= 0) {
        alert('Debes ingresar una cantidad válida mayor a 0');
        return;
    }

    if (tipo === 'existente' && !destinoSeleccionado) {
    alert('Debes seleccionar destino');
    return;
}

if (tipo === 'nuevo' && !$('#sucursalTraspaso').val()) {
    alert('Debe seleccionarse la sucursal destino');
    return;
}

    // 🔹 obtener stock del origen seleccionado
    let filaOrigen = $('#tablaOrigen input[value="'+origenSeleccionado+'"]').closest('tr');
    let stock = parseFloat(filaOrigen.find('td').eq(8).text());

    if (cantidad > stock) {
        alert('La cantidad supera el stock disponible');
        return;
    }

    $.post('{{ url("realizar-traspaso") }}', {
        _token: '{{ csrf_token() }}',
        codigo_origen: origenSeleccionado,
        codigo_destino: destinoSeleccionado,
        cantidad: cantidad,
        tipo: tipo,
        sucursal: sucursal
    }, function(res) {

        alert('Traspaso realizado correctamente');
        location.reload();

    }).fail(function(err) {
        alert(err.responseJSON.error);
    });

});

$('#tipoTraspaso').change(function() {

    let tipo = $(this).val();

    if (tipo === 'nuevo') {

        $('#tablaDestino tbody tr').show();
        // 🔥 1. deseleccionar todo destino
        $('.checkDestino').prop('checked', false);

        // 🔥 2. limpiar variable
        destinoSeleccionado = null;

        // 🔥 3. mostrar todas las filas (por si estaban ocultas)
        $('#tablaDestino tbody tr').show();

        // 🔥 4. ocultar tabla destino
        $('#tablaDestino').closest('.table-responsive').hide();

        // 🔥 5. habilitar sucursal
        $('#sucursalTraspaso').prop('disabled', false);

        // 🔥 6. autocompletar sucursal si hay origen
        if (origenSeleccionado) {

            let fila = $('#tablaOrigen input[value="'+origenSeleccionado+'"]').closest('tr');
            let ciudadOrigen = fila.find('td').eq(9).text().trim();

            if (ciudadOrigen === 'COCHABAMBA') {
                $('#sucursalTraspaso').val('SANTA CRUZ');
            } else {
                $('#sucursalTraspaso').val('COCHABAMBA');
            }
        }

    } else {

        // 🔹 mostrar tabla destino otra vez
        $('#tablaDestino').closest('.table-responsive').show();

        // 🔹 limpiar sucursal
        $('#sucursalTraspaso').val('');
        $('#sucursalTraspaso').prop('disabled', true);
    }
});
$(document).ready(function() {
    $('#sucursalTraspaso').val('');
    $('#sucursalTraspaso').prop('disabled', true);
});
$(document).on('change', '.checkOrigen', function() {

    if ($(this).is(':checked')) {

        origenSeleccionado = $(this).val();

        let fila = $(this).closest('tr');
        let ciudadOrigen = fila.find('td').eq(9).text().trim();

        // 🔥 ciudad destino
        let ciudadDestino = (ciudadOrigen === 'COCHABAMBA') ? 'SANTA CRUZ' : 'COCHABAMBA';

        // 🔥 FILTRAR TABLA DESTINO
        $('#tablaDestino tbody tr').each(function() {

            let ciudadFila = $(this).find('td').eq(9).text().trim();

            if (ciudadFila === ciudadDestino) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // 🔥 autocompletar sucursal SOLO si es nuevo
        if (ciudadOrigen === 'COCHABAMBA') {
            $('#sucursalTraspaso').val('SANTA CRUZ');
        } else {
            $('#sucursalTraspaso').val('COCHABAMBA');
        }
        // 🔥 ocultar otros orígenes
        $('.checkOrigen').not(this).closest('tr').hide();

    } else {

        origenSeleccionado = null;

        // 🔹 reset completo (IMPORTANTE: mostrar todo)
        $('#tablaOrigen tbody tr').show();
        $('#tablaDestino tbody tr').show();

        // 🔥 limpiar destino seleccionado también
        destinoSeleccionado = null;
        $('.checkDestino').prop('checked', false);
    }
});
</script>

@endsection