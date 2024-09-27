@extends('adminlte::page')

@section('content_header')
<h1>Datos del cliente</h1>
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

<div class="content">
    <div class="row">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="border-bottom text-center pb-4">
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <img src="{{asset('image/'.$cliente->image)}}" alt="profile" class="col-md-10 mb-9" id="vista-previa" style="width: 100%;" />
                                    <h3>ID Cliente: {{$cliente->id}}</h3>
                                </div>
                            </div>
                            <div class="py-1">
                    
                                <p class="clearfix">
                                    <span class="float-left h6" style="font-weight: bold;">
                                        Nombres
                                    </span>
                                    <span class="float-right text-muted">
                                        {{$cliente->nombres}}
                                        </a>
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left" style="font-weight: bold;">
                                        Apellido Paterno
                                    </span>
                                    <span class="float-right text-muted">
                                        {{$cliente->apepaterno}}
                                        </a>
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left" style="font-weight: bold;">
                                        Apellido Materno
                                    </span>
                                    <span class="float-right text-muted">
                                        {{$cliente->apematerno}}
                                        </a>
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="profile-feed">
                                <div class="d-flex align-items-start profile-feed-item">
                                    <div class="form-group col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <th>Tipo de identificacion</th>
                                                            <td>{{$cliente->tipoidentificacion}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>CI</th>
                                                            <td>{{$cliente->ci}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Complemento</th>
                                                            <td>{{$cliente->cicomplemento}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>C/exp.</th>
                                                            <td>{{$cliente->ciexp}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Fecha Ven/CI</th>
                                                            <td>{{$cliente->fechavencci}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Edad</th>
                                                            <td>{{$cliente->edad}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Ciudad nac.</th>
                                                            <td>{{$cliente->lugarnacimiento}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Genero</th>
                                                            <td>{{$cliente->genero}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Estado civil</th>
                                                            <td>{{$cliente->estadocivil}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>{{$cliente->email}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Profesion / Ocupacion</th>
                                                            <td>{{$cliente->ocupacion}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>G. inst.</th>
                                                            <td>{{$cliente->gradoinstruccion}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Celular</th>
                                                            <td>{{$cliente->celular}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Telefono</th>
                                                            <td>{{$cliente->telefono}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Domicilio</th>
                                                            <td>{{$cliente->domicilio}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="profile-feed">
                                <div class="d-flex align-items-start profile-feed-item">
                                    <div class="form-group col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <th>NUA/CUA</th>
                                                            <td>{{$cliente->nuacua}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Estado laboral</th>
                                                            <td>{{$cliente->estadolaboral}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Empresa</th>
                                                            <td>{{$cliente->empresa}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Pais res.</th>
                                                            <td>{{$cliente->paisresidencia}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Dep. res.</th>
                                                            <td>{{$cliente->departamentoresidencia}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Ciudad res.</th>
                                                            <td>{{$cliente->ciudadresidencia}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Aseguradora</th>
                                                            <td>{{$cliente->aseguradora}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Referenciador</th>
                                                            <td>{{$cliente->referenciador}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>AFP</th>
                                                            <td>{{$cliente->afp}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>N. hijos &lt; 25</th>
                                                            <td>{{$cliente->numhijosmenores}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Alertas</th>
                                                            <td>{{$cliente->alertas}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- <td width="10px">
                <div class="card-footer text-muted">
                    <a href="{{route('admin.clientes.index')}}" class="btn btn-outline-primary float-right" style="margin-left: 10px">Regresar</a>
                </td>
                </div> --}}
            </div>
        </div>
    </div>

</div>
@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
@stop

@section('css')
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h3 {color:black; 
        font-family: "Segoe UI";
        font-weight: 700;
        font-size: 130%;
        margin-top: 20px;
        }
    #vista-previa {
        display: block;
        height: auto;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
    table {
        border-collapse: separate;
        border-spacing: 8px; /* Ajusta el valor según lo desees */
    }

    th, td {
        padding: 3px; /* Ajusta el valor según lo desees */
    }
    td{
        text-align: right;
    }
</style>
@stop
