// Token for form
var token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    $('#num_documento_area').prop('disabled', true);
    $('select').selectpicker();
    setData();
    setDateLimitsInicioFin();      // ⬅ Establece límites válidos
    bindValidationInicioFin();     // ⬅ Valida dinámicamente
});

// ========== BLOQUEO de FECHAS inválidas ==========
function setDateLimitsInicioFin() {
    const minDate = new Date('2020-01-01');
    const today = new Date();
    const maxDate = new Date(today);
    maxDate.setMonth(maxDate.getMonth() + 3);

    const minStr = toYMD(minDate);
    const maxStr = toYMD(maxDate);

    ['#fecha_inicio', '#fecha_fin'].forEach(id => {
        const $inp = $(id);
        if ($inp.length) {
            $inp.attr('min', minStr).attr('max', maxStr);
            const val = $inp.val();
            if (val && (val < minStr || val > maxStr)) {
                $inp.val('');
            }
        }
    });
}

function bindValidationInicioFin() {
    const minStr = '2020-01-01';

    ['#fecha_inicio', '#fecha_fin'].forEach(selector => {
        $(selector).on('change input', function () {
            const $inp = $(this);
            const val = $inp.val();

            const today = new Date();
            const maxDate = new Date(today);
            maxDate.setMonth(maxDate.getMonth() + 3);
            const maxStr = toYMD(maxDate);

            $inp.attr('min', minStr).attr('max', maxStr);

            if (!val) {
                clearFieldError(selector);
                return;
            }

            if (val < minStr || val > maxStr) {
                showFieldError(selector, `La fecha debe estar entre ${minStr} y ${maxStr}.`);
                $inp.val('');
            } else {
                clearFieldError(selector);
            }
        });
    });
}

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
    const $fb = $inp.next('.invalid-feedback');
    if ($fb.length) $fb.text('').hide();
}

function toYMD(d) {
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ========== FUNCIONES ORIGINALES ==========

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

$('#idcheckboxTemplate').change(function () {
    let bool = false;
    bool = $(this).prop('checked') ? true : '';
    $('#es_por_area').val(bool);
    setCheckboxArea();
});

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
    let fecha_captura = $('#fecha_captura').val();
    $('#_labFechaCaptura').text(fecha_captura);

    let num_turno_sistema = $('#num_turno_sistema').val();
    $('#_labNoCorrespondencia').text(num_turno_sistema);

    let usuario = $('#usuario').val();
    $('#_labUsuario').text(usuario);

    let enlace = $('#enlace').val();
    $('#_labEnlace').text(enlace);

    getData();
}

function getData() {
    let id_cat_anio = $('#id_cat_anio').val();

    $.ajax({
        url: URL_DEFAULT.concat('/year/getYear'),
        type: 'POST',
        data: {
            id_cat_anio: id_cat_anio,
            _token: token
        },
        success: function (response) {
            let item = response.nameYear;
            $('#_labAño').text(item.name);
        },
    });
}

$('#num_correspondencia').on('input', function () {
    let value = $(this).val().trim();
    if (value !== '') {
        getNoDocument(value, '#_labUsuario', '#_labEnlace', '#_labArea', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace', '#id_tbl_correspondencia');
    } else {
        $('#_labUsuario').text(' _');
        $('#_labEnlace').text(' _');
        $('#_labArea').text(' _');
    }
});
