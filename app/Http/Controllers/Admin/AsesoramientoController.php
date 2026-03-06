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
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmacionAsesoriaMail;
use Illuminate\Support\Facades\Validator;

class AsesoramientoController extends Controller
{
    /* public function index(Request $request)
    {
        return view('admin.empresas.index');
    } */

    public function confirmarCorreo(Request $request) 
    {
        $request->validate([
            'id' => 'required'
        ]);

        $programacion = DB::table('asesoraprogramacion')
            ->where('id', $request->id)
            ->first();

        if (!$programacion) {
            return response()->json([
                'ok' => false,
                'msg' => 'No se encontró la asesoría.'
            ]);
        }

        if (empty($programacion->email)) {
            return response()->json([
                'ok' => false,
                'msg' => 'El cliente no registró su email.'
            ]);
        }

        if (!filter_var($programacion->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'ok' => false,
                'msg' => 'El email '.$programacion->email.' no es válido.'
            ]);
        }

        try {

            Mail::to($programacion->email)
                ->send(new ConfirmacionAsesoriaMail($programacion));

            DB::table('asesoraprogramacion')
                ->where('id', $request->id)
                ->update([
                    'modalidad' => 'PRESENCIAL'
                ]);

            return response()->json(['ok' => true]);

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'msg' => 'El email '.$programacion->email.' no existe o no se pudo enviar.'
            ]);
        }
    }

    public function horarios()
    {
        /* $asesorId = Auth::id(); */
        $asesorId = 8;

        $horarios = AsesoraHorarios::where('asesorid', $asesorId)->get();
        $bloqueos = AsesoraBloqueos::where('asesorid', $asesorId)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('admin.asesoramiento.horarios', compact('horarios', 'bloqueos'));
    }

    public function progasesoramiento()
    {
        $asesorId = 8;

        return view('admin.asesoramiento.progasesoramiento', compact('asesorId'));
    }

    public function programarasesoria()
    {
        $asesorId = 8;

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

        $conflictoHorario = DB::table('asesoraprogramacion')
            ->where('asesorid', $request->asesorid)
            ->whereDate('fecha', $request->fecha)
            ->where(function ($query) use ($request) {
                $query->where('horadesde', '<', $request->horahasta)
                    ->where('horahasta', '>', $request->horadesde);
            })
            ->exists();

        if ($conflictoHorario) {
            return response()->json([
                'ok' => false,
                'msg' => 'El asesor ya tiene una asesoría programada en ese horario.'
            ], 403);
        }

        $id = DB::table('asesoraprogramacion')->insertGetId([
            'asesorid' => $request->asesorid,
            'clientenombre' => $request->nombre,
            'celular'  => $request->celular,
            'email'  => $request->email,
            'motivo'   => $request->motivo,
            'sucursal'   => $request->sucursal,
            'modalidad'   => $request->modalidad,
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
            . "*Modalidad:* {$request->modalidad}\n"
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
            'ticket_url' => route('admin.asesoramiento.ticket.pdf', $id)
        ]);
    }

    public function ticketPdf($id)
    {
        $ticket = DB::table('asesoraprogramacion')->where('id', $id)->first();

        if (!$ticket) {
            abort(404);
        }

        $pdf = Pdf::loadView('admin.asesoramiento.ticket_pdf', compact('ticket'))
        ->setPaper([0, 0, 226.77, 420], 'portrait');

    $pdf->setOptions([
        'margin-top' => 20,
        'margin-bottom' => 20,
        'margin-left' => 20,
        'margin-right' => 20,
    ]);

        return $pdf->download(
            'ASESORIA_' .
            Carbon::parse($ticket->fecha)->format('d-m-Y') . '_' .
            substr($ticket->horadesde, 0, 5) . '.pdf'
        );
    }


    public function ticketImagen($id)
    {
        $ticket = DB::table('asesoraprogramacion')->find($id);

        return view('admin.asesoramiento.ticket_imagen', compact('ticket'));
    }

    public function horariosOcupados($asesorId, $fecha)
{
    // 🔴 Verificar bloqueo de día completo
    $bloqueoDiaCompleto = DB::table('asesorabloqueos')
        ->where('asesorid', $asesorId)
        ->where('fecha', $fecha)
        ->whereNull('horainicio')
        ->exists();

    if ($bloqueoDiaCompleto) {
        return response()->json([
            'dia_completo' => true,
            'rangos' => []
        ]);
    }

    // 🟠 Programaciones normales
    $programados = DB::table('asesoraprogramacion')
        ->where('asesorid', $asesorId)
        ->where('fecha', $fecha)
        ->whereNotIn('estado', ['SE REPROGRAMÓ', 'NO ASISTIÓ'])
        ->select(
            DB::raw("TIME_FORMAT(horadesde,'%H:%i') as horadesde"),
            DB::raw("TIME_FORMAT(horahasta,'%H:%i') as horahasta")
        );

    // 🟡 Bloqueos por rango
    $bloqueados = DB::table('asesorabloqueos')
        ->where('asesorid', $asesorId)
        ->where('fecha', $fecha)
        ->whereNotNull('horainicio')
        ->select(
            DB::raw("TIME_FORMAT(horainicio,'%H:%i') as horadesde"),
            DB::raw("TIME_FORMAT(horafin,'%H:%i') as horahasta")
        );

    $rangos = $programados->union($bloqueados)->get();

    return response()->json([
        'dia_completo' => false,
        'rangos' => $rangos
    ]);
}

    public function listaprogramaciones()
    {
        $hoy = Carbon::today();
        $asesorId = 8;
        $programacionesHoy = DB::table('asesoraprogramacion')
            ->whereDate('fecha', $hoy)
            ->where('estado', 'PENDIENTE')
            ->orderBy('horadesde')
            ->get();

        $programacionesFuturas = DB::table('asesoraprogramacion')
            ->whereDate('fecha', '>', $hoy)
            ->where('estado', 'PENDIENTE')
            ->orderBy('fecha')
            ->get();

        $programacionesHistoricas = DB::table('asesoraprogramacion')
    ->where(function ($query) use ($hoy) {

        $query->whereBetween('fecha', [
                $hoy->copy()->subDays(7),
                $hoy->copy()->subDay() // hasta ayer
            ])
            ->orWhere(function ($q) use ($hoy) {
                $q->whereDate('fecha', $hoy)
                  ->where('estado', '!=', 'PENDIENTE');
            });

    })
    ->orderByDesc('fecha')
    ->orderByDesc('horadesde')
    ->get();

        return view('admin.asesoramiento.listaprogramaciones', compact(
            'programacionesHoy',
            'programacionesFuturas',
            'programacionesHistoricas','asesorId'
        ));
    }

    public function actualizarEstado(Request $request)
{
    $programacion = DB::table('asesoraprogramacion')
        ->where('id', $request->id)
        ->first();

    if ($request->estado === 'SE REPROGRAMÓ') {

        // 1️⃣ Actualizar estado del actual
        DB::table('asesoraprogramacion')
            ->where('id', $request->id)
            ->update([
                'estado' => 'SE REPROGRAMÓ'
            ]);

        $conflictoHorario = DB::table('asesoraprogramacion')
            ->where('asesorid', $programacion->asesorid)
            ->whereDate('fecha', $request->nueva_fecha)
            ->where(function ($query) use ($request) {
                $query->where('horadesde', '<', $request->nueva_horahasta)
                    ->where('horahasta', '>', $request->nueva_horadesde);
            })
            ->exists();

        if ($conflictoHorario) {
            return response()->json([
                'ok' => false,
                'msg' => 'El asesor ya tiene una asesoría programada en ese horario.'
            ], 403);
        }

        // 2️⃣ Crear nuevo registro con nueva fecha y horas
        DB::table('asesoraprogramacion')->insert([
            'asesorid' => $programacion->asesorid,
            'clientenombre' => $programacion->clientenombre,
            'celular' => $programacion->celular,
            'motivo' => $programacion->motivo,
            'sucursal' => $programacion->sucursal,
            'modalidad' => $programacion->modalidad,
            'fecha' => $request->nueva_fecha,
            'horadesde' => $request->nueva_horadesde,
            'horahasta' => $request->nueva_horahasta,
            'estado' => 'PENDIENTE',
            'created_at' => now(),
        ]);

    } else {

        DB::table('asesoraprogramacion')
            ->where('id', $request->id)
            ->update([
                'estado' => $request->estado
            ]);
    }

    return response()->json(['ok' => true]);
}

    public function store(Request $request)
    {
        AsesoraHorarios::create([
            'asesorid' => '8',
            'asesornombre' => 'FABRICIO ORLANDO PRADO PARRADO',
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
            ->where('asesorid', '8')
            ->update($request->only('horainicio', 'horafin', 'duracioncita'));

        return back()->with('success', 'HORARIO ACTUALIZADO');
    }

    public function toggle($id)
    {
        $h = AsesoraHorarios::where('id', $id)
            ->where('asesorid', '8')
            ->firstOrFail();

        $h->estado = $h->estado === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
        $h->save();

        return back();
    }
    public function bloquear(Request $request)
    {
        $inicio = \Carbon\Carbon::parse($request->fecha_inicio);
        $fin = \Carbon\Carbon::parse($request->fecha_fin);

        $horaInicio = $request->horainicio;
        $horaFin = $request->horafin;

        while ($inicio <= $fin) {

            $query = DB::table('asesoraprogramacion')
                ->where('asesorid', '8')
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
                'asesorid' => '8',
                'asesornombre' => 'FABRICIO ORLANDO PRADO PARRADO',
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
        $bloqueo = AsesoraBloqueos::where('asesorid', '8')
            ->findOrFail($id);

        $bloqueo->delete();

        return back()->with('success', 'BLOQUEO ELIMINADO CORRECTAMENTE');
    }
    
}
