<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\CarreraMateriaController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\PeriodoAcademicoController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\OfertaAcademicaController;
use App\Http\Controllers\Docente\DocenteHomeController;
use App\Http\Controllers\Docente\DocenteCarrerasController;
use App\Http\Controllers\Docente\DocenteMateriasController;
use App\Http\Controllers\Alumno\AlumnoHomeController;
use App\Http\Controllers\Alumno\AlumnoCarrerasInscritasController;
use App\Http\Controllers\Alumno\AlumnoMateriasInscritasController;
use App\Http\Controllers\Alumno\AlumnoPagoContadoController;
use App\Http\Controllers\Alumno\AlumnoCreditosController;
use App\Http\Controllers\Admin\InscripcionController;
use App\Http\Controllers\Admin\InscripcionMateriaController;
use App\Http\Controllers\Admin\SeguimientoAcademicoController;
use App\Http\Controllers\Admin\ConceptoPagoController;
use App\Http\Controllers\Admin\PagoContadoController;
use App\Http\Controllers\Api\PagoFacilCallbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use App\Http\Controllers\Admin\CreditoController;
use App\Http\Controllers\Admin\PagoCuotaController;
use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\PublicPageController;

use App\Http\Controllers\Alumno\AlumnoOfertaAcademicaController;
use App\Http\Controllers\Alumno\AlumnoInscripcionController;
use App\Http\Controllers\PageVisitController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/page-visits', [PageVisitController::class, 'store'])
        ->name('page-visits.store');

    Route::get('/perfil', [ProfileController::class, 'show'])
        ->name('perfil.show');

    Route::put('/perfil', [ProfileController::class, 'update'])
        ->name('perfil.update');

    // Global settings for all authenticated users
    Route::get('/configuraciones', [\App\Http\Controllers\SettingsController::class, 'show'])
        ->name('configuraciones.show');

    Route::put('/configuraciones', [\App\Http\Controllers\SettingsController::class, 'update'])
        ->name('configuraciones.update');
});

Route::middleware(['auth', 'role:propietario,secretaria'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('usuarios', UsuarioController::class);
        Route::resource('aulas', AulaController::class);

        Route::resource('carreras', CarreraController::class);

        Route::post('/carreras/{carrera}/materias', [CarreraMateriaController::class, 'storeDesdeModal'])
            ->name('carreras.materias.store');

        Route::put('/carreras/{carrera}/materias/{asignacion}', [CarreraMateriaController::class, 'updateDesdeModal'])
            ->name('carreras.materias.update');

        Route::delete('/carreras/{carrera}/materias/{asignacion}', [CarreraMateriaController::class, 'destroyDesdeModal'])
            ->name('carreras.materias.destroy');

        Route::resource('materias', MateriaController::class);
        Route::resource('periodos-academicos', PeriodoAcademicoController::class);
        Route::resource('horarios', HorarioController::class);
        Route::resource('ofertas-academicas', OfertaAcademicaController::class);
        Route::resource('inscripciones', InscripcionController::class)
            ->parameters([
                'inscripciones' => 'inscripcion',
            ]);

        Route::get('/inscripciones/{inscripcion}/materias', [InscripcionMateriaController::class, 'index'])
            ->name('inscripciones.materias.index');

        Route::get('/inscripciones/{inscripcion}/materias/create', [InscripcionMateriaController::class, 'create'])
            ->name('inscripciones.materias.create');

        Route::post('/inscripciones/{inscripcion}/materias', [InscripcionMateriaController::class, 'store'])
            ->name('inscripciones.materias.store');

        Route::get('/inscripciones/{inscripcion}/materias/{inscripcionMateria}/edit', [InscripcionMateriaController::class, 'edit'])
            ->name('inscripciones.materias.edit');

        Route::put('/inscripciones/{inscripcion}/materias/{inscripcionMateria}', [InscripcionMateriaController::class, 'update'])
            ->name('inscripciones.materias.update');

        Route::delete('/inscripciones/{inscripcion}/materias/{inscripcionMateria}', [InscripcionMateriaController::class, 'destroy'])
            ->name('inscripciones.materias.destroy');

        Route::resource('seguimientos-academicos', SeguimientoAcademicoController::class)
            ->parameters([
                'seguimientos-academicos' => 'seguimiento',
            ]);

        Route::resource('concepto-pagos', ConceptoPagoController::class)
            ->parameters([
                'concepto-pagos' => 'concepto_pago',
            ]);

        Route::resource('pago-contados', PagoContadoController::class)
            ->parameters([
                'pago-contados' => 'pago_contado',
            ]);

        Route::post('/pago-contados/{pago_contado}/consultar', [PagoContadoController::class, 'consultar'])
            ->name('pago-contados.consultar');

        Route::get('/pago-contados/{pago_contado}/estado', [PagoContadoController::class, 'estado'])
            ->name('pago-contados.estado');

        Route::post('/pago-contados/{pago_contado}/consultar-json', [PagoContadoController::class, 'consultarJson'])
            ->name('pago-contados.consultar-json');

        Route::resource('creditos', CreditoController::class);

        Route::resource('pago-cuotas', PagoCuotaController::class)
            ->only(['index', 'show', 'edit', 'update'])
            ->parameters([
                'pago-cuotas' => 'pago_cuota',
            ]);

        Route::get('/creditos/{credito}/cuotas', [PagoCuotaController::class, 'cuotasPorCredito'])
            ->name('creditos.cuotas.index');

        Route::get('/pago-cuotas/{pago_cuota}/estado', [PagoCuotaController::class, 'estado'])
            ->name('pago-cuotas.estado');

        Route::post('/pago-cuotas/{pago_cuota}/consultar-json', [PagoCuotaController::class, 'consultarJson'])
            ->name('pago-cuotas.consultar-json');

        Route::get('/reportes', [ReporteController::class, 'index'])
            ->name('reportes.index');

        Route::get('/bitacora', [BitacoraController::class, 'index'])
            ->name('bitacora.index');

        Route::get('/reportes/inscripciones/pdf', [ReporteController::class, 'inscripcionesPdf'])
            ->name('reportes.inscripciones.pdf');

        Route::get('/reportes/inscripciones/excel', [ReporteController::class, 'inscripcionesExcel'])
            ->name('reportes.inscripciones.excel');

        Route::get('/reportes/pagos/pdf', [ReporteController::class, 'pagosPdf'])
            ->name('reportes.pagos.pdf');

        Route::get('/reportes/pagos/excel', [ReporteController::class, 'pagosExcel'])
            ->name('reportes.pagos.excel');

        Route::get('/reportes/creditos/pdf', [ReporteController::class, 'creditosPdf'])
            ->name('reportes.creditos.pdf');

        Route::get('/reportes/creditos/excel', [ReporteController::class, 'creditosExcel'])
            ->name('reportes.creditos.excel');
});

Route::middleware(['auth', 'role:docente'])->group(function () {
    Route::get('/docente/inicio', [DocenteHomeController::class, 'index'])
        ->name('docente.home');

    Route::get('/docente/carreras', [DocenteCarrerasController::class, 'index'])
        ->name('docente.carreras');

    Route::get('/docente/materias', [DocenteMateriasController::class, 'index'])
        ->name('docente.materias');

    Route::get('/docente/horarios', [DocenteMateriasController::class, 'horarios'])
        ->name('docente.horarios');

    Route::get('/docente/materias/{materia}', [DocenteMateriasController::class, 'show'])
        ->name('docente.materias.show');

    Route::get('/docente/materias/{materia}/estudiantes', [DocenteMateriasController::class, 'estudiantesSearch'])
        ->name('docente.materias.estudiantes-search');

    Route::post('/docente/materias/{materia}/seguimientos', [DocenteMateriasController::class, 'storeSeguimiento'])
        ->name('docente.materias.seguimientos.store');

    Route::put('/docente/materias/{materia}/seguimientos/{seguimiento}', [DocenteMateriasController::class, 'updateSeguimiento'])
        ->name('docente.materias.seguimientos.update');
});

Route::middleware(['auth', 'role:alumno'])
    ->prefix('alumno')
    ->name('alumno.')
    ->group(function () {
        Route::get('/inicio', [AlumnoHomeController::class, 'index'])
            ->name('home');

        Route::get('/carreras-inscritas', [AlumnoCarrerasInscritasController::class, 'index'])
            ->name('carreras.inscritas');

        Route::get('/materias-inscritas', [AlumnoMateriasInscritasController::class, 'index'])
            ->name('materias.inscritas');

        Route::get('/materias-inscritas/{inscripcionMateria}/seguimiento', [AlumnoMateriasInscritasController::class, 'showSeguimiento'])
            ->name('materias.seguimiento');

        Route::post('/notificaciones/{notification}/marcar-leida', [NotificationController::class, 'markAsRead'])
            ->name('notificaciones.marcar-leida');

        Route::get('/horario', [AlumnoMateriasInscritasController::class, 'horario'])
            ->name('horario');

        Route::get('/mis-pagos', [AlumnoPagoContadoController::class, 'index'])
            ->name('mis-pagos');

        Route::post('/mis-pagos', [AlumnoPagoContadoController::class, 'store'])
            ->name('mis-pagos.store');

        Route::post('/mis-pagos/generar-qr', [AlumnoPagoContadoController::class, 'generarQr'])
            ->name('mis-pagos.generar-qr');

        Route::get('/mis-pagos/{pago_contado}/estado', [AlumnoPagoContadoController::class, 'estado'])
            ->name('mis-pagos.estado');

        Route::post('/mis-pagos/{pago_contado}/consultar-json', [AlumnoPagoContadoController::class, 'consultarJson'])
            ->name('mis-pagos.consultar-json');

        Route::get('/mis-creditos', [AlumnoCreditosController::class, 'index'])
            ->name('mis-creditos');

        Route::get('/mis-creditos/{credito}/cuotas', [AlumnoCreditosController::class, 'cuotas'])
            ->name('mis-creditos.cuotas');

        Route::get('/mis-creditos/cuotas/{pago_cuota}', [AlumnoCreditosController::class, 'showCuota'])
            ->name('mis-creditos.cuotas.show');

        Route::post('/mis-creditos/cuotas/{pago_cuota}/pagar', [AlumnoCreditosController::class, 'pagarCuota'])
            ->name('mis-creditos.cuotas.pagar');

        Route::post('/mis-creditos/cuotas/{pago_cuota}/generar-qr', [AlumnoCreditosController::class, 'generarQrCuota'])
            ->name('mis-creditos.cuotas.generar-qr');

        Route::get('/mis-creditos/cuotas/{pago_cuota}/estado', [AlumnoCreditosController::class, 'estadoCuota'])
            ->name('mis-creditos.cuotas.estado');

        Route::post('/mis-creditos/cuotas/{pago_cuota}/consultar-json', [AlumnoCreditosController::class, 'consultarCuotaJson'])
            ->name('mis-creditos.cuotas.consultar-json');

        Route::get('/mis-creditos/{credito}', [AlumnoCreditosController::class, 'show'])
            ->name('mis-creditos.show');

        Route::get(
            '/ofertas-academicas',
            [AlumnoOfertaAcademicaController::class, 'index']
        )->name('ofertas.index');
        Route::post(
            '/inscripciones',
            [AlumnoInscripcionController::class, 'store']
        )->name('inscripciones.store');
});

Route::post('/pagofacil/callback', PagoFacilCallbackController::class)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('pagofacil.callback.web');

Route::get('/', [PublicPageController::class, 'inicio'])
    ->name('welcome');

Route::get('/carreras', [PublicPageController::class, 'carreras'])
    ->name('public.carreras.index');

Route::get('/ofertas-academicas', [PublicPageController::class, 'ofertasAcademicas'])
    ->name('public.ofertas.index');

Route::get('/docentes', [PublicPageController::class, 'docentes'])
    ->name('public.docentes.index');

Route::get('/ofertas-academicas/{oferta}/inscribirse', [PublicPageController::class, 'inscribirse'])
    ->name('public.ofertas.inscribirse');