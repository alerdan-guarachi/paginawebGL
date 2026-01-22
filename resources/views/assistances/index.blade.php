@extends('adminlte::page')

@section('title', 'Registros de Asistencia')

@section('content')
<div class="container mt-4">

    <h4 class="mb-3 text-center">Registros de Asistencia</h4>

    <form method="GET" action="{{ route('assistances.index') }}" class="mb-3 d-flex justify-content-center gap-2">
        <select name="reason" class="form-control">
            <option value="">-- Seleccionar Motivo --</option>
            @foreach($reasons as $r)
                <option value="{{ $r }}" {{ request('reason') == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>

        <input type="date" name="date_reason" class="form-control" value="{{ request('date_reason') }}">

        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="{{ route('assistances.export', request()->all()) }}" class="btn btn-success">Exportar CSV</a>
    </form>

    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Partner ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Motivo</th>
                <th>Fecha del Motivo</th>
                <th>Fecha de Registro</th>
                <th>Hora de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assistances as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ $a->partner_id }}</td>
                    <td>{{ $a->partner_name }}</td>
                    <td>{{ $a->partner_last_name }}</td>
                    <td>{{ $a->reason }}</td>
                    <td>{{ $a->date_reason }}</td>
                    <td>{{ $a->date_attendance }}</td>
                    <td>{{ $a->time_attendance }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay registros</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
