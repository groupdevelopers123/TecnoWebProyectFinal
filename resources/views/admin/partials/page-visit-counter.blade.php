<div id="admin-page-visit-counter" class="fixed bottom-4 right-4 z-50 rounded-full bg-slate-950/90 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-slate-950/30 backdrop-blur-sm">
    Visitas: <span id="admin-page-visit-count">...</span>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const counter = document.getElementById('admin-page-visit-counter');
        const countElement = document.getElementById('admin-page-visit-count');
        if (!counter || !countElement || typeof axios === 'undefined') {
            return;
        }

        axios.post('/page-visits', {
            page: window.location.pathname + window.location.search,
        })
        .then(function (response) {
            const visits = response.data.visits;
            countElement.textContent = Number.isFinite(visits) ? visits : 0;
        })
        .catch(function (error) {
            console.error('No se pudo cargar el contador de visitas:', error);
            countElement.textContent = '-';
        });
    });
</script>
@endpush
