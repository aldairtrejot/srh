var token = $('meta[name="csrf-token"]').attr('content'); // Token CSRF
var id_tbl_cv = $('#id_tbl_cv').val(); // ID del instructor

$(document).ready(function () {
    getDataDocument();
});

// 📌 Obtener lista de documentos
function getDataDocument() {
    if (!id_tbl_cv || isNaN(id_tbl_cv)) {
        console.error("❌ Error: ID de instructor inválido.");
        return;
    }

    let container_cv_vacio = $('#container_cv_entrada_vacio');
    let container_cv = $('#container_cv_entrada');
    let container_constancia_vacio = $('#container_constancia_entrada_vacio');
    let container_constancia = $('#container_constancia_entrada');

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

            templateCloud(container_cv, container_cv_vacio, cvs);
            templateCloud(container_constancia, container_constancia_vacio, constancias);
        },
        error: function () {
            console.error("❌ Error en la consulta de documentos.");
        }
    });
}

// 📌 Mostrar documentos con botones de acción
function templateCloud(container, emptyContainer, data) {
    container.empty();

    if (data && data.length > 0) {
        emptyContainer.hide();
        data.forEach(function (file) {
            container.append(`
                <div class="file-item">
                    <span>${file.nombre}</span>
                    <div class="file-actions">
                        <button onclick="seeDocument('${file.uid}')" class="btn-view">👁 Ver</button>
                        <button onclick="downloadDocument('${file.uid}')" class="btn-download">⬇ Descargar</button>
                        <button onclick="deleteDocument('${file.uid}')" class="btn-delete">🗑 Eliminar</button>
                    </div>
                </div>
            `);
        });
    } else {
        emptyContainer.html(`
            <div class="file-empty">
                <span style="font-size: 20px; color: #aaa;">📁 Sin contenido</span>
            </div>
        `).show();
    }
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
            setTimeout(getDataDocument, 1000); // Esperar 1 segundo antes de actualizar
        },
        error: function () {
            console.error("❌ Error al subir archivo.");
        }
    });
}

// 📌 Ver documento en nueva ventana
function seeDocument(uid) {
    let form = document.createElement("form");
    form.method = "POST";
    form.action = $('meta[name="route-cloud-see"]').attr('content');
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
function downloadDocument(uid) {
    let urlBase = $('meta[name="route-cloud-download"]').attr('content');
    if (!urlBase.includes("{uuid}")) {
        console.error("❌ Error: Ruta de descarga incorrecta.");
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
                setTimeout(getDataDocument, 1000); // Esperar 1 segundo antes de actualizar
            } else {
                console.error("❌ Error al eliminar documento.");
            }
        },
        error: function () {
            console.error("❌ Error en la solicitud de eliminación.");
        }
    });
}

// 📌 Eventos de carga de archivos
document.getElementById('file_cv_entrada').addEventListener('change', function (event) {
    sendFile(event.target.files[0], true);
});

document.getElementById('file_constancia_entrada').addEventListener('change', function (event) {
    sendFile(event.target.files[0], false);
});
