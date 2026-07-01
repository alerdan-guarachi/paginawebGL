<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sintoma;
use App\Models\IaRegla;
use App\Models\Consulta;

class ConsultaController extends Controller
{
    public function create()
    {
        $sintomas = Sintoma::where('estado', 1)->get();

        return view('admin.consultas.create', compact('sintomas'));
    }

    public function evaluar(Request $request)
    {
        $request->validate([
            'sintomas' => 'required|array|min:1',
            'sintomas.*' => 'exists:sintomas,id',
            'edad' => 'nullable|integer|min:0|max:120',
            'sexo' => 'nullable|in:todos,masculino,femenino',
            'embarazada' => 'nullable|in:0,1',
        ]);

        $sintomasIds = collect($request->sintomas)
            ->map(fn($id) => (int) $id)
            ->toArray();

        $edad = $request->edad !== null && $request->edad !== '' ? (int) $request->edad : null;
        $sexo = $request->sexo ?: 'todos';
        $embarazada = $request->embarazada !== null && $request->embarazada !== '' ? (int) $request->embarazada : null;

        $reglas = IaRegla::where('estado', 1)
            ->with(['sintomas', 'estudios', 'especialidades'])
            ->get();

        $resultados = [];

        foreach ($reglas as $regla) {

            $sintomasRegla = $regla->sintomas;
            $totalSintomasRegla = $sintomasRegla->count();

            if ($totalSintomasRegla == 0) {
                continue;
            }

            $coincidencias = 0;
            $pesoSintomas = 0;
            $sintomasCriticos = [];

            foreach ($sintomasRegla as $sintoma) {

                if (!in_array($sintoma->id, $sintomasIds)) {
                    continue;
                }

                $coincidencias++;

                $pesoSintomas += ((int)($sintoma->pivot->peso ?? 1)) * ((int)($sintoma->severidad_base ?? 1));

                if ((int)$sintoma->es_critico === 1) {
                    $sintomasCriticos[] = [
                        'id' => $sintoma->id,
                        'nombre' => $sintoma->nombre,
                        'categoria' => $sintoma->categoria,
                        'severidad_base' => $sintoma->severidad_base,
                    ];
                }
            }

            if ($coincidencias == 0) {
                continue;
            }

            $porcentaje = round(($coincidencias / $totalSintomasRegla) * 100);

            $factorEdad = 1;

            if ($edad !== null) {
                if ($regla->edad_min !== null && $edad < $regla->edad_min) {
                    $factorEdad = 0.60;
                }

                if ($regla->edad_max !== null && $edad > $regla->edad_max) {
                    $factorEdad = 0.60;
                }
            }

            $factorSexo = 1;

            if ($regla->sexo_aplica !== null && $regla->sexo_aplica !== 'todos') {
                $factorSexo = ($sexo === $regla->sexo_aplica) ? 1.15 : 0.35;
            }

            $factorEmbarazo = 1;

            if ($regla->aplica_embarazo !== null) {
                if ($embarazada === null) {
                    $factorEmbarazo = 0.70;
                } else {
                    $factorEmbarazo = ((int)$regla->aplica_embarazo === $embarazada) ? 1.20 : 0.25;
                }
            }

            $pesoRegla = (int)($regla->peso ?? 1);
            $prioridadRegla = (int)($regla->prioridad ?? 1);

            $scoreBase = ($pesoSintomas * $pesoRegla) + $prioridadRegla;

            $score = round($scoreBase * $factorEdad * $factorSexo * $factorEmbarazo, 2);

            if ($score <= 0) {
                continue;
            }

            $estudiosRegla = [];

            foreach ($regla->estudios as $estudio) {

                if ((int)$estudio->estado !== 1) {
                    continue;
                }

                $estudiosRegla[] = [
                    'id' => $estudio->id,
                    'nombre' => $estudio->nombre,
                    'tipo' => $estudio->tipo,
                    'descripcion' => $estudio->descripcion,
                    'preparacion' => $estudio->preparacion,
                    'costo_referencial' => $estudio->costo_referencial,
                    'codigo' => $estudio->codigo,
                    'requiere_ayuno' => $estudio->requiere_ayuno,
                    'requiere_orden_medica' => $estudio->requiere_orden_medica,
                    'prioridad' => $estudio->pivot->prioridad,
                    'recomendado' => $estudio->pivot->recomendado,
                    'motivo' => $estudio->pivot->motivo,
                    'score_origen' => $score,
                ];
            }

            $especialidadesRegla = [];

            foreach ($regla->especialidades as $especialidad) {

                if ((int)$especialidad->estado !== 1) {
                    continue;
                }

                $especialidadesRegla[] = [
                    'id' => $especialidad->id,
                    'nombre' => $especialidad->nombre,
                    'descripcion' => $especialidad->descripcion,
                    'codigo' => $especialidad->codigo,
                    'prioridad' => $especialidad->pivot->prioridad,
                    'score_origen' => $score,
                ];
            }

            $resultados[] = [
                'id' => $regla->id,
                'regla' => $regla->nombre,
                'descripcion' => $regla->descripcion,
                'prioridad' => $regla->prioridad,
                'peso' => $regla->peso,
                'min_sintomas' => $regla->min_sintomas,
                'urgencia' => $regla->nivel_urgencia,
                'edad_min' => $regla->edad_min,
                'edad_max' => $regla->edad_max,
                'aplica_embarazo' => $regla->aplica_embarazo,
                'sexo_aplica' => $regla->sexo_aplica,
                'mensaje_alerta' => $regla->mensaje_alerta,
                'recomendacion' => $regla->recomendacion,
                'score' => $score,
                'porcentaje' => $porcentaje,
                'coincidencias' => $coincidencias,
                'total_sintomas' => $totalSintomasRegla,
                'sintomas_criticos' => $sintomasCriticos,
                'estudios' => $estudiosRegla,
                'especialidades' => $especialidadesRegla,
            ];
        }

        usort($resultados, fn($a, $b) => $b['score'] <=> $a['score']);

        $fuertes = collect($resultados)
            ->filter(fn($r) => $r['porcentaje'] >= 50 || $r['coincidencias'] >= 2)
            ->sortByDesc('score')
            ->take(5)
            ->values()
            ->toArray();

        if (count($fuertes) > 0) {
            $resultadosFinales = $fuertes;
        } else {
            $resultadosFinales = collect($resultados)
                ->sortByDesc('score')
                ->take(3)
                ->values()
                ->toArray();
        }

        $estudios = [];
        $especialidades = [];

        foreach ($resultadosFinales as $r) {

            foreach ($r['estudios'] as $e) {
                if (!isset($estudios[$e['id']]) || $e['score_origen'] > $estudios[$e['id']]['score_origen']) {
                    $estudios[$e['id']] = $e;
                }
            }

            foreach ($r['especialidades'] as $e) {
                if (!isset($especialidades[$e['id']]) || $e['score_origen'] > $especialidades[$e['id']]['score_origen']) {
                    $especialidades[$e['id']] = $e;
                }
            }
        }

        usort($estudios, fn($a, $b) =>
            ($b['score_origen'] <=> $a['score_origen'])
            ?: (($a['prioridad'] ?? 99) <=> ($b['prioridad'] ?? 99))
        );

        usort($especialidades, fn($a, $b) =>
            ($b['score_origen'] <=> $a['score_origen'])
            ?: (($a['prioridad'] ?? 99) <=> ($b['prioridad'] ?? 99))
        );

        $alertas = collect($resultadosFinales)
            ->pluck('mensaje_alerta')
            ->filter(fn($a) => $a && $a !== 'NULL' && $a !== 'Sin alerta crítica')
            ->unique()
            ->values()
            ->toArray();

        $recomendaciones = collect($resultadosFinales)
            ->pluck('recomendacion')
            ->filter(fn($r) => $r && $r !== 'NULL')
            ->unique()
            ->values()
            ->toArray();

        $diagnosticoPrincipal = $resultadosFinales[0] ?? null;

        $diagnosticosSecundarios = collect($resultadosFinales)
            ->skip(1)
            ->take(2)
            ->values();

        return response()->json([
            'principal' => $diagnosticoPrincipal,
            'diferenciales' => $diagnosticosSecundarios,
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

        $data = Sintoma::where('estado', 1)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', '%' . $q . '%')
                    ->orWhere('descripcion', 'like', '%' . $q . '%')
                    ->orWhere('sinonimos', 'like', '%' . $q . '%');
            })
            ->select(
                'id',
                'nombre',
                'descripcion',
                'categoria',
                'es_critico',
                'severidad_base',
                'sinonimos',
                'estado'
            )
            ->orderByDesc('es_critico')
            ->orderByDesc('severidad_base')
            ->limit(20)
            ->get();

        return response()->json($data);
    }
}