/* =========================================================
   form.js — LÓGICA GENERAL DEL FORMULARIO (versión consolidada)
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
function getForm$() { return $('#myForm').length ? $('#myForm') : $('form').first(); }

function ensureHidden(id, name) {
  var $form = getForm$();
  var $hid = $form.find('#' + id);
  if ($hid.length === 0) { $hid = $('<input type="hidden">').attr({ id: id, name: name }); $form.append($hid); }
  return $hid;
}

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

function freezeWithMirror(sel) {
  var $s = $(sel);
  if (!$s.length) return;
  var name = $s.attr('name');
  if (!name) return;

  var val = $s.val();
  if (!val) {
    var first = $s.find('option[value!=""]').first().val();
    if (first) {
      val = first; $s.val(first);
      if ($.fn.selectpicker) $s.selectpicker('refresh');
    }
  }
  var hidId = name + '__mirror';
  var $hid = ensureHidden(hidId, name);
  $hid.val(val || '');

  $s.prop('disabled', true);
  if ($.fn.selectpicker) $s.selectpicker('refresh');
}

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
  if (!selector) return;
  var $inp = $(selector);
  if (!$inp.length) return;

  if ($inp.next('.invalid-feedback').length === 0) {
    $inp.after('<div class="invalid-feedback"></div>');
  }
  $inp.addClass('is-invalid');
  $inp.next('.invalid-feedback').text(message || 'Campo requerido.').show();
}

function clearFieldError(selector) {
  if (!selector) return;
  var $inp = $(selector);
  if (!$inp.length) return;

  $inp.removeClass('is-invalid');
  var $fb = $inp.next('.invalid-feedback');
  if ($fb.length) $fb.hide().text('');
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
  var addRem = $('#rfc_remitente_bool').val() === '1' || $('#rfc_remitente_bool').val() === 'true';
  $('#idcheckboxTemplate').prop('checked', !!addRem);

  if (addRem) { showDiv('mostrar_ocultar_template'); hideDiv('_hidden_select'); }
  else        { hideDiv('mostrar_ocultar_template');  showDiv('_hidden_select'); }

  var mas = $('#son_mas_remitentes').val() === '1' || $('#son_mas_remitentes').val() === 'true';
  if (mas) { showDiv('mostrar_ocultar_mas_remitentes'); hideDiv('_hidden_select'); hideDiv('mostrar_ocultar_template'); }
  else     { hideDiv('mostrar_ocultar_mas_remitentes'); }
}

/* ========================= Archivos (UI) ========================= */
function updateOficioUI() {
  var $inp = $('#file_oficio_entrada');
  var files = ($inp[0] && $inp[0].files) ? $inp[0].files : [];
  var $empty = $('#container_oficio_entrada_vacio');
  var $cont  = $('#container_oficio_entrada');
  var $label = $('#label_oficio_entrada');
  var $icon  = $('#icon_oficio_entrada');

  $cont.empty();
  if (files.length > 0) {
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
  var files = ($inp[0] && $inp[0].files) ? $inp[0].files : [];
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
function setTurnarBlocked(on) {
  var sels = [
    '#id_cat_area_1', '#id_cat_area_2', '#id_cat_area',
    '#id_usuario_area', '#id_usuario_enlace',
    '#id_cat_unidad', '#id_cat_coordinacion',
    '#id_cat_tramite', '#id_cat_clave'
  ];
  if (on) { sels.forEach(ensureFirstIfEmpty); sels.forEach(freezeWithMirror); }
  else    { sels.forEach(unfreezeWithMirror); }
}

// TURNADO (id=1) por defecto si no es Returnado y el select está vacío
function setDefaultTurnado() {
  var $st = $('#id_cat_estatus');
  if (!$st.length) return;
  var inReturnado = $('#force_returnado').val() === '1';
  if (!inReturnado && !$st.val()) {
    $st.val('1');
    if ($.fn.selectpicker) $st.selectpicker('refresh');
  }
}

// API pública — llamada por deps-areas.js al detectar Returnado
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

    setDefaultTurnado();
  }
}

/* ========================= Encabezado dinámico (Año / Clave) ========================= */
function setDataHeaderLabelsFromHidden() {
  $('#_labFechaCaptura').text($('#fecha_captura').val());
  $('#_labNoCorrespondencia').text($('#num_turno_sistema').val());
}

function fetchAndPaintYearClave() {
  var id_cat_anio  = $('#id_cat_anio').val();
  var id_cat_clave = $('#id_cat_clave_aux').val();

  $.post(URL_DEFAULT + '/letter/collection/dataClave', {
    id_cat_anio: id_cat_anio,
    id_cat_clave: id_cat_clave,
    _token: token
  }, function (response) {
    var item      = response.nameYear || {};
    var itemClave = response.dataClave || {};

    $('#_labAño').text(item.name || $('#_labAño').text());
    $('#_labClave').text(itemClave._labClave || '');
    $('#_labClaveCodigo').text(itemClave._labClaveCodigo || '');
    $('#_labClaveRedaccion').text(itemClave._labClaveRedaccion || '');
  });
}

/* ========================= Encabezado de resumen (fallback de año) ========================= */
function fillHeaderSummary() {
  var anioText = ($('#id_cat_anio_text').val && $('#id_cat_anio_text').val()) || '';
  var noTurno = $('#num_turno_sistema').val() || '—';
  var fecha   = $('#fecha_captura').val()     || '—';

  var anioRaw  = anioText || $('#id_cat_anio').val() || '';
  var currentY = (new Date()).getFullYear().toString();
  var anio     = (/^\d{4}$/.test(anioRaw) ? anioRaw : currentY);

  var labNo   = document.getElementById('_labNoCorrespondencia');
  var labFec  = document.getElementById('_labFechaCaptura');
  var labAnio = document.getElementById('_labAño');

  if (labNo)   labNo.textContent   = noTurno;
  if (labFec)  labFec.textContent  = fecha;
  if (labAnio) labAnio.textContent = anio;
}

/* ========================= Notificaciones ========================= */
(function(){
  function ensureBannerHost(){
    var $host = $('#__notify_host');
    if ($host.length) return $host;
    $host = $('<div id="__notify_host" style="position:fixed; top:16px; right:16px; z-index:9999; display:flex; flex-direction:column; gap:8px;"></div>');
    $('body').append($host);
    return $host;
  }
  function banner(msg, type){
    var $host = ensureBannerHost();
    var bg = '#1f2937', bd = '#374151';
    if (type === 'ok')  { bg = '#065f46'; bd = '#059669'; }
    if (type === 'err') { bg = '#7f1d1d'; bd = '#ef4444'; }
    if (type === 'warn'){ bg = '#78350f'; bd = '#f59e0b'; }
    var $n = $('<div style="max-width:360px; padding:10px 12px; color:#fff; background:'+bg+'; border:1px solid '+bd+'; border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,.2); font-size:14px;"></div>').text(msg);
    $host.append($n);
    setTimeout(function(){ $n.fadeOut(200, function(){ $(this).remove(); }); }, 4000);
  }
  window.__notify = function(msg, kind){
    if (typeof notyfEM !== 'undefined') {
      if (kind === 'ok'   && notyfEM.success) return notyfEM.success(msg);
      if (kind === 'warn' && notyfEM.warning) return notyfEM.warning(msg);
      if (notyfEM.error) return notyfEM.error(msg);
    }
    if (typeof toastr !== 'undefined') {
      if (kind === 'ok')   return toastr.success(msg);
      if (kind === 'warn') return toastr.warning(msg);
      return toastr.error(msg);
    }
    banner(msg, kind);
  };
})();

/* ===== Toas ts persistentes ===== */
function rememberToast(kind, msg) { try { sessionStorage.setItem('__next_toast', JSON.stringify({k: kind, m: msg, ts: Date.now()})); } catch(_) {} }
function playToast(kind, msg) {
  if (typeof notyfEM !== 'undefined') {
    if (kind === 'ok'   && notyfEM.success) return notyfEM.success(msg);
    if (kind === 'warn' && notyfEM.warning) return notyfEM.warning(msg);
    if (notyfEM.error)  return notyfEM.error(msg);
  }
  return __notify(msg, kind);
}

/* ========================= Ready ========================= */
$(function () {
  setDateLimits();
  setCheckboxArea();
  setDefaultTurnado();

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
      $('#file_oficio_entrada').val('');
      $('#file_anexo_entrada').val('');
      updateOficioUI();
      updateAnexosUI();
    }
  });

  // Files
  $('#file_oficio_entrada').on('change', updateOficioUI);
  $('#file_anexo_entrada').on('change', updateAnexosUI);

  // Selects → placeholder/refresh
  [
    '#id_cat_area_1','#id_cat_area_2','#id_cat_area','#id_cat_tramite',
    '#id_usuario_area','#id_usuario_enlace','#id_cat_unidad','#id_cat_coordinacion',
    '#id_cat_clave','#id_cat_estatus'
  ].forEach(function(s){ if ($(s).length && $.fn.selectpicker) refreshSelect(s); });

  // Estado inicial: NO Returnado (hasta que deps-areas.js diga)
  applyReturnadoMode(false);

  // Encabezado: pinta inmediato y luego catálogos
  fillHeaderSummary();
  setDataHeaderLabelsFromHidden();
  fetchAndPaintYearClave();

  // Reproducir toast persistente solo fuera de “alta”
  try {
    var raw = sessionStorage.getItem('__next_toast');
    if (raw) {
      var isCreatePage = (function(){
        var $f = $('#myForm'); if (!$f.length) return false;
        var $id = $f.find('[name="id_tbl_correspondencia"]');
        if (!$id.length) return false;
        return (($id.val() || '').toString().trim() === '');
      })();
      if (isCreatePage) { sessionStorage.removeItem('__next_toast'); }
      else {
        sessionStorage.removeItem('__next_toast');
        var t = JSON.parse(raw || '{}');
        if (t && t.m) playToast(t.k || 'ok', t.m);
      }
    }
  } catch(_) {}
});

/* ========================= Submit AJAX ========================= */
(function () {
  var $form = $('#myForm');
  if (!$form.length) return;

  function spinnerOn()  {
    if (typeof showSpinner === 'function') return showSpinner();
    var $ov = $('#savingOverlay, #spinnerOverlay, #loadingScreen, .saving-overlay');
    if ($ov.length) $ov.show();
  }
  function spinnerOff() {
    if (typeof hideSpinner === 'function') return hideSpinner();
    var $ov = $('#savingOverlay, #spinnerOverlay, #loadingScreen, .saving-overlay');
    if ($ov.length) $ov.hide();
  }

  $(document).on('input change', 'input,select,textarea', function () {
    if (this && this.id) clearFieldError('#' + this.id);
  });

  function clearAllFieldErrors() {
    $form.find('input[id],select[id],textarea[id]').each(function () {
      clearFieldError('#' + this.id);
    });
  }

  var FIELD_SEL = {
    'num_documento':'[name="num_documento"]',
    'folio_gestion':'[name="folio_gestion"]',
    'fecha_documento':'[name="fecha_documento"]',
    'fecha_inicio':'[name="fecha_inicio"]',
    'fecha_fin':'[name="fecha_fin"]',
    'id_cat_entidad':'[name="id_cat_entidad"]',
    'horas_respuesta':'[name="horas_respuesta"]',
    'asunto':'[name="asunto"]',
    'observaciones':'[name="observaciones"]',
    'id_cat_area_1':'[name="id_cat_area_1"]',
    'id_cat_area_2':'[name="id_cat_area_2"]',
    'id_cat_area':'[name="id_cat_area"]',
    'id_usuario_area':'[name="id_usuario_area"]',
    'id_usuario_enlace':'[name="id_usuario_enlace"]',
    'id_cat_unidad':'[name="id_cat_unidad"]',
    'id_cat_coordinacion':'[name="id_cat_coordinacion"]',
    'id_cat_tramite':'[name="id_cat_tramite"]',
    'id_cat_clave':'[name="id_cat_clave"]',
    'id_cat_estatus':'[name="id_cat_estatus"]',
    'id_cat_remitente':'[name="id_cat_remitente"]',
    'file_oficio_entrada':'#file_oficio_entrada',
    'file_anexo_entrada':'#file_anexo_entrada'
  };
  function findInputForErrorKey(key) {
    var base = key.replace(/\.\d+$/,'');
    var sel = FIELD_SEL[key] || FIELD_SEL[base] || ('[name="'+base+'"]');
    var $el = $form.find(sel);
    if ($el.length) return $el;
    $el = $form.find('[name="'+base+'[]"]');
    return $el.length ? $el : $();
  }

  var submitting = false;

  $form.off('submit.minAjax').on('submit.minAjax', function (e) {
    if (!validarFechasAntesDeEnviar()) { e.preventDefault(); e.stopImmediatePropagation(); return false; }

    e.preventDefault(); e.stopImmediatePropagation();
    if (submitting) return false;
    submitting = true;

    clearAllFieldErrors();

    var fd = new FormData(this);
    var action = this.action;
    var $btn = $form.find('button[type="submit"], .btn-submit');

    spinnerOn();
    $(document).trigger('form:save:start', [$form[0]]);
    $btn.prop('disabled', true).addClass('disabled');

    fetch(action, {
      method: 'POST',
      body: fd,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(async function (res) {
      const ct = (res.headers.get('content-type') || '').toLowerCase();
      const isJson = ct.indexOf('application/json') !== -1;

      // 1) Redirect inmediato (Laravel redirect)
      if (res.redirected && res.url) {
        rememberToast('ok','El registro se realizó de forma exitosa.');
        $(document).trigger('form:save:success', [$form[0], 'redirect']);
        window.location.href = res.url;
        return;
      }

      // 2) Validaciones 422 (JSON con errors)
      if (res.status === 422) {
        let data = {};
        try { data = await res.json(); } catch(_) {}
        var errors = (data && data.errors) ? data.errors : {};
        var firstFocused = false;
        Object.keys(errors).forEach(function (k) {
          var msg = errors[k] && errors[k][0] ? errors[k][0] : 'Campo requerido.';
          var $el = findInputForErrorKey(k);
          if ($el.length) {
            var sel = $el.attr('id') ? ('#' + $el.attr('id')) : $el;
            showFieldError(sel, msg);
            if (!firstFocused) { try { $el[0].focus(); } catch(_){} firstFocused = true; }
          }
        });
        $(document).trigger('form:save:error', [$form[0], 422]);
        return;
      }

      // 3) Éxito sin redirect (200/201 + JSON/HTML)
      if (res.ok) {
        let okMsg = 'El registro se realizó de forma exitosa.';
        if (isJson) {
          try {
            const data = await res.clone().json();
            if (data && data.message) okMsg = data.message;
          } catch(_) {}
        }
        playToast('ok', okMsg);
        rememberToast('ok', okMsg);
        $(document).trigger('form:save:success', [$form[0], 'ok']);
        setTimeout(function(){ window.location.reload(); }, 600);
        return;
      }

      // 4) Duplicado explícito (409) o respuesta JSON con folio_gestion
      if (res.status === 409) {
        var $fg = $form.find('[name="folio_gestion"]');
        if ($fg.length) {
          var selFG = $fg.attr('id') ? ('#' + $fg.attr('id')) : $fg;
          showFieldError(selFG, 'El folio de gestión ya existe.');
          try { $fg[0].focus(); } catch (_) {}
        }
        __notify('El folio de gestión ya existe.', 'warn');
        $(document).trigger('form:save:error', [$form[0], 409]);
        return;
      }
      if (isJson) {
        try {
          const data = await res.clone().json();
          if (data && data.errors && data.errors.folio_gestion) {
            var $fg2 = $form.find('[name="folio_gestion"]');
            if ($fg2.length) {
              var selFG2 = $fg2.attr('id') ? ('#' + $fg2.attr('id')) : $fg2;
              showFieldError(selFG2, data.errors.folio_gestion[0] || 'El folio de gestión ya existe.');
              try { $fg2[0].focus(); } catch (_) {}
            }
            __notify('El folio de gestión ya existe.', 'warn');
            $(document).trigger('form:save:error', [$form[0], res.status || 409]);
            return;
          }
        } catch(_) {}
      }

      // 5) Otros errores (500/403/etc.)
      try { console.error('[SAVE_ERROR]', res.status, await res.text()); } catch(_) {}
      $(document).trigger('form:save:error', [$form[0], res.status]);
      __notify('No se pudo completar la acción. Por favor, vuelve a intentarlo.', 'err');
    })
    .catch(function (err) {
      console.error('[NETWORK]', err);
      $(document).trigger('form:save:error', [$form[0], 0]);
      __notify('Error de red. Intenta nuevamente.', 'err');
    })
    .finally(function () {
      submitting = false;
      spinnerOff();
      $btn.prop('disabled', false).removeClass('disabled');
      $(document).trigger('form:save:finish', [$form[0]]);
    });

    return false;
  });
})();

/* ========================= Encabezado (llamadas) ========================= */
// Toast inmediato si el folio ya está registrado
$(document).on('blur', '[name="folio_gestion"]', function () {
  var $fg   = $(this);
  var value = ($fg.val() || '').trim();
  if (!value) return;

  var id = ($('[name="id_tbl_correspondencia"]').val() || '').trim();

  $.post(URL_DEFAULT + '/letter/validateUnique', {
    _token: token,
    type: 'folio',
    id: id,
    value: value
  })
  .done(function (res) {
    if (res && res.ok && res.exists) {
      __notify('El folio de gestión ya existe.', 'warn');
    }
  })
  .fail(function(){ /* silencioso */ });
});
