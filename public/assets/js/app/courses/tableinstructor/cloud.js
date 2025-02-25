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

// Definir funciones antes de llamar en document.ready()
function getDataCloud() {
    $.ajax({
        url: routeCloudData,
        type: 'POST',
        data: { id: id, _token: token },
        success: function (response) {
            let item = response.value;
            $('#_noCv').text(item.num_turno_sistema);
            $('#_noConstancia').text(item.num_documento);
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener datos de Cloud:", error);
        }
    });
}

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
                : enableInput('#label_cv_entrada', '#icon_cv_entrada', '#file_cv_entrada');

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
                : enableInput('#label_constancia_entrada', '#icon_constancia_entrada', '#file_constancia_entrada');

            templateCloud(container_constancia_entrada, container_constancia_entrada_vacio, constanciasEntrada);
        }
    });
}
