@extends('layouts.admin')

@section('title', 'Bitácora y Control de Acceso')
@section('page-title', 'Bitácora y Control de Acceso')
@section('page-subtitle', 'Registros de inicio de sesión y matriz de permisos del sistema')

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1.4fr_0.8fr]">
        <div class="space-y-5">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Control de Acceso</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-900">Matriz de roles y permisos</h2>
                        <p class="mt-2 text-sm text-slate-500">Visualiza qué roles tienen acceso a cada módulo del sistema.</p>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.18em] text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Recurso</th>
                            <th class="whitespace-nowrap px-4 py-3">Propietario</th>
                            <th class="whitespace-nowrap px-4 py-3">Secretaria</th>
                            <th class="whitespace-nowrap px-4 py-3">Docente</th>
                            <th class="whitespace-nowrap px-4 py-3">Alumno</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($controlDeAcceso as $recurso => $roles)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-900">{{ $recurso }}</td>
                                <td class="whitespace-nowrap px-4 py-4">@if($roles['propietario'])<span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Sí</span>@else<span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">No</span>@endif</td>
                                <td class="whitespace-nowrap px-4 py-4">@if($roles['secretaria'])<span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Sí</span>@else<span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">No</span>@endif</td>
                                <td class="whitespace-nowrap px-4 py-4">@if($roles['docente'])<span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Sí</span>@else<span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">No</span>@endif</td>
                                <td class="whitespace-nowrap px-4 py-4">@if($roles['alumno'])<span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Sí</span>@else<span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">No</span>@endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Logins aceptados</p>
                    <p class="mt-4 text-4xl font-black text-slate-900">{{ $loginAceptados }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Logins fallidos</p>
                    <p class="mt-4 text-4xl font-black text-slate-900">{{ $loginFallados }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Recursos más accedidos</p>
                    <p class="mt-4 text-4xl font-black text-slate-900">{{ $recursosMasAccedidos->sum('total') }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">Recursos más accedidos</p>
                        <p class="mt-2 text-sm text-slate-500">Los endpoints con mayor frecuencia de uso en la sección administrativa.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($recursosMasAccedidos as $recurso)
                        <div class="flex items-center justify-between rounded-3xl border border-slate-100 bg-slate-50 px-4 py-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $recurso->recurso ?? 'Sin recurso' }}</p>
                                <p class="text-xs text-slate-500">Accesos registrados</p>
                            </div>
                            <span class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-full bg-blue-100 px-3 text-sm font-bold text-blue-700">{{ $recurso->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No hay datos de acceso registrados todavía.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">Últimos eventos</p>
                        <p class="mt-2 text-sm text-slate-500">Historial reciente de inicio de sesión y accesos.</p>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.18em] text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Fecha</th>
                            <th class="whitespace-nowrap px-4 py-3">Tipo</th>
                            <th class="whitespace-nowrap px-4 py-3">Estado</th>
                            <th class="whitespace-nowrap px-4 py-3">Recurso</th>
                            <th class="whitespace-nowrap px-4 py-3">Usuario</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($ultimosEventos as $evento)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4">{{ $evento->created_at->format('d/m/Y H:i') }}</td>
                                <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-900">{{ ucfirst($evento->tipo) }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ ucfirst($evento->estado ?? '-') }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $evento->recurso ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $evento->email ?? $evento->user?->email ?? 'Anonimo' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-sm text-slate-500">No hay eventos registrados.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside class="space-y-5">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">Resumen de acceso</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-700">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        Propietario: acceso completo al sistema administrativo y a la bitácora.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-sky-500"></span>
                        Secretaria: acceso administrativo general, sin bitácora privada de propietario.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-violet-500"></span>
                        Docente: acceso a sus materias y seguimiento académico.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                        Alumno: acceso a su información académica y pagos personales.
                    </li>
                </ul>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 text-sm text-slate-600 shadow-sm">
                <p class="font-semibold text-slate-900">Nota</p>
                <p class="mt-3 leading-7">Esta pantalla es una vista Blade para el propietario que muestra la matriz de acceso y la bitácora de eventos sin utilizar Vue ni Inertia.</p>
            </div>
        </aside>
    </div>
@endsection
