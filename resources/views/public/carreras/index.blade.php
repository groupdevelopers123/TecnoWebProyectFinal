@extends('public.layouts.app')

@section('title', 'Carreras | Instituto')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 pt-[84px]">
    <section class="px-5 pb-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl rounded-[2rem] bg-white/90 border border-slate-200/70 px-6 py-12 shadow-xl shadow-slate-200/40 sm:px-8 relative z-10">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">Carreras</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
                Conoce las carreras disponibles, su régimen, descripción y datos principales.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 pb-20 sm:px-6 lg:px-8 mt-8">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($carreras as $carrera)
                @include('public.partials.carrera-card', ['carrera' => $carrera])
            @empty
                <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-white/80 py-16 text-center shadow-sm shadow-slate-200">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100">
                        <svg class="h-10 w-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.996 10-10.747S17.5 6.253 12 6.253z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-black text-slate-900">No hay carreras registradas</h3>
                    <p class="mt-2 text-sm text-slate-500">Aún no se ha publicado ninguna carrera en el sistema.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>

@endsection