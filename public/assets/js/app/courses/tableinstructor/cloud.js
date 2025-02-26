// Obtener el token CSRF del meta tag para proteger las solicitudes AJAX.
var token = $('meta[name="csrf-token"]').attr('content');

function uploadFile(isCv) {
    let fileInput = isCv ? document.getElementById("file_cv_entrada") : document.getElementById("file_constancia_entrada");
    let file = fileInput.files[0];

    if (!file) {
        alert("Selecciona un archivo.");
        return;
    }

    let idTblCvElement = document.getElementById("id_tbl_cv");

    if (!idTblCvElement) {
        console.error("Error en JavaScript: id_tbl_cv no encontrado.");
        alert("Error: No se encontró el campo id_tbl_cv en la vista.");
        return;
    }

    let idTblCv = idTblCvElement.value;

    if (!idTblCv || isNaN(idTblCv)) {
        console.error("Error en JavaScript: id_tbl_cv es inválido", { id_tbl_cv: idTblCv });
        alert("Error: ID de instructor inválido.");
        return;
    }

    let formData = new FormData();
    formData.append("file", file);
    formData.append("id_tbl_cv", idTblCv);
    formData.append("esCv", isCv ? 1 : 0);

    fetch(document.querySelector('meta[name="route-cloud-upload"]').content, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.messages);
        location.reload();
    })
    .catch(error => console.error("Error en subida:", error));
}
