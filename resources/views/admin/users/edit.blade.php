@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.users.index') }}">REGRESAR</a>
<h5>ASIGNAR ROL A:</h5>
<h3>{{$user->name}}</h3>
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
{{-- <div class="card">
    <div class="card-body">
        <h2>Lista de roles:</h2>
        {!! Form::model($user, ['route' => ['admin.users.update', $user], 'method' => 'put']) !!}
            @foreach ($roles as $role)
                <div class="form-check">
                    {!! Form::checkbox('roles[]', $role->id, null, ['id' => 'role_'.$role->id, 'class' => 'form-check-input']) !!}
                    <label class="form-check-label" for="role_{{ $role->id }}">
                        {{ $role->name }}
                    </label>
                </div>
            @endforeach
            <div class="mt-2">
                {!! Form::submit('ASIGNAR ROL', ['class' => 'btn btn-crear']) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div> --}}
<style>
    .role-box {
        width: 100%;
        height: 80px;
        border: 2px solid #ccc;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: bold;
        text-transform: uppercase;
        transition: all 0.2s ease-in-out;
        background-color: #f8f9fa;
    }

    .role-box:hover {
        background-color: #e2e6ea;
    }

    .role-box.active {
        background-color: #94c93b;
        color: white;
        border-color: #94c93b;
    }

    .btn-check {
        display: none;
    }
</style>

<div class="card shadow-sm border-0">
    
    <div class="card-body">
        {!! Form::model($user, ['route' => ['admin.users.update', $user], 'method' => 'put']) !!}

        <h5>Lista de roles:</h5>
        <div class="row g-3">
            @foreach ($roles as $role)
                @php
                    $checked = $user->roles->pluck('id')->contains($role->id);
                @endphp

                <div class="col-md-3">
                    <input type="checkbox" class="btn-check" name="roles[]" value="{{ $role->id }}"
                        id="role_{{ $role->id }}" autocomplete="off" {{ $checked ? 'checked' : '' }}>

                    <label class="role-box {{ $checked ? 'active' : '' }}" for="role_{{ $role->id }}">
                        {{ $role->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-crear px-4">
                ASIGNAR ROL
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-check').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const label = document.querySelector(`label[for="${this.id}"]`);
                label.classList.toggle('active', this.checked);
            });
        });
    });
</script>


@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    h2 {color:black; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 100%;
        }
    label {color:black; 
        font-family: "Segoe UI";
        font-weight: 450;
        font-size: 90%;
        margin-top: 1rem;
        margin-bottom: 1rem;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .form-check-input {
        margin-top: 1.2rem;
        margin-bottom: 1.2rem;
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
</style>
@stop

@section('js')
<script>console.log('Hi!');</script>
@stop