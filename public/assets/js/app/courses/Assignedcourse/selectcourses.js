const token = $('meta[name="csrf-token"]').attr('content');
const URL_BASE = window.location.origin + "/srh/public";
let iterator = 1;
let currentSearch = '';

// Configurar token CSRF para todas las peticiones AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

$(document).ready(function () {
    cargarCursosActivos();

    // Buscar cursos al presionar Enter
    $('#searchCurso, input[name="search"]').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarCursos();
        }
    });

    // Eventos de paginación si usas botones personalizados
    $('#btnPaginatorMin').on('click', paginatorMin);
    $('#btnPaginatorMax').on('click', paginatorMax);
});

// 🔹 Cargar cursos activos vía AJAX
function cargarCursosActivos() {
    $.ajax({
        url: routeCursosActivos, // Definida en el blade
        type: 'POST',
        data: {
            iterator: iterator,
            search: currentSearch,
            _token: token
        },
        success: function (response) {
            const tbody = $('#cursosBody');
            tbody.empty();

            if (response.data && response.data.length > 0) {
                response.data.forEach(function (curso) {
                    const row = `
                        <tr>
                            <td class="text-center">
                                <input type="radio" name="id_curso" value="${curso.id}">
                            </td>
                            <td>${curso.nombre}</td>
                            <td>${curso.fecha_inicio}</td>
                            <td>${curso.fecha_fin}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No hay cursos disponibles.
                        </td>
                    </tr>
                `);
            }
        },
        error: function (xhr) {
            console.error('Error al cargar cursos:', xhr);
            $('#cursosBody').html(`
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        Error al cargar los cursos disponibles.
                    </td>
                </tr>
            `);
        }
    });
}

// 🔹 Buscar cursos
function buscarCursos() {
    currentSearch = $('#searchCurso').val() || $('input[name="search"]').val();
    iterator = 1;
    cargarCursosActivos();
}

// 🔹 Paginación
function paginatorMin() {
    if (iterator > 1) {
        iterator--;
        cargarCursosActivos();
    }
}

function paginatorMax() {
    iterator++;
    cargarCursosActivos();
}
