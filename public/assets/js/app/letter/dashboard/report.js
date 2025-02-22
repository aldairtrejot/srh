
// Codigo para la implementacioon de reporte de exel
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form


// Carga de formulario inicial
$(document).ready(function () {
    // Refresh add solicitante
    $(window).click(function (event) {
        if ($(event.target).is('#modalCopy')) {
            $('#modalCopy').fadeOut(); // Ocultar la ventana modal
        }
    });
});


function generateReport() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/dashboard/generate'),
        type: 'POST',
        data: {
            id: 1,
            id2: 2,
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
        },
        error: function (xhr, status, error) {
            console.error('Error al generar el archivo:', error);
        }
    });
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
    $('#modalCopy').fadeIn();//Iniciar ventana modal
}

// Cerrar modal refresh no oficio
$('#cancel_copy').click(function () { //Se pulsa el boton de cancelar
    $('#modalCopy').fadeOut(); // Cerrar la ventana modal
});
