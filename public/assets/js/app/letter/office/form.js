// ===============================
// Token CSRF
// ===============================
var token = $('meta[name="csrf-token"]').attr('content');

// ===============================
// Al cargar
// ===============================
$(document).ready(function () {
    $('#num_documento_area').prop('disabled', true);
    $('#id_checkbox_Template_tooltip').prop('disabled', false);
    $('select').selectpicker();

    setData();
    setCheckboxArea();

    tooltip('#id_checkbox_Template_tooltip', 'Marcar para añadir un folio de gestión manual');
    tooltip('#num_correspondencia', 'Asociar por Fol. Gestión');

    // ---- Fechas (dinámicas) ----
    aplicarLimitesFechas();          // fija min/max según el día actual
    validarFechasAlCambiar();        // valida cambios y relación inicio/fin
    programarActualizacionLimitesDiaria(); // vuelve a calcular a medianoche

    // (Opcional) valida al enviar
    $('#formulario').on('submit', function (e) {
        if (!validarFechasAntesDeEnviar()) e.preventDefault();
    });
});

// ===============================
// Fechas: helpers
// ===============================
function toISODate(d) { return d.toISOString().split('T')[0]; }
function toDMY(d) {
    const dd = String(d.getDate()).padStart(2,'0');
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const yyyy = d.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
}
function parseFecha($el) {
    const v = ($el.val() || '').trim();
    if (!v) return null;
    // dd/mm/yyyy ?
    const m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(v);
    if (m) {
        const d = new Date(+m[3], +m[2]-1, +m[1]); d.setHours(0,0,0,0);
        return isNaN(d) ? null : d;
    }
    // yyyy-mm-dd
    const d2 = new Date(v); d2.setHours(0,0,0,0);
    return isNaN(d2) ? null : d2;
}

// ===============================
// APLICAR BLOQUEO DE FECHAS (dinámico)
// ===============================
function aplicarLimitesFechas() {
    const minDate = new Date(2020, 0, 1); // 01/01/2020
    const hoy = new Date(); hoy.setHours(0,0,0,0);
    const maxDate = new Date(hoy.getFullYear(), hoy.getMonth() + 3, hoy.getDate()); // hoy + 3m

    const minISO = toISODate(minDate);
    const maxISO = toISODate(maxDate);

    // HTML5 <input type="date">
    $('#fecha_inicio, #fecha_fin').each(function () {
        const $i = $(this);
        if ($i.attr('type') === 'date') {
            $i.attr('min', minISO).attr('max', maxISO);
            const v = $i.val();
            if (v && (v < minISO || v > maxISO)) $i.val('');
        }
    });

    // Bootstrap Datepicker (si se usa)
    if ($.fn.datepicker) {
        ['#fecha_inicio','#fecha_fin'].forEach(id => {
            $(id).datepicker('destroy').datepicker({
                format: 'dd/mm/yyyy',
                language: 'es',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                startDate: minDate,
                endDate: maxDate
            });
        });
    }

    // Ajustar min dinámico de fin según inicio actual
    const fi = parseFecha($('#fecha_inicio'));
    if (fi) {
        // HTML5
        if ($('#fecha_fin').attr('type') === 'date') {
            $('#fecha_fin').attr('min', toISODate(fi));
        }
        // Datepicker
        if ($.fn.datepicker) {
            $('#fecha_fin').datepicker('setStartDate', fi);
        }
    }
}

// Recalcular a medianoche (sin recargar)
function programarActualizacionLimitesDiaria() {
    const ahora = new Date();
    const siguienteMedianoche = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate() + 1);
    const ms = siguienteMedianoche - ahora;
    setTimeout(function () {
        aplicarLimitesFechas();
        programarActualizacionLimitesDiaria(); // reprogramar para el siguiente día
    }, ms);
}

// ===============================
// VALIDAR CAMBIOS EN FECHAS
// ===============================
function validarFechasAlCambiar() {
    const $fi = $('#fecha_inicio');
    const $ff = $('#fecha_fin');

    $fi.on('change blur', function () {
        clearFieldError('#fecha_inicio');
        const d = parseFecha($fi);
        if (!d) return;

        // mover min de fin a la fecha de inicio
        if ($ff.attr('type') === 'date') $ff.attr('min', toISODate(d));
        if ($.fn.datepicker) $ff.datepicker('setStartDate', d);

        // si fin actual es menor, limpiar y marcar error
        const fin = parseFecha($ff);
        if (fin && fin < d) {
            $ff.val('');
            showFieldError('#fecha_fin', 'La fecha fin no puede ser menor que la fecha inicio.');
        }
    });

    [$fi, $ff].forEach($el => {
        $el.on('change blur', function () {
            const minDate = new Date(2020,0,1);
            const hoy = new Date(); hoy.setHours(0,0,0,0);
            const maxDate = new Date(hoy.getFullYear(), hoy.getMonth() + 3, hoy.getDate());

            const d = parseFecha($(this));
            if (!d) return;

            if (d < minDate || d > maxDate) {
                $(this).val('');
                showFieldError('#' + this.id, 'Fecha fuera de rango (mín ' + toDMY(minDate) + ', máx ' + toDMY(maxDate) + ').');
            } else {
                clearFieldError('#' + this.id);
            }
        });
    });
}

// (Opcional) Validación al enviar
function validarFechasAntesDeEnviar() {
    clearFieldError('#fecha_inicio');
    clearFieldError('#fecha_fin');

    const minDate = new Date(2020,0,1);
    const hoy = new Date(); hoy.setHours(0,0,0,0);
    const maxDate = new Date(hoy.getFullYear(), hoy.getMonth() + 3, hoy.getDate());

    const fi = parseFecha($('#fecha_inicio'));
    const ff = parseFecha($('#fecha_fin'));

    let ok = true;
    if (!fi) { showFieldError('#fecha_inicio', 'Por favor, ingresa la fecha de inicio.'); ok = false; }
    if (!ff) { showFieldError('#fecha_fin', 'Por favor, ingresa la fecha de fin.'); ok = false; }
    if (!ok) return false;

    if (fi < minDate) { showFieldError('#fecha_inicio', 'La fecha inicio no puede ser menor a ' + toDMY(minDate) + '.'); ok = false; }
    if (ff < minDate) { showFieldError('#fecha_fin', 'La fecha fin no puede ser menor a ' + toDMY(minDate) + '.'); ok = false; }
    if (fi > maxDate) { showFieldError('#fecha_inicio', 'La fecha inicio no puede ser mayor a ' + toDMY(maxDate) + '.'); ok = false; }
    if (ff > maxDate) { showFieldError('#fecha_fin', 'La fecha fin no puede ser mayor a ' + toDMY(maxDate) + '.'); ok = false; }
    if (ok && ff < fi) { showFieldError('#fecha_fin', 'La fecha fin no puede ser menor que la fecha inicio.'); ok = false; }

    return ok;
}

// ===============================
// UI feedback helpers
// ===============================
function showFieldError(selector, message) {
    const $inp = $(selector);
    let $fb = $inp.next('.invalid-feedback');
    if ($fb.length === 0) {
        $fb = $('<div class="invalid-feedback"></div>');
        $inp.after($fb);
    }
    $inp.addClass('is-invalid');
    $fb.text(message).show();
}
function clearFieldError(selector) {
    const $inp = $(selector);
    $inp.removeClass('is-invalid');
    $inp.next('.invalid-feedback').hide().text('');
}

// ===============================
// Lógica existente
// ===============================
function setCheckboxArea() {
    if ($('#es_por_area').val()) {
        $('#idcheckboxTemplate').prop('checked', true);
        showDiv('mostrar_ocultar_no_area');
        $('#num_correspondencia').val('');
        $('#num_correspondencia').prop('disabled', true);
    } else {
        hideDiv('mostrar_ocultar_no_area');
        cleanSelect('#id_cat_area_documento');
        $('#num_documento_area').val('');
        $('#num_correspondencia').prop('disabled', false);
        cleanSelectMoreSelect('#id_usuario_area');
        cleanSelectMoreSelect('#id_usuario_enlace');
    }
}

function getRole() {
    let bool_user_role = $('#bool_user_role').val();
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false;
    if (!new_variable) {
        $('#num_correspondencia').prop('disabled', true);
        $('#fecha_inicio').prop('disabled', true);
        $('#fecha_fin').prop('disabled', true);
        $('#asunto').prop('disabled', true);
        $('#idcheckboxTemplate').prop('disabled', true);
        $('#id_cat_area_documento').prop('disabled', true);
        $('#id_cat_area_documento').selectpicker('refresh');
    }
}

function setData() {
    $('#_labFechaCaptura').text($('#fecha_captura').val());
    $('#_labNoCorrespondencia').text($('#num_turno_sistema').val());
    $('#_labUsuario').text($('#user_name').val());
    $('#_labEnlace').text($('#user_enlace').val());
    $('#_labArea').text($('#area_format').val());
    getData();
}

function getData() {
    let id_cat_anio = $('#id_cat_anio').val();
    $.ajax({
        url: URL_DEFAULT.concat('/year/getYear'),
        type: 'POST',
        data: { id_cat_anio: id_cat_anio, _token: token },
        success: function (response) {
            $('#_labAño').text(response.nameYear.name);
        },
    });
}

// Detectar cambio de No. correspondencia
$('#num_correspondencia').on('input', function () {
    let value = $(this).val().trim();
    if (value !== '') {
        getNoDocument(value, '#_labUsuario', '#_labEnlace', '#_labArea',
            '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace', '#id_tbl_correspondencia');
    } else {
        $('#_labUsuario').text(' _');
        $('#_labEnlace').text(' _');
        $('#_labArea').text(' _');
    }
});
