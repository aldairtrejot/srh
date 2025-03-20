// Elcodigo inicia los combox dependiendo de los roles
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// La función inicia los datos para la busqueda de información de combox
function initSelect() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/dashboard/getSelect'),
        type: 'POST',
        data: {
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {

            console.log(response);
            /*
            allTextForeachSelect(response.resultCollectionArea, '#id_cat_area_informe');
            allTextForeachSelect(response.resultCollectionStatus, '#id_cat_status_informe');
            allTextForeachSelect(response.resultCollectionDate, '#id_cat_date_informe');
            */
        },
    });
}