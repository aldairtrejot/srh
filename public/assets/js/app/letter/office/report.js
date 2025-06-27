var token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    $(window).click(function (event) {
        if ($(event.target).is('#modalReport')) {
            $('#modalReport').fadeOut();
        }
    });
});

function generateReport() {
    showSpinner();

    $.ajax({
        url: reporteURL,
        type: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (response, status, xhr) {
            const filename = "reporte_oficios.xlsx";
            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            hideSpinner();
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'El archivo se descargó correctamente.',
                confirmButtonText: 'OK',
    confirmButtonColor: '#10312b' // Verde
            });
        },
        error: function () {
            hideSpinner();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo generar el archivo.'
            });
        }
    });
}

function openModal() {
    $('#modalReport').fadeIn();
}

$('#cancel_copy').click(function () {
    $('#modalReport').fadeOut();
});
