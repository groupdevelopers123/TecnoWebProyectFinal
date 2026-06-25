document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("ajax-search-input");
    const tbody = document.getElementById("ajax-table-body");
    const pagination = document.getElementById("ajax-pagination");

    if (!input || !tbody) {
        return;
    }

    let timer = null;

    input.addEventListener("input", function () {
        clearTimeout(timer);

        timer = setTimeout(() => {
            buscarAjax(input.value);
        }, 300);
    });

    function buscarAjax(valor, urlPersonalizada = null) {
        const urlBase = urlPersonalizada || input.dataset.url;

        if (!urlBase) {
            return;
        }

        const url = new URL(urlBase, window.location.origin);
        url.searchParams.set("buscar", valor);

        tbody.innerHTML = `
            <tr>
                <td colspan="20" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando...
                </td>
            </tr>
        `;

        fetch(url, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                tbody.innerHTML = data.html;

                if (pagination && data.pagination !== undefined) {
                    pagination.innerHTML = data.pagination;
                }
            })
            .catch(() => {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="20" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    document.addEventListener("click", function (event) {
        const link = event.target.closest("#ajax-pagination a");

        if (!link) {
            return;
        }

        event.preventDefault();

        buscarAjax(input.value, link.href);
    });
});
