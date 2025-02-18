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

// Capturar el cambio del checkbox de estatus y actualizar el campo oculto
$('#estatus').on('change', function () {
    $('#estatus-hidden').val(this.checked ? '1' : '0');
});

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
        error: function (xhr) {
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

    $('input[name="nombre"]').val(data.nombre || '');
    $('input[name="primer_apellido"]').val(data.primer_apellido || '');
    $('input[name="segundo_apellido"]').val(data.segundo_apellido || '');
    $('input[name="rfc"]').val(data.rfc || '');
    $('#estatus-hidden').val(data.estatus || '0');
    $('#estatus').prop('checked', data.estatus === "1");
}

// Envío del formulario con el método correcto (PUT en edición)
$('#form-instructor').on('submit', function (event) {
    event.preventDefault();

    let formData = $(this).serialize();
    let method = isEditing ? 'PUT' : 'POST'; // Cambia a PUT si es edición

    $.ajax({
        url: $(this).attr('action'),
        type: method,
        data: formData,
        success: function () {
            alert("Instructor actualizado correctamente.");
            window.location.href = URL_DEFAULT.concat('/tableinstructor/list');
        },
        error: function () {
            alert("Ocurrió un error al actualizar el instructor.");
        }
    });
});
