<div class="col-lg-4">
    <div class="form-group">
        {!! Form::label('name', 'Nombre:') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre del rol']) !!}
        @error('name')
        <small class="text-danger fas fa-exclamation-circle">
            {{$message}}
        </small>
            
        @enderror
    </div>
</div>

<div class="col-lg-12">
    <h2>Lista de permisos:</h2>
    <div class="row">
        @foreach ($permissions as $permission)
            <div class="col-sm-4">
                <div class="form-check">
                    <h3>
                        {!! Form::checkbox('permissions[]', $permission->id, null, ['class' => 'mr-1']) !!}
                        {{$permission->description}} - {{$permission->name}}
                    </h3>
                </div>
            </div> 
        @endforeach
    </div>
</div>
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1 {color:green; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h2 {color:black; 
        font-family: "Arial";
        font-weight: 900;
        font-size: 80%;
        }
    h3 {color:black; 
        font-family: "Arial";
        font-weight: 500;
        font-size: 80%;
        }
    
</style>
@stop