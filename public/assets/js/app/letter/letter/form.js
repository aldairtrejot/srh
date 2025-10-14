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

/* ========================= Helpers FORM & Mirrors ========================= */
// Obtiene el formulario correcto (por defecto #myForm)
function getForm$() {
  return $('#myForm').length ? $('#myForm') : $('form').first();
}

// Crea/recupera un input hidden dentro del form
function ensureHidden(id, name) {
  var $form = getForm$();
  var $hid = $form.find('#' + id);
  if ($hid.length === 0) {
    $hid = $('<input type="hidden">').attr({ id: id, name: name });
    $form.append($hid);
  }
  return $hid;
}

// Si el select está vacío pero hay opciones, toma la primera no-vacía
function ensureFirstIfEmpty(sel) {
  var $s = $(sel);
  if (!$s.length) return;
  if (!$s.val()) {
    var v = $s.find('option[value!=""]').first().val();
    if (v) {
      $s.val(v);
      if ($.fn.selectpicker) $s.selectpicker('refresh');
      $s.trigger('change');
    }
  }
}

// Congela un select y crea su “espejo” hidden para que viaje en el POST
function freezeWithMirror(sel) {
  var $s = $(sel);
  if (!$s.length) return;
  var name = $s.attr('name');
  if (!name) return;

  // Garantiza que tenga algún valor razonable
  var val = $s.val();
  if (!val) {
    var first = $s.find('option[value!=""]').first().val();
    if (first) {
      val = first;
      $s.val(first);
      if ($.fn.selectpicker) $s.selectpicker('refresh');
    }
  }

  var hidId = name + '__mirror';
  var $hid = ensureHidden(hidId, name);
  $hid.val(val || '');

  $s.prop('disabled', true);
  if ($.fn.selectpicker) $s.selectpicker('refresh');
}

// Descongela y elimina el espejo
function unfreezeWithMirror(sel) {
  var $s = $(sel);
  if (!$s.length) return;
  var name = $s.attr('name');
  if (!name) return;

  var hidId = name + '__mirror';
  getForm$().find('#' + hidId).remove();

  $s.prop('disabled', false);
  if ($.fn.selectpicker) $s.selectpicker('refresh');
}

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

/* ========================= Bloqueo Turnar A & Returnado ========================= */
// Congela/descongela todo el bloque "Turnar A" con espejos para el POST
function setTurnarBlocked(on) {
  var sels = [
    '#id_cat_area_1', '#id_cat_area_2', '#id_cat_area',
    '#id_usuario_area', '#id_usuario_enlace',
    '#id_cat_unidad', '#id_cat_coordinacion',
    '#id_cat_tramite', '#id_cat_clave'
  ];

  if (on) {
    // Evita que algo viaje vacío al backend
    sels.forEach(ensureFirstIfEmpty);
    sels.forEach(freezeWithMirror);
  } else {
    sels.forEach(unfreezeWithMirror);
  }
}

// API pública que usa deps-areas.js al detectar Returnado
function applyReturnadoMode(on, idReturnado) {
  var $form = getForm$();
  var $force = $form.find('#force_returnado');
  if ($force.length === 0) {
    $force = $('<input type="hidden" id="force_returnado" name="force_returnado">').appendTo($form);
  }

  var $st = $('#id_cat_estatus');
  if (on) {
    var idRet = String(idReturnado || 8);
    $st.val(idRet);
    freezeWithMirror('#id_cat_estatus');
    $force.val('1');

    setTurnarBlocked(true);

    $st.prop('disabled', true);
    if ($.fn.selectpicker) $st.selectpicker('refresh');
  } else {
    $force.val('');
    setTurnarBlocked(false);

    unfreezeWithMirror('#id_cat_estatus');
    $st.prop('disabled', false);
    if ($.fn.selectpicker) $st.selectpicker('refresh');
  }
}

/* ========================= Encabezado dinámico (Año / Clave) ========================= */
// Pinta fecha y turno rápidos desde los hidden y luego resuelve año/clave desde backend
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

    // nameYear.name debe traer "2025", etc.
    $('#_labAño').text(item.name || $('#_labAño').text());
    $('#_labClave').text(itemClave._labClave || '');
    $('#_labClaveCodigo').text(itemClave._labClaveCodigo || '');
    $('#_labClaveRedaccion').text(itemClave._labClaveRedaccion || '');
  });
}

/* ========================= Encabezado de resumen ========================= */
/** Copia los valores de los hidden a los labels del encabezado.
 *  Si el "año" no es de 4 dígitos (p.ej. "2"), usa el año actual como fallback.
 */
function fillHeaderSummary() {
  var anioText = ($('#id_cat_anio_text').val && $('#id_cat_anio_text').val()) || '';

  var noTurno = $('#num_turno_sistema').val() || '—';
  var fecha   = $('#fecha_captura').val()     || '—';

  var anioRaw  = anioText || $('#id_cat_anio').val() || '';
  var currentY = (new Date()).getFullYear().toString();
  var anio     = (/^\d{4}$/.test(anioRaw) ? anioRaw : currentY);  // si viene "2", usa el año actual

  var labNo   = document.getElementById('_labNoCorrespondencia');
  var labFec  = document.getElementById('_labFechaCaptura');
  var labAnio = document.getElementById('_labAño'); // usar getElementById por el caracter ñ

  if (labNo)   labNo.textContent   = noTurno;
  if (labFec)  labFec.textContent  = fecha;
  if (labAnio) labAnio.textContent = anio;
}

/* ========================= Ready ========================= */
$(function () {
  setDateLimits();
  setCheckboxArea();

  // Tooltips
  safeTooltip('#id_checkbox_Template_tooltip_fisico','Marcar si el documento es físico');
  safeTooltip('#id_checkbox_Template_tooltip','Añadir un remitente no registrado');
  safeTooltip('#mas_remitentes','Añadir dos o más remitentes');
  safeTooltip('#habilitar_carga_archivos','Mostrar/ocultar la sección de carga de Oficio y Anexos');

  // Limpiar errores de fechas al escribir/cambiar
  $('#fecha_inicio, #fecha_fin, #fecha_documento').on('input change', function () {
    clearFieldError('#' + this.id);
  });

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

  // Submit: validaciones y sincronización de espejos
  $('#myForm').on('submit', function (e) {
    // 🔴 Asegurar que Área (id_cat_area) no vaya vacío
    ensureFirstIfEmpty('#id_cat_area');

    if (!validarFechasAntesDeEnviar()) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return false;
    }

    // Si está activo el modo Returnado, resincroniza mirrors por seguridad
    if ($('#force_returnado').val() === '1') {
      [
        '#id_cat_area_1', '#id_cat_area_2', '#id_cat_area',
        '#id_usuario_area', '#id_usuario_enlace',
        '#id_cat_unidad', '#id_cat_coordinacion',
        '#id_cat_tramite', '#id_cat_clave', '#id_cat_estatus'
      ].forEach(function(sel){
        var $s = $(sel);
        if ($s.length) {
          var name = $s.attr('name');
          if (name) {
            var hidId = name + '__mirror';
            var $hid = ensureHidden(hidId, name);
            $hid.val($s.val() || '');
          }
        }
      });
    }

    // Sugerencia visual si habilitó carga y no adjuntó oficio (no bloquea)
    if ($('#habilitar_carga').val() === '1') {
      var files = ($('#file_oficio_entrada')[0].files || []).length;
      if (files === 0) {
        $('#msg_oficio_req').show();
        safeTooltip('#label_oficio_entrada', 'Hace falta cargar un oficio.');
      }
    }
  });

  // Placeholder y refresh de selects por si llegan vacíos
  [
    '#id_cat_area_1','#id_cat_area_2','#id_cat_area','#id_cat_tramite',
    '#id_usuario_area','#id_usuario_enlace','#id_cat_unidad','#id_cat_coordinacion',
    '#id_cat_clave'
  ].forEach(refreshSelect);

  // Estado inicial: NO bloqueado (hasta que deps-areas.js llame applyReturnadoMode(true))
  applyReturnadoMode(false);

  // >>> Encabezado: pinta algo inmediato y luego resuelve el nombre real del año
  fillHeaderSummary(); // si id_cat_anio trae "2", mostramos año actual mientras
  setData();           // sobreescribe con el texto correcto del catálogo (p.ej. "2025")
});
