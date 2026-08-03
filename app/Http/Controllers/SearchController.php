<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Materia;
use App\Models\Carrera;
use App\Models\OfertaAcademica;
use App\Models\Aula;
use App\Models\PeriodoAcademico;
use App\Models\Inscripcion;
use App\Models\Horario;
use App\Models\PagoContado;
use App\Models\PagoCuota;
use App\Models\SeguimientoAcademico;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $term = mb_strtolower($q);
        $like = "%{$term}%";

        try {
            $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) ?? 'mysql';
        } catch (\Throwable $e) {
            $driver = 'mysql';
        }
        $useIlike = $driver === 'pgsql';

        $results = collect();

        // debug counters
        $debugCounts = [];

        // helper to compute url safely
        $makeUrl = function ($routeName, $indexRoute, $fallbackPath, $id = null) {
            if ($id !== null) {
                if (RouteFacade::has($routeName)) {
                    return route($routeName, $id);
                }
                if (RouteFacade::has($indexRoute)) {
                    return route($indexRoute) . '#' . ($indexRoute === $indexRoute ? strtolower(str_replace(['\\\/', ' '], ['', ''], $indexRoute)) . '-' . $id : '') . '#'.$id;
                }
                return url($fallbackPath) . '#'.$id;
            }
            if (RouteFacade::has($routeName)) return route($routeName);
            if (RouteFacade::has($indexRoute)) return route($indexRoute);
            return url($fallbackPath);
        };

        // PUBLIC search
        if (!$request->user()) {
            $carreras = Carrera::where('estado', true)
                ->where(function ($s) use ($q, $like, $useIlike) {
                    if ($useIlike) {
                        $s->whereRaw('nombre ILIKE ?', ["%{$q}%"]);
                    } else {
                        $s->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                    }
                    $s->orWhere('nombre', 'like', "%{$q}%");
                })->take(6)->get();

            foreach ($carreras as $c) {
                $url = RouteFacade::has('public.carreras.show') ? route('public.carreras.show', $c->id) : (RouteFacade::has('public.carreras.index') ? route('public.carreras.index') . '#carrera-' . $c->id : url('/carreras') . '#carrera-' . $c->id);
                $results->push(['type' => 'Carrera', 'id' => $c->id, 'title' => $c->nombre ?? 'Carrera', 'subtitle' => '', 'url' => $url]);
            }

            $ofertas = OfertaAcademica::where('estado', true)
                ->where(function ($s) use ($q, $like, $useIlike) {
                    if ($useIlike) {
                        $s->whereRaw('nombre ILIKE ?', ["%{$q}%"]);
                    } else {
                        $s->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                    }
                    $s->orWhere('nombre', 'like', "%{$q}%");
                })->take(6)->get();

            foreach ($ofertas as $o) {
                $url = RouteFacade::has('public.ofertas.show') ? route('public.ofertas.show', $o->id) : (RouteFacade::has('public.ofertas.index') ? route('public.ofertas.index') . '#oferta-' . $o->id : url('/ofertas-academicas') . '#oferta-' . $o->id);
                $results->push(['type' => 'Oferta', 'id' => $o->id, 'title' => $o->nombre ?? 'Oferta', 'subtitle' => $o->descripcion ? Str::limit($o->descripcion, 60) : '', 'url' => $url]);
            }

            $docentes = User::whereHas('role', function ($q) use ($useIlike) {
                if ($useIlike) {
                    $q->whereRaw('nombre ILIKE ?', ["%docente%"]);
                } else {
                    $q->whereRaw('LOWER(nombre) = ?', ['docente']);
                }
            })->where(function ($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('nombres ILIKE ?', ["%{$q}%"])->orWhereRaw('apellidos ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(nombres) LIKE ?', [$like])->orWhereRaw('LOWER(apellidos) LIKE ?', [$like]);
                }
                $s->orWhere('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
            })->take(6)->get();

            foreach ($docentes as $u) {
                $url = RouteFacade::has('public.docentes.show') ? route('public.docentes.show', $u->id) : (RouteFacade::has('public.docentes.index') ? route('public.docentes.index') . '#docente-' . $u->id : url('/docentes') . '#docente-' . $u->id);
                $results->push(['type' => 'Docente', 'id' => $u->id, 'title' => trim(($u->nombres ?? '') . ' ' . ($u->apellidos ?? '')) ?: 'Docente', 'subtitle' => $u->email ?? '', 'url' => $url]);
            }

            return response()->json(['results' => $results->values()]);
        }

        // AUTHENTICATED search
        $roleName = mb_strtolower($request->user()->role->nombre ?? '');

        if ($roleName === 'alumno') {
            return response()->json($this->searchForAlumno($request, $q, $like, $useIlike));
        }

        if ($roleName === 'docente') {
            return response()->json($this->searchForDocente($request, $q, $like, $useIlike));
        }

        $users = User::where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombres ILIKE ?', ["%{$q}%"])->orWhereRaw('apellidos ILIKE ?', ["%{$q}%"])->orWhereRaw('email ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombres) LIKE ?', [$like])->orWhereRaw('LOWER(apellidos) LIKE ?', [$like])->orWhereRaw('LOWER(email) LIKE ?', [$like]);
            }
            $s->orWhere('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($users as $u) {
            $results->push([
                'type' => 'Usuario',
                'id' => $u->id,
                'title' => trim(($u->nombres ?? '') . ' ' . ($u->apellidos ?? '')) ?: ($u->email ?? 'Usuario'),
                'subtitle' => $u->email ?? '',
                'url' => route('admin.usuarios.index', [
                    'highlight_user' => $u->id,
                ]),
            ]);
        }
        $debugCounts['users'] = $users->count();

        $materias = Materia::where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%")->orWhere('codigo', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($materias as $m) {
            $results->push(['type' => 'Materia', 'id' => $m->id, 'title' => $m->nombre ?? 'Materia', 'subtitle' => $m->codigo ?? '', 'url' => route('admin.materias.index', ['highlight_materia' => $m->id])]);
        }
        $debugCounts['materias'] = $materias->count();

        $carrerasAdmin = Carrera::where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($carrerasAdmin as $c) {
            $results->push(['type' => 'Carrera', 'id' => $c->id, 'title' => $c->nombre ?? 'Carrera', 'subtitle' => '', 'url' => route('admin.carreras.index', ['highlight_carrera' => $c->id])]);
        }
        $debugCounts['carreras'] = $carrerasAdmin->count();

        $ofertasAdmin = OfertaAcademica::where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($ofertasAdmin as $o) {
            $results->push(['type' => 'Oferta', 'id' => $o->id, 'title' => $o->nombre ?? 'Oferta', 'subtitle' => $o->descripcion ? Str::limit($o->descripcion, 60) : '', 'url' => route('admin.ofertas-academicas.index', ['highlight_oferta' => $o->id])]);
        }
        $debugCounts['ofertas'] = $ofertasAdmin->count();

        // also include docentes for authenticated users (same logic as public)
        $docentesAdmin = User::whereHas('role', function ($q) use ($useIlike) {
            if ($useIlike) {
                $q->whereRaw('nombre ILIKE ?', ["%docente%"]);
            } else {
                $q->whereRaw('LOWER(nombre) = ?', ['docente']);
            }
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombres ILIKE ?', ["%{$q}%"])->orWhereRaw('apellidos ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombres) LIKE ?', [$like])->orWhereRaw('LOWER(apellidos) LIKE ?', [$like]);
            }
            $s->orWhere('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($docentesAdmin as $u) {
            $url = RouteFacade::has('public.docentes.show') ? route('public.docentes.show', $u->id) : (RouteFacade::has('public.docentes.index') ? route('public.docentes.index') . '#docente-' . $u->id : url('/docentes') . '#docente-' . $u->id);
            $results->push(['type' => 'Docente', 'id' => $u->id, 'title' => trim(($u->nombres ?? '') . ' ' . ($u->apellidos ?? '')) ?: 'Docente', 'subtitle' => $u->email ?? '', 'url' => $url]);
        }
        $debugCounts['docentes'] = $docentesAdmin->count();

        // propietario-specific
        $isPropietario = mb_strtolower($request->user()->role->nombre ?? '') === 'propietario';
        if ($isPropietario) {
            $aulas = Aula::where(function ($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"])->orWhereRaw('ubicacion ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like])->orWhereRaw('LOWER(ubicacion) LIKE ?', [$like]);
                }
                $s->orWhere('nombre', 'like', "%{$q}%")->orWhere('codigo', 'like', "%{$q}%")->orWhere('ubicacion', 'like', "%{$q}%");
            })->take(6)->get();

            foreach ($aulas as $a) {
                $title = trim(($a->codigo ?? '') . ' ' . ($a->nombre ?? '')) ?: 'Aula';
                $url = RouteFacade::has('admin.aulas.index') ? route('admin.aulas.index', ['highlight_aula' => $a->id]) : url('/admin/aulas?highlight_aula=' . $a->id);
                $results->push(['type'=>'Aula','id'=>$a->id,'title'=>$title,'subtitle'=>$a->ubicacion ?? '','url'=>$url]);
            }
            $debugCounts['aulas'] = $aulas->count();

            $periodos = PeriodoAcademico::where(function ($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('CAST(gestion AS TEXT) ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(gestion) LIKE ?', [$like]);
                }
                $s->orWhere('nombre', 'like', "%{$q}%")->orWhere('gestion', 'like', "%{$q}%");
            })->take(6)->get();

            foreach ($periodos as $p) {
                $url = RouteFacade::has('admin.periodos-academicos.index') ? route('admin.periodos-academicos.index', ['highlight_periodo' => $p->id]) : url('/admin/periodos-academicos?highlight_periodo=' . $p->id);
                $results->push(['type'=>'Periodo','id'=>$p->id,'title'=>$p->nombre ?? 'Periodo','subtitle'=>$p->gestion ?? '','url'=>$url]);
            }
            $debugCounts['periodos'] = $periodos->count();

            $inscripciones = Inscripcion::where(function($s) use ($q, $like, $driver) {
                $s->whereRaw('LOWER(observacion) LIKE ?', [$like]);
                if (ctype_digit($q)) {
                    $s->orWhere('id', (int)$q);
                } else {
                    if ($driver === 'pgsql') {
                        $s->orWhereRaw('CAST(id AS TEXT) like ?', [$like]);
                    } else {
                        $s->orWhereRaw('CAST(id AS CHAR) like ?', [$like]);
                    }
                }
            })->take(6)->get();

            foreach ($inscripciones as $i) {
                $alumno = $i->alumnoDetalle?->user ? trim(($i->alumnoDetalle->user->nombres ?? '') . ' ' . ($i->alumnoDetalle->user->apellidos ?? '')) : '';
                $url = RouteFacade::has('admin.inscripciones.index') ? route('admin.inscripciones.index', ['highlight_inscripcion' => $i->id]) : url('/admin/inscripciones?highlight_inscripcion=' . $i->id);
                $results->push(['type'=>'Inscripción','id'=>$i->id,'title'=>'Inscripción #'.$i->id,'subtitle'=>$alumno?:($i->fecha_inscripcion ?? ''),'url'=>$url]);
            }
            $debugCounts['inscripciones'] = $inscripciones->count();

            $horarios = Horario::where(function ($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('dia ILIKE ?', ["%{$q}%"])->orWhereRaw('turno ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(dia) LIKE ?', [$like])->orWhereRaw('LOWER(turno) LIKE ?', [$like]);
                }
                $s->orWhere('dia','like',"%{$q}%")->orWhere('turno','like',"%{$q}%");
            })->take(6)->get();

            foreach ($horarios as $h) {
                $title = 'Horario #'.$h->id;
                $subtitle = ($h->dia ? $h->dia.' ' : '') . ($h->hora_inicio ? $h->hora_inicio : '');
                $url = RouteFacade::has('admin.horarios.index') ? route('admin.horarios.index', ['highlight_horario' => $h->id]) : url('/admin/horarios?highlight_horario=' . $h->id);
                $results->push(['type'=>'Horario','id'=>$h->id,'title'=>$title,'subtitle'=>$subtitle,'url'=>$url]);
            }
            $debugCounts['horarios'] = $horarios->count();

            $pagosContado = PagoContado::where(function($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('codigo_transaccion ILIKE ?', ["%{$q}%"])->orWhereRaw('correo_solicitante ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(codigo_transaccion) LIKE ?', [$like])->orWhereRaw('LOWER(correo_solicitante) LIKE ?', [$like]);
                }
                $s->orWhere('codigo_transaccion','like',"%{$q}%")->orWhere('correo_solicitante','like',"%{$q}%");
            })->take(6)->get();

            foreach ($pagosContado as $p) {
                $url = RouteFacade::has('admin.pago-contados.index') ? route('admin.pago-contados.index', ['highlight_pago_contado' => $p->id]) : url('/admin/pago-contados?highlight_pago_contado=' . $p->id);
                $results->push(['type'=>'PagoContado','id'=>$p->id,'title'=>$p->codigo_transaccion ?? ('Pago #'.$p->id),'subtitle'=>$p->correo_solicitante ?? '','url'=>$url]);
            }
            $debugCounts['pagos_contado'] = $pagosContado->count();

            $pagosCuota = PagoCuota::where(function($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('codigo_transaccion ILIKE ?', ["%{$q}%"])->orWhereRaw('correo_solicitante ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(codigo_transaccion) LIKE ?', [$like])->orWhereRaw('LOWER(correo_solicitante) LIKE ?', [$like]);
                }
                $s->orWhere('codigo_transaccion','like',"%{$q}%")->orWhere('correo_solicitante','like',"%{$q}%");
            })->take(6)->get();

            foreach ($pagosCuota as $p) {
                $url = RouteFacade::has('admin.pago-cuotas.index') ? route('admin.pago-cuotas.index', ['highlight_pago_cuota' => $p->id]) : url('/admin/pago-cuotas?highlight_pago_cuota=' . $p->id);
                $results->push(['type'=>'PagoCuota','id'=>$p->id,'title'=>$p->codigo_transaccion ?? ('Pago cuota #'.$p->id),'subtitle'=>$p->correo_solicitante ?? '','url'=>$url]);
            }
            $debugCounts['pagos_cuota'] = $pagosCuota->count();

            $seguimientos = SeguimientoAcademico::where(function($s) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $s->whereRaw('observacion ILIKE ?', ["%{$q}%"]);
                } else {
                    $s->whereRaw('LOWER(observacion) LIKE ?', [$like]);
                }
                $s->orWhere('observacion','like',"%{$q}%");
            })->take(6)->get();

            foreach ($seguimientos as $sg) {
                $url = RouteFacade::has('admin.seguimientos-academicos.index') ? route('admin.seguimientos-academicos.index', ['highlight_seguimiento' => $sg->id]) : url('/admin/seguimientos-academicos?highlight_seguimiento=' . $sg->id);
                $results->push(['type'=>'Seguimiento','id'=>$sg->id,'title'=>'Seguimiento #'.$sg->id,'subtitle'=>$sg->estado_academico ?? '','url'=>$url]);
            }
            $debugCounts['seguimientos'] = $seguimientos->count();
        }

        $payload = ['results' => $results->values()];
        if (config('app.debug')) {
            $payload['debug'] = $debugCounts;
        }

        return response()->json($payload);
    }

    private function searchForAlumno(Request $request, string $q, string $like, bool $useIlike): array
    {
        $userId = $request->user()?->id;
        $results = collect();
        $debugCounts = [];

        if (! $userId) {
            return ['results' => []];
        }

        $carreras = Carrera::whereHas('ofertasAcademicas.inscripciones.alumnoDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%")->orWhere('codigo', 'like', "%{$q}%");
        })->with([
            'ofertasAcademicas' => function ($s) use ($userId) {
                $s->whereHas('inscripciones.alumnoDetalle', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->with('periodoAcademico');
            },
        ])->take(6)->get();

        foreach ($carreras as $carrera) {
            $oferta = $carrera->ofertasAcademicas->first();
            $results->push([
                'type' => 'Carrera inscrita',
                'id' => $carrera->id,
                'title' => $carrera->nombre ?? 'Carrera',
                'subtitle' => $oferta
                    ? trim(($oferta->nombre ?? '') . ' ' . ($oferta->periodoAcademico?->nombre ? '· ' . $oferta->periodoAcademico->nombre : ''))
                    : '',
                'url' => route('alumno.carreras.inscritas'),
            ]);
        }
        $debugCounts['carreras'] = $carreras->count();

        $materias = \App\Models\InscripcionMateria::whereHas('inscripcion.alumnoDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereHas('carreraMateria.materia', function ($mq) use ($q) {
                    $mq->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
                })->orWhereHas('carreraMateria.carrera', function ($cq) use ($q) {
                    $cq->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
                });
            } else {
                $s->whereHas('carreraMateria.materia', function ($mq) use ($like) {
                    $mq->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
                })->orWhereHas('carreraMateria.carrera', function ($cq) use ($like) {
                    $cq->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
                });
            }
        })->with(['carreraMateria.carrera', 'carreraMateria.materia'])->take(6)->get();

        foreach ($materias as $inscripcionMateria) {
            $materia = $inscripcionMateria->carreraMateria?->materia;
            $carrera = $inscripcionMateria->carreraMateria?->carrera;
            $results->push([
                'type' => 'Materia inscrita',
                'id' => $inscripcionMateria->id,
                'title' => $materia?->nombre ?? 'Materia',
                'subtitle' => trim(($materia?->codigo ?? '') . ' ' . ($carrera?->nombre ? '· ' . $carrera->nombre : '')),
                'url' => route('alumno.materias.inscritas'),
            ]);
        }
        $debugCounts['materias'] = $materias->count();

        $horarios = Horario::whereHas('carreraMateria.carrera.ofertasAcademicas.inscripciones.alumnoDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('dia ILIKE ?', ["%{$q}%"])->orWhereRaw('turno ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(dia) LIKE ?', [$like])->orWhereRaw('LOWER(turno) LIKE ?', [$like]);
            }

            $s->orWhereHas('carreraMateria.materia', function ($mq) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $mq->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
                } else {
                    $mq->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
                }
            })->orWhereHas('carreraMateria.carrera', function ($cq) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $cq->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
                } else {
                    $cq->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
                }
            });
        })->with(['carreraMateria.carrera', 'carreraMateria.materia'])->take(6)->get();

        foreach ($horarios as $horario) {
            $materia = $horario->carreraMateria?->materia;
            $carrera = $horario->carreraMateria?->carrera;
            $results->push([
                'type' => 'Horario',
                'id' => $horario->id,
                'title' => trim(($horario->dia ?? 'Horario') . ' ' . ($horario->turno ?? '')),
                'subtitle' => trim(($materia?->nombre ?? '') . ' · ' . ($carrera?->nombre ?? '')),
                'url' => route('alumno.horario'),
            ]);
        }
        $debugCounts['horarios'] = $horarios->count();

        $pagos = PagoContado::whereHas('inscripcion.alumnoDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('codigo_transaccion ILIKE ?', ["%{$q}%"])->orWhereRaw('correo_solicitante ILIKE ?', ["%{$q}%"])->orWhereRaw('observacion ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(codigo_transaccion) LIKE ?', [$like])->orWhereRaw('LOWER(correo_solicitante) LIKE ?', [$like])->orWhereRaw('LOWER(observacion) LIKE ?', [$like]);
            }
            $s->orWhere('codigo_transaccion', 'like', "%{$q}%")->orWhere('correo_solicitante', 'like', "%{$q}%");
        })->with(['inscripcion.ofertaAcademica.carrera'])->take(6)->get();

        foreach ($pagos as $pago) {
            $carrera = $pago->inscripcion?->ofertaAcademica?->carrera;
            $results->push([
                'type' => 'Mis pagos',
                'id' => $pago->id,
                'title' => $pago->codigo_transaccion ?? ('Pago #' . $pago->id),
                'subtitle' => $carrera?->nombre ?? '',
                'url' => route('alumno.mis-pagos'),
            ]);
        }
        $debugCounts['pagos'] = $pagos->count();

        $creditos = PagoCuota::whereHas('credito.inscripcion.alumnoDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('codigo_transaccion ILIKE ?', ["%{$q}%"])->orWhereRaw('correo_solicitante ILIKE ?', ["%{$q}%"])->orWhereRaw('observacion ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(codigo_transaccion) LIKE ?', [$like])->orWhereRaw('LOWER(correo_solicitante) LIKE ?', [$like])->orWhereRaw('LOWER(observacion) LIKE ?', [$like]);
            }
            $s->orWhere('codigo_transaccion', 'like', "%{$q}%")->orWhere('correo_solicitante', 'like', "%{$q}%");
        })->with(['credito.inscripcion.ofertaAcademica.carrera'])->take(6)->get();

        foreach ($creditos as $cuota) {
            $carrera = $cuota->credito?->inscripcion?->ofertaAcademica?->carrera;
            $results->push([
                'type' => 'Mis créditos',
                'id' => $cuota->id,
                'title' => $cuota->codigo_transaccion ?? ('Cuota #' . $cuota->id),
                'subtitle' => $carrera?->nombre ?? '',
                'url' => route('alumno.mis-creditos'),
            ]);
        }
        $debugCounts['creditos'] = $creditos->count();

        $ofertas = OfertaAcademica::whereHas('inscripciones.alumnoDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%");
        })->with(['carrera', 'periodoAcademico'])->take(6)->get();

        foreach ($ofertas as $oferta) {
            $results->push([
                'type' => 'Oferta',
                'id' => $oferta->id,
                'title' => $oferta->nombre ?? 'Oferta',
                'subtitle' => trim(($oferta->carrera?->nombre ?? '') . ' · ' . ($oferta->periodoAcademico?->nombre ?? '')),
                'url' => route('alumno.ofertas.index'),
            ]);
        }
        $debugCounts['ofertas'] = $ofertas->count();

        $payload = ['results' => $results->values()];

        if (config('app.debug')) {
            $payload['debug'] = $debugCounts;
        }

        return $payload;
    }

    private function searchForDocente(Request $request, string $q, string $like, bool $useIlike): array
    {
        $userId = $request->user()?->id;
        $results = collect();
        $debugCounts = [];

        if (! $userId) {
            return ['results' => []];
        }

        $carreras = Carrera::whereHas('carreraMaterias.horarios.docenteDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"])->orWhereRaw('regimen_academico ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like])->orWhereRaw('LOWER(regimen_academico) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%")->orWhere('codigo', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($carreras as $carrera) {
            $results->push([
                'type' => 'Carrera',
                'id' => $carrera->id,
                'title' => $carrera->nombre ?? 'Carrera',
                'subtitle' => $carrera->codigo ?? '',
                'url' => route('docente.carreras'),
            ]);
        }
        $debugCounts['carreras'] = $carreras->count();

        $materias = Materia::whereHas('docenteDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
            }
            $s->orWhere('nombre', 'like', "%{$q}%")->orWhere('codigo', 'like', "%{$q}%");
        })->take(6)->get();

        foreach ($materias as $materia) {
            $results->push([
                'type' => 'Materia',
                'id' => $materia->id,
                'title' => $materia->nombre ?? 'Materia',
                'subtitle' => $materia->codigo ?? '',
                'url' => route('docente.materias.show', $materia->id),
            ]);
        }
        $debugCounts['materias'] = $materias->count();

        $horarios = Horario::whereHas('docenteDetalle', function ($s) use ($userId) {
            $s->where('user_id', $userId);
        })->where(function ($s) use ($q, $like, $useIlike) {
            if ($useIlike) {
                $s->whereRaw('dia ILIKE ?', ["%{$q}%"])->orWhereRaw('turno ILIKE ?', ["%{$q}%"]);
            } else {
                $s->whereRaw('LOWER(dia) LIKE ?', [$like])->orWhereRaw('LOWER(turno) LIKE ?', [$like]);
            }

            $s->orWhereHas('carreraMateria.materia', function ($mq) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $mq->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
                } else {
                    $mq->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
                }
            })->orWhereHas('carreraMateria.carrera', function ($cq) use ($q, $like, $useIlike) {
                if ($useIlike) {
                    $cq->whereRaw('nombre ILIKE ?', ["%{$q}%"])->orWhereRaw('codigo ILIKE ?', ["%{$q}%"]);
                } else {
                    $cq->whereRaw('LOWER(nombre) LIKE ?', [$like])->orWhereRaw('LOWER(codigo) LIKE ?', [$like]);
                }
            });
        })->with(['carreraMateria.carrera', 'carreraMateria.materia'])->take(6)->get();

        foreach ($horarios as $horario) {
            $materia = $horario->carreraMateria?->materia;
            $carrera = $horario->carreraMateria?->carrera;
            $results->push([
                'type' => 'Horario',
                'id' => $horario->id,
                'title' => trim(($horario->dia ?? 'Horario') . ' ' . ($horario->turno ?? '')),
                'subtitle' => trim(($materia?->nombre ?? '') . ' · ' . ($carrera?->nombre ?? '')),
                'url' => route('docente.horarios'),
            ]);
        }
        $debugCounts['horarios'] = $horarios->count();

        $payload = ['results' => $results->values()];

        if (config('app.debug')) {
            $payload['debug'] = $debugCounts;
        }

        return $payload;
    }
}
