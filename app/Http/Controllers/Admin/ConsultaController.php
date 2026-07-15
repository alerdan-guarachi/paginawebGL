<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Symptom;
use App\Models\AiRule;

class ConsultaController extends Controller
{
    public function create()
    {
        $sintomas = Symptom::where('state', 1)->get();

        return view('admin.consultas.create', compact('sintomas'));
    }

    public function evaluar(Request $request)
{
    $request->validate([
        'sintomas' => 'required|array|min:1',
        'sintomas.*' => 'exists:symptoms,id',
        'edad' => 'nullable|integer|min:0|max:120',
        'sexo' => 'nullable|in:todos,masculino,femenino',
        'embarazada' => 'nullable|in:0,1',
    ]);

    $sintomasIds = collect($request->sintomas)->map(fn($id) => (int)$id)->toArray();

    $edad = $request->edad !== null && $request->edad !== '' ? (int)$request->edad : null;
    $sexo = $request->sexo ?: 'todos';
    $embarazada = $request->embarazada !== null && $request->embarazada !== '' ? (int)$request->embarazada : null;

    $reglas = AiRule::where('state', 1)
        ->with(['symptoms', 'studies', 'specialties'])
        ->get();

    $resultados = [];

    foreach ($reglas as $regla) {

        $sintomasRegla = $regla->symptoms;

        if ($sintomasRegla->isEmpty()) {
            continue;
        }

        $coincidencias = 0;
        $pesoTotal = 0;
        $scoreSintomas = 0;
        $penalizacion = 0;
        $sintomasCriticos = [];

        foreach ($sintomasRegla as $sintoma) {

            $peso = ((int)($sintoma->pivot->weight ?? 1)) * ((int)($sintoma->base_severity ?? 1));
            $pesoTotal += $peso;

            if (in_array($sintoma->id, $sintomasIds)) {

                $coincidencias++;
                $scoreSintomas += $peso;

                // 🔥 Bonus por crítico
                if ((int)$sintoma->is_critical === 1) {
                    $scoreSintomas += $peso * 0.5;

                    $sintomasCriticos[] = [
                        'id' => $sintoma->id,
                        'nombre' => $sintoma->name,
                        'category' => $sintoma->category,
                        'base_severity' => $sintoma->base_severity,
                    ];
                }

            } else {

                // 🔥 Penalización si falta síntoma importante
                if ($peso >= 3) {
                    $penalizacion += $peso * 0.7;
                }
            }
        }

        $minimoRequerido = (int)($regla->min_symptom ?? 1);
        if ($coincidencias < $minimoRequerido) {
            continue;
        }

        // 🎯 Score normalizado real
        $scoreBase = $pesoTotal > 0
            ? ($scoreSintomas - $penalizacion) / $pesoTotal
            : 0;

        $score = $scoreBase * 100;

        // 🔥 BONUS si hay síntomas críticos
        if (count($sintomasCriticos) > 0) {
            $score *= 1.3;
        }

        // 🧠 FACTORES SUAVES

        $factorEdad = 1;
        if ($edad !== null) {
            if ($regla->age_min !== null && $edad < $regla->age_min) {
                $factorEdad = 0.8;
            }
            if ($regla->age_max !== null && $edad > $regla->age_max) {
                $factorEdad = 0.8;
            }
        }

        $factorSexo = 1;
        if ($regla->sex_applies !== null && $regla->sex_applies !== 'todos') {
            $factorSexo = ($sexo === $regla->sex_applies) ? 1.1 : 0.8;
        }

        $factorEmbarazo = 1;
        if ($regla->applies_pregnancy !== null) {
            if ($embarazada === null) {
                $factorEmbarazo = 0.9;
            } else {
                $factorEmbarazo = ((int)$regla->applies_pregnancy === $embarazada) ? 1.1 : 0.7;
            }
        }

        $score = round($score * $factorEdad * $factorSexo * $factorEmbarazo, 2);

        // ❌ Eliminar ruido
        if ($score < 20) {
            continue;
        }

        $porcentaje = round(($coincidencias / $sintomasRegla->count()) * 100);

        // =====================
        // 🧪 ESTUDIOS
        // =====================
        $estudiosRegla = [];

        foreach ($regla->studies as $estudio) {

            if ((int)$estudio->state !== 1) continue;

            $prioridadFinal = ($estudio->pivot->priority ?? 1) * $score;

            $estudiosRegla[] = [
                'id' => $estudio->id,
                'name' => $estudio->name,
                'type' => $estudio->type,
                'description' => $estudio->description,
                'preparation' => $estudio->preparation,
                'code' => $estudio->code,
                'requires_fasting' => $estudio->requires_fasting,
                'requires_medical_order' => $estudio->requires_medical_order,
                'priority' => $prioridadFinal,
                'motive' => $estudio->pivot->motive,
                'score_origen' => $score,
            ];
        }

        // =====================
        // 👨‍⚕️ ESPECIALIDADES
        // =====================
        $especialidadesRegla = [];

        foreach ($regla->specialties as $especialidad) {

            if ((int)$especialidad->state !== 1) continue;

            $prioridadFinal = ($especialidad->pivot->priority ?? 1) * $score;

            $especialidadesRegla[] = [
                'id' => $especialidad->id,
                'name' => $especialidad->name,
                'description' => $especialidad->description,
                'code' => $especialidad->code,
                'priority' => $prioridadFinal,
                'score_origen' => $score,
            ];
        }

        $resultados[] = [
            'id' => $regla->id,
            'regla' => $regla->name,
            'description' => $regla->description,
            'urgency_level' => $regla->urgency_level,
            'score' => $score,
            'percentage' => $porcentaje,
            'matches' => $coincidencias,
            'total_symptoms' => $sintomasRegla->count(),
            'critical_symptoms' => $sintomasCriticos,
            'alert_message' => $regla->alert_message,
            'recommendation' => $regla->recommendation,
            'studies' => $estudiosRegla,
            'specialties' => $especialidadesRegla,
        ];
    }

    // =====================
    // 🔥 ORDEN FINAL
    // =====================
    usort($resultados, fn($a, $b) => $b['score'] <=> $a['score']);

    $resultadosFinales = collect($resultados)
        ->take(5)
        ->values()
        ->toArray();

    // =====================
    // 🧪 UNIFICAR ESTUDIOS
    // =====================
    $estudios = [];
    $especialidades = [];

    foreach ($resultadosFinales as $r) {

        foreach ($r['studies'] as $e) {
            if (!isset($estudios[$e['id']]) || $e['priority'] > $estudios[$e['id']]['priority']) {
                $estudios[$e['id']] = $e;
            }
        }

        foreach ($r['specialties'] as $e) {
            if (!isset($especialidades[$e['id']]) || $e['priority'] > $especialidades[$e['id']]['priority']) {
                $especialidades[$e['id']] = $e;
            }
        }
    }

    usort($estudios, fn($a, $b) => $b['priority'] <=> $a['priority']);
    usort($especialidades, fn($a, $b) => $b['priority'] <=> $a['priority']);

    // =====================
    // 🚨 ALERTAS Y RECOMENDACIONES
    // =====================
    $alertas = collect($resultadosFinales)
        ->pluck('alert_message')
        ->filter(fn($a) => $a && $a !== 'NULL')
        ->unique()
        ->values()
        ->toArray();

    $recomendaciones = collect($resultadosFinales)
        ->pluck('recommendation')
        ->filter(fn($r) => $r && $r !== 'NULL')
        ->unique()
        ->values()
        ->toArray();

    return response()->json([
        'principal' => $resultadosFinales[0] ?? null,
        'diferenciales' => collect($resultadosFinales)->skip(1)->take(2)->values(),
        'diagnosticos' => $resultadosFinales,
        'estudios' => array_slice(array_values($estudios), 0, 4),
        'especialidades' => array_slice(array_values($especialidades), 0, 3),
        'alertas' => $alertas,
        'recomendaciones' => $recomendaciones,
    ]);
}

    public function buscarSintomas(Request $request)
    {
        $q = $request->get('q');

        $data = Symptom::where('state', 1)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
                    ->orWhere('synonyms', 'like', '%' . $q . '%');
            })
            ->select(
                'id',
                'name',
                'description',
                'category',
                'is_critical',
                'base_severity',
                'synonyms',
                'state'
            )
            ->orderByDesc('is_critical')
            ->orderByDesc('base_severity')
            ->limit(20)
            ->get();

        return response()->json($data);
    }
}