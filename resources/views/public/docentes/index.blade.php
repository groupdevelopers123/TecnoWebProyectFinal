@extends('public.layouts.app')

@section('title', 'Docentes | Instituto')

@section('content')

<section class="bg-slate-900">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black text-white">Docentes</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
            Conoce a los docentes que forman parte de nuestra institución.
        </p>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        @forelse ($docentes as $docente)
            @include('public.partials.docente-card', ['docente' => $docente])
        @empty
            <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
                No hay docentes registrados.
            </div>
        @endforelse
    </div>
</section>

@endsection