// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');

// Configurar el token CSRF para todas las solicitudes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Función para validar la CURP

function validarcurp() {
    let curp = $('#curp').val().trim();

    if (curp === '') {
        alert('Por favor, ingresa una CURP.');
        return;
    }

    const curpRegex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i;
    if (!curpRegex.test(curp)) {
        alert('El formato de CURP no es válido.');
        return;
    }

    $.ajax({
        url: URL_DEFAULT.concat('/tableinstructor/table/dataCurp'),
        type: 'POST',
        data: { curp: curp },
        success: function (response) {
            if (response.status) {
                let data = response.value[0] // Asume que es un array con al menos un resultado
                $('#remitente_nombre').text(data.nombre || 'N/A');
                $('#remitente_primer_apellido').text(data.primer_apellido || 'N/A');
                $('#remitente_segundo_apellido').text(data.segundo_apellido || 'N/A');
                $('#remitente_rfc').text(data.rfc || 'N/A');
                notyfEM.success("CURP localizado con éxito.");
            } else {
                notyfEM.error("No se encontró el CURP. Verifica los datos e inténtalo de nuevo.");
                limpiarValores();
            }
        },
        error: function () {
            limpiarValores();
        }
    });
}

function limpiarValores() {
    $('#remitente_nombre').text('_');
    $('#remitente_primer_apellido').text('_');
    $('#remitente_segundo_apellido').text('_');
    $('#remitente_rfc').text('_');
}
