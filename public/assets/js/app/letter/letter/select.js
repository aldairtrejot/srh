// public/assets/js/app/letter/letter/select.js

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

// ====== Lógica existente, con refuerzos del placeholder ======

// Código para la selección de área y cómo cambia el valor de los demás select que dependen de ella
$('#id_cat_area').on('change', function () {
  let idValue = $(this).val();

  if (idValue) {
    $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
      type: 'POST',
      data: { id: idValue, _token: token },
      success: function (response) {
        foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
        foreachSelectNull(response.selectUsuario, '#id_usuario_area');
        foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
        foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
        foreachSelect(response.selectTramite,     '#id_cat_tramite');

        // Si alguno quedó vacío, dejar "SELECCIONE"
        ensurePickerHasPlaceholder('#id_usuario_enlace');
        ensurePickerHasPlaceholder('#id_usuario_area');
        ensurePickerHasPlaceholder('#id_cat_unidad');
        ensurePickerHasPlaceholder('#id_cat_coordinacion');
        ensurePickerHasPlaceholder('#id_cat_tramite');

        // Clave depende de trámite: limpiar y forzar placeholder
        cleanSelectMoreSelect('#id_cat_clave');
        setPickerEmpty('#id_cat_clave');

        clearClaveData();
        setClaveInNuSystem(response.clave);
      },
    });
  } else {
    // Sin Área 3 -> todos vacíos con "SELECCIONE"
    setPickerEmpty('#id_usuario_area');
    setPickerEmpty('#id_usuario_enlace');
    setPickerEmpty('#id_cat_tramite');
    setPickerEmpty('#id_cat_unidad');
    setPickerEmpty('#id_cat_coordinacion');
    setPickerEmpty('#id_cat_clave');
    clearClaveData();
    setClaveInNuSystem('-');
  }
});

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


