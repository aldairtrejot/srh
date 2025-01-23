//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form
var id = $('#id').val();//Obtener elemento
var id_cat_area = $('#id_cat_area').val(); //Se obtiene el id de la area
var id_cat_salida = $('#id_cat_salida').val(); //Se obtiene el id de la area
var id_cat_entrada = $('#id_cat_entrada').val(); //Se obtiene el id de la area
var id_cat_tipo_oficio = $('#id_cat_tipo_oficio').val(); //Se obtiene el id de la area
var es_cons = 1; //Identifica si es cons
var es_cv = 0;//Identifca si es un cv

//Inicio de variables
$(document).ready(function () {
    getDataCloud();
    getDataDocument();

    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        }
    });
});

//La funcion lista los documentos que existen en el cloud
function getDataDocument() {
    let container_cv_entrada_vacio = $('#container_cv_entrada_vacio');
    let container_cv_entrada = $('#container_cv_entrada');
    let container_cons_entrada_vacio = $('#container_cons_entrada_vacio');
    let container_cons_entrada = $('#container_cons_entrada');
    let container_cv_salida_vacio = $('#container_cv_salida_vacio');
    let container_cv_salida = $('#container_cv_salida');
    let container_cons_salida_vacio = $('#container_cons_salida_vacio');
    let container_cons_salida = $('#container_cons_salida');

    $.ajax({
        url: URL_DEFAULT.concat('/round/cloud/cvs'),
        type: 'POST',
        data: {
            id_tbl_cons: id,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let cvsEntrada = response.cvsEntrada;  // Suponiendo que la respuesta tiene una propiedad 'value' con los datos
            let consEntrada = response.consEntrada;
            let cvSalida = response.cvSalida;
            let consSalida = response.consSalida;

            //Habilita o desabilita los botones de agregar
            response.resultConsEntrada ? disabledInput('#label_cons_entrada', '#icon_cons_entrada', '#file_cons_entrada') : enableIput('#label_cons_entrada', '#icon_cons_entrada', '#file_cons_entrada');
            response.resultConsSalida ? disabledInput('#label_cons_salida', '#icon_cons_salida', '#file_cons_salida') : enableIput('#label_cons_salida', '#icon_cons_salida', '#file_cons_salida');
            response.resultCvsEntrada ? disabledInput('#label_cv_entrada', '#icon_cv_entrada', '#file_cv_entrada') : enableIput('#label_cv_entrada', '#icon_cv_entrada', '#file_cv_entrada');
            response.resultCvsSalida ? disabledInput('#label_cv_salida', '#icon_cv_salida', '#file_cv_salida') : enableIput('#label_cv_salida', '#icon_cv_salida', '#file_cv_salida');

            templateCloud(container_cv_entrada, container_cv_entrada_vacio, cvsEntrada); //Listamos la informacion
            templateCloud(container_cons_entrada, container_cons_entrada_vacio, consEntrada); //Listamos la informacion
            templateCloud(container_cv_salida, container_cv_salida_vacio, cvSalida);
            templateCloud(container_cons_salida, container_cons_salida_vacio, consSalida);

        },
    });
}

//La funcion obtiene los datos del encabezado de cloud
function getDataCloud() {

    $.ajax({
        url: URL_DEFAULT.concat('/round/cloud/data'),
        type: 'POST',
        data: {
            id: id,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.value; //Obtenemos la consulta

            $('#_noCons').text(item.num_turno_sistema); // establecer los valores
            $('#_noCorrespondencia').text(item.num_turno_sistema_correspondencia); // establecer los valores
            $('#_noAnio').text(item.anio); // establecer los valores
            $('#_fechaInicio').text(item.fecha_inicio); // establecer los valores
            $('#_fechaFin').text(item.fecha_fin); // establecer los valores
        },
    });
}


//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_cons_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_cons); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_cv_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_cv); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_cons_salida').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_salida, es_cons); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_cv_salida').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_salida, es_cv); // Pasa el archivo real a la función
    }
});

function sendFile(file, id_entrada_salida, esCons) {
    if (file) {
        let data = new FormData();// Crear el objeto FormData
        data.append('file', file);
        data.append('id_cat_tipo_cons', id_cat_tipo_oficio);
        data.append('id_cat_area', id_cat_area);
        data.append('id', id);
        data.append('id_entrada_salida', id_entrada_salida);
        data.append('esCons', esCons);
        $.ajax({
            url: URL_DEFAULT.concat("/round/cloud/upload"),
            type: 'POST',
            data:
                data, // Enviar directamente el FormData
            processData: false,  // No procesar los datos, jQuery no debe intentar convertir los datos en una cadena
            contentType: false,  // No establecer un Content-Type porque el navegador lo hará automáticamente
            headers: {
                'X-CSRF-TOKEN': token  // Usar el token CSRF para proteger la solicitud
            },
            success: function (response) {
                if (response.status) { //Validacion si es que los cambios se han agregado correctamente
                    notyfEM.success("Documento agregado correctamente.");
                } else {
                    notyfEM.error(response.messages);
                }
                getDataDocument(); //Lista de nuevo e directorio
            },
        });
    }
}

//La funcion elimina un documento
function deleteDocument(uid) {

    $('#modalBackdrop').fadeIn();//Iniciar ventana modal

    $('#cancelBtn').click(function () { //Se pulsa el boton de cancelar
        $('#modalBackdrop').fadeOut(); // Cerrar la ventana modal
    });

    $('#confirmBtn').click(function () {///Se da click al boton de confirmar y se ejecuta el evento de eliminacion
        deleteDocumenServer(uid);
        $('#modalBackdrop').fadeOut(); // Cerrar modal después de confirmar
    });
}

//La funcion elimina cons del repositorio, solo de la base
function deleteDocumenServer(uid) {
    $.ajax({
        url: URL_DEFAULT.concat('/round/cloud/delete'),
        type: 'POST',
        data: {
            uid: uid,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (response.messages) {
                notyfEM.success("El archivo se eliminó correctamente.");
            } else {
                notyfEM.error("Algo inesperado ocurrió al realizar la acción.");
            }
            getDataDocument(); //Lista de nuevo e directorio
        },
    });
}