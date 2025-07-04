var token = $('meta[name="csrf-token"]').attr('content');

window.openModal = function() {
    console.log("openModal se llamó!");
    loadCatalogs();
    $('#modalReport').fadeIn();
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
            $('#id_cat_area_informe').empty().append('<option value="">-- Todas las áreas --</option>');
            $.each(response.areas, function(i, item) {
                $('#id_cat_area_informe').append(`<option value="${item.id_cat_area}">${item.descripcion}</option>`);
            });
            $('#id_cat_status_informe').empty().append('<option value="">-- Todos los estatus --</option>');
            $.each(response.estatus, function(i, item) {
                $('#id_cat_status_informe').append(`<option value="${item.id_cat_estatus}">${item.descripcion}</option>`);
            });
            $('#id_cat_date_informe').empty().append('<option value="">-- Todos los años --</option>');
            $.each(response.anios, function(i, item) {
                $('#id_cat_date_informe').append(`<option value="${item.id_cat_anio}">${item.descripcion}</option>`);
            });
            $('.selectpicker').selectpicker('refresh');
        },
        error: function(xhr) {
            console.error("Error al cargar catálogos:", xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los catálogos.' });
        }
    });
}

function validateDate() {
    const area = $('#id_cat_area_informe').val();
    const status = $('#id_cat_status_informe').val();
    const year = $('#id_cat_date_informe').val();
    $('#modalReport').fadeOut();
    generateReport(area, status, year);
}

function generateReport(area, status, year) {
    showSpinner();
    $.ajax({
        url: reporteURL,
        type: 'GET',
        data: { area, status, year },
        xhrFields: { responseType: 'blob' },
        success: function (response, status, xhr) {
            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "reporte_interno.xlsx";
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
