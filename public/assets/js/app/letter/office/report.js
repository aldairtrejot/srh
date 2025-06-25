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
    $('#modalReport').fadeOut();
    showSpinner();

    $.ajax({
        url: '/letter/dashboard/reporte', // ⚠️ Asegúrate que esta es la ruta correcta
        type: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (response, status, xhr) {
            const filename = "informe_oficios.xlsx";
            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            notyfEM.success("Documento generado correctamente.");
            hideSpinner();
        },
        error: function () {
            notyfEM.error('Error al generar el archivo.');
            hideSpinner();
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
