// public/assets/js/app/letter/letter/select.js
/* =========================================================
   Llenado de dependientes (Usuario, Enlace, Unidad,
   Coordinación, Trámite y Clave) a partir de un id de área.
   - Handler SOLO para Área (A3) aquí.
   - A1/A2 se disparan desde deps-areas.js
   ========================================================= */

// ===== Helpers comunes para placeholders =====
function setPickerEmpty(selector) {
  $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
}

function ensurePickerHasPlaceholder(selector) {
  const $sel = $(selector);
  const hasAny = $sel.find('option').length > 0;
  if (!hasAny) setPickerEmpty(selector);
  else $sel.selectpicker('refresh');
}

// ===== Helpers encabezado =====
function clearClaveData() {
  $('#_labClave').text('_');
  $('#_labClaveCodigo').text('_');
  $('#_labClaveRedaccion').text('_');
}

function setClaveInNuSystem(value) {
  let num_turno_sistema = $('#num_turno_sistema').val() || '';
  if (!num_turno_sistema) return;
  let result = num_turno_sistema.replace(/^[^/]+/, value || '-');
  $('#num_turno_sistema').val(result);
  $('#_labNoCorrespondencia').text(result);
}

// ====== FUNCIÓN ÚNICA: llena dependientes a partir de un id de área ======
window.__FILL_INFLIGHT = null; // { areaId, xhr }
window.fillDependentsFromArea = function (areaId, initials) {
  if (!areaId) {
    setPickerEmpty('#id_usuario_area');
    setPickerEmpty('#id_usuario_enlace');
    setPickerEmpty('#id_cat_tramite');
    setPickerEmpty('#id_cat_unidad');
    setPickerEmpty('#id_cat_coordinacion');
    setPickerEmpty('#id_cat_clave');
    clearClaveData();
    setClaveInNuSystem('-');
    return;
  }

  // Evita doble AJAX para el mismo id en vuelo
  if (window.__FILL_INFLIGHT && String(window.__FILL_INFLIGHT.areaId) === String(areaId)) {
    try { if (window.__FILL_INFLIGHT.xhr && window.__FILL_INFLIGHT.xhr.readyState < 4) return; } catch (_) {}
  }

  const xhr = $.ajax({
    url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
    type: 'POST',
    data: { id: areaId, _token: token },
    success: function (response) {
      // Llenado de dependientes
      foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
      foreachSelectNull(response.selectUsuario, '#id_usuario_area');
      foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
      foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
      foreachSelect(response.selectTramite,     '#id_cat_tramite');

      // Placeholders/refrescos
      ensurePickerHasPlaceholder('#id_usuario_enlace');
      ensurePickerHasPlaceholder('#id_usuario_area');
      ensurePickerHasPlaceholder('#id_cat_unidad');
      ensurePickerHasPlaceholder('#id_cat_coordinacion');
      ensurePickerHasPlaceholder('#id_cat_tramite');

      // Restaurar valores en edición (si vienen)
      if (initials) {
        if (initials.usuario_enlace)  $('#id_usuario_enlace').val(String(initials.usuario_enlace));
        if (initials.usuario_area)    $('#id_usuario_area').val(String(initials.usuario_area));
        if (initials.unidad)          $('#id_cat_unidad').val(String(initials.unidad));
        if (initials.coordinacion)    $('#id_cat_coordinacion').val(String(initials.coordinacion));
        ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion'].forEach(s => $(s).selectpicker('refresh'));
      }

      // === Seleccionar Trámite ===
      const $tram = $('#id_cat_tramite');
      const tramOptions = $tram.find('option').not('[value=""]');

      if (initials && initials.tramite) {
        $tram.val(String(initials.tramite)).selectpicker('refresh').trigger('change');
        // Dar tiempo a que el handler de Trámite llene Clave y luego fijar la inicial si existe
        setTimeout(function () {
          if (initials.clave) $('#id_cat_clave').val(String(initials.clave)).selectpicker('refresh').trigger('change');
        }, 150);
      } else if (tramOptions.length > 0) {
        const firstVal = tramOptions.first().val();
        $tram.val(firstVal).selectpicker('refresh').trigger('change'); // dispara carga de Clave
      } else {
        setPickerEmpty('#id_cat_tramite');
        setPickerEmpty('#id_cat_clave');
        clearClaveData();
      }

      // Prefijo de clave en No. Turno (si backend lo envía)
      setClaveInNuSystem(response.clave);
    },
    complete: function () {
      window.__FILL_INFLIGHT = null;
    }
  });

  window.__FILL_INFLIGHT = { areaId, xhr };
};

// ===== Disparador SOLO para Área (A3) =====
function __getInitials() {
  return (window.LETTER && window.LETTER.initials) || null;
}

$('#id_cat_area').on('change', function () {
  window.fillDependentsFromArea($(this).val(), __getInitials());
});
// por si usas bootstrap-select:
$('#id_cat_area').on('changed.bs.select', function () {
  window.fillDependentsFromArea($(this).val(), __getInitials());
});

// ===== Autoinicialización en carga (si ya hay valor) =====
$(function primeFillFromCurrentSelection() {
  // Preferencia: A3 > A2 > A1
  var a3 = $('#id_cat_area').val();
  var a2 = $('#id_cat_area_2').val();
  var a1 = $('#id_cat_area_1').val();
  var target = a3 || a2 || a1;

  // Solo si todo está vacío
  var need = !$('#id_usuario_area').val() &&
             !$('#id_usuario_enlace').val() &&
             !$('#id_cat_unidad').val() &&
             !$('#id_cat_tramite').val() &&
             !$('#id_cat_clave').val();

  if (target && need) {
    window.fillDependentsFromArea(target, __getInitials());
  }
});

// ===== Encadenamientos adicionales (igual que tenías) =====

// Unidad -> Coordinación
$('#id_cat_unidad').on('change', function () {
  let idValue = $(this).val();
  if (idValue) {
    $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionUnidad'),
      type: 'POST',
      data: { id: idValue, _token: token },
      success: function (response) {
        foreachSelectNull(response.selectCoordinacion, '#id_cat_coordinacion');
        ensurePickerHasPlaceholder('#id_cat_coordinacion');
      },
    });
  } else {
    setPickerEmpty('#id_cat_coordinacion');
  }
});

// Trámite -> Clave
$('#id_cat_tramite').on('change', function () {
  let idValue = $(this).val();
  if (idValue) {
    $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionTramite'),
      type: 'POST',
      data: { id: idValue, _token: token },
      success: function (response) {
        foreachSelectNull(response.selectClave, '#id_cat_clave');
        ensurePickerHasPlaceholder('#id_cat_clave');
      },
    });
  } else {
    setPickerEmpty('#id_cat_clave');
    clearClaveData();
  }
});

// Clave -> Encabezado
$('#id_cat_clave').on('change', function () {
  let idValue = $(this).val();
  if (idValue) {
    $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionClave'),
      type: 'POST',
      data: { id: idValue, _token: token },
      success: function (response) {
        let valueClave = response.valueOfClave;
        $('#_labClave').text(valueClave._labClave);
        $('#_labClaveCodigo').text(valueClave._labClaveCodigo);
        $('#_labClaveRedaccion').text(valueClave._labClaveRedaccion);
      },
    });
  } else {
    clearClaveData();
  }
});

// Helpers encabezado
function clearClaveData() {
  $('#_labClave').text('_');
  $('#_labClaveCodigo').text('_');
  $('#_labClaveRedaccion').text('_');
}

function setClaveInNuSystem(value) {
  let num_turno_sistema = $('#num_turno_sistema').val();
  let result = num_turno_sistema.replace(/^[^/]+/, value);
  $('#num_turno_sistema').val(result);
  $('#_labNoCorrespondencia').text(result);
}


