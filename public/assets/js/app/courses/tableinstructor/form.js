// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');

// Configurar el token CSRF para todas las solicitudes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Detectar si estamos en edición
const isEditing = $('#form-instructor').attr('action').includes("update");

// Si estamos en edición, deshabilitar la consulta de CURP
if (isEditing) {
    $('#boton-consultar-curp').prop('disabled', true);
}

// Función para validar la CURP y obtener información del instructor
function validarcurp() {
    let curp = $('#curp').val().trim();

    if (curp === '') {
        alert('Por favor, ingresa una CURP.');
        return;
    }

    $.ajax({
        url: URL_DEFAULT.concat('/tableinstructor/table/dataCurp'),
        type: 'POST',
        data: { curp: curp },
        success: function (response) {
            if (response.status && response.value) {
                llenarDatosInstructor(response.value);
            } else {
                alert(response.message || 'No se encontraron datos.');
                limpiarValores();
            }
        },
        error: function () {
            alert('Ocurrió un error al validar la CURP.');
            limpiarValores();
        }
    });
}

// Función para llenar los datos del instructor en los campos del formulario
function llenarDatosInstructor(data) {
    $('#remitente_nombre').text(data.nombre || 'N/A');
    $('#remitente_primer_apellido').text(data.primer_apellido || 'N/A');
    $('#remitente_segundo_apellido').text(data.segundo_apellido || 'N/A');
    $('#remitente_rfc').text(data.rfc || 'N/A');

    $('#curp').val(data.curp || '');
    $('#estatus').prop('checked', data.estatus === "1");
}

// Envío del formulario con el método correcto (PUT en edición)
$('#form-instructor').on('submit', function (event) {
    event.preventDefault();

    let formData = $(this).serializeArray();

    // Agregar estatus manualmente
    formData.push({ name: "estatus", value: $('#estatus').is(':checked') ? "1" : "0" });

    // Si es edición, agregamos el método PUT manualmente
    if (isEditing) {
        formData.push({ name: "_method", value: "PUT" });
    }

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST', // Laravel manejará la conversión a PUT
        data: $.param(formData),
        success: function () {
            alert("Instructor actualizado correctamente.");
            window.location.href = URL_DEFAULT.concat('/tableinstructor/list');
        },
        error: function () {
            alert("Ocurrió un error al actualizar el instructor.");
        }
    });
});
