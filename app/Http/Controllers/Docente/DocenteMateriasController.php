<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\InscripcionMateria;
use App\Models\SeguimientoAcademico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DocenteMateriasController extends Controller
{
    public function index(): Response
    {
        $usuario = auth()->user();
        $docenteDetalle = $usuario->docenteDetalle;
        
        $materias = $docenteDetalle ? $docenteDetalle->materias()->with('carreraMaterias.carrera')->get() : collect();
        
        return Inertia::render('docente/materias', [
            'materias' => $materias->map(fn($materia) => [
                'id' => $materia->id,
                'codigo' => $materia->codigo,
                'nombre' => $materia->nombre,
                'carga_horaria' => $materia->carga_horaria,
                'estado' => $materia->estado,
                'carreras' => $materia->carreraMaterias->map(fn($cm) => $cm->carrera->nombre)->unique()->values(),
            ]),
        ]);
    }

    public function show(Materia $materia): Response
    {
        // Verificar que el docente sea dueño de esta materia
        $usuario = auth()->user();
        if ($materia->docente_detalle_id !== $usuario->docenteDetalle?->id) {
            abort(403, 'No autorizado para acceder a esta materia');
        }

        return Inertia::render('docente/materias/show', [
            'materia' => [
                'id' => $materia->id,
                'codigo' => $materia->codigo,
                'nombre' => $materia->nombre,
                'carga_horaria' => $materia->carga_horaria,
                'estado' => $materia->estado,
            ],
        ]);
    }

    public function storeSeguimiento(Materia $materia, Request $request): JsonResponse
    {
        $usuario = auth()->user();
        if ($materia->docente_detalle_id !== $usuario->docenteDetalle?->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'inscripcion_materia_id' => [
                'required',
                'exists:inscripcion_materias,id',
                Rule::unique('seguimientos_academicos', 'inscripcion_materia_id'),
            ],
            'nota_final' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'porcentaje_asistencia' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observacion' => ['nullable', 'string', 'max:1000'],
            'estado_academico' => ['required', Rule::in(['Cursando', 'Aprobado', 'Reprobado', 'Retirado'])],
            'fecha_registro' => ['required', 'date'],
        ]);

        $inscripcionMateria = InscripcionMateria::with('carreraMateria')->findOrFail($validated['inscripcion_materia_id']);
        if ($inscripcionMateria->carreraMateria->materia_id !== $materia->id) {
            return response()->json(['message' => 'La inscripción no corresponde a esta materia'], 403);
        }

        $seguimiento = SeguimientoAcademico::create([
            'inscripcion_materia_id' => $validated['inscripcion_materia_id'],
            'docente_detalle_id' => $usuario->docenteDetalle->id,
            'nota_final' => $validated['nota_final'] ?? null,
            'porcentaje_asistencia' => $validated['porcentaje_asistencia'] ?? null,
            'observacion' => $validated['observacion'] ?? null,
            'estado_academico' => $validated['estado_academico'],
            'fecha_registro' => $validated['fecha_registro'],
        ]);

        return response()->json([
            'message' => 'Registro del seguimiento exitoso',
            'seguimiento' => $seguimiento,
        ], 201);
    }

    public function updateSeguimiento(Materia $materia, SeguimientoAcademico $seguimiento, Request $request): JsonResponse
    {
        $usuario = auth()->user();
        if ($materia->docente_detalle_id !== $usuario->docenteDetalle?->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($seguimiento->inscripcionMateria->carreraMateria->materia_id !== $materia->id) {
            return response()->json(['message' => 'Este seguimiento no pertenece a la materia seleccionada'], 403);
        }

        $validated = $request->validate([
            'inscripcion_materia_id' => [
                'required',
                'exists:inscripcion_materias,id',
                Rule::unique('seguimientos_academicos', 'inscripcion_materia_id')->ignore($seguimiento->id),
            ],
            'nota_final' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'porcentaje_asistencia' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observacion' => ['nullable', 'string', 'max:1000'],
            'estado_academico' => ['required', Rule::in(['Cursando', 'Aprobado', 'Reprobado', 'Retirado'])],
            'fecha_registro' => ['required', 'date'],
        ]);

        $seguimiento->update([
            'inscripcion_materia_id' => $validated['inscripcion_materia_id'],
            'nota_final' => $validated['nota_final'] ?? null,
            'porcentaje_asistencia' => $validated['porcentaje_asistencia'] ?? null,
            'observacion' => $validated['observacion'] ?? null,
            'estado_academico' => $validated['estado_academico'],
            'fecha_registro' => $validated['fecha_registro'],
        ]);

        return response()->json([
            'message' => 'Actualización del seguimiento exitosa',
            'seguimiento' => $seguimiento,
        ]);
    }

    public function estudiantesSearch(Materia $materia, Request $request): JsonResponse
    {
        // Verificar que el docente sea dueño de esta materia
        $usuario = auth()->user();
        if ($materia->docente_detalle_id !== $usuario->docenteDetalle?->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $busqueda = $request->get('q', '');

        $docenteNombre = $usuario->nombreCompleto();

        $estudiantes = InscripcionMateria::query()
            ->whereHas('carreraMateria', fn($q) => $q->where('materia_id', $materia->id))
            ->with([
                'inscripcion.alumnoDetalle.user',
                'seguimientoAcademico',
            ])
            ->when($busqueda, fn($q) => $q->whereHas('inscripcion.alumnoDetalle.user', fn($q2) => 
                $q2->where('nombres', 'ilike', "%{$busqueda}%")
                   ->orWhere('apellidos', 'ilike', "%{$busqueda}%")
                   ->orWhere('email', 'ilike', "%{$busqueda}%")
            ))
            ->get()
            ->map(fn($inscripcionMateria) => [
                'id' => $inscripcionMateria->id,
                'estudiante_id' => $inscripcionMateria->inscripcion->alumnoDetalle->id,
                'nombre' => $inscripcionMateria->inscripcion->alumnoDetalle->user->nombres,
                'apellido' => $inscripcionMateria->inscripcion->alumnoDetalle->user->apellidos,
                'email' => $inscripcionMateria->inscripcion->alumnoDetalle->user->email,
                'ci' => $inscripcionMateria->inscripcion->alumnoDetalle->user->ci,
                'telefono' => $inscripcionMateria->inscripcion->alumnoDetalle->user->telefono,
                'direccion' => $inscripcionMateria->inscripcion->alumnoDetalle->user->direccion,
                'codigo' => $inscripcionMateria->inscripcion->alumnoDetalle->codigo,
                'colegio_origen' => $inscripcionMateria->inscripcion->alumnoDetalle->colegio_origen,
                'anio_bachillerato' => $inscripcionMateria->inscripcion->alumnoDetalle->anio_bachillerato,
                'estado_academico' => $inscripcionMateria->inscripcion->alumnoDetalle->estado_academico,
                'foto_url' => $inscripcionMateria->inscripcion->alumnoDetalle->user->foto_url,
                'materia_nombre' => $materia->nombre,
                'docente_nombre' => $docenteNombre,
                'seguimiento' => $inscripcionMateria->seguimientoAcademico ? [
                    'id' => $inscripcionMateria->seguimientoAcademico->id,
                    'nota_final' => $inscripcionMateria->seguimientoAcademico->nota_final,
                    'porcentaje_asistencia' => $inscripcionMateria->seguimientoAcademico->porcentaje_asistencia,
                    'estado_academico' => $inscripcionMateria->seguimientoAcademico->estado_academico,
                    'observacion' => $inscripcionMateria->seguimientoAcademico->observacion,
                    'fecha_registro' => $inscripcionMateria->seguimientoAcademico->fecha_registro?->format('Y-m-d'),
                ] : null,
            ]);

        return response()->json($estudiantes);
    }
}
