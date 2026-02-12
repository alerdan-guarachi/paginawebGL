<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use App\Models\AsesoraHorarios;
use App\Models\AsesoraBloqueos;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class AsesoramientoController extends Controller
{
    /* public function index(Request $request)
    {
        return view('admin.empresas.index');
    } */

    public function horarios()
    {
        $asesorId = Auth::id();

        $horarios = AsesoraHorarios::where('asesorid', $asesorId)->get();
        $bloqueos = AsesoraBloqueos::where('asesorid', $asesorId)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('admin.asesoramiento.horarios', compact('horarios', 'bloqueos'));
    }

    public function progasesoramiento()
    {
        $asesorId = 3;

        return view('admin.asesoramiento.progasesoramiento', compact('asesorId'));
    }

    public function programarasesoria()
    {
        $asesorId = 3;

        return view('programarasesoria', compact('asesorId'));
    }

    public function horariosPorDia($asesorId, $dia)
    {
        $horarios = DB::table('asesorahorarios')
            ->where('asesorid', $asesorId)
            ->where('dia', strtoupper($dia))
            ->where('estado', 'ACTIVO')
            ->get();

        return response()->json($horarios);
    }

    public function guardarProgramacion(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(7)->toDateString(),
        ]);

        $userId = Auth::id();

        $existe = DB::table('asesoraprogramacion')
            ->where('clientenombre', $request->nombre)
            ->where('celular', $request->celular)
            ->whereDate('fecha', '>=', Carbon::today())
            ->exists();

        if ($existe) {
            return response()->json([
                'ok' => false,
                'msg' => 'Ya tienes una asesoría programada. Debes esperar a que pase la fecha.'
            ], 403);
        }

        $id = DB::table('asesoraprogramacion')->insertGetId([
            'asesorid' => $request->asesorid,
            'clientenombre' => $request->nombre,
            'celular'  => $request->celular,
            'motivo'   => $request->motivo,
            'sucursal'   => $request->sucursal,
            'fecha'    => $request->fecha,
            'horadesde'=> $request->horadesde,
            'horahasta'=> $request->horahasta,
            'estado'=> 'PENDIENTE',
            'created_at' => now(),
        ]);

        DB::table('preclientes')->insert([
            'apepaterno' => $request->apepaterno,
            'apematerno' => $request->apematerno,
            'nombres'  => $request->nombres,
            'nombrecompleto'   => $request->nombre,
            'ci'   => $request->ci,
            'email'    => $request->email,
            'celular'=> $request->celular,
            'estado'=> 'PENDIENTE',
            'sucursal'=> $request->sucursal,
            'created_at' => now(),
        ]);

        $BOT_TOKEN = '8371266559:AAEFqgFt5w0n4Ac5rpCENCaOqoUP4ohFx3I';
        $CHAT_ID = '-1003828158402';

        $fecha = Carbon::parse($request->fecha);
        $diaSemana = ucfirst($fecha->translatedFormat('l'));
        $fechaFormateada = $fecha->translatedFormat('d F Y');

        $mensaje = "*📍 NUEVA ASESORÍA PROGRAMADA*\n\n"
            . "*Cliente:* {$request->nombre}\n"
            . "*Motivo:* {$request->motivo}\n"
            . "*Sucursal:* {$request->sucursal}\n"
            . "*Fecha:* {$diaSemana}, {$fechaFormateada}\n"
            . "*Horario:* {$request->horadesde} - {$request->horahasta}";

        Http::post("https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage", [
            'chat_id' => $CHAT_ID,
            'text' => $mensaje,
            'parse_mode' => 'Markdown'
        ]);


        return response()->json([
            'ok' => true,
            'msg' => 'Asesoría programada correctamente',
            'ticket_url' => route('admin.asesoramiento.ticket.imagen', $id)
        ]);
    }

    public function ticketImagen($id)
    {
        $ticket = DB::table('asesoraprogramacion')->find($id);

        return view('admin.asesoramiento.ticket_imagen', compact('ticket'));
    }

    public function horariosOcupados($asesorId, $fecha)
    {
        $ocupados = DB::table('asesoraprogramacion')
            ->where('asesorid', $asesorId)
            ->where('fecha', $fecha)
            ->where('sucursal', 'SANTA CRUZ')
            ->whereNotIn('estado', ['SE REPROGRAMÓ'])
            ->select(
                DB::raw("TIME_FORMAT(horadesde,'%H:%i') as horadesde"),
                DB::raw("TIME_FORMAT(horahasta,'%H:%i') as horahasta")
            )
            ->get();

        return response()->json($ocupados);
    }

    public function listaprogramaciones()
    {
        $hoy = Carbon::today();

        $programacionesHoy = DB::table('asesoraprogramacion')
            ->whereDate('fecha', $hoy)
            ->orderBy('horadesde')
            ->get();

        $programacionesFuturas = DB::table('asesoraprogramacion')
            ->whereDate('fecha', '>', $hoy)
            ->where('estado', 'PENDIENTE')
            ->orderBy('fecha')
            ->get();

        $programacionesHistoricas = DB::table('asesoraprogramacion')
            ->whereDate('fecha', '<=', $hoy)
            ->orderByDesc('fecha')
            ->get();

        return view('admin.asesoramiento.listaprogramaciones', compact(
            'programacionesHoy',
            'programacionesFuturas',
            'programacionesHistoricas'
        ));
    }

    public function actualizarEstado(Request $request)
    {
        DB::table('asesoraprogramacion')
            ->where('id', $request->id)
            ->update([
                'estado' => $request->estado
            ]);

        return response()->json(['ok' => true]);
    }

    public function store(Request $request)
    {
        AsesoraHorarios::create([
            'asesorid' => Auth::id(),
            'asesornombre' => Auth::user()->name,
            'dia' => $request->dia,
            'horainicio' => $request->horainicio,
            'horafin' => $request->horafin,
            'duracioncita' => $request->duracioncita,
            'estado' => 'ACTIVO',
        ]);

        return back()->with('success', 'HORARIO AGREGADO');
    }

    public function update(Request $request, $id)
    {
        AsesoraHorarios::where('id', $id)
            ->where('asesorid', Auth::id())
            ->update($request->only('horainicio', 'horafin', 'duracioncita'));

        return back()->with('success', 'HORARIO ACTUALIZADO');
    }

    public function toggle($id)
    {
        $h = AsesoraHorarios::where('id', $id)
            ->where('asesorid', Auth::id())
            ->firstOrFail();

        $h->estado = $h->estado === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
        $h->save();

        return back();
    }

    /* public function bloquear(Request $request)
    {
        $inicio = \Carbon\Carbon::parse($request->fecha_inicio);
        $fin = \Carbon\Carbon::parse($request->fecha_fin);

        while ($inicio <= $fin) {
            AsesoraBloqueos::create([
                'asesorid' => Auth::id(),
                'asesornombre' => Auth::user()->name,
                'fecha' => $inicio->toDateString(),
                'horainicio' => $request->horainicio,
                'horafin' => $request->horafin,
                'motivo' => $request->motivo,
            ]);
            $inicio->addDay();
        }

        return back()->with('success', 'FECHAS BLOQUEADAS');
    } */
   public function bloquear(Request $request)
    {
        $inicio = \Carbon\Carbon::parse($request->fecha_inicio);
        $fin = \Carbon\Carbon::parse($request->fecha_fin);

        $horaInicio = $request->horainicio;
        $horaFin = $request->horafin;

        while ($inicio <= $fin) {

            $query = DB::table('asesoraprogramacion')
                ->where('asesorid', Auth::id())
                ->where('fecha', $inicio->toDateString())
                ->where('estado', 'PENDIENTE');

            if ($horaInicio && $horaFin) {
                $query->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->where('horadesde', '<', $horaFin)
                    ->where('horahasta', '>', $horaInicio);
                });
            }

            $existePendiente = $query->exists();

            if ($existePendiente) {
                return back()->with('error',
                    'No se puede bloquear. Existen asesorías PENDIENTES en la fecha ' .
                    $inicio->format('d/m/Y')
                );
            }

            $inicio->addDay();
        }

        $inicio = \Carbon\Carbon::parse($request->fecha_inicio);

        while ($inicio <= $fin) {
            AsesoraBloqueos::create([
                'asesorid' => Auth::id(),
                'asesornombre' => Auth::user()->name,
                'fecha' => $inicio->toDateString(),
                'horainicio' => $horaInicio,
                'sucursal' => 'SANTA CRUZ',
                'horafin' => $horaFin,
                'motivo' => $request->motivo,
            ]);

            $inicio->addDay();
        }

        return back()->with('success', 'FECHAS BLOQUEADAS CORRECTAMENTE');
    }


    public function destroy($id)
    {
        $bloqueo = AsesoraBloqueos::where('asesorid', auth()->id())
            ->findOrFail($id);

        $bloqueo->delete();

        return back()->with('success', 'BLOQUEO ELIMINADO CORRECTAMENTE');
    }
    
}
