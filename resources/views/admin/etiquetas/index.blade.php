@extends('adminlte::page')
    
@section('content_header')

{{-- @can('admin.profiles.create') --}}
{{-- @if ($cliente) --}}
{{-- @else --}}
<a class="btn btn-crear btn-sm float-right" href="{{route('admin.clientes.create')}}">Crear cliente</a>
{{-- @endif --}}
{{-- @endcan --}}
<h1>Formulario Medico</h1>
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
        {{-- <h4>Preguntas</h4> --}}
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Apellido Paterno:') !!}
                        {!! Form::text('apepaterno', $cliente->nombres, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                        @error('celular')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('telefono', 'Telefono:') !!}
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                        @error('telefono')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                        @error('celular')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('telefono', 'Telefono:') !!}
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                        @error('telefono')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Enfermedad o defecto de ojos, oídos, nariz o garganta</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta1" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta1" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Mareos, desmayos, convulsiones, cefaleas, torpeza al hablar, parálisis o ataque cerebral; enfermedades de tipo mental  o nerviosa</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta2" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta2" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Dificultad al respirar, ronquera o tos persistente, hemóptisis, bronquitis, pleuresía, asma, efisema, tuberculosis o enfermedad respiratoria crónica</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta3" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta3" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Dolor en el pecho, palpitaciones, hipertensión, fiebre reumática, soplo cardíaco, ataque cardíaco u otra enfermedad del sistema cardiovascular</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta4" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta4" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Ictericia, hemorragia intestinal, úlcera, hernia, apendicitis, colitis, diverticulitis, hemorroides, indigestión frecuente, o cualquier otra enfermedad del estómago, intestinos, hígado o vesícula</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta5" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta5" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Azúcar, albúmina, sangre o pus en la orina, enfermedad venérea, piedra u otra enfermedad de los riñones, vejiga, próstata o aparato reproductor</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta6" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta6" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Diabetes, enfermedad de la tiroides u otras glándulas endocrinas</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta7" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta7" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Neuritis, ciática, reumatismo, artritis, gota, o cualquier otra enfermedad o defecto muscular óseo, incluyendo la columna, espalda y articulacione</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta8" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta8" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Alguna deformidad, cojera o amputación</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta9" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta9" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Enfermedad de la piel, ganglios linfáticos, quistes, tumores, cáncer</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta10" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta10" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Alergias, anemia u otra enfermedad de la sangre</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta11" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta11" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Uso excesivo del alcohol</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta12" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta12" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>En la actualidad, fuma usted o durante los últimos 12 meses ha fumado cigarrillos, cigarros, pipa o ha usado tabaco en cualquier forma</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta13" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta13" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Ha usado alguna vez drogas estupefacientes, menos que fuera bajo consejo médico</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta14" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta14" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Está usted actualmente sometido a observación, tratamiento o medicación por alguna enfermedad</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta15" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta15" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Tiene usted la intención de buscar consejo médico, tratamiento o hacer cualquier prueba médica</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta16" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta16" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>



            <p>En los últimos 5 años:</p>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Ha tenido alguna enfermedad física o mental aparte de las ya mencionadas</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta17" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta17" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Ha tenido alguna revisión, consulta, lesión u operación quirúrgica</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta18" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta18" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Ha sido paciente en hospital, clínica, sanatorio u otros estable cimientos médicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta19" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta19" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Ha sido sometido a electrocardiograma, rayos x u otro tipo de análisis</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta20" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta20" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Se le ha aconsejado algún análisis, hospitalización u operación que no se hubiera realizado</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta21" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta21" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Ha tenido aplazamiento, rechazo o reducción del servicio militar por deficiencia física o mental</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta22" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta22" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Realiza Deportes u hobbies riesgosos como: Bombero, Piloto Civil, Andinismo, Carreras de Veleocidad, Alas Delta, Parapente, Paracaidismo, Buceo, Motociclismo, u otro que se considere peligroso</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta23" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta23" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Ha solicitado o percibido alguna vez indemnizaciones por incapacidad de cualquier tipo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pregunta24" value="si" id="rsi" onclick="mostrarFormulario()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pregunta24" value="no" id="rno" onclick="ocultarFormulario()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Card que se mostrará cuando se seleccione "SI" -->
                <div id="card" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Campo 1">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Campo 2">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Campo 3">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Campo 4">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Campo 5">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Campo 6">
                                </div>
                                <button type="button" class="btn btn-primary" onclick="mostrarSiguienteCard()">Siguiente</button>
                            </form>
                        </div>
                    </div>
                </div>
            
                <!-- Card adicional que se mostrará cuando se haga clic en "Siguiente" -->
                <div id="siguienteCard" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <!-- Aquí agregarías los elementos del siguiente formulario -->
                            <form>
                                <!-- Campos del siguiente formulario -->
                                <button type="button" class="btn btn-primary" onclick="mostrarSiguienteCard()">Siguiente</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
            function mostrarFormulario() {
                document.getElementById('card').style.display = 'block';
            }
            
            function ocultarFormulario() {
                document.getElementById('card').style.display = 'none';
            }
            
            function mostrarSiguienteCard() {
                document.getElementById('siguienteCard').style.display = 'block';
            }
            </script>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Hay en su familia antecedentes de tuberculosis, diabetes, cáncer, hipertensión, enfermedad sanguínea o renal, enfermedad mental o suicidio</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta25" value="si" id="rsi">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pregunta25" value="no" id="rno">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
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
    
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
        }
    
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    /* Estilo para filas impares */
.odd-row {
    background-color: #f0f0f0; /* Puedes ajustar el color de fondo según tus preferencias */
}

    </style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif
@endsection