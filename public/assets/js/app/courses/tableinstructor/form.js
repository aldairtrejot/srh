// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    // Tu código aquí
    establecervalores();
    console.log('El documento está listo');
});

function establecervalores(data = {}) {
    let nombre = data.nombre || $('#nombre').val();
    let primer_apellido = data.primer_apellido || $('#primer_apellido').val();
    let segundo_apellido = data.segundo_apellido || $('#segundo_apellido').val();
    let rfc = data.rfc || $('#rfc').val();

    $('#label_nombre').text(nombre);
    $('#label_primer_apellido').text(primer_apellido);
    $('#label_segundo_apellido').text(segundo_apellido);
    $('#label_rfc').text(rfc);
}

function limpiarValores() {
    $('#label_nombre').text('');
    $('#label_primer_apellido').text('');
    $('#label_segundo_apellido').text('');
    $('#label_rfc').text('');
}

function validarcurp() {
    let curp = $('#curp').val().trim();

    $.ajax({
        url: URL_DEFAULT.concat('/tableinstructor/table/dataCurp'),
        type: 'POST',
        data: { curp: curp },
        success: function (response) {
            if (response.status && response.value) {
                establecervalores(response.value);
            } else {
                alert(response.message || 'No se encontraron datos.');
                limpiarValores();
            }
        },
        error: function () {
            limpiarValores();
        }
    });
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

const isEditing = $('#form-instructor').attr('action').includes("update");

if (isEditing) {
    $('#boton-consultar-curp').prop('disabled', true);
}

function llenarDatosInstructor(data) {
    $('#remitente_nombre').text(data.nombre || 'N/A');
    $('#remitente_primer_apellido').text(data.primer_apellido || 'N/A');
    $('#remitente_segundo_apellido').text(data.segundo_apellido || 'N/A');
    $('#remitente_rfc').text(data.rfc || 'N/A');

    $('#curp').val(data.curp || '');
    $('#estatus').prop('checked', data.estatus === "1");
}

$('#form-instructor').on('submit', function (event) {
    event.preventDefault();

    let formData = $(this).serializeArray();

    formData.push({ name: "estatus", value: $('#estatus').is(':checked') ? "1" : "0" });

    if (isEditing) {
        formData.push({ name: "_method", value: "PUT" });
    }

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
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

/*document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form-instructor");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Evita la recarga de la página

        let formData = new FormData(this);
        let instructorId = this.getAttribute("data-id"); // Obtener el ID del instructor

        fetch(`/instructor/${instructorId}/editar-curp`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                alert("✅ CURP actualizada correctamente.");
                window.location.reload();
            } else {
                alert("❌ Error al actualizar la CURP: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
}); */
