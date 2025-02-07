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
            console.log("✅ Respuesta del servidor:", response); // Depuración en consola

            if (response.status && response.value) {
                let data = response.value;

                // Evita errores si `data` es undefined o no tiene las propiedades esperadas
                $('#remitente_nombre').text(data.nombre || 'N/A');
                $('#remitente_primer_apellido').text(data.primer_apellido || 'N/A');
                $('#remitente_segundo_apellido').text(data.segundo_apellido || 'N/A');
                $('#remitente_rfc').text(data.rfc || 'N/A');

            } else {
                console.warn("⚠ No se encontraron datos en response.value");
                alert(response.message || 'No se encontraron datos.');
                limpiarValores();
            }
        },
        error: function (xhr) {
            console.error("❌ Error en AJAX:", xhr.responseText);
            alert('Ocurrió un error al validar la CURP.');
            limpiarValores();
        }
    });
}

// Función para limpiar los valores en caso de error o datos no encontrados
function limpiarValores() {
    $('#remitente_nombre').text('');
    $('#remitente_primer_apellido').text('');
    $('#remitente_segundo_apellido').text('');
    $('#remitente_rfc').text('');
}
