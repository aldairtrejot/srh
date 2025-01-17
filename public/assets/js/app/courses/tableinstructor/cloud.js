// Variables iniciales y configuración
var token = $('meta[name="csrf-token"]').attr('content'); // Token CSRF
var id_instructor = $('#id_instructor').val(); // ID del instructor
var tipo_cv = "cv"; // Identificador para CV
var tipo_constancia = "constancia"; // Identificador para constancia

// Inicialización al cargar el documento
$(document).ready(function () {
    getDataCloud(); // Obtener datos del instructor
    getDataDocument(); // Cargar documentos

    // Cerrar modal al hacer clic fuera de él
    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar modal
        }
    });
});

// Obtener datos generales del instructor
function getDataCloud() {
    $.ajax({
        url: URL_DEFAULT.concat('/cloud/data'),
        type: 'POST',
        data: {
            id_instructor: id_instructor,
            _token: token,
        },
        success: function (response) {
            let item = response.value;

            // Actualizar datos del encabezado
            $('#_nombreInstructor').text(item.nombre_instructor || 'No disponible');
            $('#_estatusInstructor').text(item.estatus || 'No definido');
        },
    });
}

// Cargar documentos asociados al instructor
function getDataDocument() {
    let container_cv = $('#container_cv');
    let container_cv_empty = $('#container_cv_empty');
    let container_constancia = $('#container_constancia');
    let container_constancia_empty = $('#container_constancia_empty');

    $.ajax({
        url: URL_DEFAULT.concat('/cloud/files'),
        type: 'POST',
        data: {
            id_instructor: id_instructor,
            _token: token,
        },
        success: function (response) {
            let cvs = response.cvs;
            let constancias = response.constancias;

            // Renderizar documentos en la interfaz
            templateCloud(container_cv, container_cv_empty, cvs);
            templateCloud(container_constancia, container_constancia_empty, constancias);
        },
    });
}

// Plantilla para renderizar documentos
function templateCloud(container, containerEmpty, data) {
    container.empty();
    if (data && data.length > 0) {
        data.forEach(function (doc) {
            const fileHTML = `
                <div class="document-item">
                    <p>${doc.nombre}</p>
                    <button class="btn btn-primary" onclick="viewDocument('${doc.uid}')">Ver</button>
                    <button class="btn btn-danger" onclick="deleteDocument('${doc.uid}')">Eliminar</button>
                </div>
            `;
            container.append(fileHTML);
        });
        containerEmpty.hide();
    } else {
        containerEmpty.show();
    }
}

// Subir un archivo
document.getElementById('file_cv').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], tipo_cv);
    }
});

document.getElementById('file_constancia').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], tipo_constancia);
    }
});

function sendFile(file, tipo) {
    if (file) {
        let data = new FormData();
        data.append('file', file);
        data.append('tipo', tipo);
        data.append('id_instructor', id_instructor);

        $.ajax({
            url: URL_DEFAULT.concat("/cloud/upload"),
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': token,
            },
            success: function (response) {
                if (response.status) {
                    alert("Archivo subido correctamente.");
                } else {
                    alert(response.message || "Error al subir archivo.");
                }
                getDataDocument(); // Actualizar lista de documentos
            },
        });
    }
}

// Ver un documento
function viewDocument(uid) {
    window.open(URL_DEFAULT.concat(`/cloud/see?uid=${uid}`), '_blank');
}

// Eliminar un documento
function deleteDocument(uid) {
    $('#modalBackdrop').fadeIn(); // Mostrar modal

    $('#cancelBtn').click(function () {
        $('#modalBackdrop').fadeOut(); // Cerrar modal al cancelar
    });

    $('#confirmBtn').click(function () {
        deleteDocumenServer(uid);
        $('#modalBackdrop').fadeOut(); // Cerrar modal al confirmar
    });
}

// Eliminar un documento en el servidor
function deleteDocumenServer(uid) {
    $.ajax({
        url: URL_DEFAULT.concat('/cloud/delete'),
        type: 'POST',
        data: {
            uid: uid,
            _token: token,
        },
        success: function (response) {
            if (response.status) {
                alert("Archivo eliminado correctamente.");
            } else {
                alert("Error al eliminar archivo.");
            }
            getDataDocument(); // Actualizar lista de documentos
        },
    });
}
