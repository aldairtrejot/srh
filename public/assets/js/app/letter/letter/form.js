/* =========================
 * form.js — FINAL (CORREGIDO)
 * ========================= */

// CSRF
var token = $('meta[name="csrf-token"]').attr('content') || (window.CSRF_TOKEN || '');

// LÍMITE DE TAMAÑO PARA ARCHIVOS (20 MB)
const MAX_FILE_SIZE_BYTES = 20 * 1024 * 1024;

/* =========================
 * INIT
 * ========================= */
$(document).ready(function () {
    // Evita doble inicialización si el script se carga 2 veces
    if (window.__FORM_JS_INIT__) return;
    window.__FORM_JS_INIT__ = true;

    $('select').selectpicker();
    setData();
    getRole();
    setCheckboxArea();
    setCheckbox();
    setDateLimits(); // límites visuales a las fechas

    tooltip('#id_checkbox_Template_tooltip_fisico', 'Marcar si el documento es físico');
    tooltip('#id_checkbox_Template_tooltip', 'Añadir un remitente no registrado');
    tooltip('#mas_remitentes', 'Añadir dos o más remitentes');

    // limpiar feedback en fechas
    $('#fecha_inicio, #fecha_fin, #fecha_documento').on('input change', function () {
        clearFieldError('#' + this.id);
    });

    // limpiar feedback en obligatorios adicionales
    $('#asunto, #puesto_remitente').on('input change', function () {
        clearFieldError('#' + this.id);
    });

    // limpiar feedback en selects
    $('#id_cat_entidad, #id_cat_area_1, #id_cat_tramite, #id_cat_clave, #id_cat_clave_aux')
      .on('changed.bs.select change', function () {
        clearFieldError('#' + this.id);
      });

    // === PATCH: Config Asunto (máximo 300) y Observaciones (máximo 250) ===
    const ASUNTO_MAX = 400;
    const OBS_MAX    = 350;

    // Atributos maxlength en HTML
    $('#asunto').attr('maxlength', ASUNTO_MAX);
    $('#observaciones').attr('maxlength', OBS_MAX);

    // (Opcional) contador si existe <small id="asunto_counter"></small>
    function updateAsuntoCounter() {
      if ($('#asunto_counter').length) {
        const left = ASUNTO_MAX - ($('#asunto').val() || '').length;
        $('#asunto_counter').text(left);
      }
    }
    updateAsuntoCounter();

    let asuntoToastGuard = false; // evita spam del toast
    $('#asunto').on('input', function () {
      const val = $(this).val() || '';
      if (val.length > ASUNTO_MAX) {
        $(this).val(val.slice(0, ASUNTO_MAX));
      }
      updateAsuntoCounter();

      if ((val.length >= ASUNTO_MAX) && !asuntoToastGuard) {
        asuntoToastGuard = true;
        toastError('Máximo 400 caracteres para Asunto.');
        setTimeout(() => asuntoToastGuard = false, 1500);
      }
    });

    let obsToastGuard = false; // evita spam del toast
    $('#observaciones').on('input', function () {
      const val = $(this).val() || '';
      if (val.length > OBS_MAX) {
        $(this).val(val.slice(0, OBS_MAX));
      }
      if ((val.length >= OBS_MAX) && !obsToastGuard) {
        obsToastGuard = true;
        toastError('Máximo 350 caracteres para Observaciones.');
        setTimeout(() => obsToastGuard = false, 1500);
      }
    });
    // === /PATCH: Asunto & Observaciones ===

    // << PATCH: toast de éxito si el backend deja alguna "señal" al volver de guardar >>
    // Toast de éxito si backend deja señal
    try {
        var qs = new URLSearchParams(window.location.search);
        var metaSaved = $('meta[name="x-letter-saved"]').attr('content');
        if ((window.LETTER && window.LETTER.saveOk === true) ||
            metaSaved === '1' ||
            qs.get('saved') === '1' || qs.get('ok') === '1' || qs.get('saved_ok') === '1') {
            toastSuccess('Registro guardado con éxito.');
        }
    } catch (_){}

    // Submit
    $('#formulario').on('submit', function (e) {
        // Limpia memoria de toasts para este intento
        if (window.__TOAST_KEYS__) window.__TOAST_KEYS__.clear();

        if (!validarFechasAntesDeEnviar()) {
            e.preventDefault();
            return;
        }

        if (typeof validarObligatoriosMinimos === 'function' && !validarObligatoriosMinimos()) {
            e.preventDefault();
            if (typeof hideSpinner === 'function') hideSpinner();
            return;
        }

        if (typeof ensureUsuarioAreaFallback === 'function') ensureUsuarioAreaFallback();

        if (typeof showSpinner === 'function') showSpinner();
    });

    // =========================
    // VALIDACIÓN INMEDIATA DE TAMAÑO DE ARCHIVOS (20 MB)
    // =========================
    $('#file_oficio_entrada').on('change', function () {
        var files = this.files || [];
        if (files.length > 0 && files[0].size > MAX_FILE_SIZE_BYTES) {
            toastError('El archivo de oficio supera el tamaño máximo permitido (20 MB).');
            // limpiar input y UI
            this.value = '';
            updateOficioUI();
            return;
        }
        // si es válido, solo actualizamos la UI
        updateOficioUI();
    });

    $('#file_anexo_entrada').on('change', function () {
        var files = this.files || [];
        for (var i = 0; i < files.length; i++) {
            if (files[i].size > MAX_FILE_SIZE_BYTES) {
                toastError('Uno de los anexos supera el tamaño máximo permitido (20 MB).');
                // limpiar input y UI
                this.value = '';
                updateAnexosUI();
                return;
            }
        }
        // si todos son válidos, solo actualizamos la UI
        updateAnexosUI();
    });
});

/* =========================
 * Guard de toasts (anti-duplicados) + helpers
 * ========================= */
window.__TOAST_KEYS__ = window.__TOAST_KEYS__ || new Set();

function toastOnce(type, key, message) {
    if (key && window.__TOAST_KEYS__.has(key)) return;
    if (key) window.__TOAST_KEYS__.add(key);
    try {
        if (type === 'error') {
            if (window.notyfEM?.error) {
                window.notyfEM.error(message);
            } else {
                alert(message);
            }
        } else {
            if (window.notyfEM?.success) {
                window.notyfEM.success(message);
            } else {
                alert(message);
            }
        }
    } catch(err) {
        console.error('Error al mostrar toast:', err);
        alert(message);
    }
}

// Versiones simples sin el sistema de keys (para toasts repetitivos como input)
function toastError(message, key) {
    // Si se proporciona una key, usar el sistema anti-duplicados
    if (key) {
        toastOnce('error', key, message);
    } else {
        // Sin key, mostrar directamente (para eventos input repetitivos)
        try {
            if (window.notyfEM?.error) {
                window.notyfEM.error(message);
            } else {
                alert(message);
            }
        } catch(err) {
            console.error('Error al mostrar toast:', err);
            alert(message);
        }
    }
}

function toastSuccess(message, key) {
    // Si se proporciona una key, usar el sistema anti-duplicados
    if (key) {
        toastOnce('success', key, message);
    } else {
        // Sin key, mostrar directamente
        try {
            if (window.notyfEM?.success) {
                window.notyfEM.success(message);
            } else {
                alert(message);
            }
        } catch(err) {
            console.error('Error al mostrar toast:', err);
            alert(message);
        }
    }
}

/* =========================
 * LÓGICA FECHAS
 * ========================= */
function setDateLimits() {
    const today = new Date();
    const maxDate = new Date(today);
    const minDate = new Date('2020-01-01');
    maxDate.setMonth(maxDate.getMonth() + 3);

    const minStr = minDate.toISOString().split('T')[0];
    const maxStr = maxDate.toISOString().split('T')[0];

    ['#fecha_inicio', '#fecha_fin', '#fecha_documento'].forEach(id => {
        $(id).attr('min', minStr);
        $(id).attr('max', maxStr);
        const val = $(id).val();
        if (val && (val < minStr || val > maxStr)) {
            $(id).val('');
        }
    });
}

function validarFechasAntesDeEnviar() {
    clearFieldError('#fecha_inicio');
    clearFieldError('#fecha_fin');

    const fechaInicio = $('#fecha_inicio').val();
    const fechaFin = $('#fecha_fin').val();

    const today = new Date();
    const maxDate = new Date(today);
    const minDate = new Date('2020-01-01');
    maxDate.setMonth(maxDate.getMonth() + 3);

    const limitStr = maxDate.toISOString().split('T')[0];
    const minStr = minDate.toISOString().split('T')[0];

    let ok = true;

    if (!fechaInicio) { showFieldError('#fecha_inicio', 'Por favor, ingresa la fecha de inicio.'); ok = false; }
    if (!fechaFin)    { showFieldError('#fecha_fin',    'Por favor, ingresa la fecha de fin.');    ok = false; }
    if (!ok) return false;

    const fi = new Date(fechaInicio);
    const ff = new Date(fechaFin);

    if (fi < minDate) { showFieldError('#fecha_inicio', 'La fecha de inicio no puede ser menor a ' + minStr + '.'); ok = false; }
    if (ff < minDate) { showFieldError('#fecha_fin',    'La fecha de fin no puede ser menor a ' + minStr + '.');    ok = false; }
    if (ff < fi)      { showFieldError('#fecha_fin',    'La fecha de fin no puede ser menor a la fecha de inicio.'); ok = false; }
    if (fi > maxDate) { showFieldError('#fecha_inicio', 'La fecha de inicio no puede ser mayor a ' + limitStr + '.'); ok = false; }
    if (ff > maxDate) { showFieldError('#fecha_fin',    'La fecha de fin no puede ser mayor a ' + limitStr + '.');    ok = false; }

    return ok;
}

/* =========================
 * Validación UI (sin bordes rojos)
 * ========================= */
function showFieldError(selector, message) {
    // Solo mostramos toast único por selector (clave = selector)
    toastError(message, selector);
}

function clearFieldError(selector) {
    const $inp = $(selector);
    $inp.removeClass('is-invalid');
    $inp.next('.invalid-feedback').hide().text('');
}

/* =========================
 * Helpers mínimos + Obligatorios
 * ========================= */
function _isMissing($el) {
    if (!$el || $el.length === 0) return false;        // si no existe, no bloquea
    if ($el.prop && $el.prop('disabled')) return false;// deshabilitado no participa
    let v = ($el.val != null) ? $el.val() : null;

    if (Array.isArray(v)) return v.length === 0 || v[0] === '' || v[0] === '0' || v[0] === 0;
    if (v === null || v === undefined) return true;
    if (typeof v === 'number') return v === 0;
    if (typeof v === 'string') return v.trim() === '' || v === '0';
    return false;
}

function validarObligatoriosMinimos() {
    let ok = true;

    // Detecta si es edición por la presencia del id del registro
    const isEdit = !!($('#id_tbl_correspondencia').val());

    // ENTIDAD
    (function(){
        const $el = $('#id_cat_entidad');
        if (_isMissing($el)) {
            showFieldError('#id_cat_entidad', 'Selecciona la Entidad.');
            ok = false;
        } else {
            clearFieldError('#id_cat_entidad');
        }
    })();

    // ASUNTO
    (function(){
        const $el = $('#asunto');
        if ($el.length && !$el.prop('disabled')) {
            const v = ($el.val() || '').trim();
            if (v === '') {
                showFieldError('#asunto', 'Ingresa el Asunto.');
                ok = false;
            } else if (v.length > 300) { // alineado con ASUNTO_MAX
                showFieldError('#asunto', 'El Asunto no puede exceder 300 caracteres.');
                ok = false;
            } else {
                clearFieldError('#asunto');
            }
        }
    })();

    // Observaciones (opcional, pero si trae texto, tope 250)
    (function(){
        const $el = $('#observaciones');
        if ($el.length && !$el.prop('disabled')) {
            const v = ($el.val() || '');
            if (v && v.length > 250) { // alineado con OBS_MAX
                showFieldError('#observaciones', 'Observaciones no puede exceder 250 caracteres.');
                ok = false;
            } else {
                clearFieldError('#observaciones');
            }
        }
    })();

    // Área 1
    // ÁREA 1 — SOLO EN CREATE
    (function(){
        if (!isEdit) {                           // ← clave: no validar en edición
            const $el = $('#id_cat_area_1');
            if (_isMissing($el)) {
                showFieldError('#id_cat_area_1', 'Selecciona Área 1.');
                ok = false;
            } else {
                clearFieldError('#id_cat_area_1');
            }
        } else {
            clearFieldError('#id_cat_area_1');   // en edición, nunca bloquea
        }
    })();

    // TRÁMITE
    (function(){
        const $el = $('#id_cat_tramite');
        if (_isMissing($el)) {
            showFieldError('#id_cat_tramite', 'Selecciona el Trámite.');
            ok = false;
        } else {
            clearFieldError('#id_cat_tramite');
        }
    })();

    // CLAVE o AUX
    (function(){
        let $el = $('#id_cat_clave');
        if ($el.length === 0) $el = $('#id_cat_clave_aux');
        if (_isMissing($el)) {
            showFieldError('#' + $el.attr('id'), 'Selecciona la Clave.');
            ok = false;
        } else {
            clearFieldError('#' + $el.attr('id'));
        }
    })();

    // PUESTO REMITENTE
    (function(){
        const $el = $('#puesto_remitente');
        if ($el.length && !$el.prop('disabled')) {
            const v = ($el.val() || '').trim();
            if (v === '') {
                showFieldError('#puesto_remitente', 'Ingresa el Puesto del remitente.');
                ok = false;
            } else {
                clearFieldError('#puesto_remitente');
            }
        }
    })();

    return ok;
}

/* =========================
 * Fallback silencioso para id_usuario_area
 * ========================= */
function ensureUsuarioAreaFallback() {
    var $ua = $('#id_usuario_area');
    if ($ua.length && !$ua.prop('disabled')) {
        var val = $ua.val();
        if (!val) {
            var fallback = $ua.data('default')
                        || ($('#id_usuario_area_default').length ? $('#id_usuario_area_default').val() : null)
                        || ($('#id_usuario_captura').length ? $('#id_usuario_captura').val() : null)
                        || ($('#id_usuario_sistema').length ? $('#id_usuario_sistema').val() : null);
            if (fallback) {
                $ua.val(String(fallback)).trigger('change');
            }
        }
    }
}

/* =========================
 * FUNCIONES EXISTENTES (checkboxes / roles / data)
 * ========================= */
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
}

function setCheckbox() {
    let es_doc_fisico = $('#es_doc_fisico').val();
    let son_mas_remitentes = $('#son_mas_remitentes').val();

    $('#es_doc_fisico_box').prop('checked', !!es_doc_fisico);
    $('#son_mas_remitentes_box').prop('checked', !!son_mas_remitentes);
    setValueOfMoreRem();
}

function setValueOfMoreRem() {
    let son_mas_remitentes = $('#son_mas_remitentes').val();
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
}

$('#es_doc_fisico_box').change(function () {
    $('#es_doc_fisico').val($(this).is(':checked') ? true : '');
});

$('#son_mas_remitentes_box').change(function () {
    $('#son_mas_remitentes').val($(this).is(':checked') ? true : '');
    setCheckbox();
});

$('#idcheckboxTemplate').change(function () {
    $('#rfc_remitente_bool').val($(this).is(':checked') ? true : '');
    setCheckboxArea();
});

function getRole() {
    let bool_user_role = $('#bool_user_role').val();
    let new_variable = bool_user_role && bool_user_role.trim() !== '';
    if (!new_variable) {
        validateEstatus();
        const toDisable = [
            '#num_documento', '#num_copias', '#fecha_inicio', '#fecha_fin',
            '#num_flojas', '#num_tomos', '#asunto', '#remitente_nombre',
            '#remitente_apellido_paterno', '#remitente_apellido_materno', '#remitente_rfc',
            '#horas_respuesta', '#puesto_remitente', '#id_cat_remitente', '#folio_gestion',
            '#remitente', '#fecha_documento', '#idcheckboxTemplate', '#es_doc_fisico_box',
            '#son_mas_remitentes_box', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace',
            '#id_cat_unidad', '#id_cat_coordinacion', '#id_cat_tramite', '#id_cat_clave',
            '#id_cat_remitente', '#id_cat_entidad', '#id_cat_area_1', '#id_cat_area_2'
        ];
        toDisable.forEach(id => $(id).prop('disabled', true));
        ['#id_cat_entidad', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace',
            '#id_cat_unidad', '#id_cat_coordinacion', '#id_cat_tramite', '#id_cat_clave', '#id_cat_area_1', '#id_cat_area_2',
            '#id_cat_remitente'].forEach(id => $(id).selectpicker('refresh'));
    }
}

function validateEstatus() {
    if ([2, 5].includes(Number($('#id_cat_estatus').val()))) {
        $('#id_cat_estatus').prop('disabled', true).selectpicker('refresh');
    } else {
        $('#id_cat_estatus option[value="2"], #id_cat_estatus option[value="5"], #id_cat_estatus option[value="6"],#id_cat_estatus option[value="8"]').remove();
        $('#id_cat_estatus').selectpicker('refresh');
    }
}

function setData() {
    $('#_labFechaCaptura').text($('#fecha_captura').val());
    $('#_labNoCorrespondencia').text($('#num_turno_sistema').val());
    getData();
}

function getData() {
    const id_cat_anio = $('#id_cat_anio').val();
    const id_cat_clave = $('#id_cat_clave_aux').val();
    $.post(URL_DEFAULT + '/letter/collection/dataClave', {
        id_cat_anio, id_cat_clave, _token: token
    }, function (response) {
        let item = response.nameYear;
        let itemClave = response.dataClave;
        $('#_labAño').text(item.name);
        $('#_labClave').text(itemClave._labClave);
        $('#_labClaveCodigo').text(itemClave._labClaveCodigo);
        $('#_labClaveRedaccion').text(itemClave._labClaveRedaccion);
    });
}

/* =========================
 * UI de archivos (oficio / anexos)
 * ========================= */

// Oficio
function updateOficioUI() {
    var $inp = $('#file_oficio_entrada');
    var files = ($inp[0] && $inp[0].files) ? $inp[0].files : [];
    var $empty = $('#container_oficio_entrada_vacio');
    var $cont = $('#container_oficio_entrada');
    var $label = $('#label_oficio_entrada');
    var $icon = $('#icon_oficio_entrada');

    $cont.empty();
    if (files.length > 0) {
        $empty.hide();
        var f = files[0];
        var pill = $('<div class="file-pills-row"><span class="file-pill"></span></div>');
        pill.find('.file-pill').text(f.name + ' (' + Math.ceil(f.size / 1024) + ' KB)');
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

// Anexos
function updateAnexosUI() {
    var $inp = $('#file_anexo_entrada');
    var files = ($inp[0] && $inp[0].files) ? $inp[0].files : [];
    var $empty = $('#container_anexo_entrada_vacio');
    var $cont = $('#container_anexo_entrada');
    var $label = $('#label_anexo_entrada');
    var $icon = $('#icon_anexo_entrada');

    $cont.empty();
    if (files.length > 0) {
        $empty.hide();
        var row = $('<div class="file-pills-row"></div>');
        Array.prototype.slice.call(files).forEach(function (f) {
            row.append('<span class="file-pill" title="' + f.name + '">' +
                f.name + ' (' + Math.ceil(f.size / 1024) + ' KB)' +
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
