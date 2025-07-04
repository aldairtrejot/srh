var token = $('meta[name="csrf-token"]').attr('content');

window.openModal = function() {
    console.log("openModal se llamó!");
    $('#modalReport').fadeIn();
    loadCatalogs();
};

$(document).ready(function () {
    $(window).click(function (event) {
        if ($(event.target).is('#modalReport')) {
            $('#modalReport').fadeOut();
        }
    });
    $('#cancel_copy').click(function () {
        $('#modalReport').fadeOut();
    });
});

function loadCatalogs() {
    console.log("Intentando cargar catálogos...");

    $.ajax({
        url: catalogosURL,
        type: "GET",
        success: function(response) {
            console.log("Datos recibidos:", response);

            $('#id_cat_area_informe').selectpicker('destroy');
            $('#id_cat_date_informe').selectpicker('destroy');

            // Área
            const areaOptions = response.areas.map(item =>
                `<option value="${item.id_cat_dependencia_area}">${item.descripcion}</option>`
            );
            $("#id_cat_area_informe").html('<option value="">-- Todas las áreas --</option>' + areaOptions.join(''));

            // Año
            const yearOptions = response.anios.map(item =>
                `<option value="${item.descripcion}">${item.descripcion}</option>`
            );
            $("#id_cat_date_informe").html('<option value="">-- Todos los años --</option>' + yearOptions.join(''));

            $('.selectpicker').selectpicker();
        },
        error: function(xhr) {
            console.error("Error al cargar catálogos:", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los catálogos.'
            });
        }
    });
}

function validateDate() {
    const area = $('#id_cat_area_informe').val();
    const year = $('#id_cat_date_informe').val();

    $('#modalReport').fadeOut();
    generateReport(area, year);
}

function generateReport(area, year) {
    showSpinner();

    $.ajax({
        url: reporteURL,
        type: 'GET',
        data: { area, year },
        xhrFields: { responseType: 'blob' },
        success: function (response, status, xhr) {
            const filename = "reporte_circulares_externas.xlsx";
            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            hideSpinner();
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'El archivo se descargó correctamente.' });
        },
        error: function () {
            hideSpinner();
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el archivo.' });
        }
    });
}

function showSpinner() {
    Swal.fire({ title: 'Generando...', text: 'Por favor espera.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
}
function hideSpinner() {
    Swal.close();
}
