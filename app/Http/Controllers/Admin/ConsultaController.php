<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiRule;
use App\Models\AiTextReplacement;
use App\Models\Symptom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    public function create(): View
    {
        return view('admin.consultas.create');
    }

    public function buscarSintomas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
        ]);

        $q = trim($validated['q']);

        $sintomas = Symptom::query()
            ->where('state', 1)
            ->where(function ($query) use ($q) {
                $query
                    ->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('normalized_name', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%")
                    ->orWhere('synonyms', 'LIKE', "%{$q}%");
            })
            ->select([
                'id',
                'name',
                'description',
                'category',
                'is_critical',
                'base_severity',
                'urgency_level',
                'synonyms',
                'alert_message',
            ])
            ->orderByRaw(
                '
                    CASE
                        WHEN name LIKE ? THEN 0
                        WHEN name LIKE ? THEN 1
                        WHEN synonyms LIKE ? THEN 2
                        ELSE 3
                    END
                ',
                [
                    "{$q}%",
                    "%{$q}%",
                    "%{$q}%",
                ]
            )
            ->orderByDesc('is_critical')
            ->orderByDesc('base_severity')
            ->orderBy('name')
            ->limit(15)
            ->get();

        return response()->json($sintomas);
    }

    public function evaluar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'texto_sintomas' => [
                'required',
                'string',
                'min:3',
                'max:1500',
            ],

            'edad' => [
                'nullable',
                'integer',
                'min:0',
                'max:120',
            ],

            'sexo' => [
                'nullable',
                'in:TODOS,MASCULINO,FEMENINO',
            ],

            'embarazada' => [
                'nullable',
                'in:0,1',
            ],
        ]);

        $textoOriginal = trim($validated['texto_sintomas']);

        $edad = array_key_exists('edad', $validated)
            && $validated['edad'] !== null
                ? (int) $validated['edad']
                : null;

        $sexo = $validated['sexo'] ?? 'TODOS';

        $estadoEmbarazo = null;

        if (
            $sexo === 'FEMENINO' &&
            array_key_exists('embarazada', $validated) &&
            $validated['embarazada'] !== null
        ) {
            $estadoEmbarazo =
                (int) $validated['embarazada'] === 1
                    ? 'EMBARAZO'
                    : 'NO_EMBARAZO';
        }

        $esEmbarazo =
            $estadoEmbarazo === 'EMBARAZO';

        $sintomasDetectados =
            $this->extraerSintomasDesdeTexto(
                $textoOriginal,
                $esEmbarazo
            );

        if ($sintomasDetectados->isEmpty()) {
            return response()->json([
                'message' =>
                    'No se reconocieron síntomas en el texto escrito.',

                'sugerencia' =>
                    'Escriba síntomas concretos, por ejemplo: dolor de cabeza, fiebre, tos, debilidad o mareos.',
            ], 422);
        }

        $sintomasIds = $sintomasDetectados
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $sintomasSeleccionados = Symptom::query()
            ->where('state', 1)
            ->whereIn('id', $sintomasIds)
            ->get();

        if (
            $sintomasSeleccionados->count() !==
            $sintomasIds->count()
        ) {
            return response()->json([
                'message' =>
                    'Uno o más síntomas detectados ya no están activos.',
            ], 422);
        }

        $sintomasSeleccionadosLookup = array_fill_keys(
            $sintomasIds->all(),
            true
        );

        $reglas = AiRule::query()
            ->where('state', 1)
            ->with([
                'symptoms' => function ($query) {
                    $query->where('symptoms.state', 1);
                },

                'studies' => function ($query) {
                    $query->where('studies.state', 1);
                },

                'specialties' => function ($query) {
                    $query
                        ->where('specialties.state', 1)
                        ->where(
                            'specialties.patient_referral_enabled',
                            1
                        );
                },
            ])
            ->get();

        $resultados = [];

        foreach ($reglas as $regla) {
            $sintomasRegla = $regla->symptoms;

            if ($sintomasRegla->isEmpty()) {
                continue;
            }

            $factorDemografico =
                $this->calcularFactorDemografico(
                    $regla,
                    $edad,
                    $sexo,
                    $estadoEmbarazo
                );

            if ($factorDemografico <= 0) {
                continue;
            }

            $totalSintomasPositivos = 0;
            $totalPesoPositivo = 0;

            $coincidencias = 0;
            $pesoCoincidente = 0;

            $penalizacionExclusion = 0;
            $fallaObligatoria = false;

            $cantidadObligatorios = 0;
            $haySintomaDeAlarma = false;

            $sintomasCriticos = [];
            $sintomasCoincidentes = [];

            foreach ($sintomasRegla as $sintoma) {
                $pivot = $sintoma->pivot;

                if (
                    (int) ($pivot->state ?? 1) !== 1
                ) {
                    continue;
                }

                $condicion = mb_strtolower(
                    (string) (
                        $pivot->condition ?? 'presente'
                    )
                );

                if (
                    !in_array(
                        $condicion,
                        ['presente', 'ausente'],
                        true
                    )
                ) {
                    $condicion = 'presente';
                }

                $seleccionado = isset(
                    $sintomasSeleccionadosLookup[
                        $sintoma->id
                    ]
                );

                $pesoRelacion = max(
                    1,
                    min(
                        5,
                        (int) (
                            $pivot->weight ?? 1
                        )
                    )
                );

                $severidadSintoma = max(
                    1,
                    min(
                        3,
                        (int) (
                            $sintoma->base_severity ?? 1
                        )
                    )
                );

                $pesoCalculado =
                    $pesoRelacion * $severidadSintoma;

                $esObligatorio =
                    (int) (
                        $pivot->is_mandatory ?? 0
                    ) === 1;

                if ($condicion === 'ausente') {
                    if ($seleccionado) {
                        if ($esObligatorio) {
                            $fallaObligatoria = true;
                            break;
                        }

                        $penalizacionExclusion +=
                            $pesoCalculado;
                    }

                    continue;
                }

                $totalSintomasPositivos++;
                $totalPesoPositivo += $pesoCalculado;

                if ($esObligatorio) {
                    $cantidadObligatorios++;
                }

                if (!$seleccionado) {
                    if ($esObligatorio) {
                        $fallaObligatoria = true;
                        break;
                    }

                    continue;
                }

                $coincidencias++;
                $pesoCoincidente += $pesoCalculado;

                $urgenciaSintoma = mb_strtoupper(
                    (string) (
                        $sintoma->urgency_level ?? ''
                    )
                );

                $esSintomaDeAlarma =
                    (bool) $sintoma->is_critical ||
                    $urgenciaSintoma === 'EMERGENCIA' ||
                    $esObligatorio;

                if ($esSintomaDeAlarma) {
                    $haySintomaDeAlarma = true;
                }

                $sintomasCoincidentes[] = [
                    'id' => $sintoma->id,
                    'name' => $sintoma->name,
                    'weight' => $pesoRelacion,
                    'base_severity' =>
                        $severidadSintoma,
                ];

                if (
                    (bool) $sintoma->is_critical ||
                    $urgenciaSintoma === 'EMERGENCIA'
                ) {
                    $sintomasCriticos[] = [
                        'id' => $sintoma->id,
                        'name' => $sintoma->name,
                        'category' =>
                            $sintoma->category,

                        'base_severity' =>
                            $severidadSintoma,

                        'alert_message' =>
                            $sintoma->alert_message,
                    ];
                }
            }

            if (
                $fallaObligatoria ||
                $totalSintomasPositivos === 0
            ) {
                continue;
            }

            $minimoSintomas = max(
                1,
                (int) (
                    $regla->min_symptoms ?? 1
                )
            );

            if ($coincidencias < $minimoSintomas) {
                continue;
            }

            $esReglaDeEmergencia =
                mb_strtoupper(
                    (string) $regla->urgency_level
                ) === 'EMERGENCIA';

            if (
                $esReglaDeEmergencia &&
                !$haySintomaDeAlarma
            ) {
                continue;
            }

            $esReglaGrave = in_array(
                mb_strtoupper(
                    (string) $regla->severity_class
                ),
                ['ALTA', 'CRITICA'],
                true
            );

            if (
                $esReglaGrave &&
                !$haySintomaDeAlarma &&
                $coincidencias < 3
            ) {
                continue;
            }

            $totalSintomasIngresados = max(
                1,
                $sintomasIds->count()
            );

            /*
             * Precisión respecto de lo escrito por el usuario.
             */
            $porcentajeCoincidencia = round(
                (
                    $coincidencias /
                    $totalSintomasIngresados
                ) * 100,
                2
            );

            /*
             * Cobertura de los síntomas positivos de la regla.
             */
            $coberturaRegla = round(
                (
                    $coincidencias /
                    max(1, $totalSintomasPositivos)
                ) * 100,
                2
            );

            /*
             * Cobertura ponderada según peso y severidad.
             */
            $porcentajePonderado =
                $totalPesoPositivo > 0
                    ? round(
                        min(
                            100,
                            (
                                $pesoCoincidente /
                                $totalPesoPositivo
                            ) * 100
                        ),
                        2
                    )
                    : 0;

            $porcentajeMinimo = max(
                1,
                min(
                    100,
                    (int) (
                        $regla->min_match_percentage
                        ?? 50
                    )
                )
            );

            if (
                $coberturaRegla <
                $porcentajeMinimo
            ) {
                continue;
            }

            $scoreBase =
                ($porcentajePonderado * 0.60) +
                ($porcentajeCoincidencia * 0.25) +
                ($coberturaRegla * 0.15);

            $bonusPrioridad = min(
                8,
                ((int) $regla->priority) * 0.60
            );

            $bonusPesoRegla = min(
                6,
                (int) $regla->weight
            );

            $bonusCriticos = min(
                10,
                count($sintomasCriticos) * 3
            );

            $penalizacionPorcentaje =
                $totalPesoPositivo > 0
                    ? (
                        $penalizacionExclusion /
                        $totalPesoPositivo
                    ) * 50
                    : 0;

            $score = (
                $scoreBase +
                $bonusPrioridad +
                $bonusPesoRegla +
                $bonusCriticos -
                $penalizacionPorcentaje
            ) * $factorDemografico;

            $score = round(
                max(
                    0,
                    min(100, $score)
                ),
                2
            );

            if ($score < 25) {
                continue;
            }

            $estudiosRegla = [];

            foreach ($regla->studies as $estudio) {
                if (!(bool) $estudio->state) {
                    continue;
                }

                if (
                    (int) (
                        $estudio->pivot->state ?? 1
                    ) !== 1
                ) {
                    continue;
                }

                if (
                    (int) (
                        $estudio->pivot->is_recommended
                        ?? 1
                    ) !== 1
                ) {
                    continue;
                }

                $prioridad = max(
                    1,
                    min(
                        10,
                        (int) (
                            $estudio->pivot->priority
                            ?? 10
                        )
                    )
                );

                $estudiosRegla[] = [
                    'id' => $estudio->id,
                    'name' => $estudio->name,
                    'type' => $estudio->type,
                    'subtype' => $estudio->subtype,
                    'service_kind' =>
                        $estudio->service_kind,

                    'description' =>
                        $estudio->description,

                    'preparation' =>
                        $estudio->preparation,

                    'code' => $estudio->code,

                    'requires_fasting' =>
                        (bool) $estudio->requires_fasting,

                    'requires_medical_order' =>
                        (bool) $estudio
                            ->requires_medical_order,

                    'priority' => $prioridad,

                    'motive' =>
                        $estudio->pivot->motive,

                    'score_origen' => $score,
                ];
            }

            $especialidadesRegla = [];

            foreach (
                $regla->specialties
                as $especialidad
            ) {
                if (!(bool) $especialidad->state) {
                    continue;
                }

                if (
                    !(bool) $especialidad
                        ->patient_referral_enabled
                ) {
                    continue;
                }

                if (
                    (int) (
                        $especialidad->pivot->state
                        ?? 1
                    ) !== 1
                ) {
                    continue;
                }

                $prioridad = max(
                    1,
                    min(
                        10,
                        (int) (
                            $especialidad->pivot
                                ->priority ?? 10
                        )
                    )
                );

                $especialidadesRegla[] = [
                    'id' => $especialidad->id,
                    'name' => $especialidad->name,

                    'description' =>
                        $especialidad->description,

                    'code' =>
                        $especialidad->code,

                    'professional_group' =>
                        $especialidad
                            ->professional_group,

                    'specialty_level' =>
                        $especialidad
                            ->specialty_level,

                    'priority' => $prioridad,
                    'score_origen' => $score,
                ];
            }

            $resultados[] = [
                'id' => $regla->id,
                'regla' => $regla->name,
                'description' =>
                    $regla->description,

                'clinical_category' =>
                    $regla->clinical_category,

                'rule_type' =>
                    $regla->rule_type,

                'severity_class' =>
                    $regla->severity_class,

                'urgency_level' =>
                    $regla->urgency_level,

                'emergency_confirmed' =>
                    $esReglaDeEmergencia &&
                    $haySintomaDeAlarma,

                'priority' =>
                    (int) $regla->priority,

                'score' => $score,

                'confidence' =>
                    $this->obtenerConfianza($score),

                'percentage' =>
                    $porcentajeCoincidencia,

                'rule_coverage' =>
                    $coberturaRegla,

                'weighted_percentage' =>
                    $porcentajePonderado,

                'matches' => $coincidencias,

                'total_symptoms' =>
                    $totalSintomasIngresados,

                'rule_symptoms_total' =>
                    $totalSintomasPositivos,

                'matched_symptoms' =>
                    $sintomasCoincidentes,

                'critical_symptoms' =>
                    $sintomasCriticos,

                'alert_message' =>
                    $regla->alert_message,

                'recommendation' =>
                    $regla->recommendation,

                'studies' => $estudiosRegla,

                'specialties' =>
                    $especialidadesRegla,
            ];
        }

        $hayResultadoEspecificoSolido = collect($resultados)
            ->contains(function ($resultado) {
                $tipoRegla = mb_strtoupper(
                    (string) (
                        $resultado['rule_type'] ?? ''
                    )
                );

                return
                    $tipoRegla !== 'SINDROME' &&
                    (int) (
                        $resultado['matches'] ?? 0
                    ) >= 3 &&
                    (float) (
                        $resultado['rule_coverage'] ?? 0
                    ) >= 60;
            });

        usort(
            $resultados,
            function ($a, $b) use (
                $hayResultadoEspecificoSolido
            ) {
                $tipoA = mb_strtoupper(
                    (string) (
                        $a['rule_type'] ?? ''
                    )
                );

                $tipoB = mb_strtoupper(
                    (string) (
                        $b['rule_type'] ?? ''
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | PRIORIZAR EMERGENCIAS CONFIRMADAS
                |--------------------------------------------------------------------------
                |
                | Una regla de emergencia solamente se prioriza cuando:
                | - Tiene un síntoma de alarma reconocido.
                | - Tiene al menos tres coincidencias.
                | - Su score es igual o mayor a 60.
                | - No es una regla genérica de tipo SINDROME.
                |
                */

                $aEsEmergenciaConfirmada =
                    (bool) (
                        $a['emergency_confirmed']
                        ?? false
                    ) &&
                    $tipoA !== 'SINDROME' &&
                    (int) (
                        $a['matches'] ?? 0
                    ) >= 3 &&
                    (float) (
                        $a['score'] ?? 0
                    ) >= 60;

                $bEsEmergenciaConfirmada =
                    (bool) (
                        $b['emergency_confirmed']
                        ?? false
                    ) &&
                    $tipoB !== 'SINDROME' &&
                    (int) (
                        $b['matches'] ?? 0
                    ) >= 3 &&
                    (float) (
                        $b['score'] ?? 0
                    ) >= 60;

                if (
                    $aEsEmergenciaConfirmada !==
                    $bEsEmergenciaConfirmada
                ) {
                    return $aEsEmergenciaConfirmada
                        ? -1
                        : 1;
                }

                /*
                |--------------------------------------------------------------------------
                | BAJAR REGLAS GENÉRICAS CUANDO HAY RESULTADO ESPECÍFICO
                |--------------------------------------------------------------------------
                */

                if ($hayResultadoEspecificoSolido) {
                    $aEsSindrome =
                        $tipoA === 'SINDROME';

                    $bEsSindrome =
                        $tipoB === 'SINDROME';

                    if ($aEsSindrome !== $bEsSindrome) {
                        return $aEsSindrome
                            ? 1
                            : -1;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | ORDEN NORMAL POR SCORE
                |--------------------------------------------------------------------------
                */

                $comparacionScore =
                    ($b['score'] ?? 0)
                    <=>
                    ($a['score'] ?? 0);

                if ($comparacionScore !== 0) {
                    return $comparacionScore;
                }

                /*
                |--------------------------------------------------------------------------
                | DESEMPATE POR COBERTURA DE LA REGLA
                |--------------------------------------------------------------------------
                */

                $comparacionCobertura =
                    ($b['rule_coverage'] ?? 0)
                    <=>
                    ($a['rule_coverage'] ?? 0);

                if ($comparacionCobertura !== 0) {
                    return $comparacionCobertura;
                }

                /*
                |--------------------------------------------------------------------------
                | DESEMPATE POR PORCENTAJE DE SÍNTOMAS INGRESADOS
                |--------------------------------------------------------------------------
                */

                $comparacionPorcentaje =
                    ($b['percentage'] ?? 0)
                    <=>
                    ($a['percentage'] ?? 0);

                if ($comparacionPorcentaje !== 0) {
                    return $comparacionPorcentaje;
                }

                /*
                |--------------------------------------------------------------------------
                | DESEMPATE POR CANTIDAD DE COINCIDENCIAS
                |--------------------------------------------------------------------------
                */

                $comparacionCoincidencias =
                    ($b['matches'] ?? 0)
                    <=>
                    ($a['matches'] ?? 0);

                if (
                    $comparacionCoincidencias !== 0
                ) {
                    return
                        $comparacionCoincidencias;
                }

                /*
                |--------------------------------------------------------------------------
                | ÚLTIMO DESEMPATE POR PRIORIDAD DE BASE DE DATOS
                |--------------------------------------------------------------------------
                */

                return
                    ($b['priority'] ?? 0)
                    <=>
                    ($a['priority'] ?? 0);
            }
        );

        $coleccionResultados = collect(
            $resultados
        )->values();

        $principal =
            $coleccionResultados->first();

        $diferenciales = $coleccionResultados
            ->skip(1)
            ->reject(function ($resultado) use ($principal) {
                if (!$principal) {
                    return false;
                }

                return $this->sonDiagnosticosSimilares(
                    $principal,
                    $resultado
                );
            })
            ->take(3)
            ->values();

        $diagnosticos = $principal
            ? collect([$principal])
                ->concat($diferenciales)
                ->values()
            : collect();

        $estudios = $principal
            ? collect($principal['studies'])
                ->sortBy('priority')
                ->take(4)
                ->values()
                ->all()
            : [];

        $especialidades = $principal
            ? collect($principal['specialties'])
                ->sortBy('priority')
                ->take(3)
                ->values()
                ->all()
            : [];

        $alertasSintomas = $sintomasSeleccionados
            ->filter(function ($sintoma) {
                return
                    (bool) $sintoma->is_critical ||
                    mb_strtoupper(
                        (string) (
                            $sintoma->urgency_level ?? ''
                        )
                    ) === 'EMERGENCIA';
            })
            ->pluck('alert_message')
            ->filter(
                fn ($mensaje) =>
                    !empty($mensaje) &&
                    mb_strtoupper(
                        trim((string) $mensaje)
                    ) !== 'NULL'
            );

        $alertasReglaPrincipal = collect();

        if ($principal) {
            $urgenciaPrincipal = mb_strtoupper(
                (string) (
                    $principal['urgency_level']
                    ?? 'BAJO'
                )
            );

            $mensajePrincipal = trim(
                (string) (
                    $principal['alert_message']
                    ?? ''
                )
            );

            if (
                in_array(
                    $urgenciaPrincipal,
                    ['ALTO', 'EMERGENCIA'],
                    true
                ) &&
                $mensajePrincipal !== '' &&
                mb_strtoupper(
                    $mensajePrincipal
                ) !== 'NULL'
            ) {
                $alertasReglaPrincipal->push(
                    $mensajePrincipal
                );
            }
        }

        $alertas = $alertasSintomas
            ->merge($alertasReglaPrincipal)
            ->unique()
            ->values();

        $recomendaciones = collect([
            $principal['recommendation'] ?? null,
        ])
            ->filter(
                fn ($mensaje) =>
                    !empty($mensaje) &&
                    mb_strtoupper(
                        trim((string) $mensaje)
                    ) !== 'NULL'
            )
            ->unique()
            ->values();

        return response()->json([
            'principal' => $principal,

            'diferenciales' =>
                $diferenciales,

            'diagnosticos' =>
                $diagnosticos,

            'estudios' => $estudios,

            'especialidades' =>
                $especialidades,

            'alertas' => $alertas,

            'recomendaciones' =>
                $recomendaciones,

            'texto_original' =>
                $textoOriginal,

            'sintomas_detectados' =>
                $sintomasDetectados
                    ->map(function ($sintoma) {
                        return [
                            'id' =>
                                $sintoma['id'],

                            'name' =>
                                $sintoma['name'],

                            'category' =>
                                $sintoma['category'],

                            'is_critical' =>
                                $sintoma['is_critical'],

                            'match_score' => round(
                                $sintoma[
                                    'match_score'
                                ] * 100,
                                2
                            ),

                            'fragmento' =>
                                $sintoma['fragmento'],
                        ];
                    })
                    ->values(),

            'metadata' => [
                'total_sintomas_detectados' =>
                    $sintomasIds->count(),

                'total_reglas_evaluadas' =>
                    $reglas->count(),

                'total_resultados' =>
                    $diagnosticos->count(),
            ],
        ]);
    }

    private function extraerSintomasDesdeTexto(
        string $texto,
        bool $esEmbarazo = false
    ): Collection {
        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR SIN REEMPLAZOS PARA EVALUAR NEGACIONES
        |--------------------------------------------------------------------------
        */

        $textoBase =
            $this->normalizarTextoClinico(
                $texto,
                false,
                $esEmbarazo
            );

        $fragmentosBase = preg_split(
            '/[,;.\n]+|\s+(?:ademas|tambien|pero)\s+/u',
            $textoBase,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $fragmentos = collect($fragmentosBase ?: [])
            ->map(
                fn ($fragmento) =>
                    trim((string) $fragmento)
            )
            ->filter(
                fn ($fragmento) =>
                    mb_strlen($fragmento) >= 2
            )
            ->reject(
                fn ($fragmento) =>
                    $this->esFragmentoNegado(
                        $fragmento
                    )
            )
            ->flatMap(function ($fragmento) use (
                $esEmbarazo
            ) {
                $fragmentoNormalizado =
                    $this->normalizarTextoClinico(
                        $fragmento,
                        true,
                        $esEmbarazo
                    );

                return preg_split(
                    '/[,;.\n]+|\s+(?:ademas|tambien|pero)\s+/u',
                    $fragmentoNormalizado,
                    -1,
                    PREG_SPLIT_NO_EMPTY
                ) ?: [];
            })
            ->map(
                fn ($fragmento) =>
                    trim((string) $fragmento)
            )
            ->filter(
                fn ($fragmento) =>
                    mb_strlen($fragmento) >= 2
            )
            ->values();

        $sintomas = Symptom::query()
            ->where('state', 1)
            ->select([
                'id',
                'name',
                'normalized_name',
                'synonyms',
                'category',
                'is_critical',
                'base_severity',
                'urgency_level',
                'pregnancy_related',
                'alert_message',
            ])
            ->get();

        $variantesPorSintoma = [];

        foreach ($sintomas as $sintoma) {
            $variantesPorSintoma[$sintoma->id] =
                $this->obtenerVariantesSintoma(
                    $sintoma
                );
        }

        $detectados = [];

        foreach ($fragmentos as $fragmento) {
            $coincidenciasDirectas = [];

            foreach ($sintomas as $sintoma) {
                $variantes =
                    $variantesPorSintoma[
                        $sintoma->id
                    ] ?? [];

                foreach ($variantes as $variante) {
                    if (mb_strlen($variante) < 3) {
                        continue;
                    }

                    if (
                        $this->contieneFrase(
                            $fragmento,
                            $variante
                        )
                    ) {
                        $coincidenciasDirectas[] = [
                            'sintoma' => $sintoma,
                            'variante' => $variante,
                        ];

                        break;
                    }
                }
            }

            usort(
                $coincidenciasDirectas,
                function ($a, $b) use ($esEmbarazo) {
                    if ($esEmbarazo) {
                        $embarazoA = (int) (
                            $a['sintoma']
                                ->pregnancy_related ?? 0
                        );

                        $embarazoB = (int) (
                            $b['sintoma']
                                ->pregnancy_related ?? 0
                        );

                        if ($embarazoA !== $embarazoB) {
                            return $embarazoB <=> $embarazoA;
                        }
                    }

                    $comparacionLongitud =
                        mb_strlen($b['variante'])
                        <=>
                        mb_strlen($a['variante']);

                    if ($comparacionLongitud !== 0) {
                        return $comparacionLongitud;
                    }

                    return
                        (int) $b['sintoma']->base_severity
                        <=>
                        (int) $a['sintoma']->base_severity;
                }
            );

            $variantesAceptadas = [];
            $huboCoincidenciaDirecta = false;

            foreach (
                $coincidenciasDirectas
                as $coincidencia
            ) {
                $variante =
                    $coincidencia['variante'];

                $estaDentroDeOtra = collect(
                    $variantesAceptadas
                )->contains(
                    fn ($aceptada) =>
                        $this->contieneFrase(
                            $aceptada,
                            $variante
                        )
                );

                if ($estaDentroDeOtra) {
                    continue;
                }

                $sintoma =
                    $coincidencia['sintoma'];

                $detectados[$sintoma->id] = [
                    'id' => $sintoma->id,
                    'name' => $sintoma->name,
                    'category' =>
                        $sintoma->category,

                    'is_critical' =>
                        (bool) $sintoma->is_critical,

                    'match_score' => 1,
                    'fragmento' => $fragmento,
                ];

                $variantesAceptadas[] =
                    $variante;

                $huboCoincidenciaDirecta = true;
            }

            if ($huboCoincidenciaDirecta) {
                continue;
            }

            $mejorSintoma = null;
            $mejorPuntaje = 0;

            foreach ($sintomas as $sintoma) {
                $variantes =
                    $variantesPorSintoma[
                        $sintoma->id
                    ] ?? [];

                foreach ($variantes as $variante) {
                    $puntaje =
                        $this->calcularSimilitudTexto(
                            $fragmento,
                            $variante
                        );

                    if (
                        $esEmbarazo &&
                        (bool) $sintoma->pregnancy_related
                    ) {
                        $puntaje = min(
                            1,
                            $puntaje + 0.15
                        );
                    }

                    if ($puntaje > $mejorPuntaje) {
                        $mejorPuntaje = $puntaje;
                        $mejorSintoma = $sintoma;
                    }
                }
            }

            $umbralCoincidencia = 0.72;

            if ($mejorSintoma !== null) {
                $urgenciaMejorSintoma = mb_strtoupper(
                    (string) (
                        $mejorSintoma->urgency_level ?? ''
                    )
                );

                if (
                    (bool) $mejorSintoma->is_critical ||
                    $urgenciaMejorSintoma === 'EMERGENCIA'
                ) {
                    $umbralCoincidencia = 0.85;
                }
            }

            if (
                $mejorSintoma !== null &&
                $mejorPuntaje >= $umbralCoincidencia
            ) {
                $id = $mejorSintoma->id;

                if (
                    !isset($detectados[$id]) ||
                    $mejorPuntaje >
                    $detectados[$id]['match_score']
                ) {
                    $detectados[$id] = [
                        'id' => $id,

                        'name' =>
                            $mejorSintoma->name,

                        'category' =>
                            $mejorSintoma->category,

                        'is_critical' =>
                            (bool) $mejorSintoma
                                ->is_critical,

                        'match_score' =>
                            round(
                                $mejorPuntaje,
                                4
                            ),

                        'fragmento' =>
                            $fragmento,
                    ];
                }
            }
        }

        return collect(
            array_values($detectados)
        )
            ->sortByDesc('match_score')
            ->values();
    }

    private function obtenerVariantesSintoma(
        Symptom $sintoma
    ): array {
        $variantes = collect([
            $sintoma->name,
            $sintoma->normalized_name,
        ]);

        if (!empty($sintoma->synonyms)) {
            $sinonimos = preg_split(
                '/[,;|]+/u',
                $sintoma->synonyms,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $variantes = $variantes->merge(
                $sinonimos ?: []
            );
        }

        return $variantes
            ->map(
                fn ($variante) =>
                    $this->normalizarTextoClinico(
                        (string) $variante,
                        false,
                        false
                    )
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function obtenerReemplazosTexto(
        bool $esEmbarazo
    ): Collection {
        /*
         * Caché únicamente durante la petición actual.
         * Los cambios en la base de datos se aplican
         * automáticamente en la siguiente petición.
         */
        static $reemplazosCache = [];

        $cacheKey = $esEmbarazo
            ? 'EMBARAZO'
            : 'GENERAL';

        if (isset($reemplazosCache[$cacheKey])) {
            return $reemplazosCache[$cacheKey];
        }

        $contextos = $esEmbarazo
            ? ['EMBARAZO', 'GENERAL']
            : ['GENERAL'];

        $reemplazos = AiTextReplacement::query()
            ->where('state', 1)
            ->whereIn('context', $contextos)
            ->orderByRaw(
                "
                    CASE
                        WHEN context = 'EMBARAZO' THEN 0
                        ELSE 1
                    END
                "
            )
            ->orderBy('priority')
            ->orderBy('id')
            ->get([
                'id',
                'context',
                'pattern',
                'replacement',
                'priority',
            ]);

        $reemplazosCache[$cacheKey] = $reemplazos;

        return $reemplazos;
    }

    private function normalizarTextoClinico(
        string $texto,
        bool $aplicarReemplazos = true,
        bool $esEmbarazo = false
    ): string {
        $texto = Str::ascii(
            mb_strtolower(trim($texto))
        );

        if ($aplicarReemplazos) {
            $reemplazos =
                $this->obtenerReemplazosTexto(
                    $esEmbarazo
                );

            /*
             * Cada resultado producido por una regla se protege
             * temporalmente para que otra regla no lo transforme.
             */
            $resultadosProtegidos = [];

            foreach ($reemplazos as $reglaTexto) {
                $patron =
                    '~' .
                    $reglaTexto->pattern .
                    '~u';

                if (@preg_match($patron, '') === false) {
                    Log::warning(
                        'Patrón de normalización inválido.',
                        [
                            'replacement_id' =>
                                $reglaTexto->id,

                            'pattern' =>
                                $reglaTexto->pattern,
                        ]
                    );

                    continue;
                }

                $textoReemplazado = preg_replace_callback(
                    $patron,
                    function (array $coincidencia) use (
                        &$resultadosProtegidos,
                        $reglaTexto
                    ): string {
                        $clave =
                            'zqxreemplazo' .
                            count($resultadosProtegidos) .
                            'qxz';

                        $resultadosProtegidos[$clave] =
                            (string) $reglaTexto->replacement;

                        return $clave;
                    },
                    $texto
                );

                if ($textoReemplazado !== null) {
                    $texto = $textoReemplazado;
                }
            }

            if (!empty($resultadosProtegidos)) {
                $texto = strtr(
                    $texto,
                    $resultadosProtegidos
                );
            }

            /*
             * Los replacements también quedan sin tildes
             * y en minúsculas para compararlos con normalized_name.
             */
            $texto = Str::ascii(
                mb_strtolower($texto)
            );
        }

        $texto = preg_replace(
            '/\b(?:desde\s+)?hace\s+\d+\s+' .
            '(?:hora|horas|dia|dias|semana|semanas|' .
            'mes|meses|ano|anos)\b/u',
            ' ',
            $texto
        );

        $texto = preg_replace(
            '/\bdesde\s+(?:ayer|anoche|hoy|' .
            'esta manana|esta tarde)\b/u',
            ' ',
            $texto
        );

        $texto = preg_replace(
            '/\bpor\s+\d+\s+' .
            '(?:hora|horas|dia|dias|semana|semanas|' .
            'mes|meses)\b/u',
            ' ',
            $texto
        );

        $texto = preg_replace(
            '/[^a-z0-9\s,;.\n]+/u',
            ' ',
            $texto
        );

        $texto = preg_replace(
            '/\s+/u',
            ' ',
            $texto
        );

        return trim($texto);
    }

    private function esFragmentoNegado(
        string $fragmento
    ): bool {
        $fragmento = trim($fragmento);

        return preg_match(
            '/^(?:' .
                'sin|' .
                'niega|' .
                'niego|' .
                'no\s+presenta|' .
                'no\s+presento|' .
                'no\s+tiene|' .
                'no\s+tengo|' .
                'no\s+refiere|' .
                'no\s+refiero|' .
                'no\s+hay' .
            ')\b/u',
            $fragmento
        ) === 1;
    }

    private function contieneFrase(
        string $texto,
        string $frase
    ): bool {
        if ($frase === '') {
            return false;
        }

        $patron =
            '/(?<![a-z0-9])' .
            preg_quote($frase, '/') .
            '(?![a-z0-9])/u';

        return preg_match(
            $patron,
            $texto
        ) === 1;
    }

    private function calcularSimilitudTexto(
        string $texto,
        string $variante
    ): float {
        if ($texto === $variante) {
            return 1;
        }

        if (
            $this->contieneFrase(
                $texto,
                $variante
            )
        ) {
            return 1;
        }

        $tokensTexto =
            $this->obtenerTokensSignificativos(
                $texto
            );

        $tokensVariante =
            $this->obtenerTokensSignificativos(
                $variante
            );

        if (
            empty($tokensTexto) ||
            empty($tokensVariante)
        ) {
            return 0;
        }

        if (
            count($tokensVariante) === 1 &&
            in_array(
                $tokensVariante[0],
                $tokensTexto,
                true
            )
        ) {
            return 1;
        }

        $interseccion = array_values(
            array_intersect(
                $tokensTexto,
                $tokensVariante
            )
        );

        if (
            count($tokensVariante) >= 2 &&
            count($interseccion) < 2
        ) {
            return 0;
        }

        if (empty($interseccion)) {
            similar_text(
                implode(' ', $tokensTexto),
                implode(' ', $tokensVariante),
                $porcentajeCaracteres
            );

            return $porcentajeCaracteres >= 94
                ? $porcentajeCaracteres / 100
                : 0;
        }

        $coberturaVariante =
            count($interseccion) /
            max(
                1,
                count($tokensVariante)
            );

        $precisionFragmento =
            count($interseccion) /
            max(
                1,
                count($tokensTexto)
            );

        $similitudTokens =
            ($coberturaVariante * 0.70) +
            ($precisionFragmento * 0.30);

        similar_text(
            implode(' ', $tokensTexto),
            implode(' ', $tokensVariante),
            $porcentajeCaracteres
        );

        $similitudCaracteres =
            ($porcentajeCaracteres / 100) * 0.80;

        return max(
            $similitudTokens,
            $similitudCaracteres
        );
    }

    private function obtenerTokensSignificativos(
        string $texto
    ): array {
        $palabrasIgnoradas = [
            'a',
            'al',
            'algo',
            'con',
            'de',
            'del',
            'desde',
            'durante',
            'el',
            'en',
            'es',
            'esta',
            'estoy',
            'hace',
            'la',
            'las',
            'le',
            'lo',
            'los',
            'me',
            'mi',
            'mucho',
            'muy',
            'orina',
            'orino',
            'por',
            'presenta',
            'presento',
            'puede',
            'puedo',
            'que',
            'respira',
            'respiro',
            'se',
            'siente',
            'siento',
            'sin',
            'su',
            'tengo',
            'tiene',
            'un',
            'una',
            'unos',
            'unas',
            'veces',
        ];

        return collect(
            preg_split(
                '/\s+/u',
                trim($texto),
                -1,
                PREG_SPLIT_NO_EMPTY
            ) ?: []
        )
            ->map(
                fn ($palabra) =>
                    $this->normalizarToken(
                        $palabra
                    )
            )
            ->filter(
                function ($palabra) use (
                    $palabrasIgnoradas
                ) {
                    return
                        mb_strlen($palabra) >= 2 &&
                        !is_numeric($palabra) &&
                        !in_array(
                            $palabra,
                            $palabrasIgnoradas,
                            true
                        );
                }
            )
            ->unique()
            ->values()
            ->all();
    }

    private function normalizarToken(
        string $token
    ): string {
        $token = trim($token);

        if (
            mb_strlen($token) > 5 &&
            str_ends_with($token, 'es')
        ) {
            return mb_substr(
                $token,
                0,
                -2
            );
        }

        if (
            mb_strlen($token) > 4 &&
            str_ends_with($token, 's')
        ) {
            return mb_substr(
                $token,
                0,
                -1
            );
        }

        return $token;
    }

    private function sonDiagnosticosSimilares(
        array $principal,
        array $resultado
    ): bool {
        $tokensPrincipal =
            $this->obtenerTokensDiagnostico(
                (string) (
                    $principal['regla'] ?? ''
                )
            );

        $tokensResultado =
            $this->obtenerTokensDiagnostico(
                (string) (
                    $resultado['regla'] ?? ''
                )
            );

        if (
            empty($tokensPrincipal) ||
            empty($tokensResultado)
        ) {
            return false;
        }

        $interseccion = array_intersect(
            $tokensPrincipal,
            $tokensResultado
        );

        $base = min(
            count($tokensPrincipal),
            count($tokensResultado)
        );

        if ($base === 0) {
            return false;
        }

        $similitud =
            count($interseccion) / $base;

        return $similitud >= 0.67;
    }

    private function obtenerTokensDiagnostico(
        string $nombre
    ): array {
        $nombre = Str::ascii(
            mb_strtolower($nombre)
        );

        $nombre = preg_replace(
            '/[^a-z0-9\s]+/u',
            ' ',
            $nombre
        );

        $palabrasIgnoradas = [
            'sindrome',
            'sospecha',
            'probable',
            'posible',
            'persistente',
            'recurrente',
            'cronica',
            'cronico',
            'aguda',
            'agudo',
            'sintomatica',
            'sintomatico',
            'grave',
            'severa',
            'severo',
            'leve',
            'moderada',
            'moderado',
            'cuadro',
            'con',
            'sin',
            'por',
            'para',
            'del',
            'de',
            'la',
            'el',
            'los',
            'las',
            'un',
            'una',
            'y',
            'o',
        ];

        return collect(
            preg_split(
                '/\s+/u',
                trim((string) $nombre),
                -1,
                PREG_SPLIT_NO_EMPTY
            ) ?: []
        )
            ->filter(
                fn ($palabra) =>
                    mb_strlen($palabra) >= 4 &&
                    !in_array(
                        $palabra,
                        $palabrasIgnoradas,
                        true
                    )
            )
            ->map(function ($palabra) {
                return mb_strlen($palabra) >= 6
                    ? mb_substr($palabra, 0, 5)
                    : $palabra;
            })
            ->unique()
            ->values()
            ->all();
    }

    private function calcularFactorDemografico(
        AiRule $regla,
        ?int $edad,
        string $sexo,
        ?string $estadoEmbarazo
    ): float {
        $factor = 1.0;

        $grupoEdad = mb_strtoupper(
            (string) ($regla->age_group ?? 'TODOS')
        );

        $reglaTieneRestriccionEdad =
            $grupoEdad !== 'TODOS' ||
            $regla->age_min_years !== null ||
            $regla->age_max_years !== null ||
            $regla->age_max_months !== null;

        if ($edad === null) {
            if ($reglaTieneRestriccionEdad) {
                $factor *= 0.90;
            }
        } else {
            if (
                $regla->age_min_years !== null &&
                $edad < (int) $regla->age_min_years
            ) {
                return 0;
            }

            if (
                $regla->age_max_years !== null &&
                $edad > (int) $regla->age_max_years
            ) {
                return 0;
            }

            if ($regla->age_max_months !== null) {
                $maximoMesesRegla =
                    (int) $regla->age_max_months;

                $minimoMesesPaciente =
                    $edad * 12;

                $maximoMesesPaciente =
                    ($edad * 12) + 11;

                if (
                    $minimoMesesPaciente >
                    $maximoMesesRegla
                ) {
                    return 0;
                }

                if (
                    $maximoMesesPaciente >
                    $maximoMesesRegla
                ) {
                    $factor *= 0.90;
                }
            }

            $grupoCompatible = match ($grupoEdad) {
                'LACTANTE' =>
                    $edad <= 1,

                'PEDIATRICO' =>
                    $edad <= 17,

                'ADULTO' =>
                    $edad >= 18,

                'ADULTO_MAYOR' =>
                    $edad >= 60,

                default => true,
            };

            if (!$grupoCompatible) {
                return 0;
            }
        }

        $sexoRegla = mb_strtoupper(
            (string) ($regla->sex_applies ?? 'TODOS')
        );

        if ($sexoRegla !== 'TODOS') {
            if ($sexo === 'TODOS') {
                $factor *= 0.85;
            } elseif ($sexo !== $sexoRegla) {
                return 0;
            }
        }

        $embarazoRegla = mb_strtoupper(
            (string) (
                $regla->pregnancy_applicability
                ?? 'TODOS'
            )
        );

        if ($embarazoRegla !== 'TODOS') {
            if ($sexo !== 'FEMENINO') {
                return 0;
            }

            if ($estadoEmbarazo === null) {
                $factor *= 0.80;
            } elseif (
                $estadoEmbarazo !== $embarazoRegla
            ) {
                return 0;
            }
        }

        return $factor;
    }

    private function obtenerConfianza(
        float $score
    ): string {
        return match (true) {
            $score >= 80 => 'ALTA',
            $score >= 60 => 'MEDIA',
            default => 'BAJA',
        };
    }
}
