
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

    $('#modalReport').fadeOut(); // Ocultar la ventana modal
    showSpinner();// Inicio de spinner
    $.ajax({
        url: URL_DEFAULT.concat('/letter/dashboard/generate'),
        type: 'POST',
        data: {
            id_cat_area: $('#id_cat_area_informe').val(),
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
            responseType: 'blob' // Para manejar archivos binarios
        },
        success: function (response, status, xhr) {
            let filename = "DATA_GC_SIRH.xlsx";
            let blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });

            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            notyfEM.success("Documento generado correctamente.");
            hideSpinner(); // Se oculta el spinner
        },
        error: function (xhr, status, error) {
            notyfEM.error('Error al generar el archivo:', error);
            hideSpinner(); // Se oculta el spinner
        }
    });
}

function getFormattedHourValue(inputId) {
    var value = $(inputId).val();  // Obtiene el valor del input range
    return value == 24 ? value : parseInt(value); // Si es 24, lo muestra como 24, si no, solo el número entero
}

/*
function generateReport() {
    let id_cat_area_informe = $('#id_cat_area_informe').val();

    let incluir_horas = $('#incluir_horas').prop('checked') ? true : false;

    let inicio = $('#inicio').val();

    console.log(inicio);
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
