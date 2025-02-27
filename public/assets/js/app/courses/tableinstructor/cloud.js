var token = $('meta[name="csrf-token"]').attr('content'); // Token CSRF
var id_tbl_cv = $('#id_tbl_cv').val(); // ID del instructor

$(document).ready(function () {
    if (!id_tbl_cv || isNaN(id_tbl_cv)) {
        console.error("❌ Error: ID de instructor inválido.");
        return;
    }
    getDataDocument();
});

// 📌 Obtener lista de documentos
function getDataDocument() {
    let container_cv = $('#container_cv_entrada');
    let container_cv_vacio = $('#container_cv_entrada_vacio');
    let container_constancia = $('#container_constancia_entrada');
    let container_constancia_vacio = $('#container_constancia_entrada_vacio');

    $.ajax({
        url: $('meta[name="route-cloud-data"]').attr('content'),
        type: 'POST',
        data: { id_tbl_cv: id_tbl_cv, _token: token },
        success: function (response) {
            if (!response.status) {
                console.warn("⚠️ No se encontraron documentos.");
                container_cv_vacio.show();
                container_constancia_vacio.show();
                return;
            }

            let constancias = response.constancias || [];
            let cvs = response.cvs || [];

            templateCloud(true, container_cv, container_cv_vacio, cvs);
            templateCloud(true, container_constancia, container_constancia_vacio, constancias);
        },
        error: function (xhr, status, error) {
            console.error("❌ Error en la consulta de documentos:", xhr.responseText);
        }
    });
}

// 📌 Mostrar documentos con botones de acción
function templateCloud(bool, templateData, templateDataNull, data) {
    templateData.empty();

    if (data.length !== 0) {
        templateDataNull.hide();
        data.forEach(function (file) {
            let fileHTML = generateFileHTML(bool, file);
            templateData.append(fileHTML);
        });
    } else {
        templateDataNull.show();
    }
}

// 📌 Generar HTML para cada documento
function generateFileHTML(boolx, template) {
    return `
        <div class="custom-file-container">
            <div class="custom-file-icon-container">
                <i style="color:#777777" class="fa fa-file" aria-hidden="true"></i>
                <div class="custom-button-container">
                    <button onclick="seeDocumentUid('${template.uid}')" style="background: #10312b" class="custom-button" title="Ver">
                        <i style="color: white" class="fa fa-eye"></i>
                    </button> 
                    ${boolx ? `
                        <button onclick="download('${template.uid}')" style="background: #707070" class="custom-button" title="Descargar">
                            <i style="color: white" class="fa fa-download"></i>
                        </button>
                        <button onclick="deleteDocument('${template.uid}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                            <i style="color: white" class="fa fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
            <div class="custom-file-name">
                <p>${template.nombre}</p>
            </div>
        </div>
    `;
}

// 📌 Subir archivo
function sendFile(file, esCv) {
    if (!file) return;

    let formData = new FormData();
    formData.append('file', file);
    formData.append('id_tbl_cv', id_tbl_cv);
    formData.append('esCv', esCv ? 1 : 0);

    $.ajax({
        url: $('meta[name="route-cloud-upload"]').attr('content'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': token },
        success: function () {
            console.log("✅ Archivo subido correctamente.");
            setTimeout(getDataDocument, 1000);
        },
        error: function (xhr, status, error) {
            console.error("❌ Error al subir archivo:", xhr.responseText);
        }
    });
}

// 📌 Ver documento en nueva ventana
function seeDocumentUid(uid) {
    let url = $('meta[name="route-cloud-see"]').attr('content');
    
    let form = document.createElement("form");
    form.method = "POST";
    form.action = url;
    form.target = "_blank";

    let inputUid = document.createElement("input");
    inputUid.type = "hidden";
    inputUid.name = "uid";
    inputUid.value = uid;
    form.appendChild(inputUid);

    let inputToken = document.createElement("input");
    inputToken.type = "hidden";
    inputToken.name = "_token";
    inputToken.value = token;
    form.appendChild(inputToken);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// 📌 Descargar documento
function download(uid) {
    let urlBase = $('meta[name="route-cloud-download"]').attr('content');
    if (!urlBase || !urlBase.includes("{uuid}")) {
        console.error("❌ Error: Ruta de descarga incorrecta.", urlBase);
        return;
    }
    let url = urlBase.replace("{uuid}", uid);
    window.location.href = url;
}

// 📌 Eliminar documento
function deleteDocument(uid) {
    if (!confirm("¿Estás seguro de eliminar este documento?")) return;

    $.ajax({
        url: $('meta[name="route-cloud-delete"]').attr('content'),
        type: 'POST',
        data: { uid: uid, _token: token },
        success: function (response) {
            if (response.status) {
                console.log("✅ Documento eliminado correctamente.");
                setTimeout(getDataDocument, 1000);
            } else {
                console.error("❌ Error al eliminar documento:", response);
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ Error en la solicitud de eliminación:", xhr.responseText);
        }
    });
}

// 📌 Eventos de carga de archivos
$('#file_cv_entrada').on('change', function (event) {
    sendFile(event.target.files[0], true);
});

$('#file_constancia_entrada').on('change', function (event) {
    sendFile(event.target.files[0], false);
});
