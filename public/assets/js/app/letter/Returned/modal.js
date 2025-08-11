// ================= modal Returnado (compatible con tu modalTemplate) =================
var token = $('meta[name="csrf-token"]').attr('content');

// Abre el modal y carga área + subáreas
// Nota: tu tabla llama a openReturnModal(id) desde el botón "Returnado".
// Dejamos ese nombre para que NO tengas que tocar table.js.
window.openReturnModal = function(id) {
    // Mostrar modal (mismo comportamiento que modalReport)
    $('#modalReturnado').fadeIn();

    // Estado de "cargando" en la tabla
    setReturnadoTableState('loading');

    // Limpia el texto del área
    $('#areaActual').text('—');

    // Construye URL segura (inyectada desde Blade)
    const baseGet = (window.RETURNED_GET_URL || '/returned/get-area-subareas').replace(/\/$/, '');
    const url = baseGet + '/' + id;

    // GET: área + subáreas
    $.ajax({
        url: url,
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token }
    })
    .done(function(response) {
        // Área
        if (response && response.area && response.area.descripcion) {
            $('#areaActual').text(response.area.descripcion);
        } else {
            $('#areaActual').text('Área no encontrada');
        }

        // Subáreas (tu API devuelve id_sub_area)
        const subareas = Array.isArray(response && response.subareas) ? response.subareas : [];
        const $tbody = $('#tablaSubareas');

        if (subareas.length === 0) {
            setReturnadoTableState('empty');
            return;
        }

        $tbody.empty();
        subareas.forEach(function(sub) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${sub.descripcion || ''}</td>
                <td class="text-right" style="width:160px;">
                    <button class="btn btn-sm btn-primary"
                        onclick="selectReturnadoSubarea(${id}, ${sub.id_sub_area}, this)">
                        Asignar
                    </button>
                </td>`;
            $tbody.append(tr);
        });
    })
    .fail(function(xhr) {
        setReturnadoTableState('error', `No se pudo cargar la información (HTTP ${xhr.status}).`);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar subáreas' });
    });
};

// Cerrar modal al hacer click fuera (igual que modalReport)
$(document).ready(function () {
    $(window).click(function (event) {
        if ($(event.target).is('#modalReturnado')) {
            $('#modalReturnado').fadeOut();
        }
    });
    // Botón cancelar de tu componente
    $('#cancel_returnado').click(function () {
        $('#modalReturnado').fadeOut();
    });
});

// POST: asignar subárea seleccionada
window.selectReturnadoSubarea = function(idArea, idSubarea, el) {
    const postUrl = (window.RETURNED_ASSIGN_URL || '/returned/assign');

    const $btn = $(el);
    const original = $btn.html();

    $btn.prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm mr-1" role="status"></span> Guardando...'
    );

    $.post(postUrl, { id_area: idArea, id_subarea: idSubarea, _token: token })
      .done(function() {
          $btn.removeClass('btn-primary').addClass('btn-success').text('Asignado');
          Swal.fire({ icon: 'success', title: '¡Listo!', text: 'Subárea asignada.' });
          // Si deseas cerrar el modal automáticamente:
          // $('#modalReturnado').fadeOut();
      })
      .fail(function(xhr) {
          const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'No se pudo guardar la asignación.';
          Swal.fire({ icon: 'error', title: 'Error', text: msg });
          $btn.prop('disabled', false).html(original);
      });
};

// Helper visual para la tabla del modal (loading / empty / error)
function setReturnadoTableState(state, message) {
    const $tbody = $('#tablaSubareas');
    $tbody.empty();

    let html = '';
    if (state === 'loading') {
        html = `
            <tr>
                <td colspan="2" class="text-center">
                    <div class="spinner-border spinner-border-sm mr-2" role="status"></div> Cargando...
                </td>
            </tr>`;
    } else if (state === 'empty') {
        html = `<tr><td colspan="2" class="text-center text-muted">Sin subáreas disponibles</td></tr>`;
    } else if (state === 'error') {
        html = `<tr><td colspan="2" class="text-center text-danger">${message || 'Error al cargar.'}</td></tr>`;
    }
    if (html) $tbody.html(html);
}
