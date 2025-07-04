// Obtener el token CSRF
var token = $('meta[name="csrf-token"]').attr('content');

// Abrir el modal al hacer clic en el botón Informe
window.openModal = function() {
    console.log("openModal se llamó!");
    loadCatalogs();
    $('#modalReport').fadeIn();
};

// Cerrar modal si se hace clic fuera
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

// Cargar catálogos vía AJAX
function loadCatalogs() {
    console.log("Intentando cargar catálogos...");

    $.ajax({
        url: catalogosURL,
        type: "GET",
        success: function(response) {
            console.log("Datos recibidos:", response);

            const areaSelect = $('#id_cat_area_informe');
            areaSelect.empty().append('<option value="">-- Todas las áreas --</option>');
            $.each(response.areas, (i, item) => {
                areaSelect.append(`<option value="${item.id_cat_area}">${item.descripcion}</option>`);
            });
            areaSelect.selectpicker('refresh');

            const statusSelect = $('#id_cat_status_informe');
            statusSelect.empty().append('<option value="">-- Todos los estatus --</option>');
            $.each(response.estatus, (i, item) => {
                statusSelect.append(`<option value="${item.id_cat_estatus}">${item.descripcion}</option>`);
            });
            statusSelect.selectpicker('refresh');

            const yearSelect = $('#id_cat_date_informe');
            yearSelect.empty().append('<option value="">-- Todos los años --</option>');
            $.each(response.anios, (i, item) => {
                yearSelect.append(`<option value="${item.id_cat_anio}">${item.descripcion}</option>`);
            });
            yearSelect.selectpicker('refresh');

            console.log("Selects reinicializados correctamente.");
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

// Validar campos y generar reporte
function validateDate() {
    const area = $('#id_cat_area_informe').val();
    const status = $('#id_cat_status_informe').val();
    const year = $('#id_cat_date_informe').val();

    $('#modalReport').fadeOut();
    generateReport(area, status, year);
}

// Descargar el reporte vía AJAX
function generateReport(area, status, year) {
    showSpinner();

    $.ajax({
        url: reporteURL,
        type: 'GET',
        data: { area, status, year },
        xhrFields: { responseType: 'blob' },
        success: function (response, status, xhr) {
            const filename = "reporte_internos.xlsx";
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
                text: 'El archivo se descargó correctamente.'
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

// Spinner de carga
function showSpinner() {
    Swal.fire({
        title: 'Generando...',
        text: 'Por favor espera.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
}

// Ocultar spinner
function hideSpinner() {
    Swal.close();
}
