
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form
var id = $('#id').val();//Obtener elemento
var id_cat_area = $('#id_cat_area').val(); //Se obtiene el id de la area
var id_cat_salida = $('#id_cat_salida').val(); //Se obtiene el id de la area
var id_cat_entrada = $('#id_cat_entrada').val(); //Se obtiene el id de la area
var id_cat_tipo_oficio = $('#id_cat_tipo_oficio').val(); //Se obtiene el id de la area
var es_oficio = 1; //Identifica si es oficio
var es_anexo = 0;//Identifca si es un anexo

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
    let container_anexo_entrada_vacio = $('#container_anexo_entrada_vacio');
    let container_anexo_entrada = $('#container_anexo_entrada');
    let container_oficio_entrada_vacio = $('#container_oficio_entrada_vacio');
    let container_oficio_entrada = $('#container_oficio_entrada');
    let container_anexo_salida_vacio = $('#container_anexo_salida_vacio');
    let container_anexo_salida = $('#container_anexo_salida');
    let container_oficio_salida_vacio = $('#container_oficio_salida_vacio');
    let container_oficio_salida = $('#container_oficio_salida');

    $.ajax({
        url: URL_DEFAULT.concat('/inside/cloud/anexos'),
        type: 'POST',
        data: {
            id_tbl_oficio: id,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let anexosEntrada = response.anexosEntrada;  // Suponiendo que la respuesta tiene una propiedad 'value' con los datos
            let oficosEntrada = response.oficosEntrada;
            let anexoSalida = response.anexoSalida;
            let oficosSalida = response.oficosSalida;

            //Habilita o desabilita los botones de agregar
            response.resultOficioEntrada ? disabledInput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada') : enableIput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');
            response.resultOficioSalida ? disabledInput('#label_oficio_salida', '#icon_oficio_salida', '#file_oficio_salida') : enableIput('#label_oficio_salida', '#icon_oficio_salida', '#file_oficio_salida');
            response.resultAnexosEntrada ? disabledInput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada') : enableIput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');
            response.resultAnexosSalida ? disabledInput('#label_anexo_salida', '#icon_anexo_salida', '#file_anexo_salida') : enableIput('#label_anexo_salida', '#icon_anexo_salida', '#file_anexo_salida');

            templateCloud(true, container_anexo_entrada, container_anexo_entrada_vacio, anexosEntrada); //Listamos la informacion
            templateCloud(true, container_oficio_entrada, container_oficio_entrada_vacio, oficosEntrada); //Listamos la informacion
            templateCloud(true, container_anexo_salida, container_anexo_salida_vacio, anexoSalida);
            templateCloud(true, container_oficio_salida, container_oficio_salida_vacio, oficosSalida);
        },
    });
}

//La funcion obtiene los datos del encabezado de cloud
function getDataCloud() {

    $.ajax({
        url: URL_DEFAULT.concat('/inside/cloud/data'),
        type: 'POST',
        data: {
            id: id,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.value; //Obtenemos la consulta

            $('#_noOficio').text(item.num_turno_sistema); // establecer los valores
            $('#_noCorrespondencia').text(item.num_turno_sistema_correspondencia); // establecer los valores
            $('#_noAnio').text(item.anio); // establecer los valores
            $('#_fechaInicio').text(item.fecha_inicio); // establecer los valores
            $('#_fechaFin').text(item.fecha_fin); // establecer los valores
        },
    });
}


//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_oficio_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_oficio); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_anexo_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_anexo); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_oficio_salida').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_salida, es_oficio); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_anexo_salida').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_salida, es_anexo); // Pasa el archivo real a la función
    }
});

function sendFile(file, id_entrada_salida, esOficio) {
    if (file) {
        showSpinner();// Inicio de spinner
        let data = new FormData();// Crear el objeto FormData
        data.append('file', file);
        data.append('id_cat_tipo_oficio', id_cat_tipo_oficio);
        data.append('id_cat_area', id_cat_area);
        data.append('id', id);
        data.append('id_entrada_salida', id_entrada_salida);
        data.append('esOficio', esOficio);
        $.ajax({
            url: URL_DEFAULT.concat("/inside/cloud/upload"),
            type: 'POST',
            data:
                data, // Enviar directamente el FormData
            processData: false,  // No procesar los datos, jQuery no debe intentar convertir los datos en una cadena
            contentType: false,  // No establecer un Content-Type porque el navegador lo hará automáticamente
            headers: {
                'X-CSRF-TOKEN': token  // Usar el token CSRF para proteger la solicitud
            },
            success: function (response) {
                hideSpinner(); // Se oculta el spinner
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

//La funcion elimina oficios del repositorio, solo de la base
function deleteDocumenServer(uid) {
    $.ajax({
        url: URL_DEFAULT.concat('/inside/cloud/delete'),
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