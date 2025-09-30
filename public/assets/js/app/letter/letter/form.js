/* form.js — LÓGICA GENERAL DE FORMULARIO
   - Fechas (límites y validación)
   - Roles/permits, selectpickers, tooltips
   - Remitentes (uno o varios)
   - Carga de archivos (UI) usando IDs REALES del Blade
*/

var token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    // UI base
    $('select').selectpicker();
    setData();
    getRole();
    setCheckboxArea();
    setCheckbox();
    setDateLimits(); // límites visuales a las fechas

    // tooltips existentes
    tooltip('#id_checkbox_Template_tooltip_fisico', 'Marcar si el documento es físico');
    tooltip('#id_checkbox_Template_tooltip', 'Añadir un remitente no registrado');
    tooltip('#mas_remitentes', 'Añadir dos o más remitentes');

    // tooltip para el checkbox de archivos (el contenedor es #habilitar_carga_archivos)
    tooltip('#habilitar_carga_archivos', 'Mostrar/ocultar la sección de carga de Oficio y Anexos');

    // Estado inicial del bloque de archivos (usa hidden real #habilitar_carga)
    setCheckboxFiles();

    // Validación visual de fechas
    $('#fecha_inicio, #fecha_fin, #fecha_documento').on('input change', function () {
        clearFieldError('#' + this.id);
    });

    // Validación en submit
    $('#myForm').on('submit', function (e) {
        if (!validarFechasAntesDeEnviar()) {
            e.preventDefault();
        }
    });

    // limpiar error local al seleccionar archivos (IDs reales)
    ['#file_oficio_entrada', '#file_anexo_entrada'].forEach(function (id) {
        $(id).on('change', function () {
            clearLocalFileError(id);
        });
    });

    // Sincroniza hidden al cambiar el checkbox correcto
    $('#habilitar_carga_box').on('change', function () {
        $('#habilitar_carga').val($(this).is(':checked') ? true : '');
        setCheckboxFiles();
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

function showFieldError(selector, message) {
    const $inp = $(selector);
    if ($inp.next('.invalid-feedback').length === 0) {
        $inp.after('<div class="invalid-feedback"></div>');
    }
    $inp.addClass('is-invalid');
    $inp.next('.invalid-feedback').text(message).show();
}

function clearFieldError(selector) {
    const $inp = $(selector);
    $inp.removeClass('is-invalid');
    $inp.next('.invalid-feedback').hide().text('');
}

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
            '#id_cat_remitente', '#id_cat_entidad'
        ];
        toDisable.forEach(id => $(id).prop('disabled', true));
        ['#id_cat_entidad', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace',
         '#id_cat_unidad', '#id_cat_coordinacion', '#id_cat_tramite', '#id_cat_clave',
         '#id_cat_remitente'].forEach(id => $(id).selectpicker('refresh'));
    }
}

function validateEstatus() {
    if ([2, 5, 7].includes(Number($('#id_cat_estatus').val()))) {
        $('#id_cat_estatus').prop('disabled', true).selectpicker('refresh');
    } else {
        $('#id_cat_estatus option[value="2"], #id_cat_estatus option[value="5"]').remove();
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
// CARGA DE ARCHIVOS (UI) — IDs reales del Blade
// =========================
function setCheckboxFiles() {
    const activo = !!$('#habilitar_carga').val();

    if (activo) {
        showDiv('contenedor_carga_archivos');
        enableFile('#file_oficio_entrada', '#label_oficio_entrada', '#icon_oficio_entrada');
        enableFile('#file_anexo_entrada', '#label_anexo_entrada', '#icon_anexo_entrada');
        $('#habilitar_carga_box').prop('checked', true);
    } else {
        hideDiv('contenedor_carga_archivos');
        resetFile('#file_oficio_entrada', '#label_oficio_entrada', '#icon_oficio_entrada');
        resetFile('#file_anexo_entrada', '#label_anexo_entrada', '#icon_anexo_entrada');
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
    const id = inputSel.replace('#', '');
    const $err = $('#error_' + id);
    if ($err.length) {
        $err.hide().text('');
        $(inputSel).removeClass('is-invalid');
    }
}

// (Limpieza defensiva) elimina listeners antiguos si existieran
try {
  $('#carga_archivos, #carga_archivos_box, #id_checkbox_carga_archivos').off();
} catch(e) {}
