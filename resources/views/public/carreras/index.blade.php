@extends('public.layouts.app')

@section('title', 'Carreras | Instituto')

@section('content')

<section class="bg-slate-900">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black text-white">Carreras</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
            Conoce las carreras disponibles, su régimen, descripción y datos principales.
        </p>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($carreras as $carrera)
            @include('public.partials.carrera-card', ['carrera' => $carrera])
        @empty
            <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
                No hay carreras registradas.
            </div>
        @endforelse
    </div>
</section>

@endsection