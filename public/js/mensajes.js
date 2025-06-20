document.addEventListener("DOMContentLoaded", function () {
    const selectCategoria = document.getElementById("categoria");
    const inputBuscar = document.getElementById("buscar");
    const resultadosBusqueda = document.getElementById("resultados-busqueda");

    let todosLosBienes = [];

    async function cargarBienes() {
        try {
            let response = await fetch("{{ route('bienes.todos') }}");
            todosLosBienes = await response.json();
        } catch (error) {
            console.error("Error al cargar bienes:", error);
        }
    }
    cargarBienes();

    function buscarBienes() {
        let texto = inputBuscar.value.toLowerCase().trim();
        if (texto.length < 1) {
            resultadosBusqueda.style.display = "none";
            return;
        }

        let resultados = todosLosBienes.filter(
            (bien) =>
                bien.numero_inventario.toString().includes(texto) ||
                bien.nombre.toLowerCase().includes(texto)
        );

        let listaResultados = resultados
            .slice(0, 5)
            .map((bien) => {
                return `<a href="/admin/bienes/${bien.id_bien}/ver"
                class="list-group-item list-group-item-action">
                    <strong>${bien.numero_inventario}</strong> - ${bien.nombre}
                </a>`;
            })
            .join("");

        resultadosBusqueda.innerHTML =
            listaResultados ||
            '<div class="list-group-item text-center">No se encontraron resultados</div>';
        resultadosBusqueda.style.display = "block";
    }

    inputBuscar.addEventListener("keyup", buscarBienes);

    document.addEventListener("click", function (event) {
        if (!event.target.closest("#buscar, #resultados-busqueda")) {
            resultadosBusqueda.style.display = "none";
        }
    });

    selectCategoria.addEventListener("change", function () {
        let categoria = selectCategoria.value;
        let urlBase = "{{ route('bienes.index') }}";

        if (categoria === "") {
            window.location.href = urlBase;
        } else {
            window.location.href = `${urlBase}?categoria=${encodeURIComponent(
                categoria
            )}`;
        }
    });
});

function confirmDelete(estado, asignado, id_bien, url) {
    if (estado === "activo") {
        Swal.fire(
            "No permitido",
            "El bien está activo y no se puede eliminar.",
            "warning"
        );
        return;
    }
    if (asignado === "true") {
        Swal.fire(
            "No permitido",
            "El bien está asignado a un resguardante y no se puede eliminar.",
            "warning"
        );
        return;
    }

    Swal.fire({
        title: `¿Eliminar bien #${id_bien}?`,
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.createElement("form");
            form.method = "POST";
            form.action = url;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    let successMessage = window.messages.success;
    let errorMessage = window.messages.error;

    if (successMessage) {
        Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: successMessage,
            confirmButtonColor: "#28a745",
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: errorMessage,
            confirmButtonColor: "#d33",
        });
    }
});
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('input[name="bienes[]"]');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = source.checked;
    });
}
