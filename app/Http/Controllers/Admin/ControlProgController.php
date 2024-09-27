<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ControlProgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:admin.users.index')->only('index');
    }

    public function index()
    {
        // Obtener todos los usuarios
        $usuarios = DB::table('users')->select('id', 'name')->get();

        // Listado de tablas a combinar (esto es para el gráfico unificado)
        $tablas = [
            'aprobacioninformesfinales',
            'bateriaproveedores',
            'bateriasubclientes',
            'clientes',
            'documentacionsubclientes',
            'estadoprogramacionsubclientes',
            'estadocotizacionsubclientes',
            'programacionsubclientes',
            'requisitosubclientes'
        ];

        // Almacenamiento temporal de los resultados unificados
        $data = collect();

        // Iterar sobre cada tabla y obtener los datos
        foreach ($tablas as $tabla) {
            $registros = DB::table($tabla)
                ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('count(*) as total'))
                ->whereNotNull('created_at') // Asegurarse de que 'created_at' no sea nulo
                ->where('created_at', '>=', now()->subDays(7)) // Filtrar los registros de la última semana
                ->groupBy('dia')
                ->get();

            // Unir los resultados en la colección $data
            $data = $data->merge($registros);
        }

        // Traducción de los días de la semana
        $diasTraduccion = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        // Obtener el día de hoy en español
        $hoy = $diasTraduccion[date('l')];

        // Días de la semana en orden
        $diasEnOrden = ['Sábado', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        // Encontrar la posición del día de hoy
        $indiceHoy = array_search($hoy, $diasEnOrden);

        // Reorganizar los días para que el día de hoy sea el último, y los anteriores aparezcan antes
        $diasEnOrden = array_merge(array_slice($diasEnOrden, $indiceHoy + 1), array_slice($diasEnOrden, 0, $indiceHoy), [$hoy]);

        // Agrupar los datos por día
        $unifiedData = $data->groupBy('dia')->map(function ($dayGroup) use ($diasTraduccion) {
            $day = $dayGroup->first()->dia;
            return [
                'dia' => $diasTraduccion[$day] ?? 'Día desconocido',
                'total' => $dayGroup->sum('total')
            ];
        });

        // Mapear los días y asignar valores de total, si no existe, lo asignamos a 0
        $finalDataGeneral = collect($diasEnOrden)->mapWithKeys(function ($dia) use ($unifiedData) {
            return [$dia => $unifiedData->firstWhere('dia', $dia)['total'] ?? 0];
        });

        // Asegurarse de que el datasetUsuario esté vacío para gráficos específicos de usuario
        $datasetUsuario = collect();

        // Pasar tanto los días en orden como los datos finales a la vista junto con los usuarios
        return view('admin.controlprogramacion.index', compact('finalDataGeneral', 'diasEnOrden', 'usuarios', 'datasetUsuario'));
    }

    public function buscarPorUsuario(Request $request)
    {
        // Obtener el usuario seleccionado
        $usuarioId = $request->input('usuario');

        // Obtener todos los usuarios para el dropdown
        $usuarios = DB::table('users')->select('id', 'name')->get();

        // Verificar si el usuario fue seleccionado
        if (is_null($usuarioId)) {
            // Si no hay usuario seleccionado, mostrar la gráfica general
            return $this->index(); // Llama al método index para mostrar la gráfica general
        }

        // Listado de tablas a combinar para gráficos individuales por usuario
        $tablas = [
            'clientes',
            'contactosubclientes',
            'tramitessubclientes',
            'requisitosubclientes',
            'bateriasubclientes',
            'estadocotizacionsubclientes',
            'programacionsubclientes',
            'estadoprogramacionsubclientes',
            'documentacionsubclientes',
            'aprobacioninformesfinales',
            'bateriaproveedores',
        ];

        // Almacenamiento temporal de los resultados por tabla
        $dataPorTabla = [];

        // Iterar sobre cada tabla y obtener los datos filtrados por el usuario seleccionado
        foreach ($tablas as $tabla) {
            if ($tabla == 'clientes') {
                $registros = DB::table($tabla)
                    ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('count(*) as total'))
                    ->where('users_id', $usuarioId)
                    ->whereNotNull('created_at')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('dia')
                    ->get();
            } elseif (Schema::hasColumn($tabla, 'usuarioid')) {
                $registros = DB::table($tabla)
                    ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('count(*) as total'))
                    ->where('usuarioid', $usuarioId)
                    ->whereNotNull('created_at')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('dia')
                    ->get();
            }

            // Agrupar los totales por día para cada tabla
            $dataPorTabla[$tabla] = collect();
            foreach ($registros as $registro) {
                $dia = $registro->dia;
                $total = $registro->total;

                // Sumar el total de registros para cada día en la tabla correspondiente
                $dataPorTabla[$tabla][$dia] = isset($dataPorTabla[$tabla][$dia]) ? $dataPorTabla[$tabla][$dia] + $total : $total;
            }
        }

        // Si no se encontraron registros para el usuario seleccionado
        if (empty($dataPorTabla)) {
            return redirect()->route('admin.controlprogramacion.index')->with('info', 'Este usuario tiene datos registrados, pero no ha tenido actividad que mostrar en el gráfico.');
        }

        // Traducción de los días de la semana
        $diasTraduccion = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        // Obtener el día de hoy en español
        $hoy = $diasTraduccion[date('l')];
        $diasEnOrden = ['Sábado', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $indiceHoy = array_search($hoy, $diasEnOrden);
        $diasEnOrden = array_merge(array_slice($diasEnOrden, $indiceHoy + 1), array_slice($diasEnOrden, 0, $indiceHoy), [$hoy]);

        // Agrupar los datos por día de la semana para cada tabla
        $finalDataPorTablaUsuario = [];
        foreach ($dataPorTabla as $tabla => $data) {
            $finalDataPorTablaUsuario[$tabla] = collect($diasEnOrden)->mapWithKeys(function ($dia) use ($data, $diasTraduccion) {
                $dayInEnglish = array_search($dia, $diasTraduccion);
                $total = $data[$dayInEnglish] ?? 0;
                return [$dia => $total];
            });
        }

        // Verificación si todos los valores son cero por tabla
        $totalRegistros = collect($finalDataPorTablaUsuario)->flatten()->sum();

        if ($totalRegistros === 0) {
            return redirect()->route('admin.controlprogramacion.index')->with('info', 'Este usuario tiene datos registrados, pero no ha tenido actividad que mostrar en el gráfico.');
        }

        // Inicializar datasetUsuario como una colección vacía para evitar errores en la vista
        $datasetUsuario = $finalDataPorTablaUsuario; // Aquí los datos específicos del usuario

        // Definir finalDataGeneral como una colección vacía para evitar errores en la vista
        $finalDataGeneral = collect();

        //dd($finalDataPorTablaUsuario); // Verificar si los datos están correctamente formateados

        return view('admin.controlprogramacion.index', compact('finalDataPorTablaUsuario', 'diasEnOrden', 'usuarios', 'usuarioId', 'datasetUsuario', 'finalDataGeneral'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);

        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignaron los roles correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index', $user)->with('eliminar', 'ok');
    }
}
