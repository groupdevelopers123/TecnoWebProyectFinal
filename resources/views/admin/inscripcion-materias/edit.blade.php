<div class="mb-6">
    <h3 class="text-lg font-black text-slate-900">Editar materia inscrita</h3>
    <p class="mt-1 text-sm text-slate-500">
        Modifica el estado académico de la materia seleccionada.
    </p>
</div>

<form method="POST"
      action="{{ route('admin.inscripciones.materias.update', [$inscripcion, $inscripcionMateria]) }}"
      data-modal-form>
    @csrf
    @method('PUT')

    @include('admin.inscripcion-materias._form', [
        'soloLecturaMateria' => true
    ])
</form>