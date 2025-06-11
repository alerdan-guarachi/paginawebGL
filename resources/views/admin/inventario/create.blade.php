@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-outline-secondary" href="{{ route('admin.inventario.index') }}">REGRESAR</a>
<h1>NUEVO PRODUCTO DE ALMACÉN</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 5000);
    </script>
@endif
    <div class="card">
        <div class="card-body">
            <div class="row ">
                <div class="col-lg-12">
                    {!! Form::open(['route' => 'admin.inventario.store', 'method'=>'POST']) !!}
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label for="tipoinventario">Tipo Inventario</label>
                                <select name="tipo_inventario" id="tipo_inventario" class="form-control">
                                    <option value=""></option>
                                    <option value="ALMACEN">ALMACEN</option>
                                    {{-- <option value="ACTIVO FIJO">ACTIVO FIJO</option> --}}
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="proveedornombre">Proveedor</label>
                                <select id="proveedornombre" name="proveedornombre" class="form-control" required>
                                    <option value=""></option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" data-razon="{{ $proveedor->razonsocial }}">
                                            {{ $proveedor->razonsocial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 form-group">
                                <label>ID.Prov</label>
                                <input type="text" id="proveedorid" name="proveedorid" class="form-control" readonly>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const select = document.getElementById('proveedornombre');
                                    const hiddenInput = document.getElementById('proveedorid');

                                    select.addEventListener('change', function () {
                                        hiddenInput.value = this.value;
                                    });
                                });
                            </script>

                            <div class="col-md-2 form-group">
                                <label for="ciudad">Ciudad</label>
                                <select id="ciudad" name="ciudad" class="form-control" required>
                                    <option value="" disabled selected></option>
                                    <option value="SANTA CRUZ">SANTA CRUZ</option>
                                    <option value="COCHABAMBA">COCHABAMBA</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label for="seccion">Sección</label>
                                <select name="seccion" id="seccion" class="form-control" required>
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="deposito">Deposito</label>
                                <select id="deposito" name="deposito" class="form-control" required>
                                    <option value="" disabled selected></option>
                                    <option value="PRINCIPAL">PRINCIPAL</option>
                                    <option value="SECUNDARIO">SECUNDARIO</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Producto</label>
                                <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Especif. Medida</label>
                                <input type="text" class="form-control" id="especificacionmedida" name="especificacionmedida" required>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="inventario">Inventario</label>
                                <select id="inventario" name="inventario" class="form-control" required>
                                    <option value="" disabled selected></option>
                                    <option value="PRINCIPAL">PRINCIPAL</option>
                                    <option value="ASIGNACION Y DEVOLUCION">ASIGNACION Y DEVOLUCION</option>
                                    <option value="STOCK DEPURADO">STOCK DEPURADO</option>
                                    <option value="AGOTADO">AGOTADO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label for="materiaprima">Mat. Prima</label>
                                <select name="materia_prima" id="materia_prima" class="form-control" required>
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label for="unidadmedida">Unidad Medida</label>
                                <select name="unidad_medida" id="unidad_medida" class="form-control" required>
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="marca">Marca</label>
                                <select name="marca" id="marca" class="form-control" required>
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="marca">Color</label>
                                <div class="d-flex align-items-center">
                                    {!! Form::text('color', null, ['class' => 'form-control', 'id' => 'colorInput', 'readonly' => 'readonly', 'placeholder' => '']) !!}
                                    <div class="dropdown ml-2">
                                        <button class="btn btn-secondary dropdown-toggle color-selector" type="button" id="colorDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            ▼
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="colorDropdown">
                                            <div class="d-flex flex-wrap">
                                                <div class="col-4 p-1">
                                                    <div class="color-box" data-color="#000000"></div>
                                                    <div class="color-box" data-color="#FFFFFF"></div>
                                                    <div class="color-box" data-color="#FF0000"></div>
                                                    <div class="color-box" data-color="#008000"></div>
                                                    <div class="color-box" data-color="#0000FF"></div>
                                                </div>
                                                <div class="col-4 p-1">
                                                    <div class="color-box" data-color="#FFFF00"></div>
                                                    <div class="color-box" data-color="#FFA500"></div>
                                                    <div class="color-box" data-color="#800080"></div>
                                                    <div class="color-box" data-color="#FFC0CB"></div>
                                                    <div class="color-box" data-color="#808080"></div>
                                                </div>
                                                <div class="col-4 p-1">
                                                    <div class="color-box" data-color="#A52A2A"></div>
                                                    <div class="color-box" data-color="#00BFFF"></div>
                                                    <div class="color-box" data-color="#FFD700"></div>
                                                    <div class="color-box" data-color="#008B8B"></div>
                                                    <div class="color-box" data-color="#B22222"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('color')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                                @enderror
                            </div>
                            
                            {{-- <div class="col-md-2 form-group">
                                <label>Modelo</label>
                                <input type="text" class="form-control" name="modelo" id="modelo">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Serie</label>
                                <input type="text" class="form-control" name="serie" id="serie">
                            </div> --}}
                            <div class="col-md-2 form-group">
                                <label>Cant. Minima</label>
                                <input type="number" class="form-control" id="minimocantidad" name="minimocantidad" required>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Stock Inicial</label>
                                <input type="number" class="form-control" id="stockinicial" name="stockinicial" required>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Stock Actual</label>
                                <input type="number" class="form-control" id="stockactual" name="stockactual" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label>Presentación</label>
                                <input type="text" class="form-control" id="presentacion" name="presentacion">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Unidades</label>
                                <input type="number" class="form-control" id="unidades" name="unidades" required>
                            </div>
                            <div class="col-md-2 form-group"> 
                                <label>Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Precio Total</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Precio Unitario</label>
                                <input type="number" step="0.01" class="form-control" id="preciounitario" name="preciounitario" required readonly>
                            </div>
                        </div>
                            
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const cantidadInput = document.getElementById("cantidad");
                                const precioTotalInput = document.getElementById("precio");
                                const precioUnitarioInput = document.getElementById("preciounitario");
                            
                                function calcularPrecioUnitario() {
                                    const cantidad = parseFloat(cantidadInput.value) || 0;
                                    const precioTotal = parseFloat(precioTotalInput.value) || 0;
                                    
                                    if (cantidad > 0) {
                                        precioUnitarioInput.value = (precioTotal / cantidad).toFixed(2);
                                    } else {
                                        precioUnitarioInput.value = "";
                                    }
                                }
                            
                                cantidadInput.addEventListener("input", calcularPrecioUnitario);
                                precioTotalInput.addEventListener("input", calcularPrecioUnitario);
                            });
                        </script>
                        {!! Form::submit('REGISTRAR PRODUCTO', ['class' => 'btn btn-sm btn-outline-secondary']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
</script>

{{-- RELLENAR SEGUN LO SELECCIONADO --}}
<script>
    const opciones = {
        "ALMACEN": {
            "seccion": ["ESCRITORIO", "COCINA", "USO MEDICO", "PROMOCIONAL", "LIMPIEZA", "CONSTRUCCION Y FERRETERIA", "INSUMOS DECORATIVOS"],
            "unidad_medida": {
                "ESCRITORIO": ["BOLSA", "CAJA", "PAQUETE", "UNIDADES"],
                "COCINA": ["UNIDADES", "BOLSA"],
                "USO MEDICO": ["CAJA", "UNIDADES"],
                "PROMOCIONAL": ["UNIDADES"],
                "LIMPIEZA": ["CAJA", "PAQUETE", "UNIDADES"],
                "CONSTRUCCION Y FERRETERIA": ["BOLSA", "CAJA", "ROLLO", "UNIDADES"],
                "INSUMOS DECORATIVOS": ["UNIDADES"]
            },
            "materia_prima": {
                "ESCRITORIO": ["ACRILICO", "CARBONICO", "CARTON", "GOMA", "LIQUIDO", "MADERA", "METALICO", "PAPEL", "PLASTICO", "TELA", "VENESTA"],
                "COCINA": ["PLASTICO", "MADERA", "PAPEL"],
                "USO MEDICO": ["FIBRA", "GEL", "GOMA", "ISOPROPILICO", "LATEX", "LIQUIDO", "MADERA", "METALICO", "PAPEL", "PLASTICO", "POLIPROPILENO", "ROLLO", "TELA", "TERMICO"],
                "PROMOCIONAL": ["ALGODÓN", "CARTULINA", "CERAMICA", "LONA", "METALICO", "PAPEL", "PLASTICO", "POLIESTER", "PORCELANA", "PVC"],
                "LIMPIEZA": ["GOMA", "INTERFOLIADAS", "LIQUIDO", "MADERA", "MICROFIBRA", "PAPEL", "PLASTICO", "POLVO", "TELA"],
                "CONSTRUCCION Y FERRETERIA": ["ACRILICA", "ALUMINIO", "CARTULINA", "CAUCHO", "CERAMICA", "COBRE", "CONCRETO", "CUERO", "GOMA", "LATA", "LINO", "LIQUIDO", "MADERA", "MALLA", "METALICO", "PLASTICO", "POLIESTER", "PORCELANA", "PVC", "SINTETICO", "TELA", "YESO"],
                "INSUMOS DECORATIVOS": ["MADERA"]
            },
            "marca": {
                "ESCRITORIO": ["ACRICOLOR", "ARTESCO", "BWHITE", "CASIO", "CHRISTMAS HOUSE", "CONDOR", "ENERGIZER", "FIVE STICK", "FRINGE CURTAIN", "ISOFIT", "MADISON", "MAXOFFICE", "MERTETTO", "MILCAR", "MONAMI","PAPER ONE", "RICOH"],
                "COCINA": ["SCOTT", "COPOBRAS", "BELEN"],
                "USO MEDICO": ["A&E", "BIOHIT", "BIOPLAST", "BRAUN", "BTL", "CAPULLO", "CUREBAND", "DRENACATH", "EARNIZ", "EKOSUR"],
                "PROMOCIONAL": ["S/M"],
                "LIMPIEZA": ["ARCHER", "ARISTECH", "ARMORALL", "BELEN", "BRISTAR", "CLIN", "ELITE", "HIGIA", "LIZ"],
                "CONSTRUCCION Y FERRETERIA": ["ABRO", "ADHEPLAS", "AMERICAN WORKS", "ARATY", "ARCELOR MITTAL", "NXL"],
                "INSUMOS DECORATIVOS": ["S/M"]
            }
        },
        "ACTIVO FIJO": {
            "seccion": [
                "ALMACEN","GERENCIA GENERAL","GERENCIA FINANCIERA", "GERENCIA COMERCIAL Y FINANCIERA", "SALA DE REUNIONES", "ZONA DE MONITOREO", "BAÑO GERENCIAL",
                "SALA DE ESPERA PLANTA ALTA", "COCINA", "BAÑO PLANTA ALTA", "OFICINA 1 PLANTA ALTA", "CONSULTORIO 1 PLANTA ALTA",
                "CONSULTORIO 2 PLANTA ALTA", "CONSULTORIO 3 PLANTA ALTA", "CONSULTORIO 4 PLANTA ALTA", "CONSULTORIO 5 PLANTA ALTA",
                "CONSULTORIO 6 PLANTA BAJA", "CONSULTORIO 7 PLANTA BAJA", "CONSULTORIO 8 PLANTA BAJA", "CONSULTORIO 9 PLANTA BAJA",
                "OFICINA 2 PLANTA BAJA", "OFICINA 3 PLANTA BAJA", "SALA DE ESPERA PLANTA BAJA", "SALA DE ATENCION AL CLIENTE", "SALA DE ESPERA", "SALA DE REUNIONES",
                "BAÑO PLANTA BAJA", "BAÑO CONSULTORIO 7 PLANTA BAJA", "DEPOSITO PRINCIPAL", "DEPOSITO SECUNDARIO",
                "PASILLO PLANTA ALTA", "PASILLO PLANTA BAJA", "GRADAS", "ENTRADA PRINCIPAL", "VISTA FRONTAL",
                "ADMINISTRACION","AUDIOMETRIA","CAJA","ELECTROCARDIOGRAMA","ERGOMETRIA","ESPIROMETRIA","FISIOTERAPIA-MEDICINA LABORAL","LABORATORIO",
                "OFICINA ADMINISTRATIVA","OFTALMOLOGIA","PRESTACIONES 1","PRESTACIONES 2","PROGRAMACION","PSICOLOGIA","SISTEMAS"
            ],
            "unidad_medida": ["UNIDADES"],
            "materia_prima": ["CONCRETO", "GOMA", "MADERA", "METALICO", "PLASTICO", "POLIESTER"],
            "marca": ["3D OPTICAL MOUSE", "AC-DELL", "ARRIX", "BIZLINK", "BREATHALYZER", "CONTEC", "DAHUA", "DIMAX", "DYMO", "ECCOSUR", "RICOH"]
        }
    };

    document.getElementById("tipo_inventario").addEventListener("change", function () {
        const tipo = this.value;
        const seccion = document.getElementById("seccion");
        const materia = document.getElementById("materia_prima");
        const unidad = document.getElementById("unidad_medida");
        const marca = document.getElementById("marca");
        const modelo = document.getElementById("modelo");

        seccion.innerHTML = '<option value="" disabled selected></option>';
        materia.innerHTML = '<option value="" disabled selected></option>';
        unidad.innerHTML = '<option value="" disabled selected></option>';
        marca.innerHTML = '<option value="" disabled selected></option>';

        if (tipo in opciones) {
            opciones[tipo].seccion.forEach(s => {
                seccion.innerHTML += `<option value="${s}">${s}</option>`;
            });

            opciones[tipo].materia_prima.forEach(m => {
                materia.innerHTML += `<option value="${m}">${m}</option>`;
            });

            opciones[tipo].unidad_medida.forEach(u => {
                unidad.innerHTML += `<option value="${u}">${u}</option>`;
            });

            opciones[tipo].marca.forEach(m => {
                marca.innerHTML += `<option value="${m}">${m}</option>`;
            });

            if (tipo === "ACTIVO FIJO") {
                modelo.disabled = false;
                modelo.style.display = "block";
            } else {
                modelo.disabled = true;
                modelo.style.display = "none";
            }
        }
    });

    document.getElementById("seccion").addEventListener("change", function () {
        const tipo = document.getElementById("tipo_inventario").value;
        const seccionSeleccionada = this.value;
        const materia = document.getElementById("materia_prima");
        const unidad = document.getElementById("unidad_medida");
        const marca = document.getElementById("marca");

        materia.innerHTML = '<option value="" disabled selected></option>';
        unidad.innerHTML = '<option value="" disabled selected></option>';
        marca.innerHTML = '<option value="" disabled selected></option>';

        if (tipo === "ALMACEN") {
            const materias = opciones[tipo].materia_prima[seccionSeleccionada] || opciones[tipo].materia_prima["default"];
            const unidades = opciones[tipo].unidad_medida[seccionSeleccionada] || opciones[tipo].unidad_medida["default"];
            const marcas = opciones[tipo].marca[seccionSeleccionada] || opciones[tipo].marca["default"];

            materias.forEach(m => {
                materia.innerHTML += `<option value="${m}">${m}</option>`;
            });

            unidades.forEach(u => {
                unidad.innerHTML += `<option value="${u}">${u}</option>`;
            });

            marcas.forEach(m => {
                marca.innerHTML += `<option value="${m}">${m}</option>`;
            });
        }

        if (tipo === "ACTIVO FIJO" && seccionSeleccionada) {
            const materias = opciones[tipo].materia_prima || [];
            const unidades = opciones[tipo].unidad_medida || [];
            const marcas = opciones[tipo].marca || [];

            materias.forEach(m => {
                materia.innerHTML += `<option value="${m}">${m}</option>`;
            });

            unidades.forEach(u => {
                unidad.innerHTML += `<option value="${u}">${u}</option>`;
            });

            marcas.forEach(m => {
                marca.innerHTML += `<option value="${m}">${m}</option>`;
            });
        }
    });
    // Ejecutar automáticamente al cargar la página si ya hay un valor seleccionado
    window.addEventListener("DOMContentLoaded", function () {
        document.getElementById("tipo_inventario").dispatchEvent(new Event("change"));
    });

</script>
<style>
    .color-box {
        width: 35px;
        height: 35px;
        margin: 5px;
        border: 2px solid #ddd;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .color-box:hover {
        transform: scale(1.1);
    }
</style>

{{-- COLORES --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let colorBoxes = document.querySelectorAll(".color-box");
        let colorInput = document.getElementById("colorInput");
        let colorDropdown = document.getElementById("colorDropdown");

        const colorNames = {
            "#000000": "NEGRO",
            "#FFFFFF": "BLANCO",
            "#FF0000": "ROJO",
            "#008000": "VERDE",
            "#0000FF": "AZUL",
            "#FFFF00": "AMARILLO",
            "#FFA500": "NARANJA",
            "#800080": "MORADO",
            "#FFC0CB": "ROSADO",
            "#808080": "GRIS",
            "#A52A2A": "MARRON",
            "#00BFFF": "CELESTE",
            "#FFD700": "DORADO",
            "#008B8B": "TURQUESA",
            "#B22222": "GUINDO"
        };

        colorBoxes.forEach(box => {
            box.style.backgroundColor = box.getAttribute("data-color");
            box.addEventListener("click", function() {
                colorBoxes.forEach(b => b.classList.remove("selected"));
                this.classList.add("selected");
                let selectedColor = this.getAttribute("data-color");
                colorInput.value = colorNames[selectedColor];
                colorDropdown.style.backgroundColor = selectedColor;
            });
        });
    });
</script>
@endsection