var token = $('meta[name="csrf-token"]').attr('content');

// =========================
// Funciones del Código 1
// =========================
$(document).ready(function () {
    $('select').selectpicker();
    setData();
    getRole();
    setCheckboxArea();
    setCheckbox();
    setDateLimits(); // <-- APLICAMOS límites visuales a las fechas

    tooltip('#id_checkbox_Template_tooltip_fisico', 'Marcar si el documento es físico');
    tooltip('#id_checkbox_Template_tooltip', 'Añadir un remitente no registrado');
    tooltip('#mas_remitentes', 'Añadir dos o más remitentes');

    $('#fecha_inicio, #fecha_fin, #fecha_documento').on('input change', function () {
        clearFieldError('#' + this.id);
    });

    // << PATCH: limpiar feedback en campos obligatorios adicionales (sin usuario) >>
    $('#asunto, #puesto_remitente').on('input change', function () {
        clearFieldError('#' + this.id);
    });
    $('#id_cat_entidad, #id_cat_area_1, #id_cat_tramite, #id_cat_clave, #id_cat_clave_aux')
      .on('changed.bs.select change', function () {
        // (quitamos estilos rojos en selects)
        // try { $(this).selectpicker('setStyle', 'is-invalid', 'remove'); } catch(_){}
        clearFieldError('#' + this.id);
      });
    // << /PATCH >>

    // === PATCH: Config Asunto (máximo 300) y Observaciones (máximo 250) ===
    const ASUNTO_MAX = 300;
    const OBS_MAX    = 250;

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
        toastError('Máximo 300 caracteres para Asunto.');
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
        toastError('Máximo 250 caracteres para Observaciones.');
        setTimeout(() => obsToastGuard = false, 1500);
      }
    });
    // === /PATCH: Asunto & Observaciones ===

    // << PATCH: toast de éxito si el backend deja alguna “señal” al volver de guardar >>
    try {
        var qs = new URLSearchParams(window.location.search);
        var metaSaved = $('meta[name="x-letter-saved"]').attr('content');
        if ((window.LETTER && window.LETTER.saveOk === true) ||
            metaSaved === '1' ||
            qs.get('saved') === '1' || qs.get('ok') === '1' || qs.get('saved_ok') === '1') {
            toastSuccess('Registro guardado con éxito.');
        }
    } catch (_){}
    // << /PATCH >>

    $('#formulario').on('submit', function (e) {
        if (!validarFechasAntesDeEnviar()) {
            e.preventDefault(); // Detener envío si hay errores
        } else {
            // << PATCH: validar obligatorios mínimos (sin tocar Usuario auto-asignado) >>
            if (typeof validarObligatoriosMinimos === 'function' && !validarObligatoriosMinimos()) {
                e.preventDefault();
                if (typeof hideSpinner === 'function') hideSpinner();
                return;
            }
            // << PATCH: fallback silencioso de usuario de área si viene vacío >>
            if (typeof ensureUsuarioAreaFallback === 'function') ensureUsuarioAreaFallback();
            // << /PATCH >>

            // << PATCH: spinner al guardar (solo si la validación pasó)
            if (typeof showSpinner === 'function') showSpinner();
        }
    });
});

// =========================
// LÓGICA FECHAS
// =========================
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

    if (!fechaInicio) {
        showFieldError('#fecha_inicio', 'Por favor, ingresa la fecha de inicio.');
        ok = false;
    }
    if (!fechaFin) {
        showFieldError('#fecha_fin', 'Por favor, ingresa la fecha de fin.');
        ok = false;
    }
    if (!ok) return false;

    const fi = new Date(fechaInicio);
    const ff = new Date(fechaFin);

    if (fi < minDate) {
        showFieldError('#fecha_inicio', 'La fecha de inicio no puede ser menor a ' + minStr + '.');
        ok = false;
    }
    if (ff < minDate) {
        showFieldError('#fecha_fin', 'La fecha de fin no puede ser menor a ' + minStr + '.');
        ok = false;
    }
    if (ff < fi) {
        showFieldError('#fecha_fin', 'La fecha de fin no puede ser menor a la fecha de inicio.');
        ok = false;
    }
    if (fi > maxDate) {
        showFieldError('#fecha_inicio', 'La fecha de inicio no puede ser mayor a ' + limitStr + '.');
        ok = false;
    }
    if (ff > maxDate) {
        showFieldError('#fecha_fin', 'La fecha de fin no puede ser mayor a ' + limitStr + '.');
        ok = false;
    }

    return ok;
}

// << PATCH: toasts unificados >>
function toastError(message) {
    if (window.notyfEM?.error) { window.notyfEM.error(message); }
    else { alert(message); }
}
function toastSuccess(message) {
    if (window.notyfEM?.success) { window.notyfEM.success(message); }
    else { alert(message); }
}
// << /PATCH >>

function showFieldError(selector, message) {
    // << PATCH: NO marcar rojo (excepto lo de oficios que vive en otro archivo).
    //           Aquí solo mostramos toast y salimos. >>
    toastError(message);
    // --- Comportamiento anterior (desactivado):
    // const $inp = $(selector);
    // if ($inp.next('.invalid-feedback').length === 0) {
    //     $inp.after('<div class="invalid-feedback"></div>');
    // }
    // $inp.addClass('is-invalid');
    // $inp.next('.invalid-feedback').text(message).show();
}

function clearFieldError(selector) {
    // Mantenemos la limpieza por si quedan restos en algún template,
    // pero ya no estamos agregando 'is-invalid' desde aquí.
    const $inp = $(selector);
    $inp.removeClass('is-invalid');
    $inp.next('.invalid-feedback').hide().text('');
}

// << PATCH: helpers mínimos y validación de obligatorios (sin usuario) >>
function _isMissing($el) {
    if (!$el || $el.length === 0) return false;       // si no existe, no bloquea
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

    // Entidad
    (function(){
        const $el = $('#id_cat_entidad');
        if (_isMissing($el)) {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'add'); } catch(_){}
            showFieldError('#id_cat_entidad', 'Selecciona la Entidad.');
            ok = false;
        } else {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'remove'); } catch(_){}
            clearFieldError('#id_cat_entidad');
        }
    })();

    // Asunto
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
    (function(){
        const $el = $('#id_cat_area_1');
        if (_isMissing($el)) {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'add'); } catch(_){}
            showFieldError('#id_cat_area_1', 'Selecciona Área 1.');
            ok = false;
        } else {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'remove'); } catch(_){}
            clearFieldError('#id_cat_area_1');
        }
    })();

    // Trámite
    (function(){
        const $el = $('#id_cat_tramite');
        if (_isMissing($el)) {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'add'); } catch(_){}
            showFieldError('#id_cat_tramite', 'Selecciona el Trámite.');
            ok = false;
        } else {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'remove'); } catch(_){}
            clearFieldError('#id_cat_tramite');
        }
    })();

    // Clave (o auxiliar)
    (function(){
        let $el = $('#id_cat_clave');
        if ($el.length === 0) $el = $('#id_cat_clave_aux');
        if (_isMissing($el)) {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'add'); } catch(_){}
            showFieldError('#' + $el.attr('id'), 'Selecciona la Clave.');
            ok = false;
        } else {
            // try { $el.selectpicker('setStyle', 'is-invalid', 'remove'); } catch(_){}
            clearFieldError('#' + $el.attr('id'));
        }
    })();

    // Puesto remitente
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
// << /PATCH >>

// << PATCH: fallback silencioso para id_usuario_area si la auto-asignación no llegó >>
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
// << /PATCH >>

// =========================
// FUNCIONES EXISTENTES
// =========================

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
    if ([2, 5, 7].includes(Number($('#id_cat_estatus').val()))) {
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

// =========================
// Funciones del Código 2
// =========================

// Función para actualizar UI de archivo (oficio)
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

// Función para actualizar UI de anexos
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

