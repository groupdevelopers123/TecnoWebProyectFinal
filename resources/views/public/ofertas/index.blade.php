@extends('public.layouts.app')

@section('title', 'Ofertas Académicas | Instituto')

@section('content')

<section class="bg-blue-700">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black text-white">Ofertas Académicas</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-50">
            Revisa las ofertas disponibles e inicia tu proceso de inscripción.
        </p>
    </div>
</section>

@if (session('info'))
    <div class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-bold text-blue-700">
            {{ session('info') }}
        </div>
    </div>
@endif

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($ofertas as $oferta)
            @include('public.partials.oferta-card', ['oferta' => $oferta])
        @empty
            <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
                No hay ofertas académicas registradas.
            </div>
        @endforelse
    </div>
</section>

@endsection