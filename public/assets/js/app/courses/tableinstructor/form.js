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

    // Verificar si el campo CURP está vacío
    if (curp === '') {
        alert('Por favor, ingresa una CURP.');
        return;
    }

    // Validar formato de CURP con expresión regular
    const curpRegex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i;
    if (!curpRegex.test(curp)) {
        alert('El formato de CURP no es válido.');
        return;
    }

    console.log(`Validando CURP: ${curp}`);

    // Realizar la solicitud AJAX
    $.ajax({
        url: URL_DEFAULT.concat('/tableinstructor/table/dataCurp'),
        type: 'POST',
        data: {
            curp: curp
        },
        success: function (response) {
            if (response.status) {
                const datos = response.value[0]; // Se asume que es un array con al menos un resultado
                $('#remitente_nombre').text(datos.NOMBRE || '');
                $('#remitente_primer_apellido').text(datos.PRIMER_APELLIDO || '');
                $('#remitente_segundo_apellido').text(datos.SEGUNDO_APELLIDO || '');
                $('#remitente_rfc').text(datos.RFC || '');
            } else {
                alert(response.message);
                limpiarValores();
            }
        },
        error: function () {
            alert('Ocurrió un error al validar la CURP.');
            limpiarValores();
        }
    });
}

// Función para limpiar los valores del contenedor
function limpiarValores() {
    $('#remitente_nombre').text(' ');
    $('#remitente_primer_apellido').text(' ');
    $('#remitente_segundo_apellido').text(' ');
    $('#remitente_rfc').text(' ');
}
