@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
    @can('admin.asociados.crearcontactoclienteita')
        <a class="btn btn-sm float-right btn-crear" data-toggle="modal" data-target="#crearContactoModal">
            NUEVO CONTACTO
        </a>
        <div class="modal fade" id="crearContactoModal" tabindex="-1" role="dialog" aria-labelledby="crearContactoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearContactoModalLabel"><strong>NUEVO CONTACTO</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {!! Form::model($cliente, ['route' => ['admin.asociados.guardarcontactoclienteita', $cliente], 'method' => 'POST']) !!}
                        <div class="modal-body">
                            {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                            {!! Form::hidden('clienteid', $cliente->id) !!}
                            <div class="form-group">
                                {!! Form::label('nombrecontacto', 'Nombre Completo:') !!}
                                {!! Form::text('nombrecontacto', null, ['class' => 'form-control', 'maxlength' => '90', 'required']) !!}
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    {!! Form::label('celularcontacto', 'Celular:') !!}
                                    {!! Form::text('celularcontacto', null, ['class' => 'form-control', 'maxlength' => '30', 'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 45', 'required']) !!}
                                </div>

                                <div class="form-group col-lg-6">
                                    {!! Form::label('telefonocontacto', 'Teléfono:') !!}
                                    {!! Form::text('telefonocontacto', null, ['class' => 'form-control', 'maxlength' => '30', 'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 45', 'required']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-3">
                                    {!! Form::label(null, 'Género:') !!}
                                    <div style="font-size: 12px; line-height: 1.2;">
                                        <label class="d-block mb-1">
                                            <input type="radio" name="genero_tmp" id="genero_m" value="M">
                                            MASCULINO
                                        </label>
                                        <label class="d-block">
                                            <input type="radio" name="genero_tmp" id="genero_f" value="F">
                                            FEMENINO
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-9">
                                    {!! Form::label('parentesco', 'Parentesco:') !!}
                                    {!! Form::select('parentesco', ['' => ''], null, [
                                        'class' => 'form-control',
                                        'id' => 'parentesco',
                                        'required'
                                    ]) !!}
                                </div>
                            </div>
                            <script>
                                const parentescos = {
                                    M: {
                                        'ESPOSO': 'ESPOSO',
                                        'HIJO': 'HIJO',
                                        'PADRE': 'PADRE',
                                        'ABUELO': 'ABUELO',
                                        'NIETO': 'NIETO',
                                        'HERMANO': 'HERMANO',
                                        'TIO': 'TIO',
                                        'PRIMO': 'PRIMO',
                                        'SOBRINO': 'SOBRINO',
                                        'SUEGRO': 'SUEGRO',
                                        'YERNO': 'YERNO',
                                        'CUÑADO': 'CUÑADO',
                                        'UNIÓN LIBRE': 'UNIÓN LIBRE',
                                        'PADRASTRO': 'PADRASTRO',
                                        'HIJASTRO': 'HIJASTRO',
                                    },
                                    F: {
                                        'ESPOSA': 'ESPOSA',
                                        'HIJA': 'HIJA',
                                        'MADRE': 'MADRE',
                                        'ABUELA': 'ABUELA',
                                        'NIETA': 'NIETA',
                                        'HERMANA': 'HERMANA',
                                        'TIA': 'TIA',
                                        'PRIMA': 'PRIMA',
                                        'SOBRINA': 'SOBRINA',
                                        'SUEGRA': 'SUEGRA',
                                        'NUERA': 'NUERA',
                                        'CUÑADA': 'CUÑADA',
                                        'UNIÓN LIBRE': 'UNIÓN LIBRE',
                                        'MADRASTRA': 'MADRASTRA',
                                        'HIJASTRA': 'HIJASTRA',
                                    }
                                };

                                function cargarParentesco(genero) {
                                    const select = document.getElementById('parentesco');
                                    select.innerHTML = '<option value=""></option>';

                                    Object.entries(parentescos[genero]).forEach(([value, text]) => {
                                        select.innerHTML += `<option value="${value}">${text}</option>`;
                                    });
                                }
                                document.getElementById('genero_m').addEventListener('change', () => cargarParentesco('M'));
                                document.getElementById('genero_f').addEventListener('change', () => cargarParentesco('F'));
                            </script>
                        </div>
                        <div class="modal-footer">
                            {!! Form::submit('GUARDAR', ['class' => 'btn btn-sm float-right btn-crear']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endcan
    <h5>CONTACTOS DE:</h5> 
    <h3>{{$cliente->nombrecompleto}}</h3>
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
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Parentesco</th>
                        <th>Celular</th>
                        <th>Teléfono</th>
                        <th>Usuario Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contactos as $contacto)
                        <tr>
                            <td>{{$contacto->id}}</td>
                            <td>{{$contacto->nombrecontacto}}</td>
                            <td>{{$contacto->parentesco}}</td>
                            <td>{{$contacto->celularcontacto}}</td>
                            <td>{{$contacto->telefonocontacto}}</td>
                            <td>{{$contacto->usuarioregistro}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        font-size: 23px;
        }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection