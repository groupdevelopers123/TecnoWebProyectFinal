@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')
@section('page-subtitle', 'Administración de propietarios, secretarias, docentes y alumnos')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de usuarios</h2>
            <p class="mt-1 text-sm text-slate-500">
                Busca, filtra, edita o desactiva usuarios del sistema.
            </p>
        </div>

        <a href="{{ route('admin.usuarios.create') }}"
           class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            + Nuevo usuario
        </a>
    </div>

    <form id="usuarios-search-form" method="GET" action="{{ route('admin.usuarios.index') }}" class="mt-6 grid gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input
                type="text"
                name="buscar"
                placeholder="Nombre, CI o email"
                value="{{ request('buscar') }}"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
            >
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">Rol</label>
            <select name="role_id"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <option value="">Todos los roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>
                        {{ ucfirst($role->nombre) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-3">
            <button type="submit"
                    class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                Buscar
            </button>

            <a href="{{ route('admin.usuarios.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Limpiar
            </a>
        </div>
    </form>
</div>

<div id="usuarios-list" class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">CI</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nombre completo</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Rol</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($usuarios as $usuario)
                    <tr class="transition hover:bg-slate-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">
                            {{ $usuario->ci }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-black text-blue-700">
                                    {{ strtoupper(substr($usuario->nombres, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $usuario->nombreCompleto() }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Usuario del sistema
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            {{ $usuario->email }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                                {{ ucfirst($usuario->role->nombre) }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($usuario->estado)
                                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                           <div class="flex flex-wrap items-center gap-2">

                                <a href="{{ route('admin.usuarios.show', $usuario) }}"
                                title="Ver usuario"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                title="Editar usuario"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                    action="{{ route('admin.usuarios.destroy', $usuario) }}"
                                    onsubmit="return confirm('¿Está seguro de cambiar el estado de este usuario?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="{{ $usuario->estado ? 'Desactivar usuario' : 'Activar usuario' }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $usuario->estado
                                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($usuario->estado)
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        @else
                                            <i class="fa-solid fa-user-check text-sm"></i>
                                        @endif
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        @if($usuarios->total() > 10)
            {{ $usuarios->links() }}
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('usuarios-search-form');
    const container = document.getElementById('usuarios-list');
    if (!form || !container) return;

    const searchFields = Array.from(form.querySelectorAll('input[name="buscar"], select[name="role_id"]'));
    let debounceTimer = null;

    function escapeHtml(text) {
        return text === null || text === undefined
            ? ''
            : String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function renderTable(data) {
        if (!data.length) {
            return `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">CI</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nombre completo</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Rol</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    No se encontraron usuarios registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }

        return `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">CI</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nombre completo</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Rol</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        ${data.map(usuario => `
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">
                                    ${escapeHtml(usuario.ci)}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-black text-blue-700">
                                            ${escapeHtml(usuario.nombres.charAt(0) || '')}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">${escapeHtml(usuario.nombres)} ${escapeHtml(usuario.apellidos)}</p>
                                            <p class="text-xs text-slate-500">Usuario del sistema</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${escapeHtml(usuario.email)}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                                        ${escapeHtml(usuario.role || '-')}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    ${usuario.estado
                                        ? '<span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">Activo</span>'
                                        : '<span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">Inactivo</span>'}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="/admin/usuarios/${usuario.id}"
                                           title="Ver usuario"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </a>
                                        <a href="/admin/usuarios/${usuario.id}/edit"
                                           title="Editar usuario"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            return '';
        }

        return `
            <div class="border-t border-slate-100 px-6 py-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">
                        Página ${pagination.current_page} de ${pagination.last_page} — ${pagination.total} registros
                    </p>
                    <div class="flex gap-2">
                        <button type="button"
                                ${!pagination.prev_page_url ? 'disabled' : ''}
                                onclick="fetch('${pagination.prev_page_url ?? ''}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(handleResponse)"
                                class="rounded-xl px-4 py-2 text-sm font-bold transition ${pagination.prev_page_url ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-slate-50 text-slate-300'}">
                            Anterior
                        </button>
                        <button type="button"
                                ${!pagination.next_page_url ? 'disabled' : ''}
                                onclick="fetch('${pagination.next_page_url ?? ''}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(handleResponse)"
                                class="rounded-xl px-4 py-2 text-sm font-bold transition ${pagination.next_page_url ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-slate-50 text-slate-300'}">
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function handleResponse(data) {
        container.innerHTML = renderTable(data.data) + renderPagination(data.pagination);
    }

    function request(url) {
        fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(response => response.json())
            .then(handleResponse)
            .catch(() => {
                container.innerHTML = `<div class="px-6 py-12 text-center text-sm text-red-500">Error al cargar los usuarios.</div>`;
            });
    }

    function search() {
        const qs = new URLSearchParams(new FormData(form)).toString();
        request(form.action + '?' + qs);
    }

    searchFields.forEach(field => {
        field.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(search, 250);
        });
        field.addEventListener('change', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(search, 250);
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        search();
    });
});
</script>
@endpush