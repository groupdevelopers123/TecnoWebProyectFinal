<div class="mb-6">
    <h3 class="text-lg font-black text-slate-900">Agregar materia</h3>
    <p class="mt-1 text-sm text-slate-500">
        Selecciona la materia que será registrada dentro de esta inscripción.
    </p>
</div>

<form method="POST"
      action="{{ route('admin.inscripciones.materias.store', $inscripcion) }}"
      data-modal-form>
    @csrf

    @include('admin.inscripcion-materias._form')
</form>