const token = $('meta[name="csrf-token"]').attr('content');
const URL_BASE = window.location.origin + "/srh/public";
let iterator = 1;
let currentSearch = '';

// Configurar token CSRF
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

$(document).ready(function () {
    cargarCursosActivos();

    // Buscar cursos al presionar Enter
    $('#searchValue').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            searchValue();
        }
    });

    // Botones de paginación personalizados
    $('#btnPaginatorMin1').on('click', paginatorMin1);
    $('#btnPaginatorMin5').on('click', paginatorMin5);
    $('#btnPaginatorMax1').on('click', paginatorMax1);
    $('#btnPaginatorMax5').on('click', paginatorMax5);
});

// Buscar cursos
function searchValue() {
    currentSearch = $('#searchValue').val();
    iterator = 1;
    cargarCursosActivos();
}

// Cargar cursos activos con AJAX
function cargarCursosActivos() {
    $.ajax({
        url: routeCursosActivos,
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

                setValue(response.pagination);
            } else {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hay cursos disponibles.</td>
                    </tr>
                `);
                setValue({ current_page: 1, last_page: 1 });
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

// Actualizar paginador visual
function setValue(pagination) {
    iterator = pagination.current_page;

    $('#is_iterator').text(iterator);
    $('#is_iteratorMin').text(Math.max(1, iterator - 1));
    $('#is_iteratorMax').text(Math.min(pagination.last_page, iterator + 1));
}

// Botones personalizados
function paginatorMax1() {
    iterator += 1;
    cargarCursosActivos();
}

function paginatorMax5() {
    iterator += 5;
    cargarCursosActivos();
}

function paginatorMin1() {
    iterator = Math.max(1, iterator - 1);
    cargarCursosActivos();
}

function paginatorMin5() {
    iterator = Math.max(1, iterator - 5);
    cargarCursosActivos();
}
