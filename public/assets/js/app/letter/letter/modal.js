// ===== modal.js (completo) =====

// Token CSRF
var token = $('meta[name="csrf-token"]').attr('content');

// Al cargar la página
$(document).ready(function () {
  // Cerrar modales al hacer click en el fondo
  $(window).on('click', function (event) {
    if ($(event.target).is('#modalCopy')) {
      $('#modalCopy').fadeOut();
      cleanSelectCopy();
    }
    if ($(event.target).is('#id_modal_delete_acuse')) {
      $('#id_modal_delete_acuse').fadeOut();
      cleanSelectCopy();
    }
  });

  // Deja el bloque de "Agregar registro" oculto al inicio
  cleanSelectCopy();

  // Listeners (evitan doble registro)
  bindCopyListeners();
});

/* =========================
 * Abrir / Cerrar modales
 * ========================= */
function openCopy(id, folGestion) {
  $('#name_folio_gestion').text(folGestion);
  $('#id_correspondencia_x').val(id);
  $('#modalCopy').fadeIn();
  searchInitToCopy(id); // tu función de tabla
  cleanSelectCopy();    // oculta y limpia el bloque de alta
}

function openModalDelete(id) {
  // Validación por ROLE
  let bool_user_role = $('#bool_user_role').val();
  let hasRole = (bool_user_role && bool_user_role.trim() !== '');
  if (!hasRole) {
    notyfEM?.error?.('No se han configurado permisos para este usuario.');
    return;
  }
  $('#id_delete').val(id);
  $('#id_modal_delete_acuse').fadeIn();
}

$('#cancel_copy').on('click', function () {
  $('#modalCopy').fadeOut();
});

$('#id_modal_calcel_acuse').on('click', function () {
  $('#id_modal_delete_acuse').fadeOut();
});

function confirmarCopy() {
  $('#modalCopy').fadeOut();
}

function confirmModalDelete() {
  $.ajax({
    url: URL_DEFAULT.concat('/letter/delete/copy'),
    type: 'POST',
    data: {
      id: $('#id_delete').val(),
      _token: token
    },
    success: function (response) {
      if (response.value) {
        notyfEM?.success?.('Elemento eliminado correctamente.');
      } else {
        notyfEM?.error?.('Ocurrió un error inesperado al intentar eliminar el elemento.');
      }
      searchInitToCopy($('#id_correspondencia_x').val());
      $('#id_modal_delete_acuse').fadeOut();
    }
  });
}

/* =========================
 * Alta de copias
 * ========================= */
function addCopy() {
  // 1) Mostrar SIEMPRE el contenedor
  showDiv('mostrar_ocultar_copy');

  // 2) Placeholders/limpieza inmediata
  cleanSelectMoreSelect('#id_cat_area_copy');
  cleanSelectMoreSelect('#id_usuario_area_copy');
  cleanSelectMoreSelect('#id_usuario_enlace_copy');
  cleanSelectMoreSelect('#id_cat_tramite_copy');
  cleanSelectMoreSelect('#id_cat_clave_copy');

  // 3) Cargar catálogo de áreas permitidas
  getSelectAreaCopy();
}

function hiddenCopy() {
  cleanSelectCopy();
}

// Carga de Áreas (solo las permitidas para el modal)
function getSelectAreaCopy() {
  // Usa /areasForCopy si ya lo tienes; de lo contrario, puedes
  // volver temporalmente a /letter/collection/area.
  const url = URL_DEFAULT.concat('/letter/collection/areasForCopy');

  $.ajax({
    url: url,
    type: 'POST',
    data: { _token: token },
    success: function (resp) {
      // resp.value recomendado; si tu endpoint regresa 'result', ajústalo:
      const rows = (resp && (resp.value || resp.result)) || [];
      foreachSelect(rows, '#id_cat_area_copy'); // deja "SELECCIONE" + opciones
      if ($.fn.selectpicker) $('#id_cat_area_copy').selectpicker('refresh');

      // No auto-seleccionamos para NO disparar Usuario/Enlace sin decisión del usuario
    },
    error: function () {
      foreachSelect([], '#id_cat_area_copy'); // placeholder
      if ($.fn.selectpicker) $('#id_cat_area_copy').selectpicker('refresh');
      notyfEM?.error?.('No se pudo cargar el catálogo de áreas.');
    }
  });
}

/* =========================
 * Listeners: área / trámite
 * ========================= */
function bindCopyListeners() {
  // Área → Usuario, Enlace, Trámite
  $('#id_cat_area_copy').off('change.copy').on('change.copy', function () {
    let idValue = $(this).val();

    if (idValue) {
      $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
        type: 'POST',
        data: { id: idValue, _token: token },
        success: function (response) {
          // Llenado respetando placeholders
          foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace_copy');
          foreachSelectNull(response.selectUsuario, '#id_usuario_area_copy');
          foreachSelect     (response.selectTramite, '#id_cat_tramite_copy');

          if ($.fn.selectpicker) {
            $('#id_usuario_enlace_copy').selectpicker('refresh');
            $('#id_usuario_area_copy').selectpicker('refresh');
            $('#id_cat_tramite_copy').selectpicker('refresh');
          }

          // Al cambiar de área, limpia Clave
          cleanSelectMoreSelect('#id_cat_clave_copy');
        }
      });
    } else {
      // Sin área → limpia dependientes
      cleanSelectMoreSelect('#id_usuario_enlace_copy');
      cleanSelectMoreSelect('#id_usuario_area_copy');
      cleanSelectMoreSelect('#id_cat_tramite_copy');
      cleanSelectMoreSelect('#id_cat_clave_copy');
    }
  });

  // Trámite → Clave
  $('#id_cat_tramite_copy').off('change.copy').on('change.copy', function () {
    let idValue = $(this).val();

    if (idValue) {
      $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/collectionTramite'),
        type: 'POST',
        data: { id: idValue, _token: token },
        success: function (response) {
          foreachSelectNull(response.selectClave, '#id_cat_clave_copy');
          if ($.fn.selectpicker) $('#id_cat_clave_copy').selectpicker('refresh');
        }
      });
    } else {
      cleanSelectMoreSelect('#id_cat_clave_copy');
    }
  });
}

/* =========================
 * Guardado de la copia
 * ========================= */
function saveCopy() {
  // Validación básica (puedes usar tu helper si ya lo tienes)
  if (isFieldEmpty($('#id_cat_area_copy').val(), 'Área') ||
      isFieldEmpty($('#id_usuario_area_copy').val(), 'Usuario') ||
      isFieldEmpty($('#id_usuario_enlace_copy').val(), 'Enlace') ||
      isFieldEmpty($('#id_cat_tramite_copy').val(), 'Trámite') ||
      isFieldEmpty($('#id_cat_clave_copy').val(), 'Clave')) {
    return;
  }

  // Valida que no exista ya la copia para esa área
  onlyArea($('#id_correspondencia_x').val(), $('#id_cat_area_copy').val());
}

function onlyArea(id_tbl_correspondencia, id_cat_area) {
  $.ajax({
    url: URL_DEFAULT.concat('/letter/validateCopy'),
    type: 'POST',
    data: {
      id_tbl_correspondencia: id_tbl_correspondencia,
      id_cat_area: id_cat_area,
      _token: token
    },
    success: function (response) {
      if (response.result) {
        validateIsOK(); // Guarda
      } else {
        notyfEM?.error?.('El área ya tiene asignado el folio de gestión.');
      }
    }
  });
}

function validateIsOK() {
  $.ajax({
    url: URL_DEFAULT.concat('/letter/saveCopy'),
    type: 'POST',
    data: {
      id_tbl_correspondencia: $('#id_correspondencia_x').val(),
      id_cat_area:           $('#id_cat_area_copy').val(),
      id_usuario_area:       $('#id_usuario_area_copy').val(),
      id_usuario_enlace:     $('#id_usuario_enlace_copy').val(),
      id_cat_tramite:        $('#id_cat_tramite_copy').val(),
      id_cat_clave:          $('#id_cat_clave_copy').val(),
      _token: token
    },
    success: function (response) {
      if (response.result) {
        notyfEM?.success?.('Elemento agregado correctamente.');
      } else {
        notyfEM?.error?.('Ocurrió un error inesperado al intentar agregar el elemento.');
      }
      searchInitToCopy($('#id_correspondencia_x').val());
      cleanSelectCopy();
    }
  });
}

/* =========================
 * Limpieza / helpers
 * ========================= */
function cleanSelectCopy() {
  hideDiv('mostrar_ocultar_copy');

  cleanSelectMoreSelect('#id_cat_area_copy');
  cleanSelectMoreSelect('#id_usuario_area_copy');
  cleanSelectMoreSelect('#id_usuario_enlace_copy');
  cleanSelectMoreSelect('#id_cat_tramite_copy');
  cleanSelectMoreSelect('#id_cat_clave_copy');
}

// Helper mínimo por si no existiera en tu global
function isFieldEmpty(value, label) {
  const empty = (value == null || String(value).trim() === '');
  if (empty) notyfEM?.error?.('Selecciona ' + label + '.');
  return empty;
}
