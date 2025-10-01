// Validacion de documento Inicial
// Codigo para la implementacioon de reporte de exel
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// La función inicia las variable e inactiva las que no
function initData() {
    getCollection(); // La función obtiene los catalgos iniciales
    activeDate(true); // Desactiva campo fechas
    cleanTime(); // Limpia las horas de input
    cleanSelectDate(false); //Activar Select
    activeRange(true); // Activar Range

    $('#incluir_horas').prop('checked', true); //desmarcar el check
    $('#inlcuir_usuario_capturo').prop('checked', false); //desmarcar el check
    $('#fecha_inicio_fecha_fin').prop('checked', false); //desmarcar el check
    getCount(); // inicia contador de correspondencia
}

//Validacion cuando se cambia el evento de fecha
$('#fecha_inicio_informe').change(function () {
    validateDateForm();
});

//Validacion cuando se cambia el evento de fecha
$('#fecha_fin_informe').change(function () {
    validateDateForm();
});

//La funcion valida que la fecha de inicio no sea mayor a la fecha de fin
function validateDateForm() {
    let fecha_inicio = document.getElementById('fecha_inicio_informe').value;
    let fecha_fin = document.getElementById('fecha_fin_informe').value;
    if (fecha_inicio !== '' && fecha_fin !== '') { //Valida que los campos fecchas tengan informacion
        if (fecha_inicio > fecha_fin) {
            notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
        }
    }
}

//La funcion valida que la fecha de inicio no sea mayor a la fecha de fin
function validateDate() {
    let fecha_inicio_fecha_fin = $('#fecha_inicio_fecha_fin').prop('checked') ? 1 : 0;
    let fecha_inicio = document.getElementById('fecha_inicio_informe').value;
    let fecha_fin = document.getElementById('fecha_fin_informe').value;

    if (fecha_inicio_fecha_fin) {
        if (isFieldEmpty($('#fecha_inicio_informe').val(), 'Fecha de inicio') ||
            isFieldEmpty($('#fecha_fin_informe').val(), 'Fecha Fin')) {
        } else {
            if (fecha_inicio > fecha_fin) {
                notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
            } else {
                generateReport(); // Generar reporte
            }
        }
    } else {
        generateReport(); // Generar reporte
    }
}

// Detecta el cambio del check para habilitar o no las fechas o el año
$('#incluir_horas').change(function () {
    cleanTime(); // Limpia las horas de input
    if ($(this).prop('checked')) {
        activeRange(true); //Desctivar range Range
    } else {
        activeRange(false); // Activar Range
    }
});

// Activar o desactivar Range
function activeRange(status) {
    $('#inicio').prop('disabled', status);
    $('#fin').prop('disabled', status);
}

// Detecta el cambio del check para habilitar o no las fechas o el año
$('#fecha_inicio_fecha_fin').change(function () {
    if ($(this).prop('checked')) {
        activeDate(false); // Habilita la fechas
        cleanSelectDate(true); //
    } else {
        activeDate(true); // Habilita la fechas
        cleanSelectDate(false); //
    }
});

// Limpia el select
function cleanSelectDate(status) {
    $('#id_cat_date_informe').val('');
    $('#id_cat_date_informe').prop('disabled', status); //Desabilitar selecct
    $('#id_cat_date_informe').selectpicker('refresh'); //Refresh de select 
}


function activeDate(status) { // Activa o desactiva las fechas
    $('#fecha_inicio_informe').prop('disabled', status); // desabilitar no de documento por area
    $('#fecha_fin_informe').prop('disabled', status); // desabilitar no de documento por area
    $('#fecha_inicio_informe').val(''); // Limpieza de input
    $('#fecha_fin_informe').val('');// Limpieza de input
}

// La fucion limpia las horas para inciiarlas al 0
function cleanTime() {
    $('#inicio').val(0); // Establecer el slider 'inicio' en 0
    $('#fin').val(0); // Establecer el slider 'fin' en 0

    // Actualizar el texto de las etiquetas a 00:00 para ambos sliders
    $('#inicio-hour-right').text('00:00');
    $('#fin-hour-right').text('00:00');
}

// La función obtiene los catalgoos inciales para mostrarlos
function getCollection() {
    // Limpieza de combox 
    cleanSelectMoreSelect('#cat_area_j_1');

    $.ajax({
        url: URL_DEFAULT.concat('/letter/dashboard/getCollection/let'),
        type: 'POST',
        data: {
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            allTextForeachSelect(response.cat_area_j_1, '#cat_area_j_1');
            allTextForeachSelect(response.cat_area_j_2, '#cat_area_j_2');

            allTextForeachSelect(response.resultCollectionStatus, '#id_cat_status_informe');
            allTextForeachSelect(response.resultCollectionDate, '#id_cat_date_informe');
        },
    });
}

// La función refresca el contador similar a 0 de 100
function getCount() {
    $('#lab_contador').text('Cargando ...');
    $.ajax({
        url: URL_DEFAULT.concat('/letter/countReport'),
        type: 'POST',
        data: {
            id_cat_area: $('#cat_area_j_1').val(),
            /*
            id_cat_status: $('#id_cat_status_informe').val(),
            inlcuir_usuario_capturo: $('#inlcuir_usuario_capturo').prop('checked') ? 1 : 0,
            fecha_inicio_fecha_fin: $('#fecha_inicio_fecha_fin').prop('checked') ? 1 : 0,
            incluir_horas: $('#incluir_horas').prop('checked') ? 1 : 0,
            check_copia_a: $('#check_copia_a').prop('checked') ? 1 : 0,
            fecha_inicio_informe: $('#fecha_inicio_informe').val(),
            fecha_fin_informe: $('#fecha_fin_informe').val(),
            id_cat_date_informe: $('#id_cat_date_informe').val(),
            inicio: getFormattedHourValue('#inicio'),
            fin: getFormattedHourValue('#fin'),
            */
            _token: token
        },
        success: function (response) {
            console.log(response);

            $('#lab_contador').text(response.lab_contador);
        },
    });
}

// Se detecta el cambio de select de combox C.R.H para actualizar el combo dependiente y el contador
$("#cat_area_j_1").change(function () {

    let id_cat_area_j_1 = $(this).val();
    console.log('inicio ' + id_cat_area_j_1)

    getCount();
});