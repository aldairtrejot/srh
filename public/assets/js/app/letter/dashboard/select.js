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
            let item = response;

            if (item.isAdmin) { // Validación por usuario admin
                console.log('is admin');
                allTextForeachSelect(item.collectionArea, '#id_result_cat_area_dash');
            } else { // Validación por usuario x
                console.log('is user');
            }

            console.log(response);
            /*
            allTextForeachSelect(response.resultCollectionArea, '#id_cat_area_informe');
            allTextForeachSelect(response.resultCollectionStatus, '#id_cat_status_informe');
            allTextForeachSelect(response.resultCollectionDate, '#id_cat_date_informe');
            */
        },
    });
}