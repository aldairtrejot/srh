/* =========================================================
   form.js — LÓGICA GENERAL DEL FORMULARIO
   - Inicialización UI (selectpicker, tooltips, placeholders)
   - Fechas (límites y validación)
   - Roles/permisos y bloqueo de campos
   - Remitentes (uno o varios)
   - Carga de archivos (UI) con IDs reales del Blade
   ========================================================= */

var token = $('meta[name="csrf-token"]').attr('content');

// ——— Helpers seguros para tooltips/placeholders ———
function safeTooltip(selector, text) {
  if (typeof tooltip === 'function') { tooltip(selector, text); }
}
function refreshSelect(sel) {
  $(sel).attr('data-none-selected-text', 'SELECCIONE').selectpicker('refresh');
  if (window.__applySelectPlaceholderES) window.__applySelectPlaceholderES();
}

// ——— Fechas ———
function setDateLimits() {
  var today   = new Date();
  var maxDate = new Date(today); maxDate.setMonth(maxDate.getMonth() + 3);
  var minDate = new Date('2020-01-01');

  var minStr = minDate.toISOString().split('T')[0];
  var maxStr = maxDate.toISOString().split('T')[0];

  ['#fecha_inicio', '#fecha_fin', '#fecha_documento'].forEach(function (id) {
    $(id).attr('min', minStr).attr('max', maxStr);
    var val = $(id).val();
    if (val && (val < minStr || val > maxStr)) { $(id).val(''); }
  });
}

function validarFechasAntesDeEnviar() {
  clearFieldError('#fecha_inicio');
  clearFieldError('#fecha_fin');

  var fechaInicio = $('#fecha_inicio').val();
  var fechaFin    = $('#fecha_fin').val();

  var today   = new Date();
  var maxDate = new Date(today); maxDate.setMonth(maxDate.getMonth() + 3);
  var minDate = new Date('2020-01-01');

  var limitStr = maxDate.toISOString().split('T')[0];
  var minStr   = minDate.toISOString().split('T')[0];

  var ok = true;
  if (!fechaInicio) { showFieldError('#fecha_inicio', 'Por favor, ingresa la fecha de inicio.'); ok = false; }
  if (!fechaFin)    { showFieldError('#fecha_fin', 'Por favor, ingresa la fecha de fin.');       ok = false; }
  if (!ok) return false;

  var fi = new Date(fechaInicio);
  var ff = new Date(fechaFin);

  if (fi < minDate) { showFieldError('#fecha_inicio', 'La fecha de inicio no puede ser menor a ' + minStr + '.'); ok = false; }
  if (ff < minDate) { showFieldError('#fecha_fin',    'La fecha de fin no puede ser menor a ' + minStr + '.');   ok = false; }
  if (ff < fi)      { showFieldError('#fecha_fin',    'La fecha de fin no puede ser menor a la fecha de inicio.'); ok = false; }
  if (fi > maxDate) { showFieldError('#fecha_inicio', 'La fecha de inicio no puede ser mayor a ' + limitStr + '.'); ok = false; }
  if (ff > maxDate) { showFieldError('#fecha_fin',    'La fecha de fin no puede ser mayor a ' + limitStr + '.');   ok = false; }

  return ok;
}

function showFieldError(selector, message) {
  var $inp = $(selector);
  if ($inp.next('.invalid-feedback').length === 0) {
    $inp.after('<div class="invalid-feedback"></div>');
  }
  $inp.addClass('is-invalid');
  $inp.next('.invalid-feedback').text(message).show();
}
function clearFieldError(selector) {
  var $inp = $(selector);
  $inp.removeClass('is-invalid');
  $inp.next('.invalid-feedback').hide().text('');
}

// ——— Remitentes ———
function setCheckboxArea() {
  if ($('#rfc_remitente_bool').val()) {
    $('#idcheckboxTemplate').prop('checked', true);
    cleanSelect('#id_cat_remitente');
    $('#id_cat_remitente').prop('disabled', true).selectpicker('refresh');
    showDiv('mostrar_ocultar_template');
  } else {
    $('#remitente_nombre, #remitente_apellido_paterno, #remitente_apellido_materno, #remitente_rfc').val('');
    $('#id_cat_remitente').prop('disabled', false).selectpicker('refresh');
    hideDiv('mostrar_ocultar_template');
  }
  getRole();
  if (window.__applySelectPlaceholderES) window.__applySelectPlaceholderES();
}

function setCheckbox() {
  var es_doc_fisico      = $('#es_doc_fisico').val();
  var son_mas_remitentes = $('#son_mas_remitentes').val();

  $('#es_doc_fisico_box').prop('checked', !!es_doc_fisico);
  $('#son_mas_remitentes_box').prop('checked', !!son_mas_remitentes);
  setValueOfMoreRem();
}

function setValueOfMoreRem() {
  var son_mas_remitentes = $('#son_mas_remitentes').val();
  if (son_mas_remitentes) {
    hideDiv('_hidden_select');
    hideDiv('mostrar_ocultar_template');
    showDiv('mostrar_ocultar_mas_remitentes');
    cleanSelect('#id_cat_remitente');
  } else {
    showDiv('_hidden_select');
    hideDiv('mostrar_ocultar_template');
    hideDiv('mostrar_ocultar_mas_remitentes');
    $('#remitente').val('');
  }
  if (window.__applySelectPlaceholderES) window.__applySelectPlaceholderES();
}

// ——— Roles / permisos ———
function getRole() {
  var bool_user_role = $('#bool_user_role').val();
  var isPriv = !!(bool_user_role && bool_user_role.trim() !== '');
  if (!isPriv) {
    validateEstatus();
    var toDisable = [
      '#num_documento', '#num_copias', '#fecha_inicio', '#fecha_fin',
      '#num_flojas', '#num_tomos', '#asunto', '#remitente_nombre',
      '#remitente_apellido_paterno', '#remitente_apellido_materno', '#remitente_rfc',
      '#horas_respuesta', '#puesto_remitente', '#id_cat_remitente', '#folio_gestion',
      '#remitente', '#fecha_documento', '#idcheckboxTemplate', '#es_doc_fisico_box',
      '#son_mas_remitentes_box', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace',
      '#id_cat_unidad', '#id_cat_coordinacion', '#id_cat_tramite', '#id_cat_clave',
      '#id_cat_remitente', '#id_cat_entidad', '#id_cat_area_1', '#id_cat_area_2'
    ];
    toDisable.forEach(function (id) { $(id).prop('disabled', true); });
    [
      '#id_cat_entidad', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace',
      '#id_cat_unidad', '#id_cat_coordinacion', '#id_cat_tramite', '#id_cat_clave',
      '#id_cat_remitente', '#id_cat_area_1', '#id_cat_area_2'
    ].forEach(refreshSelect);
  }
}

function validateEstatus() {
  var $sel = $('#id_cat_estatus');
  var val  = Number($sel.val());
  if ([2,5,7].includes(val)) {
    $sel.prop('disabled', true);
  } else {
    $('#id_cat_estatus option[value="2"], #id_cat_estatus option[value="5"], #id_cat_estatus option[value="6"]').remove();
  }
  $sel.selectpicker('refresh');
}

// ——— Encabezado dinámico (Año / Clave) ———
function setData() {
  $('#_labFechaCaptura').text($('#fecha_captura').val());
  $('#_labNoCorrespondencia').text($('#num_turno_sistema').val());
  getData();
}
function getData() {
  var id_cat_anio  = $('#id_cat_anio').val();
  var id_cat_clave = $('#id_cat_clave_aux').val();
  $.post(URL_DEFAULT + '/letter/collection/dataClave', {
    id_cat_anio: id_cat_anio,
    id_cat_clave: id_cat_clave,
    _token: token
  }, function (response) {
    var item      = response.nameYear || {};
    var itemClave = response.dataClave || {};
    $('#_labAño').text(item.name || '');
    $('#_labClave').text(itemClave._labClave || '');
    $('#_labClaveCodigo').text(itemClave._labClaveCodigo || '');
    $('#_labClaveRedaccion').text(itemClave._labClaveRedaccion || '');
  });
}

// ——— Carga de archivos (UI) ———
function setCheckboxFiles() {
  var activo = !!$('#habilitar_carga').val();
  if (activo) {
    showDiv('contenedor_carga_archivos');
    enableFile('#file_oficio_entrada', '#label_oficio_entrada', '#icon_oficio_entrada');
    enableFile('#file_anexo_entrada',  '#label_anexo_entrada',  '#icon_anexo_entrada');
    $('#habilitar_carga_box').prop('checked', true);
  } else {
    hideDiv('contenedor_carga_archivos');
    resetFile('#file_oficio_entrada', '#label_oficio_entrada', '#icon_oficio_entrada');
    resetFile('#file_anexo_entrada',  '#label_anexo_entrada',  '#icon_anexo_entrada');
    $('#habilitar_carga_box').prop('checked', false);
  }
}
function enableFile(inputSel, labelSel, iconSel) {
  $(inputSel).prop('disabled', false);
  $(labelSel).css({ opacity: 1, cursor: 'pointer' });
  if (iconSel) { $(iconSel).css('opacity', 1); }
}
function resetFile(inputSel, labelSel, iconSel) {
  $(inputSel).val('').prop('disabled', true);
  $(labelSel).css({ opacity: 0.5, cursor: 'not-allowed' });
  if (iconSel) { $(iconSel).css('opacity', 0.5); }
  clearLocalFileError(inputSel);
}
function clearLocalFileError(inputSel) {
  var id  = inputSel.replace('#', '');
  var $er = $('#error_' + id);
  if ($er.length) {
    $er.hide().text('');
    $(inputSel).removeClass('is-invalid');
  }
}

// ——— INIT único ———
$(document).ready(function () {
  try { $('#carga_archivos, #carga_archivos_box, #id_checkbox_carga_archivos').off(); } catch(e) {}

  $('select').attr('data-none-selected-text', 'SELECCIONE').selectpicker();
  if (window.__applySelectPlaceholderES) window.__applySelectPlaceholderES();

  setData();
  getRole();
  setCheckboxArea();
  setCheckbox();
  setDateLimits();
  setCheckboxFiles();

  // Tooltips
  safeTooltip('#id_checkbox_Template_tooltip_fisico','Marcar si el documento es físico');
  safeTooltip('#id_checkbox_Template_tooltip','Añadir un remitente no registrado');
  safeTooltip('#mas_remitentes','Añadir dos o más remitentes');
  safeTooltip('#habilitar_carga_archivos','Mostrar/ocultar la sección de carga de Oficio y Anexos');

  // Validación visual de fechas
  $('#fecha_inicio, #fecha_fin, #fecha_documento').on('input change', function () {
    clearFieldError('#' + this.id);
  });

  // Validación en submit
  $('#myForm').on('submit', function (e) {
    if (!validarFechasAntesDeEnviar()) { e.preventDefault(); }
  });

  // Limpia error local al seleccionar archivos
  ['#file_oficio_entrada', '#file_anexo_entrada'].forEach(function (id) {
    $(id).on('change', function () { clearLocalFileError(id); });
  });

  // Sincroniza hidden ↔ checkbox de carga
  $('#habilitar_carga_box').on('change', function () {
    $('#habilitar_carga').val($(this).is(':checked') ? true : '');
    setCheckboxFiles();
  });

  // Checkboxes principales
  $('#es_doc_fisico_box').on('change', function () {
    $('#es_doc_fisico').val($(this).is(':checked') ? true : '');
  });
  $('#son_mas_remitentes_box').on('change', function () {
    $('#son_mas_remitentes').val($(this).is(':checked') ? true : '');
    setCheckbox();
  });
  $('#idcheckboxTemplate').on('change', function () {
    $('#rfc_remitente_bool').val($(this).is(':checked') ? true : '');
    setCheckboxArea();
  });
});
