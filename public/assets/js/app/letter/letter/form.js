/* =========================================================
   form.js — LÓGICA GENERAL DEL FORMULARIO
   ========================================================= */

var token = $('meta[name="csrf-token"]').attr('content');

/* ========================= Helpers UI ========================= */
function safeTooltip(selector, text) {
  if (typeof tooltip === 'function') { tooltip(selector, text); }
}
function refreshSelect(sel) {
  $(sel).attr('data-none-selected-text', 'SELECCIONE').selectpicker('refresh');
  if (window.__applySelectPlaceholderES) window.__applySelectPlaceholderES();
}
function showDiv(id)  { $('#'+id).show(); }
function hideDiv(id)  { $('#'+id).hide(); }
function cleanSelect(sel) { $(sel).val('').selectpicker('refresh'); }

/* ========================= Fechas ========================= */
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

/* ========================= Remitentes ========================= */
function setCheckboxArea() {
  // “Agregar remitente” (alta por RFC)
  var addRem = $('#rfc_remitente_bool').val() === '1' || $('#rfc_remitente_bool').val() === 'true';
  $('#idcheckboxTemplate').prop('checked', !!addRem);

  if (addRem) {
    showDiv('mostrar_ocultar_template');    // formulario de nombre/apellidos/RFC
    hideDiv('_hidden_select');              // oculta select de remitente
  } else {
    hideDiv('mostrar_ocultar_template');
    showDiv('_hidden_select');
  }

  // “Varios remitentes”
  var mas = $('#son_mas_remitentes').val() === '1' || $('#son_mas_remitentes').val() === 'true';
  if (mas) {
    showDiv('mostrar_ocultar_mas_remitentes');
    hideDiv('_hidden_select');
    hideDiv('mostrar_ocultar_template');
  } else {
    hideDiv('mostrar_ocultar_mas_remitentes');
  }
}

/* ========================= Archivos (UI) ========================= */
function updateOficioUI() {
  var $inp = $('#file_oficio_entrada');
  var files = $inp[0].files;
  var $empty = $('#container_oficio_entrada_vacio');
  var $cont  = $('#container_oficio_entrada');
  var $label = $('#label_oficio_entrada');
  var $icon  = $('#icon_oficio_entrada');

  $cont.empty();
  if (files && files.length > 0) {
    $empty.hide();
    var f = files[0];
    var pill = $('<div class="file-pills-row"><span class="file-pill"></span></div>');
    pill.find('.file-pill').text(f.name + ' (' + Math.ceil(f.size/1024) + ' KB)');
    $cont.append(pill);
    $label.text('Cambiar');
    $icon.removeClass('fa-arrow-up').addClass('fa-refresh');
    $('#msg_oficio_req').hide();
  } else {
    $empty.show();
    $label.text('Cargar');
    $icon.removeClass('fa-refresh').addClass('fa-arrow-up');
  }
}

function updateAnexosUI() {
  var $inp = $('#file_anexo_entrada');
  var files = $inp[0].files || [];
  var $empty = $('#container_anexo_entrada_vacio');
  var $cont  = $('#container_anexo_entrada');
  var $label = $('#label_anexo_entrada');
  var $icon  = $('#icon_anexo_entrada');

  $cont.empty();
  if (files.length > 0) {
    $empty.hide();
    var row = $('<div class="file-pills-row"></div>');
    Array.prototype.slice.call(files).forEach(function (f) {
      row.append('<span class="file-pill" title="'+ f.name +'">' +
                  f.name + ' (' + Math.ceil(f.size/1024) + ' KB)' +
                 '</span>');
    });
    $cont.append(row);
    $label.text('Cambiar');
    $icon.removeClass('fa-arrow-up').addClass('fa-refresh');
  } else {
    $empty.show();
    $label.text('Cargar');
    $icon.removeClass('fa-refresh').addClass('fa-arrow-up');
  }
}

/* ========================= Ready ========================= */
$(function () {
  setDateLimits();
  setCheckboxArea();

  // Toggle “Agregar remitente”
  $(document).on('change', '#idcheckboxTemplate', function () {
    var checked = $(this).is(':checked');
    $('#rfc_remitente_bool').val(checked ? '1' : '');
    setCheckboxArea();
    if (checked) cleanSelect('#id_cat_remitente');
  });

  // Toggle “Varios remitentes”
  $(document).on('change', 'input[name="son_mas_remitentes_box"]', function () {
    var checked = $(this).is(':checked');
    $('#son_mas_remitentes').val(checked ? '1' : '');
    setCheckboxArea();
    if (checked) {
      cleanSelect('#id_cat_remitente');
      $('#rfc_remitente_bool').val('');
      $('#idcheckboxTemplate').prop('checked', false);
    }
  });

  // Toggle “¿Adjuntar oficio y/o anexos?”
  $(document).on('change', 'input[name="habilitar_carga_box"]', function () {
    var checked = $(this).is(':checked');
    $('#habilitar_carga').val(checked ? '1' : '');
    if (checked) {
      $('#contenedor_carga_archivos').slideDown(150);
    } else {
      $('#contenedor_carga_archivos').slideUp(150);
      // limpiar UI
      $('#file_oficio_entrada').val('');
      $('#file_anexo_entrada').val('');
      updateOficioUI();
      updateAnexosUI();
    }
  });

  // Files
  $('#file_oficio_entrada').on('change', updateOficioUI);
  $('#file_anexo_entrada').on('change', updateAnexosUI);

  // Submit
  $('#myForm').on('submit', function (e) {
    if (!validarFechasAntesDeEnviar()) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return false;
    }

    // Si el usuario habilitó la carga de archivos, sugerimos que agregue el oficio
    // (el backend ya lo valida como obligatorio en CREATE).
    if ($('#habilitar_carga').val() === '1') {
      var files = ($('#file_oficio_entrada')[0].files || []).length;
      if (files === 0) {
        $('#msg_oficio_req').show();
        safeTooltip('#label_oficio_entrada', 'Hace falta cargar un oficio.');
        // no bloqueamos el submit si está editando; solo aviso visual
      }
    }
  });

  // Placeholder y refresh de selects por si llegan vacíos
  ['#id_cat_area_1','#id_cat_area_2','#id_cat_area','#id_cat_tramite',
   '#id_usuario_area','#id_usuario_enlace','#id_cat_unidad','#id_cat_coordinacion',
   '#id_cat_clave'
  ].forEach(refreshSelect);
});
