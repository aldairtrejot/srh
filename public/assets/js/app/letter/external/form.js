// Script que se ejecuta con el formulario, para funciones u herramientas extras
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('select').selectpicker(); //Iniciar los select
    setData(); //Establecer las variables de información general
    setDateLimits(); //Aplicar límites a los campos de fecha

    // Validar campos antes de enviar el formulario
    $('form').on('submit', function (e) {
        if (!validarFechasAntesDeEnviar()) {
            e.preventDefault();
        }
    });

    // Validar dinámicamente cada campo cuando cambia
    $('#fecha_inicio, #fecha_fin, #fecha_documento, #fecha_emision, #fecha_aplicacion').on('change', function () {
        const input = $(this);
        const value = input.val();
        const min = input.attr('min');
        const max = input.attr('max');

        if (value < min || value > max) {
            input.val('');
            input.addClass('is-invalid');
            input.next('.invalid-feedback').show();
        } else {
            input.removeClass('is-invalid');
            input.next('.invalid-feedback').hide();
        }
    });
});

function setData() {
    $('#_labFechaCaptura').text($('#fecha_captura').val());
    $('#_labAño').text($('#anio').val());
    $('#_labNoCorrespondencia').text($('#num_turno_sistema').val());
}

function setDateLimits() {
    const minDate = '2020-01-01';
    const maxDate = calcularFechaLimite();

    const camposFecha = ['#fecha_inicio', '#fecha_fin', '#fecha_documento', '#fecha_emision', '#fecha_aplicacion'];

    camposFecha.forEach(function (campo) {
        const input = $(campo);
        input.attr('min', minDate);
        input.attr('max', maxDate);

        if (input.val() < minDate || input.val() > maxDate) {
            input.val('');
        }
    });
}

function calcularFechaLimite() {
    const fechaActual = new Date();
    fechaActual.setMonth(fechaActual.getMonth() + 3);
    const year = fechaActual.getFullYear();
    const month = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
    const day = fechaActual.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function validarFechasAntesDeEnviar() {
    let valido = true;
    const campos = ['#fecha_inicio', '#fecha_fin', '#fecha_documento', '#fecha_emision', '#fecha_aplicacion'];

    campos.forEach(function (campo) {
        const input = $(campo);
        const val = input.val();
        const min = input.attr('min');
        const max = input.attr('max');

        if (val && (val < min || val > max)) {
            input.addClass('is-invalid');
            input.next('.invalid-feedback').show();
            valido = false;
        } else {
            input.removeClass('is-invalid');
            input.next('.invalid-feedback').hide();
        }
    });

    return valido;
}
