
// Codigo para la implementacioon de reporte de exel
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form


// Carga de formulario inicial
$(document).ready(function () {
    // Refresh add solicitante
    $(window).click(function (event) {
        if ($(event.target).is('#modalReport')) {
            $('#modalReport').fadeOut(); // Ocultar la ventana modal
        }
    });
});


function generateReport() {
    $('#modalReport').fadeOut(); // Ocultar modal
    showSpinner(); // Mostrar spinner

    $.ajax({
        url: URL_DEFAULT.concat('/letter/dashboard/generate'),
        type: 'POST',
        data: {
            id_cat_area: $('#cat_area_j_1').val(),
            id_cat_status: $('#id_cat_status_informe').val(),
            inlcuir_usuario_capturo: $('#inlcuir_usuario_capturo').prop('checked') ? 1 : 0,
            fecha_inicio_fecha_fin: $('#fecha_inicio_fecha_fin').prop('checked') ? 1 : 0,
            incluir_horas: $('#incluir_horas').prop('checked') ? 1 : 0,
            fecha_inicio_informe: $('#fecha_inicio_informe').val(),
            fecha_fin_informe: $('#fecha_fin_informe').val(),
            id_cat_date_informe: $('#id_cat_date_informe').val(),
            inicio: getFormattedHourValue('#inicio'),
            fin: getFormattedHourValue('#fin'),
            _token: token
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function (response, status, xhr) {
            const filename = "sirh_data.xlsx";
            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            hideSpinner();
            notyfEM.success('Descarga finalizada');
        },
        error: function (xhr, status, error) {
            hideSpinner();
            notyfEM.error('No se pudo completar la acción. Por favor, vuelve a intentarlo.');
        }
    });
}

function getFormattedHourValue(inputId) {
    const value = $(inputId).val();
    return value == 24 ? value : parseInt(value);
}


function getFormattedHourValue(inputId) {
    var value = $(inputId).val();  // Obtiene el valor del input range
    return value == 24 ? value : parseInt(value); // Si es 24, lo muestra como 24, si no, solo el número entero
}

/*
function generateReport() {
    let cat_area_j_1 = $('#cat_area_j_1').val();

    let incluir_horas = $('#incluir_horas').prop('checked') ? true : false;

    let inicio = $('#inicio').val();
}*/



// La función abre el modal de reporte
function openModal() {
    initData(); // Inicio de variables y validaciones
    $('#modalReport').fadeIn();//Iniciar ventana modal
}

// Cerrar modal refresh no oficio
$('#cancel_copy').click(function () { //Se pulsa el boton de cancelar
    $('#modalReport').fadeOut(); // Cerrar la ventana modal
});




function descargarTodosLosArchivos() {
    $('#modalReport').fadeOut(); // Ocultar modal
    showSpinner(); // Mostrar spinner

    $.ajax({
        url: URL_DEFAULT.concat('/alf/download'),
        method: 'POST',
        data: {
            _token: token
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            const blob = new Blob([data]);
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sirh_file.zip';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        },
        error: function (xhr) {
            alert('Error al descargar archivos');
        },
        complete: function () {
            hideSpinner();
        }
    });
}