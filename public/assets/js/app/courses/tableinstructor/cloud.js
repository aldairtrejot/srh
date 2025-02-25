// Obtener token CSRF y valores de los inputs
var token = $('meta[name="csrf-token"]').attr('content');
var id = $('#id').val();
var uid_constancias = $('#uid_constancias').val();
var uid_cv = $('#uid_cv').val();

// Definir constantes para identificar los tipos de archivo
var es_constancia = 1;
var es_cv = 0;

// Definir valores para el tipo de documento
var id_cat_entrada = $('#id_cat_entrada').val();
var id_cat_tipo_cv = $('#id_cat_tipo_cv').val();
var id_cat_tipo_constancia = $('#id_cat_tipo_constancia').val();

// Definir rutas obtenidas de Blade
var routeCloudData = $('meta[name="route-cloud-data"]').attr('content');
var routeCloudCv = $('meta[name="route-cloud-cv"]').attr('content');
var routeCloudCons = $('meta[name="route-cloud-cons"]').attr('content');
var routeCloudUpload = $('meta[name="route-cloud-upload"]').attr('content');
var routeCloudDelete = $('meta[name="route-cloud-delete"]').attr('content');

$(document).ready(function () {
    getDataCloud();
    getDataDocument();

    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut();
        }
    });
});

// Obtener documentos almacenados
function getDataDocument() {
    let container_cv_entrada_vacio = $('#container_cv_entrada_vacio');
    let container_cv_entrada = $('#container_cv_entrada');
    let container_constancia_entrada_vacio = $('#container_constancia_entrada_vacio');
    let container_constancia_entrada = $('#container_constancia_entrada');

    $.ajax({
        url: routeCloudCv,
        type: 'POST',
        data: { id: id, _token: token },
        success: function (response) {
            let cvsEntrada = response.cvsEntrada;
            response.resultCvsEntrada 
                ? disabledInput('#label_cv_entrada', '#icon_cv_entrada', '#file_cv_entrada') 
                : enableIput('#label_cv_entrada', '#icon_cv_entrada', '#file_cv_entrada');

            templateCloud(container_cv_entrada, container_cv_entrada_vacio, cvsEntrada);
        }
    });

    $.ajax({
        url: routeCloudCons,
        type: 'POST',
        data: { id: id, _token: token },
        success: function (response) {
            let constanciasEntrada = response.constanciasEntrada;
            response.resultConstanciaEntrada 
                ? disabledInput('#label_constancia_entrada', '#icon_constancia_entrada', '#file_constancia_entrada') 
                : enableIput('#label_constancia_entrada', '#icon_constancia_entrada', '#file_constancia_entrada');

            templateCloud(container_constancia_entrada, container_constancia_entrada_vacio, constanciasEntrada);
        }
    });
}

// Subir archivos CV
document.getElementById('file_cv_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_cv);
    }
});

// Subir archivos Constancias
document.getElementById('file_constancia_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFileConstancia(event.target.files[0], id_cat_entrada, es_constancia);
    }
});

// Función para subir archivos CV
function sendFile(file, id_entrada_salida, esCv) {
    if (!file) return;

    let data = new FormData();
    data.append('file', file);
    data.append('id_cat_tipo_cv', id_cat_tipo_cv);
    data.append('id', id);
    data.append('id_entrada_salida', id_entrada_salida);
    data.append('esCv', esCv);

    $.ajax({
        url: routeCloudUpload,
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': token },
        success: function (response) {
            response.status 
                ? notyfEM.success("CV agregado correctamente.") 
                : notyfEM.error(response.messages);

            getDataDocument();
        }
    });
}

// Eliminar documentos
function deleteDocumentServer(uid) {
    $.ajax({
        url: routeCloudDelete,
        type: 'POST',
        data: { uid: uid, _token: token },
        success: function (response) {
            response.messages 
                ? notyfEM.success("El archivo se eliminó correctamente.") 
                : notyfEM.error("Error al eliminar archivo.");

            getDataDocument();
        }
    });
}
